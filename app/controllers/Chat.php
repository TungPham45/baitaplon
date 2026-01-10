<?php

require_once __DIR__ . '/../models/ChatModel.php';
require_once __DIR__ . '/../helpers/time_helper.php';

class Chat {
    private $model;
    private $chatModel;

    public function __construct($conn) {
         $this->model = new ChatModel($conn);
         $this->chatModel = new ChatModel($conn);
    }

    public function index() {
        // redirect logic hoặc gọi trực tiếp
        $this->start();
    }

    // ===== DANH SÁCH =====
    public function start($param = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        $my_id = $_SESSION['user_id']; 
        $active_conversation_id = 0;

        // 1️⃣ XÁC ĐỊNH CONVERSATION ID
        if ($param !== null) {
            if (ctype_digit((string)$param) && $this->chatModel->isConversationOfUser((int)$param, $my_id)) {
                $active_conversation_id = (int)$param;
            } else {
                // Nếu param là USxxx (seller_id)
                $active_conversation_id = $this->chatModel->getOrCreateConversation($my_id, $param);
            }
        } else {
            $latest = $this->chatModel->getLatestConversation($my_id);
            $active_conversation_id = $_SESSION['active_conversation_id'] ?? ($latest[0]['id_conversation'] ?? 0);
        }

        // 2️⃣ QUAN TRỌNG: CẬP NHẬT SENDER_ID VÀO SESSION ĐỂ GỬI TIN
        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessageByConversation($active_conversation_id);

            // Lưu lại để hàm send() sử dụng
            $_SESSION['active_conversation_id'] = $active_conversation_id;
            $_SESSION['sender_id'] = $sender_id; 
        }

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
            
            // 🔥 QUAN TRỌNG: Lấy ID hội thoại từ FORM (đáng tin cậy hơn Session)
            $conversation_id = (int)($_POST['conversation_id'] ?? 0);

            // Nếu form không có, mới fallback về session (chống cháy)
            if ($conversation_id == 0) {
                $conversation_id = $_SESSION['active_conversation_id'] ?? 0;
            }

            // A. SỬA TIN NHẮN
            if ($message_id > 0 && $content !== '') {
                $this->chatModel->updateMessage($message_id, $my_id, $content);
            }
            // B. GỬI TIN MỚI
            else if ($content !== '' && $conversation_id > 0) {
                
                // 1. Tìm ra người nhận dựa trên conversation_id này
                // (Đảm bảo dù session có sai, tin nhắn vẫn đến đúng người trong hội thoại này)
                $to_user = $this->chatModel->getOtherUserId($conversation_id, $my_id);

                if (!empty($to_user)) {
                    // Gọi hàm insert (Hàm này của bạn đã có logic tạo/tìm hội thoại rồi)
                    $this->chatModel->insertMessage($my_id, $to_user, $content);
                    
                    // Cập nhật session để khi reload vẫn ở đúng đoạn chat này
                    $_SESSION['active_conversation_id'] = $conversation_id;
                }
            }

            header("Location: /baitaplon/chat");
            exit;
        }

    public function search()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id'
        $my_id   = $_SESSION['user_id'];
        $keyword = trim($_POST['keyword'] ?? '');

        // 1️⃣ Load danh sách conversation (theo keyword)
        if ($keyword !== '') {
            $conversations = $this->chatModel
                ->searchConversationBySenderName($my_id, $keyword);
        } else {
            $conversations = $this->chatModel
                ->loadConversations($my_id);
        }

        // 2️⃣ GIỮ NGUYÊN conversation đang active (KHÔNG DÙNG $_GET)
        $active_conversation_id = $_SESSION['active_conversation_id']
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        // 3️⃣ Load sender + messages theo conversation_id
        $sender_id   = 0;
        $sender_name = '';
        $messages    = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel
                ->getOtherUserId($active_conversation_id, $my_id);

            $sender_name = $this->chatModel
                ->getNameSenderByID($sender_id);

            // 🔥 ĐÚNG KIẾN TRÚC
            $messages = $this->chatModel
                ->loadMessageByConversation($active_conversation_id);
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }

    public function deleteMessage()
    {
        if (!isset($_SESSION['user_id'])) return;

        // SỬA: Dùng 'user_id'
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
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id'
        $my_id = $_SESSION['user_id'];
        $keyword = trim($_POST['message_keyword'] ?? '');

        // 1️⃣ Load danh sách conversation (KHÔNG lọc)
        $conversations = $this->chatModel
            ->loadConversations($my_id);

        // 2️⃣ Lấy conversation đang active
        $active_conversation_id = $_SESSION['active_conversation_id']
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        $sender_id = 0;
        $sender_name = '';
        $messages = [];

        if ($active_conversation_id > 0) {

            // 3️⃣ Lấy thông tin người chat
            $sender_id = $this->chatModel
                ->getOtherUserId($active_conversation_id, $my_id);

            $sender_name = $this->chatModel
                ->getNameSenderByID($sender_id);

            // 4️⃣ Tìm message theo nội dung
            if ($keyword !== '') {
                $messages = $this->chatModel
                    ->searchMessageByContent(
                        $active_conversation_id,
                        $keyword
                    );
            } else {
                // fallback: load toàn bộ
                $messages = $this->chatModel
                    ->loadMessageByConversation($active_conversation_id);
            }
        }

        require __DIR__ . '/../views/Message/GiaoDien_Chat.php';
    }
}
?>