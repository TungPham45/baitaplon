// File: public/js/script.js

// Hàm mở Modal và lấy dữ liệu
function openModal(userId) {
    const modal = document.getElementById('accountModal');
    const modalBody = document.getElementById('modalBody');

    modal.style.display = 'flex';
    modalBody.innerHTML = '<p style="text-align:center;">Đang tải dữ liệu...</p>';

    // CẬP NHẬT: Đổi đường dẫn fetch theo luồng mới /baitaplon/Admin/getDetail/
    fetch(`/baitaplon/Admin/getDetail/${userId}`) 
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            renderDetail(data); 
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = '<div class="alert-error">Không thể kết nối đến máy chủ.</div>';
        });
}

// Hàm render nội dung và các nút chức năng dựa trên trạng thái
function renderDetail(user) {
    const modalBody = document.getElementById('modalBody');
    
    // Logic nút bấm linh hoạt theo trạng thái
    let statusActionBtn = '';
    if (user.trangthai === 'Chờ duyệt') {
        statusActionBtn = `<button class="btn btn-success" onclick="updateStatus('${user.id_user}', 'Hoạt động')">Phê duyệt</button>`;
    } else if (user.trangthai === 'Hoạt động') {
        statusActionBtn = `<button class="btn btn-warning" onclick="updateStatus('${user.id_user}', 'Bị khóa')">Khóa tài khoản</button>`;
    } else if (user.trangthai === 'Bị khóa') {
        statusActionBtn = `<button class="btn btn-primary" onclick="updateStatus('${user.id_user}', 'Hoạt động')">Mở lại tài khoản</button>`;
    }

    modalBody.innerHTML = `
        <div class="detail-container">
            <div class="detail-left">
                <img src="/baitaplon/public/uploads/avatars/${user.avatar || 'default.png'}" class="avatar-detail">
            </div>
            <div class="detail-right">
                <p><strong>Tên đăng nhập:</strong> ${user.username}</p> 
                <p><strong>Họ tên:</strong> ${user.hoten}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Loại:</strong> ${user.role}</p>
                <p><strong>Số ĐT:</strong> ${user.sdt || 'N/A'}</p>
                <p><strong>Địa chỉ:</strong> ${user.diachi || 'N/A'}</p>
                <p><strong>Trạng thái:</strong> <span class="badge">${user.trangthai}</span></p>
                <p><strong>Ngày tham gia:</strong> ${user.ngaytao}</p>
            </div>
        </div>
        <div class="modal-footer">
            ${statusActionBtn}
            <button class="btn btn-danger" onclick="deleteAccount('${user.id_user}')">Xóa tài khoản</button>
        </div>
    `;
}

// Hàm cập nhật trạng thái
function updateStatus(userId, newStatus) {
    if (!confirm(`Bạn có chắc chắn muốn chuyển trạng thái tài khoản này sang "${newStatus}"?`)) return;

    // CẬP NHẬT: Đổi đường dẫn fetch /baitaplon/Admin/updateStatus
    fetch('/baitaplon/Admin/updateStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${userId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cập nhật trạng thái thành công!');
            location.reload(); 
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

// Hàm xóa tài khoản vĩnh viễn
function deleteAccount(userId) {
    if (!confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn tài khoản. Bạn có chắc chắn không?')) return;

    // CẬP NHẬT: Đổi đường dẫn fetch /baitaplon/Admin/deleteAccount/
    fetch(`/baitaplon/Admin/deleteAccount/${userId}`, { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã xóa tài khoản thành công!');
            closeModal();
            location.reload(); 
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

// Các hàm closeModal và window.onclick giữ nguyên
function closeModal() {
    document.getElementById('accountModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('accountModal');
    if (event.target == modal) {
        closeModal();
    }
}