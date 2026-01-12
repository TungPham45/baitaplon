<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực OTP</title>
    <link rel="stylesheet" href="/baitaplon/public/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="step-indicator">Bước 2/3</div>
        <h2>XÁC THỰC OTP</h2>
        <p style="text-align: center; font-size: 0.9rem;">
            Mã OTP đã được gửi tới: <b><?php echo $_SESSION['reset_email']; ?></b>
        </p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/baitaplon/Auth/verifyOtp" method="POST">
            <div class="form-group">
                <label>Nhập 6 số OTP</label>
                <input type="text" name="otp" required maxlength="6" 
                       style="text-align: center; font-size: 1.5rem; letter-spacing: 10px;">
            </div>
            <button type="submit" class="btn">Xác nhận mã</button>
        </form>
    </div>
</body>
</html>