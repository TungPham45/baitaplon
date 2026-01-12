<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa hồ sơ</title>
    <link rel="stylesheet" href="/baitaplon/public/css/admin.css">
</head>
<body>
    <div class="auth-container wide">
        <h2>CHỈNH SỬA HỒ SƠ</h2>
        <form action="/baitaplon/Admin/editProfile" method="POST" enctype="multipart/form-data">
            
            <div style="text-align: center; margin-bottom: 20px;">
                <label>Ảnh đại diện hiện tại</label><br>
                <?php 
                    $avatarPath = $user['avatar'] 
                        ? '/baitaplon/public/uploads/avatars/' . $user['avatar'] 
                        : '/baitaplon/public/uploads/avatars/default.png';
                ?>
                <img src="<?php echo $avatarPath; ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;"><br>
                <input type="file" name="avatar" accept="image/*" style="margin-top: 10px;">
            </div>

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" value="<?php echo $user['hoten']; ?>" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="sdt" value="<?php echo $user['sdt']; ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="diachi" value="<?php echo $user['diachi']; ?>">
            </div>

            <div class="form-group">
                <label>Giới thiệu bản thân</label>
                <textarea name="gioithieu" rows="4" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"><?php echo $user['gioithieu']; ?></textarea>
            </div>

            <div style="display: flex; gap: 15px;">
                <a href="/baitaplon/Admin/profile" class="btn" style="background: #858796; text-decoration: none; text-align: center;">Hủy</a>
                <button type="submit" class="btn">Cập nhật hồ sơ</button>
            </div>
        </form>
    </div>
</body>
</html>