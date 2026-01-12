<?php

require_once __DIR__ . '/../models/ChatModel.php';
require_once __DIR__ . '/../models/SanphamModel.php'; 
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/time_helper.php';

class Chat {
    
    private $chatModel;
    private $productModel;
    private $userModel;
    private $conn;

    public function __construct($conn) {
         $this->conn = $conn;
         $this->chatModel = new ChatModel($conn);
         $this->productModel = new SanphamModel($conn); 
         $this->userModel = new UserModel($conn);
    }

    public function index() {
        $this->start();
    }

    // 1. KẾT NỐI TỪ SẢN PHẨM
    public function connect($seller_id, $product_id = null) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /baitaplon/User/Login'); exit;
        }
        $my_id = $_SESSION['user_id'];
        $conversation_id = $this->chatModel->getOrCreateConversation($my_id, $seller_id, $product_id);
        header('Location: /baitaplon/Chat/start/' . $conversation_id);
        exit;
    }

    // 2. TRANG CHÍNH
    public function start($param = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login"); exit;
        }

        $my_id = $_SESSION['user_id']; 
        $active_conversation_id = 0;

        // Xử lý ID hội thoại
        if ($param !== null) {
            if (ctype_digit((string)$param) && $this->chatModel->isConversationOfUser((int)$param, $my_id)) {
                $active_conversation_id = (int)$param;
            } else {
                $active_conversation_id = $this->chatModel->getOrCreateConversation($my_id, $param);
            }
        } else {
            $latest = $this->chatModel->getLatestConversation($my_id);
            $active_conversation_id = $latest[0]['id_conversation'] ?? 0;
        }

        // Khởi tạo biến mặc định cho View
        $sender_name = '';
        $sender_id = 0;
        $sender_avatar = ''; // Biến Avatar
        $messages = [];
        $product_context = []; 

        // Load dữ liệu hội thoại
        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            
            if ($sender_id) {
                $partnerInfo = $this->userModel->getUserById($sender_id);
                if ($partnerInfo) {
                    $sender_name = $partnerInfo['hoten'];
                    $sender_avatar = $partnerInfo['avatar']; // Lấy ảnh từ DB
                }
            }

            $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);
            $_SESSION['active_conversation_id'] = $active_conversation_id;
            $_SESSION['sender_id'] = $sender_id; 
        } else {
            unset($_SESSION['active_conversation_id']);
            unset($_SESSION['sender_id']);
        }

        // Xử lý ghim sản phẩm
        $current_product_id = null;
        if (isset($_POST['product_id_post']) && $active_conversation_id > 0) {
            $pid_post = (int)$_POST['product_id_post'];
            $this->chatModel->updateConversationProduct($active_conversation_id, $pid_post);
            header("Location: /baitaplon/Chat/start/" . $active_conversation_id); exit; 
        } else if ($active_conversation_id > 0) {
            $current_product_id = $this->chatModel->getProductOfConversation($active_conversation_id);
        }

        if ($current_product_id) {
            $product_data = $this->productModel->getProductById($current_product_id);
            if ($product_data && $sender_id == $product_data['id_user']) {
                $product_context = [
                    'id'        => $product_data['id_sanpham'],
                    'name'      => $product_data['ten_sanpham'],
                    'price'     => $product_data['gia'],
                    'image'     => $product_data['avatar'], 
                    'seller_id' => $product_data['id_user'] 
                ];
            }
        }

        // Lấy danh sách bên trái
        $conversations = $this->chatModel->loadConversations($my_id);

        // Gọi View trực tiếp (Các biến trên sẽ tự chạy sang view)
        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }

    // 3. GỬI TIN NHẮN
    public function send()
    {
        if (!isset($_SESSION['user_id'])) { header("Location: /baitaplon/Login"); exit; }

        $my_id = $_SESSION['user_id'];
        $content = trim($_POST['message'] ?? '');
        $message_id = (int)($_POST['message_id'] ?? 0);
        $conversation_id = (int)($_POST['conversation_id'] ?? 0);

        if ($message_id > 0 && $content !== '') {
            $this->chatModel->updateMessage($message_id, $my_id, $content);
        } else if ($content !== '') {
            if ($conversation_id > 0) {
                $to_user = $this->chatModel->getOtherUserId($conversation_id, $my_id);
                if ($to_user) {
                    $this->chatModel->insertMessage($my_id, $to_user, $content);
                }
            }
        }

        $redirectUrl = "/baitaplon/Chat";
        if ($conversation_id > 0) $redirectUrl .= "/start/" . $conversation_id;
        header("Location: " . $redirectUrl);
        exit;
    }

    // 4. TÌM KIẾM HỘI THOẠI
    public function search()
    {
        if (!isset($_SESSION['user_id'])) { header("Location: /baitaplon/Login"); exit; }

        $my_id   = $_SESSION['user_id'];
        $keyword = trim($_POST['keyword'] ?? '');

        if ($keyword !== '') {
            $conversations = $this->chatModel->searchConversationBySenderName($my_id, $keyword);
        } else {
            $conversations = $this->chatModel->loadConversations($my_id);
        }

        $active_conversation_id = $_SESSION['active_conversation_id'] ?? 0;
        $sender_id = 0; $sender_name = ''; $sender_avatar = ''; 
        $messages = []; $product_context = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            if ($sender_id) {
                $partnerInfo = $this->userModel->getUserById($sender_id);
                if ($partnerInfo) {
                    $sender_name = $partnerInfo['hoten'];
                    $sender_avatar = $partnerInfo['avatar'];
                }
                $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);
            }
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }

    // 5. XÓA TIN NHẮN
    public function deleteMessage()
    {
        if (!isset($_SESSION['user_id'])) return;
        $my_id = $_SESSION['user_id'];
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($message_id > 0) {
            $this->chatModel->deleteMessage($message_id, $my_id);
        }
        
        $active_id = $_SESSION['active_conversation_id'] ?? '';
        header("Location: /baitaplon/Chat/start/" . $active_id);
        exit;
    }
    public function deleteConversation() {
                // 1. Kiểm tra đăng nhập
                if (!isset($_SESSION['user_id'])) {
                    header("Location: /baitaplon/Login");
                    exit;
                }

                // 2. Lấy dữ liệu
                $my_id = $_SESSION['user_id'];
                $conversation_id = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;

                // 3. Gọi Model xóa
                if ($conversation_id > 0) {
                    // Gọi hàm xóa trong model (chúng ta sẽ viết ở bước 3)
                    $this->chatModel->removeConversationForUser($conversation_id, $my_id);
                }

                // 4. Quay về trang chat (mặc định)
                header("Location: /baitaplon/Chat");
                exit;
            }
    // 6. TÌM KIẾM TIN NHẮN
    public function searchMessage()
    {
        if (!isset($_SESSION['user_id'])) { header("Location: /baitaplon/Login"); exit; }

        $my_id = $_SESSION['user_id'];
        $keyword = trim($_POST['message_keyword'] ?? '');

        $conversations = $this->chatModel->loadConversations($my_id);
        $active_conversation_id = $_SESSION['active_conversation_id'] ?? 0;

        $sender_id = 0; $sender_name = ''; $sender_avatar = '';
        $messages = []; $product_context = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            if ($sender_id) {
                $partnerInfo = $this->userModel->getUserById($sender_id);
                if ($partnerInfo) {
                    $sender_name = $partnerInfo['hoten'];
                    $sender_avatar = $partnerInfo['avatar'];
                }

                if ($keyword !== '') {
                    $messages = $this->chatModel->searchMessageByContent($active_conversation_id, $keyword);
                } else {
                    $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);
                }
            }
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }
}
?>