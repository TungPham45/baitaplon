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
            // 1. Kiểm tra đăng nhập
            if (!isset($_SESSION['user_id'])) {
                header("Location: /baitaplon/Login");
                exit;
            }

            $my_id = $_SESSION['user_id']; 
            $active_conversation_id = 0;

            // 2. XÁC ĐỊNH CONVERSATION ID
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

            // 3. KHỞI TẠO BIẾN CHO VIEW
            $sender_name = '';
            $messages = [];
            $sender_id = 0;
            $product_context = []; // Biến chứa thông tin sản phẩm ghim

            // 4. LOAD DỮ LIỆU TIN NHẮN & NGƯỜI CHAT
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

            // 5. 🔥 LOGIC MỚI: XỬ LÝ SẢN PHẨM GHIM (DATABASE) 🔥
            $current_product_id = null;

            // TRƯỜNG HỢP A: Người dùng bấm nút "Chat ngay" từ trang chi tiết (Có dữ liệu POST)
            // Hành động: CẬP NHẬT sản phẩm mới vào Database cho cuộc hội thoại này
           if (isset($_POST['product_id_post']) && $active_conversation_id > 0) {
                $pid_post = (int)$_POST['product_id_post'];
                
                // 1. Lưu vào DB
                $this->chatModel->updateConversationProduct($active_conversation_id, $pid_post);
                
                // 2. 🔥 THÊM ĐOẠN NÀY: Chuyển hướng về chính trang này (nhưng là GET) 
                // Mục đích: Xóa sạch dữ liệu POST để F5 không bị lỗi gửi lại form
                // Lưu ý: Lúc này chuyển hướng theo ID cuộc hội thoại cho chuẩn
                header("Location: /baitaplon/Chat/start/" . $active_conversation_id);
                exit; 
            }
            // TRƯỜNG HỢP B: Người dùng vào từ danh sách chat (Không có POST)
            // Hành động: LẤY sản phẩm đã lưu trong Database da
            else if ($active_conversation_id > 0) {
                $current_product_id = $this->chatModel->getProductOfConversation($active_conversation_id);
            }

            // 6. LẤY CHI TIẾT SẢN PHẨM TỪ MODEL VÀ HIỂN THỊ
            if ($current_product_id) {
                $product_data = $this->productModel->getProductById($current_product_id);

                if ($product_data) {
                    // Logic: Chỉ hiện nếu người đang chat cùng (sender_id) là người bán (id_user)
                    if ($sender_id == $product_data['id_user']) {
                        $product_context = [
                            'id'        => $product_data['id_sanpham'],
                            'name'      => $product_data['ten_sanpham'],
                            'price'     => $product_data['gia'],
                            'image'     => $product_data['avatar'],
                            'seller_id' => $product_data['id_user'] 
                        ];
                    }
                }
            }

            // 7. Load danh sách chat & Gọi View
            $conversations = $this->chatModel->loadConversations($my_id);
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