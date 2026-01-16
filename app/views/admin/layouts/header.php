<header class="top-navbar">
    <div class="function-name">
        <h3><?php echo isset($functionTitle) ? $functionTitle : 'Bảng điều khiển'; ?></h3>
    </div>

    <div class="profile-section">
        <div class="profile-info">
            <img src="/baitaplon/public/uploads/avatars/<?php echo (!empty($_SESSION['avatar']) && $_SESSION['avatar'] !== 'default.png') ? basename(htmlspecialchars($_SESSION['avatar'])) : 'default.png'; ?>"
                alt="Avatar" class="avatar-small">
            <span><?php echo $_SESSION['username']; ?></span>
        </div>
        <div class="profile-dropdown">
            <a href="/baitaplon/Admin/profile"><i class="fas fa-user"></i> Hồ sơ cá nhân</a>
            <a href="#" onclick="openChangePasswordModal(); return false;"><i class="fas fa-key"></i> Đổi mật khẩu</a>
            <hr>
            <a href="/baitaplon/Auth/logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
    </div>
</header>

<div id="changePasswordModal" class="modal-overlay">
    <div class="modal-content password-modal">
        <div class="modal-header-custom">
            <h3><i class="fas fa-key"></i> Đổi mật khẩu</h3>
            <button class="close-btn" onclick="closeChangePasswordModal()">&times;</button>
        </div>
        <form id="changePasswordForm" onsubmit="handleChangePassword(event)">
            <div class="modal-body-custom">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mật khẩu hiện tại</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="current_password" name="current_password"
                            placeholder="Nhập mật khẩu hiện tại" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password')"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mật khẩu mới</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password"
                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password')"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Xác nhận mật khẩu mới</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password"
                            placeholder="Nhập lại mật khẩu mới" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    </div>
                </div>
                <div id="passwordMessage" class="password-message"></div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn btn-cancel" onclick="closeChangePasswordModal()">Hủy</button>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save"></i> Cập nhật mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openChangePasswordModal() {
        document.getElementById('changePasswordModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('changePasswordForm').reset();
        document.getElementById('passwordMessage').className = 'password-message';
        document.getElementById('passwordMessage').textContent = '';
    }

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    function handleChangePassword(event) {
        event.preventDefault();

        const messageEl = document.getElementById('passwordMessage');
        const formData = new FormData(document.getElementById('changePasswordForm'));

        fetch('/baitaplon/Admin/changePassword', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                messageEl.textContent = data.message;
                if (data.success) {
                    messageEl.className = 'password-message success';
                    setTimeout(() => {
                        closeChangePasswordModal();
                    }, 1500);
                } else {
                    messageEl.className = 'password-message error';
                }
            })
            .catch(error => {
                messageEl.textContent = 'Có lỗi xảy ra, vui lòng thử lại!';
                messageEl.className = 'password-message error';
            });
    }

    document.getElementById('changePasswordModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeChangePasswordModal();
        }
    });
</script>