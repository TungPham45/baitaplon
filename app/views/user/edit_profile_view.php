<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa hồ sơ - C2C Market</title>
    <link rel="stylesheet" href="/baitaplon/public/css/user_layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include_once 'layouts/header.php'; ?>

    <div class="profile-layout-container">
        <div class="profile-content-wrapper">
            <aside class="edit-profile-sidebar">
                <h2 style="font-size: 1.2rem; margin-bottom: 20px;">Thông tin cá nhân</h2>
                <ul class="edit-menu">
                    <li class="active"><a href="#">Thông tin cá nhân</a></li>
                </ul>
            </aside>

            <main class="profile-main-content card-white">
                <h2 style="font-size: 1.3rem; margin-bottom: 25px; color: #333;">Hồ sơ cá nhân</h2>

                <form action="/baitaplon/User/editProfile" method="POST" enctype="multipart/form-data">
                    <div class="edit-avatar-section">
                        <img src="/baitaplon/public/uploads/avatars/<?php echo !empty($user['avatar']) ? basename($user['avatar']) : 'default.png'; ?>"
                            id="previewAvatar" class="profile-avatar-large">
                        <div class="upload-btn-wrapper">
                            <button class="btn-upload">Thay đổi ảnh đại diện</button>
                            <input type="file" name="avatar" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>

                    <div class="form-row-grid">
                        <div class="form-group-custom">
                            <label>Họ và tên *</label>
                            <input type="text" name="fullname" value="<?php echo $user['hoten']; ?>" required
                                placeholder="Nhập họ và tên">
                        </div>
                        <div class="form-group-custom">
                            <label>Thêm số điện thoại *</label>
                            <input type="text" name="sdt" value="<?php echo $user['sdt']; ?>" required
                                placeholder="Thêm số điện thoại">
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label>Địa chỉ</label>
                        <input type="text" name="diachi" value="<?php echo $user['diachi']; ?>"
                            placeholder="Địa chỉ của bạn">
                    </div>

                    <div class="form-group-custom">
                        <label>Giới thiệu</label>
                        <textarea name="gioithieu" rows="5"
                            placeholder="Viết vài dòng giới thiệu về gian hàng của bạn..."><?php echo $user['gioithieu']; ?></textarea>
                        <span class="char-limit">Tối đa 60 từ</span>
                    </div>

                    <div class="form-footer-btns">
                        <a href="/baitaplon/User/profile" class="btn-cancel">Hủy bỏ</a>
                        <button type="submit" class="btn-save">Lưu thay đổi</button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('previewAvatar').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>