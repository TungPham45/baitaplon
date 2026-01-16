
    function confirmDeleteConversation(conversationId) {
    
        if (!conversationId || conversationId == 0) {
            alert("Chưa chọn cuộc trò chuyện nào để xóa!");
            return;
        }
       
        if (confirm("⚠️ CẢNH BÁO:\n\nBạn có chắc chắn muốn xóa cuộc trò chuyện này?\nHành động này sẽ ẩn cuộc trò chuyện khỏi danh sách của bạn.")) {
            
            document.getElementById('inputDeleteConvId').value = conversationId;
     
            document.getElementById('formDeleteConversation').submit();
        }
    }
