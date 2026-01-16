<?php
class ChatModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

  // Lấy tên người gửi dựa trên ID (Varchar)
    public function getNameSenderByID($sender_id) {
        $sql = "SELECT hoten FROM users WHERE id_user = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sender_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['hoten'] ?? '';
    }

    // Tìm hội thoại giữa 2 user (Cả 2 đều là Varchar)
    public function findConversation($user1, $user2) {
        $sql = "
            SELECT cu1.id_conversation
            FROM conversation_users cu1
            JOIN conversation_users cu2
              ON cu1.id_conversation = cu2.id_conversation
            WHERE cu1.id_user = ?
              AND cu2.id_user = ?
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $user1, $user2); // Đổi thành "ss"
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['id_conversation'] ?? 0;
    }

    // Tạo hội thoại mới
    public function createConversation($user1, $user2, $product_id = null) {
        // Bước 1: Tạo bản ghi trong bảng conversations
        if ($product_id) {
            // Nếu có sản phẩm, insert kèm id_sanpham
            $stmt = $this->conn->prepare("INSERT INTO conversations (id_sanpham, last_message_at) VALUES (?, NOW())");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $conversation_id = $stmt->insert_id;
        } else {
            // Nếu chat thông thường (không qua sản phẩm)
            $this->conn->query("INSERT INTO conversations (last_message_at) VALUES (NOW())");
            $conversation_id = $this->conn->insert_id;
        }

        // Bước 2: Gắn 2 người dùng vào hội thoại này
        $stmtUsers = $this->conn->prepare(
            "INSERT INTO conversation_users (id_conversation, id_user) VALUES (?, ?), (?, ?)"
        );
        // id_conversation (int), id_user (string) -> isis
        $stmtUsers->bind_param("isis", $conversation_id, $user1, $conversation_id, $user2);
        $stmtUsers->execute();

        return (int)$conversation_id;
    }

    public function getOrCreateConversation($user1, $user2, $product_id = null)
        {
            // Kiểm tra xem 2 người này đã có hội thoại chưa
            $sql = "
                SELECT cu1.id_conversation
                FROM conversation_users cu1
                JOIN conversation_users cu2 ON cu1.id_conversation = cu2.id_conversation
                WHERE cu1.id_user = ? AND cu2.id_user = ?
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $user1, $user2);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $existing_id = (int)$row['id_conversation'];
                

                if ($product_id) {
                    $this->updateConversationProduct($existing_id, $product_id);
                }
                
                return $existing_id;
            }

            // Nếu chưa có thì tạo mới
            return $this->createConversation($user1, $user2, $product_id);
        }

    // Thêm tin nhắn mới
        public function insertMessage($from_user, $to_user, $content) {
            $conversation_id = $this->findConversation($from_user, $to_user);
            if ($conversation_id == 0) {
                $conversation_id = $this->createConversation($from_user, $to_user);
            }

            // 1. Insert tin nhắn
            $sql = "INSERT INTO messages (id_conversation, sender_id, content) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iss", $conversation_id, $from_user, $content);
            $stmt->execute();

            // 2. CẬP NHẬT THỜI GIAN CHO CUỘC HỘI THOẠI (QUAN TRỌNG)
            $sqlUpdate = "UPDATE conversations SET last_message_at = NOW() WHERE id_conversation = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $conversation_id);
            $stmtUpdate->execute();

            return $conversation_id;
        }

    // Load tin nhắn theo ID cuộc hội thoại
    public function loadMessageByConversation($conversation_id) {
        $sql = "
            SELECT 
                m.id_message,
                m.sender_id,
                u.hoten AS sender_name,
                u.avatar AS sender_avatar,
                m.content,
                m.created_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id_user
            WHERE m.id_conversation = ?
            GROUP BY m.id_message -- Ngăn chặn lặp tin nhắn nếu bảng users trùng ID
            ORDER BY m.created_at ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

        // Lấy danh sách các cuộc hội thoại của 1 user
    public function loadConversations($user_id) {
        $sql = "
            SELECT 
                cu_me.id_conversation,
                c.last_message_at,
                u.id_user,
                u.hoten,
                u.avatar,
                (SELECT m.content FROM messages m WHERE m.id_conversation = cu_me.id_conversation ORDER BY m.created_at DESC LIMIT 1) as last_message
            FROM conversation_users cu_me
            JOIN conversation_users cu_other ON cu_me.id_conversation = cu_other.id_conversation AND cu_other.id_user != cu_me.id_user
            JOIN users u ON cu_other.id_user = u.id_user
            JOIN conversations c ON cu_me.id_conversation = c.id_conversation
            WHERE cu_me.id_user = ?
            GROUP BY cu_me.id_conversation -- Ép mỗi hội thoại chỉ hiện 1 dòng
            ORDER BY 
                (c.last_message_at IS NULL) DESC,
                c.last_message_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getOtherUserId($conversation_id, $my_id)
        {
            $sql = "
                SELECT id_user
                FROM conversation_users
                WHERE id_conversation = ?
                AND id_user != ?
                LIMIT 1
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $conversation_id, $my_id);
            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();
            return $row['id_user'] ?? '';
        }
        // Cách 1: Xóa hẳn user khỏi conversation_users (User kia vẫn thấy chat, nhưng user này sẽ mất lịch sử)
        public function removeConversationForUser($conversation_id, $user_id) 
            {
                $sql = "DELETE FROM conversation_users WHERE id_conversation = ? AND id_user = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $conversation_id, $user_id);
                return $stmt->execute();
            }

        public function isUserBanned($user_id) {
            // Giả sử bảng chứa trạng thái là 'account' và cột là 'trangthai'
            // Nếu hệ thống bạn lưu ở bảng 'users', hãy đổi 'account' thành 'users'
            $sql = "SELECT trangthai FROM account WHERE id_user = ? LIMIT 1";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            // Trả về TRUE nếu bị khóa, FALSE nếu bình thường
            return ($result && $result['trangthai'] === 'Bị khóa');
        }
        // Tìm kiếm hội thoại theo tên người nhận
        public function searchConversationBySenderName($my_id, $keyword){
            $sql = "
            SELECT 
            c.id_conversation,
            c.last_message_at,
            u.id_user,
            u.hoten,
            u.avatar,
            (SELECT m.content FROM messages m WHERE m.id_conversation = c.id_conversation ORDER BY m.created_at DESC LIMIT 1) AS last_message
                FROM conversations c
                JOIN conversation_users cu1 ON c.id_conversation = cu1.id_conversation
                JOIN conversation_users cu2 ON c.id_conversation = cu2.id_conversation
                JOIN users u ON cu2.id_user = u.id_user
                WHERE cu1.id_user = ?
                AND cu2.id_user != ?
                AND u.hoten LIKE ?
                ORDER BY c.last_message_at DESC
            ";
            $stmt = $this->conn->prepare($sql);
            $like = '%' . $keyword . '%';
            $stmt->bind_param("sss", $my_id, $my_id, $like); // "sss"
            $stmt->execute();
            return $stmt->get_result();
        }

    public function updateMessage($message_id, $user_id, $content){
        $sql = "UPDATE messages SET content = ?, updated_at = NOW() WHERE id_message = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        // content (s), id_message (i), sender_id (s)
        $stmt->bind_param("sis", $content, $message_id, $user_id);
        $stmt->execute();
    }

    public function deleteMessage($message_id, $user_id){
        $sql = "DELETE FROM messages WHERE id_message = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $message_id, $user_id); // id (i), user (s)
        $stmt->execute();
    }

    public function isConversationOfUser($conversation_id, $user_id) {
        $sql = "SELECT 1 FROM conversation_users WHERE id_conversation = ? AND id_user = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $conversation_id, $user_id); 
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    public function searchMessageByContent($conversation_id, $keyword)
    {
        $sql = "
            SELECT 
                m.id_message,
                m.sender_id,
                u.hoten AS sender_name,
                u.avatar AS sender_avatar,
                m.content,
                m.created_at,
                m.updated_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id_user
            WHERE m.id_conversation = ?
            AND m.content LIKE ?
            ORDER BY m.created_at ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $likeKeyword = '%' . $keyword . '%';
        $stmt->bind_param("is", $conversation_id, $likeKeyword);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy danh sách các cuộc trò chuyện gần nhất (kèm tin nhắn cuối)
    public function getLatestConversation($user_id) {
        $sql = "
            SELECT 
                c.id_conversation,
                c.last_message_at,
                u.id_user AS partner_id,
                u.hoten AS partner_name,
                u.avatar AS partner_avatar,
                -- Subquery lấy tin nhắn mới nhất để hiển thị preview
                (
                    SELECT content 
                    FROM messages m 
                    WHERE m.id_conversation = c.id_conversation 
                    ORDER BY m.created_at DESC 
                    LIMIT 1
                ) AS last_content
            FROM conversations c
            -- Join để lấy các hội thoại của user hiện tại
            JOIN conversation_users cu_me ON c.id_conversation = cu_me.id_conversation
            -- Join tiếp để tìm người kia (partner) trong hội thoại đó
            JOIN conversation_users cu_other ON c.id_conversation = cu_other.id_conversation
            -- Join bảng users để lấy thông tin người kia
            LEFT JOIN users u ON cu_other.id_user = u.id_user
            WHERE cu_me.id_user = ? 
            AND cu_other.id_user != ? -- Đảm bảo không lấy thông tin của chính mình
            ORDER BY c.last_message_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        // id_user là varchar nên dùng "ss" (bind 2 lần cho 2 dấu ?)
        $stmt->bind_param("ss", $user_id, $user_id);
        $stmt->execute();
        
        // Trả về mảng kết hợp (Associative Array)
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    // 1. Cập nhật sản phẩm đang quan tâm cho cuộc hội thoại này
    public function updateConversationProduct($conversation_id, $product_id) {
        $sql = "UPDATE conversations SET id_sanpham = ? WHERE id_conversation = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $product_id, $conversation_id); 
        return $stmt->execute();
    }

        // 2. Lấy ID sản phẩm đã lưu trong cuộc hội thoại
    public function getProductOfConversation($conversation_id) {
        $sql = "SELECT id_sanpham FROM conversations WHERE id_conversation = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['id_sanpham'];
        }
        return null;
    }
}
?>