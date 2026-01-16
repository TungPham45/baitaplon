
    // Hàm mở Modal
    function openBanModal(reportId, userId, userName) {
        document.getElementById('modal_report_id').value = reportId;
        document.getElementById('modal_reported_id').value = userId;
        document.getElementById('modal_user_name').value = userName;
        document.getElementById('ban_reason').value = ''; 
        document.getElementById('banModal').style.display = 'flex';
    }

    // Hàm đóng Modal
    function closeBanModal() {
        document.getElementById('banModal').style.display = 'none';
    }

    // Đóng khi click ra ngoài modal
    window.onclick = function(event) {
        let modal = document.getElementById('banModal');
        if (event.target == modal) {
            closeBanModal();
        }
    }
