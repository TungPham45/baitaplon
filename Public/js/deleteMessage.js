
    // Hàm xác nhận xóa hội thoại
    function confirmDeleteConversation(conversationId) {
        // Kiểm tra xem ID có hợp lệ không
        if (!conversationId || conversationId == 0) {
            alert("Chưa chọn cuộc trò chuyện nào để xóa!");
            return;
        }

        // Hiện hộp thoại xác nhận
        if (confirm("⚠️ CẢNH BÁO:\n\nBạn có chắc chắn muốn xóa cuộc trò chuyện này?\nHành động này sẽ ẩn cuộc trò chuyện khỏi danh sách của bạn.")) {
            
            // Gán ID vào form ẩn
            document.getElementById('inputDeleteConvId').value = conversationId;
            
            // Submit form
            document.getElementById('formDeleteConversation').submit();
        }
    }
