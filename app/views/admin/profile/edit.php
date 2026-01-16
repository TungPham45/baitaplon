<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa hồ sơ</title>
    <link rel="stylesheet" href="/baitaplon/public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <div class="main-layout">
            <?php include_once __DIR__ . '/../layouts/header.php'; ?>

            <main class="content-area admin-profile-view">
                <div class="auth-container wide edit-profile-card">
                    <h2><i class="fas fa-user-edit"></i> CHỈNH SỬA HỒ SƠ</h2>
                    <form action="/baitaplon/Admin/editProfile" method="POST" enctype="multipart/form-data">

                        <div class="avatar-upload-section">
                            <label>Ảnh đại diện</label>
                            <?php
                            $avatarFilename = !empty($user['avatar']) ? basename($user['avatar']) : 'default.png';
                            $avatarPath = '/baitaplon/public/uploads/avatars/' . htmlspecialchars($avatarFilename);
                            ?>
                            <img src="<?php echo $avatarPath; ?>" class="avatar-preview"><br>
                            <label for="avatar-upload" class="avatar-upload-btn">
                                <i class="fas fa-camera"></i> Chọn ảnh mới
                            </label>
                            <input type="file" id="avatar-upload" name="avatar" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Họ và tên</label>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['hoten']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Số điện thoại</label>
                            <input type="text" name="sdt" value="<?php echo htmlspecialchars($user['sdt'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                            <input type="text" name="diachi" value="<?php echo htmlspecialchars($user['diachi'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-quote-left"></i> Giới thiệu bản thân</label>
                            <textarea name="gioithieu" rows="4"><?php echo htmlspecialchars($user['gioithieu'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <a href="/baitaplon/Admin/profile" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Cập nhật hồ sơ
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>

</html>