<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Bước 2</title>
    <link rel="stylesheet" href="/baitaplon/public/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container wide">
        <div class="step-indicator">Bước 2/2: Thông tin cá nhân</div>
        <h2>THÔNG TIN CHI TIẾT</h2>
        
        <form action="/baitaplon/Auth/registerStep2" method="POST">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="fullname" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="sdt">
            </div>
            <div class="form-group">
                <label>Địa chỉ</label>
                <textarea name="diachi" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn" style="background:#ccc" onclick="history.back()">Quay lại</button>
                <button type="submit" class="btn">Hoàn tất đăng ký</button>
            </div>
        </form>
    </div>
</body>
</html>