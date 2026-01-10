<?php

require_once __DIR__ . '/../models/ChatModel.php';
// 1️⃣ BẮT BUỘC: Phải gọi file Model Sản phẩm
require_once __DIR__ . '/../models/SanphamModel.php'; 
require_once __DIR__ . '/../helpers/time_helper.php';

class Chat {
    private $chatModel;
    private $productModel; // 2️⃣ Khai báo biến model sản phẩm

    public function __construct($conn) {
         $this->chatModel = new ChatModel($conn);
         // 3️⃣ Khởi tạo Model Sản phẩm
         $this->productModel = new SanphamModel($conn); 
    }

    public function index() {
        $this->start();
    }

    // ===== TRANG CHÍNH =====
   public function start($param = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        $my_id = $_SESSION['user_id']; 
        $active_conversation_id = 0;

        // --- XÁC ĐỊNH CONVERSATION ID ---
        if ($param !== null) {
            if (ctype_digit((string)$param) && $this->chatModel->isConversationOfUser((int)$param, $my_id)) {
                $active_conversation_id = (int)$param;
            } else {
                $active_conversation_id = $this->chatModel->getOrCreateConversation($my_id, $param);
            }
        } else {
            $latest = $this->chatModel->getLatestConversation($my_id);
            $active_conversation_id = $_SESSION['active_conversation_id'] ?? ($latest[0]['id_conversation'] ?? 0);
        }

        // --- KHỞI TẠO BIẾN MẶC ĐỊNH CHO VIEW ---
        $sender_name = '';
        $messages = [];
        $sender_id = 0;
        $product_context = []; // Biến chứa thông tin sản phẩm ghim

        // --- LOAD DỮ LIỆU TIN NHẮN ---
        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);

            $_SESSION['active_conversation_id'] = $active_conversation_id;
            $_SESSION['sender_id'] = $sender_id; 
        } else {
            unset($_SESSION['active_conversation_id']);
            unset($_SESSION['sender_id']);
        }

        $conversations = $this->chatModel->loadConversations($my_id);

        // 4️⃣ XỬ LÝ GHIM SẢN PHẨM (LOGIC MỚI - ĐÃ FIX)
        if (isset($_SESSION['current_viewed_product_id'])) {
            $pid = $_SESSION['current_viewed_product_id'];
            
            // Gọi Model lấy dữ liệu
            $product_data = $this->productModel->getProductById($pid);

            // Kiểm tra: Có dữ liệu + Đang ở trong cuộc hội thoại
            if ($product_data && $active_conversation_id > 0) {
                
                // QUAN TRỌNG: Chỉ hiện ghim nếu người đang chat (sender_id) 
                // chính là người bán sản phẩm đó (id_user)
                if ($sender_id == $product_data['id_user']) {
                    $product_context = [
                        'id'        => $product_data['id_sanpham'],
                        'name'      => $product_data['ten_sanpham'],
                        'price'     => $product_data['gia'],
                        'image'     => $product_data['avatar'],
                        
                        // 👇 QUAN TRỌNG: Thêm cái này để fix lỗi View
                        'seller_id' => $product_data['id_user'] 
                    ];
                }
            }
            
            // ❌ KHÔNG DÙNG UNSET Ở ĐÂY NỮA (Để giữ session cho lần sau)
            // unset($_SESSION['current_viewed_product_id']); 
        }

        // Gọi View
        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }

    public function send()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        $my_id = $_SESSION['user_id'];
        $content = trim($_POST['message'] ?? '');
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($message_id > 0 && $content !== '') {
            $this->chatModel->updateMessage($message_id, $my_id, $content);
        }
        else if ($content !== '') {
            $to_user = $_SESSION['sender_id'] ?? ''; 
            if (!empty($to_user)) {
                $conversation_id = $this->chatModel->insertMessage($my_id, $to_user, $content);
                $_SESSION['active_conversation_id'] = $conversation_id;
            }
        }

        header("Location: /baitaplon/chat");
        exit;
    }

    public function search()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login"); exit;
        }

        $my_id   = $_SESSION['user_id'];
        $keyword = trim($_POST['keyword'] ?? '');

        if ($keyword !== '') {
            $conversations = $this->chatModel->searchConversationBySenderName($my_id, $keyword);
        } else {
            $conversations = $this->chatModel->loadConversations($my_id);
        }

        $active_conversation_id = $_SESSION['active_conversation_id'] 
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        $sender_id   = 0;
        $sender_name = '';
        $messages    = [];
        $product_context = []; // Search thì không hiện sản phẩm

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }

    public function deleteMessage()
    {
        if (!isset($_SESSION['user_id'])) return;

        $my_id = $_SESSION['user_id'];
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($message_id > 0) {
            $this->chatModel->deleteMessage($message_id, $my_id);
        }

        header("Location: /baitaplon/chat");
        exit;
    }

    public function searchMessage()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login"); exit;
        }

        $my_id = $_SESSION['user_id'];
        $keyword = trim($_POST['message_keyword'] ?? '');

        $conversations = $this->chatModel->loadConversations($my_id);

        $active_conversation_id = $_SESSION['active_conversation_id'] 
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        $sender_id = 0;
        $sender_name = '';
        $messages = [];
        $product_context = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);

            if ($keyword !== '') {
                $messages = $this->chatModel->searchMessageByContent($active_conversation_id, $keyword);
            } else {
                $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);
            }
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }
}
?>