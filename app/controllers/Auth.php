<?php
// 1. Nạp thủ công các file thư viện PHPMailer
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

// 2. Khai báo sử dụng Namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../models/AuthModel.php';

class Auth {
    private $authModel;

    public function __construct($conn) {
        $this->authModel = new AuthModel($conn);
    }

    /**
     * Hàm phụ trợ gửi Email OTP sử dụng SMTP Gmail
     */
    private function sendEmailOTP($recipientEmail, $otpCode) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tungp788@gmail.com'; 
            $mail->Password   = 'ndwm hwrw rjxl ahyg'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('tungp788@gmail.com', 'Hệ thống C2C Market');
            $mail->addAddress($recipientEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực OTP đặt lại mật khẩu';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                    <h2 style='color: #4e73df;'>Xác thực tài khoản</h2>
                    <p>Chào bạn, mã OTP để đặt lại mật khẩu của bạn là:</p>
                    <div style='font-size: 24px; font-weight: bold; color: #e74a3b; letter-spacing: 5px;'>$otpCode</div>
                    <p>Mã này có hiệu lực trong vòng <b>5 phút</b>.</p>
                </div>";

            return $mail->send();
        } catch (Exception $e) {
            die("Lỗi gửi mail chi tiết: " . $mail->ErrorInfo);
        }
    }

    // --- 1. ĐĂNG NHẬP ---
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['username_or_email']);
            $password   = $_POST['password'];
            $user       = $this->authModel->login($identifier);

            if ($user && password_verify($password, $user['password'])) {
                switch ($user['trangthai']) {
                    case 'Chờ duyệt':
                        $error = "Tài khoản của bạn đang chờ quản trị viên xét duyệt!";
                        require_once __DIR__ . '/../views/auth/login.php';
                        return;

                    case 'Bị khóa':
                        case 'Bị khóa':
                        $reason = $user['ban_reason'] ?? 'Vi phạm chính sách cộng đồng.';
                        
                        echo "<script>
                                alert('Tài khoản đã bị KHÓA.\\nLý do: $reason\\n\\n( LH Hotline: 0383572JQK để được hỗ trợ!)');
                              </script>";
                        require_once __DIR__ . '/../views/auth/login.php';
                        return;

                    case 'Hoạt động':
                        // BẢO MẬT: Tạo ID session mới để tránh Session Fixation
                        session_regenerate_id(true);

                        $_SESSION['user_id']  = $user['id_user'];
                        $_SESSION['role']     = $user['role'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['avatar']   = $user['avatar'] ?? 'default.png';
                        
                        // Chuyển hướng
                        $redirect = ($user['role'] == 'Quản lý') ? "/baitaplon/Admin/dashboard" : "/baitaplon/Home/index";
                        header("Location: $redirect");
                        exit();
                }
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác!";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    // --- 2. ĐĂNG KÝ (BƯỚC 1) ---
    public function registerStep1() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['tentaikhoan'];
            $email = $_POST['email'];
            
            if ($this->authModel->checkExists($username, $email)) {
                $error = "Tên đăng nhập hoặc Email đã tồn tại!";
                require_once __DIR__ . '/../views/auth/register_step1.php';
            } else {
                $_SESSION['temp_account'] = [
                    'username' => $username,
                    'email'    => $email,
                    'password' => $_POST['matkhau']
                ];
                header("Location: /baitaplon/Auth/registerStep2");
                exit();
            }
        } else {
            require_once __DIR__ . '/../views/auth/register_step1.php';
        }
    }

    // --- ĐĂNG KÝ (BƯỚC 2) ---
    public function registerStep2() {
        if (!isset($_SESSION['temp_account'])) {
            header("Location: /baitaplon/Auth/registerStep1");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountData = $_SESSION['temp_account'];
            $personalData = [
                'name'    => $_POST['fullname'],
                'phone'   => $_POST['sdt'],
                'address' => $_POST['diachi']
            ];

            if ($this->authModel->register($accountData, $personalData)) {
                unset($_SESSION['temp_account']);
                $_SESSION['success'] = "Đăng ký thành công! Vui lòng chờ phê duyệt.";
                header("Location: /baitaplon/Auth/login");
                exit();
            } else {
                $error = "Có lỗi xảy ra khi lưu dữ liệu.";
                require_once __DIR__ . '/../views/auth/register_step2.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/register_step2.php';
        }
    }

    // --- 3. QUÊN MẬT KHẨU (BƯỚC 1: Gửi OTP) ---
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = $this->authModel->findByEmail($email);

            if ($user) {
                $otp = rand(100000, 999999);
                $_SESSION['reset_email'] = $email;
                $_SESSION['otp_code'] = $otp;
                $_SESSION['otp_expire'] = time() + 300; 

                if ($this->sendEmailOTP($email, $otp)) {
                    header("Location: /baitaplon/Auth/verifyOtp");
                    exit();
                } else {
                    $error = "Lỗi gửi mail!";
                    require_once __DIR__ . '/../views/auth/forgot_password.php';
                }
            } else {
                $error = "Email không tồn tại!";
                require_once __DIR__ . '/../views/auth/forgot_password.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/forgot_password.php';
        }
    }

    // QUÊN MẬT KHẨU (BƯỚC 2: Xác thực OTP)
    public function verifyOtp() {
        if (!isset($_SESSION['reset_email'])) {
            header("Location: /baitaplon/Auth/forgotPassword");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userOtp = $_POST['otp'];
            if ($userOtp == $_SESSION['otp_code'] && time() <= $_SESSION['otp_expire']) {
                $_SESSION['otp_verified'] = true;
                header("Location: /baitaplon/Auth/resetPassword");
                exit();
            } else {
                $error = "Mã OTP sai hoặc hết hạn!";
                require_once __DIR__ . '/../views/auth/verify_otp.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/verify_otp.php';
        }
    }

    // QUÊN MẬT KHẨU (BƯỚC 3: Đổi mật khẩu)
    public function resetPassword() {
        if (!isset($_SESSION['otp_verified'])) {
            header("Location: /baitaplon/Auth/verifyOtp");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPass = $_POST['new_password'];
            if ($newPass === $_POST['confirm_password']) {
                $this->authModel->updatePassword($_SESSION['reset_email'], $newPass);
                unset($_SESSION['otp_code'], $_SESSION['otp_expire'], $_SESSION['reset_email'], $_SESSION['otp_verified']);
                $_SESSION['success'] = "Mật khẩu đã đổi!";
                header("Location: /baitaplon/Auth/login");
                exit();
            } else {
                $error = "Mật khẩu không khớp!";
                require_once __DIR__ . '/../views/auth/reset_password.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/reset_password.php';
        }
    }

    // ĐĂNG XUẤT
    public function logout() {
        session_destroy();
        header("Location: /baitaplon/Auth/login");
        exit();
    }
}