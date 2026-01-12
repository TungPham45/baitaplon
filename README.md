-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 11, 2026 lúc 08:27 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `duchai`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `account`
--

CREATE TABLE `account` (
  `id_user` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT 'Chờ duyệt',
  `ngaytao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `account`
--

INSERT INTO `account` (`id_user`, `username`, `password`, `email`, `role`, `trangthai`, `ngaytao`) VALUES
('US001', 'admin', '$2y$10$8GVaQsh9cignQeHDcU6kwe27XbicgWnCJGq8V0rAr.lo2M8RvrdeK', 'admin@gmail.com', 'Quản lý', 'Hoạt động', '2025-12-28 21:28:00'),
('US002', 'nguyenvanan', '$2y$10$8GVaQsh9cignQeHDcU6kwe27XbicgWnCJGq8V0rAr.lo2M8RvrdeK', 'an.nguyen@gmail.com', 'Người dùng', 'Hoạt động', '2025-12-28 21:28:00'),
('US003', 'tranthib', '$2y$10$8GVaQsh9cignQeHDcU6kwe27XbicgWnCJGq8V0rAr.lo2M8RvrdeK', 'b.tran@gmail.com', 'Người dùng', 'Bị khóa', '2025-12-28 21:28:00'),
('US004', 'testuser1768044544', '$2y$10$Tooa.sC5v6x8vFznVEC1du0b/Enzlt6PFWX3q8Axt.X32AfD8UljG', 'test1768044544@gmail.com', 'Người dùng', 'Chờ duyệt', '2026-01-10 18:29:04'),
('US005', 'testuser1768044561', '$2y$10$KJazaLmHLzippAFRceOXC.7OF2Cmjl.K7greV/.7mOQmQ6KfZVWxu', 'test1768044561@gmail.com', 'Người dùng', 'Chờ duyệt', '2026-01-10 18:29:21'),
('US006', 'lmao', '$2y$10$Oj.w5UK2v48o42mw9oei3usO3MKt2evpNQh9OKhg.71FotLrRMdsS', 'lmao@gmail.com', 'Người dùng', 'Chờ duyệt', '2026-01-10 18:45:10'),
('US007', 'lmao1 ', '$2y$10$Dd.XMhY9pCLTUvdQEWTRvOehAF5UdgwCMeGjs2MLBM/Iu9q2wz86u', 'lmao1@gmail.com', 'Người dùng', 'Chờ duyệt', '2026-01-10 19:15:32'),
('US008', 'duchai', '$2y$10$TL8c/3uYVHEZBMBYrtp00O5UO5tUU5ncQ.CATSEmpECRCcinvRyCe', 'toilahai05@gmail.com', 'Người dùng', 'Hoạt động', '2026-01-10 19:24:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `conversations`
--

CREATE TABLE `conversations` (
  `id_conversation` int(11) NOT NULL,
  `last_message_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `id_sanpham` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `conversations`
--

INSERT INTO `conversations` (`id_conversation`, `last_message_at`, `created_at`, `id_sanpham`) VALUES
(14, '2025-12-28 19:42:25', '2025-12-28 19:42:25', NULL),
(15, '2025-12-28 19:50:22', '2025-12-28 19:50:22', NULL),
(16, '2025-12-28 20:15:12', '2025-12-28 20:15:12', NULL),
(17, '2025-12-28 20:27:32', '2025-12-28 20:27:32', NULL),
(18, '2025-12-29 00:58:48', '2025-12-29 00:58:48', NULL),
(19, '2025-12-29 01:01:53', '2025-12-29 01:01:53', NULL),
(20, '2025-12-29 01:03:10', '2025-12-29 01:03:10', NULL),
(21, '2025-12-29 01:11:26', '2025-12-29 01:11:26', NULL),
(22, '2026-01-10 11:33:33', '2026-01-10 11:33:33', NULL),
(23, '2026-01-10 19:44:47', '2026-01-10 19:29:54', 20),
(24, '2026-01-11 12:31:29', '2026-01-10 19:30:25', 17),
(25, '2026-01-10 19:44:26', '2026-01-10 19:44:13', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `conversation_users`
--

CREATE TABLE `conversation_users` (
  `id_conversation` int(11) NOT NULL,
  `id_user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `conversation_users`
--

INSERT INTO `conversation_users` (`id_conversation`, `id_user`) VALUES
(12, 'US004'),
(14, 'US002'),
(14, 'US004'),
(15, 'US001'),
(15, 'US004'),
(16, '14'),
(16, 'US001'),
(17, 'US001'),
(17, 'US002'),
(18, ''),
(18, '1'),
(19, ''),
(19, '1'),
(20, '1'),
(20, '2'),
(21, '0'),
(21, '1'),
(22, '1'),
(22, 'US002'),
(23, 'US002'),
(23, 'US008'),
(24, 'US001'),
(24, 'US008'),
(25, 'US003'),
(25, 'US008');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `id_danhmuc` int(11) NOT NULL,
  `ten_danhmuc` varchar(255) NOT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`id_danhmuc`, `ten_danhmuc`, `id_parent`, `hinh_anh`) VALUES
(1, 'Xe cộ', NULL, 'icons8-vehicle-50.png'),
(2, 'Ô tô', 1, 'icons8-car-50.png'),
(3, 'Xe máy', 1, 'icons8-bicycle-50.png'),
(4, 'Xe đạp', 1, 'icons8-bike-50.png'),
(5, 'Xe tải & Xe khác', 1, 'icons8-truck-50.png'),
(6, 'Đồ điện tử', NULL, 'icons8-electronic-50.png'),
(7, 'Điện thoại', 6, 'icons8-smartphone-50.png'),
(8, 'Laptop', 6, 'icons8-laptop-50.png'),
(9, 'Loa, Dàn âm thanh', 6, 'icons8-subwoofer-50.png'),
(10, 'Máy tính bảng', 6, 'icons8-tablet-50.png'),
(11, 'Máy ảnh, Máy quay phim', 6, 'icons8-camera-50.png'),
(12, 'Phụ kiện, Linh kiện điện tử', 6, 'icons8-ram-50.png'),
(13, 'Tủ lạnh', 6, 'icons8-fridge-50.png'),
(14, 'Máy giặt', 6, 'icons8-washing-machine-50.png'),
(15, 'Máy lạnh, Điều hòa', 6, 'icons8-ac-50.png'),
(38, 'Đồ gia dụng, Nội thất, Cây cảnh', NULL, 'icons8-furniture-50.png'),
(39, 'Bàn ghế', 38, NULL),
(40, 'Tủ, kệ', 38, NULL),
(41, 'Giường, chăn, ga', 38, NULL),
(42, 'Dụng cụ bếp', 38, NULL),
(43, 'Quạt, đèn', 38, NULL),
(44, 'Cây cối', 38, NULL),
(45, 'Thiết bị vệ sinh', 38, NULL),
(46, 'Khác', 38, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_tri_thuoc_tinh`
--

CREATE TABLE `gia_tri_thuoc_tinh` (
  `id_giatri` int(11) NOT NULL,
  `id_sanpham` int(11) NOT NULL,
  `id_thuoctinh` int(11) NOT NULL,
  `id_option` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_tri_thuoc_tinh`
--

INSERT INTO `gia_tri_thuoc_tinh` (`id_giatri`, `id_sanpham`, `id_thuoctinh`, `id_option`) VALUES
(1, 8, 10, '2'),
(2, 8, 11, '4'),
(3, 8, 12, '16'),
(4, 8, 13, '20'),
(5, 8, 30, '48'),
(6, 8, 31, '54'),
(7, 8, 32, '57'),
(8, 8, 33, '9000'),
(9, 9, 60, '90'),
(10, 9, 61, '93'),
(11, 9, 62, '99'),
(12, 9, 100, '187'),
(13, 9, 101, '189'),
(14, 10, 60, '87'),
(15, 10, 61, '92'),
(16, 10, 62, '101'),
(17, 10, 100, '186'),
(18, 10, 101, '189'),
(19, 11, 10, '2'),
(20, 11, 11, '4'),
(21, 11, 12, '13'),
(22, 11, 13, '19'),
(23, 11, 20, '30'),
(24, 11, 21, '32'),
(25, 11, 22, '37'),
(26, 11, 23, '41'),
(27, 11, 24, '4'),
(28, 11, 25, '180'),
(29, 12, 10, '2'),
(30, 12, 11, '4'),
(31, 12, 12, '17'),
(32, 12, 13, '19'),
(33, 12, 30, '50'),
(34, 12, 31, '55'),
(35, 12, 32, '56'),
(36, 12, 33, '900'),
(37, 17, 10, '2'),
(38, 17, 11, '5'),
(39, 17, 12, '18'),
(40, 17, 13, '19'),
(41, 17, 30, '50'),
(42, 17, 31, '53'),
(43, 17, 32, '57'),
(44, 17, 33, '36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `id_conversation` int(11) NOT NULL,
  `sender_id` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id_message`, `id_conversation`, `sender_id`, `content`, `created_at`, `updated_at`) VALUES
(40, 14, 'US004', 'chào anh', '2025-12-28 19:42:25', NULL),
(41, 14, 'US004', 'em muốn xem sản phẩm này', '2025-12-28 19:42:31', '2025-12-28 19:53:18'),
(42, 14, 'US004', 'chào chị', '2025-12-28 19:42:45', NULL),
(43, 14, 'US004', 'chào', '2025-12-28 19:49:43', NULL),
(45, 15, 'US004', 'hi', '2025-12-28 19:50:24', NULL),
(46, 18, '1', 'cac', '2025-12-29 01:02:55', NULL),
(47, 20, '1', 'ádasdasdfasdf', '2025-12-29 01:03:14', '2025-12-29 01:03:54'),
(49, 14, 'US002', 'alo', '2026-01-10 11:40:30', NULL),
(50, 14, 'US002', 'lo do', '2026-01-10 11:40:33', NULL),
(51, 14, 'US002', 'sao', '2026-01-10 11:40:39', NULL),
(52, 14, 'US002', 'hell', '2026-01-10 11:45:19', '2026-01-10 11:45:26'),
(53, 23, 'US008', 'Em chào anh', '2026-01-10 19:29:57', NULL),
(54, 23, 'US008', 'Anh ơi cho em hỏi sản phẩm này có còn không ạ', '2026-01-10 19:30:07', NULL),
(55, 24, 'US008', 'Alo', '2026-01-10 19:30:28', NULL),
(56, 23, 'US008', 'anh ơi', '2026-01-10 19:30:34', NULL),
(57, 23, 'US008', 'anh ơi', '2026-01-10 19:30:48', NULL),
(58, 23, 'US008', 'alo', '2026-01-10 19:38:28', NULL),
(59, 24, 'US008', 'hi', '2026-01-10 19:38:35', NULL),
(60, 25, 'US008', 'Em chào chị', '2026-01-10 19:44:16', NULL),
(61, 25, 'US008', 'Chị ơi', '2026-01-10 19:44:22', NULL),
(62, 25, 'US003', 'Ơi', '2026-01-10 19:44:26', NULL),
(63, 23, 'US008', 'Anh ơi', '2026-01-10 19:44:47', NULL),
(64, 24, 'US008', 'alo', '2026-01-11 12:31:29', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `id_report` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL COMMENT 'Mã giao dịch/đơn hàng liên quan',
  `reporter_id` varchar(20) NOT NULL COMMENT 'Người đi báo cáo (US001)',
  `reported_id` varchar(20) NOT NULL COMMENT 'Người bị báo cáo (US002)',
  `reason` varchar(100) NOT NULL COMMENT 'Lý do: Lừa đảo, Hàng giả, Thái độ...',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết sự việc',
  `evidence_image` text DEFAULT NULL COMMENT 'Link ảnh bằng chứng (nếu có)',
  `status` enum('PENDING','PROCESSED','REJECTED') DEFAULT 'PENDING' COMMENT 'Trạng thái xử lý của Admin',
  `admin_note` text DEFAULT NULL COMMENT 'Ghi chú của Admin khi xử lý',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reports`
--

INSERT INTO `reports` (`id_report`, `transaction_id`, `reporter_id`, `reported_id`, `reason`, `description`, `evidence_image`, `status`, `admin_note`, `created_at`, `updated_at`) VALUES
(1, NULL, 'US004', 'US001', 'Lừa đảo/Chiếm đoạt tài sản', 'lừa tôi 2tr', NULL, 'PENDING', NULL, '2025-12-28 21:41:49', '2025-12-28 21:41:49'),
(2, NULL, '1', '2', 'Lừa đảo/Chiếm đoạt tài sản', 'NGuyen vip', 'public/uploads/reports/report_1766945308_910.jpg', 'PENDING', NULL, '2025-12-29 01:08:28', '2025-12-29 01:08:28'),
(3, NULL, 'US008', 'US002', 'Hàng giả/Hàng cấm', 'Không thấy trả lời tin nhắn trong 1 thời gian dài', 'public/uploads/reports/report_1768049178_974.jpg', 'PROCESSED', 'Đã khóa tài khoản người dùng.', '2026-01-10 19:46:18', '2026-01-10 19:46:57'),
(4, NULL, 'US008', 'US003', 'Lừa đảo/Chiếm đoạt tài sản', 'Lừa 2 triệu', 'public/uploads/reports/report_1768050336_967.JPG', 'PROCESSED', 'Đã khóa tài khoản người dùng.', '2026-01-10 20:05:36', '2026-01-10 20:05:47'),
(5, NULL, 'US008', 'US001', 'Hàng giả/Hàng cấm', 'Không thấy trả lời, có giấu hiệu scam', 'public/uploads/reports/report_1768114325_154.jpg', 'PENDING', NULL, '2026-01-11 13:52:05', '2026-01-11 13:52:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `user_id` varchar(20) NOT NULL COMMENT 'Người đánh giá',
  `seller_id` varchar(20) NOT NULL COMMENT 'Người được đánh giá (Chủ shop)',
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT 'Số sao (1-5)',
  `comment` text DEFAULT NULL COMMENT 'Nội dung nhận xét',
  `is_hidden` tinyint(1) DEFAULT 0 COMMENT '1 = Ẩn (nếu vi phạm)',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_transacted` tinyint(1) DEFAULT 0 COMMENT '0: Chưa giao dịch, 1: Đã giao dịch (User tự tick)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id_review`, `transaction_id`, `user_id`, `seller_id`, `rating`, `comment`, `is_hidden`, `created_at`, `is_transacted`) VALUES
(1, NULL, 'US008', 'US003', 1, 'Quá tệ', 0, '2026-01-10 20:05:24', 0),
(2, NULL, 'US008', 'US001', 2, '', 0, '2026-01-11 13:27:34', 0),
(3, NULL, 'US008', 'US001', 4, 'Tuyệt vời', 0, '2026-01-11 13:28:06', 0),
(4, NULL, 'US008', 'US001', 5, 'Thái độ rất tuyệt vời', 0, '2026-01-11 13:30:27', 0),
(5, NULL, 'US008', 'US001', 5, 'Thái độ rất tuyệt vời', 0, '2026-01-11 13:31:05', 0),
(6, NULL, 'US008', 'US002', 5, 'ádasd', 0, '2026-01-11 13:39:06', 1),
(7, NULL, 'US008', 'US002', 5, '[Nhiệt tình] [Đúng giờ]\r\nTrao đổi rất nhiệt tình và đúng về sản phẩm', 0, '2026-01-11 13:50:56', 1),
(8, NULL, 'US008', 'US001', 1, '[Thái độ kém]\r\nKhông thấy trả lời tin nhắn', 0, '2026-01-11 13:51:44', 0),
(9, NULL, 'US008', 'US002', 5, '[Đúng giờ] [Trả lời nhanh]\r\nQuá Tuyệt vời', 0, '2026-01-11 14:21:32', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review_images`
--

CREATE TABLE `review_images` (
  `id_image` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL COMMENT 'Đường dẫn ảnh trên server',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `review_images`
--

INSERT INTO `review_images` (`id_image`, `review_id`, `image_path`, `created_at`) VALUES
(1, 7, 'uploads/reviews/1768114256_696348506f86f.jpg', '2026-01-11 13:50:56'),
(2, 8, 'uploads/reviews/1768114304_696348800aff3.jpg', '2026-01-11 13:51:44'),
(3, 9, 'uploads/reviews/1768116092_69634f7cbddbe.jpg', '2026-01-11 14:21:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `id_sanpham` int(11) NOT NULL,
  `ten_sanpham` varchar(255) NOT NULL,
  `id_danhmuc` int(11) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `gia` decimal(15,2) NOT NULL,
  `khu_vuc_ban` varchar(255) DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `ngaydang` datetime DEFAULT current_timestamp(),
  `trangthai` varchar(50) DEFAULT 'hienthi',
  `luot_xem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`id_sanpham`, `ten_sanpham`, `id_danhmuc`, `id_user`, `gia`, `khu_vuc_ban`, `mota`, `avatar`, `ngaydang`, `trangthai`, `luot_xem`) VALUES
(1, 'Toyota Vios 2021 G Tự động, xe gia đình, biển Hà Nội', 2, 'US002', 455000000.00, 'Hà Nội', 'Cần bán xe Toyota Vios bản G số tự động đời 2021. Xe màu trắng, chính chủ từ đầu, đi được 3 vạn km. Cam kết không đâm đụng, ngập nước. Bao test hãng.', 'vios_thumb.jpg', '2024-05-01 08:00:00', 'Đã duyệt', 0),
(2, 'iPhone 15 Pro Max 256GB Titan Tự Nhiên VN/A', 7, 'US003', 28500000.00, 'TP.HCM', 'Máy mới mua tại TGDĐ được 2 tháng, còn bảo hành dài. Pin 100%, ngoại hình đẹp keng không vết xước. Fullbox đầy đủ phụ kiện.', 'ip15_thumb.jpg', '2024-05-02 09:30:00', 'Đã duyệt', 0),
(3, 'Laptop Dell XPS 13 9310 i7 Ram 16GB SSD 512GB', 8, 'US002', 18500000.00, 'Hà Nội', 'Máy xách tay Mỹ, ngoại hình 98%. Màn hình 4K cảm ứng cực nét. Pin dùng 5-6 tiếng. Tặng kèm túi chống sốc và chuột không dây.', 'dell_thumb.jpg', '2024-05-03 10:15:00', 'Đã duyệt', 0),
(4, 'Tủ lạnh Panasonic Inverter 255 lít mới 99%', 13, 'US003', 4500000.00, 'TP.HCM', 'Chuyển nhà nên cần thanh lý tủ lạnh Panasonic. Tủ chạy êm, tiết kiệm điện, làm đá nhanh. Mới dùng được 1 năm.', 'tulanh_thumb.jpg', '2024-05-04 14:00:00', 'Đã duyệt', 0),
(5, 'Honda SH 150i ABS 2022 Màu Xám Xi Măng', 3, 'US002', 85000000.00, 'Hà Nội', 'Xe SH 150i phanh ABS, biển số đẹp. Đã lên full ốc titan, dán keo trong. Máy móc nguyên zin chưa sửa chữa. Xem xe tại nhà.', 'sh_thumb.jpg', '2024-05-05 16:45:00', 'Đã duyệt', 0),
(8, 'xe máy chạy html', 3, 'US001', 900000000.00, 'tây sơn tiền hải', 'hàng ngon, oke, chạy tốt , kéo tốt các kèo , các sản phẩm ngon', 'public/images/sp_1766934394_6951477a4be14.jpg', '2025-12-28 22:06:34', 'Đã duyệt', 0),
(9, 'Bán Máy tính', 10, 'US002', 99999999.00, 't', 'chơi thì ngon luôn , mua đi , mua đi mại dô mại dô', 'public/images/sp_1766934651_6951487bdfddb.jpg', '2025-12-28 22:10:51', 'Đã duyệt', 0),
(10, 'Bán case máy tính', 10, 'US001', 9999999999999.00, 'Hoàng mai Hà Nội', 'dùng nogn , mại dô mại dô , anh em vào xem ủng hộ giúp em , mọi người vào xem live', 'public/images/sp_1766937896_69515528d1e8e.jpg', '2025-12-28 23:04:56', 'Đã duyệt', 0),
(11, 'Bán Xe IoV', 2, 'US001', 180000.00, 'Tây Sơn Tiền Hải', 'Xe Còn Ngon , anh em mại dô hú hú mua giúp tôi tôi tặng', 'public/images/sp_1766940068_69515da48234b.png', '2025-12-28 23:41:08', 'Đã duyệt', 0),
(12, 'EM', 3, 'US001', 9000000000.00, 'Tây Sơn Tiền Hải', 'em yêu utt em yêu utt em yêu utt em yêu utt em yêu utt em yêu utt', 'public/images/sp_1766940186_69515e1a224e1.png', '2025-12-28 23:43:06', 'Đã duyệt', 0),
(13, 'Yêu', 46, 'US001', 99999999.00, 'Tây Sơn Tiền Hải', 'toi yeu utt toi yeu utt toi yeu utt toi yeu utt', 'public/images/sp_1766942482_6951671223a93.jpg', '2025-12-29 00:21:22', 'Đã duyệt', 0),
(14, 'trai đẹp', 46, 'US001', 9999999999.00, 'Tây Sơn Tiền Hải', 'em yêu trường em với bao bạn thân cùng cô giáo hiền', 'public/images/sp_1766942546_69516752e63b2.jpg', '2025-12-29 00:22:26', 'Đã duyệt', 0),
(15, 'Deadpool', 40, 'US001', 999999.00, 'Tây Sơn Tiền Hải', 'ngoài anh em có những ai , bên em hiện tại vì yêu em nên anh vẫn ở lại', 'public/images/sp_1766942656_695167c05ae7b.jpg', '2025-12-29 00:24:16', 'Đã duyệt', 0),
(16, 'tÔI BÁN BÓNG ĐÊM', 46, 'US001', 99999.00, 'Tây Sơn Tiền Hải', 'MỘT HAI BA BỐN NGÀY CÓ NHỮNG NỐI NHỚ KHÔNG THỂ', 'public/images/sp_1766942795_6951684b15501.jpg', '2025-12-29 00:26:35', 'Đã duyệt', 0),
(17, 'Bán Hàng Giả', 3, 'US001', 600000.00, 'Tây Sơn Tiền Hải', 'Bán hàng giả người giả Faker Fake , 6 cúp', 'public/images/sp_1766945751_695173d734f31.jpg', '2025-12-29 01:15:51', 'Đã duyệt', 0),
(18, 'Lenovo', 8, 'US002', 1234.00, 'Ha Noi', 'jasid jdiasdj id dashud hasd kdaks b jsandj s u jidbasj bdjias jkdbashjk bnajksd', 'public/images/sp_1768033307_69620c1b2025f.jpg', '2026-01-10 15:21:47', 'Đã duyệt', 0),
(19, 'Honda', 3, 'US002', 356.00, 'Hà Nội', '123 da sd 123 sdf dgasd x adsa sdgdf axc xzf ds fsdfs', 'public/images/sp_1768033391_69620c6f3e9dc.jpg', '2026-01-10 15:23:11', 'Đã duyệt', 0),
(20, 'Ghế', 39, 'US002', 18.00, 'Hà Nội', 'jij qwjd njas dá jdjas asjk sahu kjas  ihdujhuadshu bj ugsdab juasd', 'public/images/sp_1768034893_6962124de2b81.jpg', '2026-01-10 15:48:13', 'Đã duyệt', 0),
(21, 'Viois', 2, 'US002', 1234.00, 'Hồ Chí Minh', 'Đéo biết mô tả cái chó j cả dài vl ấy bro ak', 'public/images/sp_1768045807_69623cefe3d73.jpg', '2026-01-10 18:50:07', 'Chờ duyệt', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham_anh`
--

CREATE TABLE `sanpham_anh` (
  `id_anh` int(11) NOT NULL,
  `id_sanpham` int(11) NOT NULL,
  `url_anh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham_anh`
--

INSERT INTO `sanpham_anh` (`id_anh`, `id_sanpham`, `url_anh`) VALUES
(0, 18, 'public/images/sp_1768033307_69620c1b2025f.jpg'),
(1, 1, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(2, 1, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(3, 1, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(4, 1, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(5, 2, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(6, 2, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(7, 2, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(8, 3, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(9, 3, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(10, 4, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(11, 4, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(12, 5, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(13, 5, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(14, 5, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(15, 8, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(16, 9, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(17, 9, 'baitaplonpublicimageskhoinguyenchuai.jpg'),
(18, 10, 'public/images/sp_1766937896_69515528d1e8e.jpg'),
(19, 10, 'public/images/sp_1766937896_69515528d209b.jpg'),
(20, 11, 'public/images/sp_1766940068_69515da48234b.png'),
(21, 12, 'public/images/sp_1766940186_69515e1a224e1.png'),
(22, 13, 'public/images/sp_1766942482_6951671223a93.jpg'),
(23, 14, 'public/images/sp_1766942546_69516752e63b2.jpg'),
(24, 14, 'public/images/sp_1766942546_69516752e66ed.jpg'),
(25, 15, 'public/images/sp_1766942656_695167c05ae7b.jpg'),
(26, 16, 'public/images/sp_1766942795_6951684b15501.jpg'),
(27, 17, 'public/images/sp_1766945751_695173d734f31.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh`
--

CREATE TABLE `thuoc_tinh` (
  `id_thuoctinh` int(11) NOT NULL,
  `id_danhmuc` int(11) NOT NULL,
  `ten_thuoctinh` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thuoc_tinh`
--

INSERT INTO `thuoc_tinh` (`id_thuoctinh`, `id_danhmuc`, `ten_thuoctinh`) VALUES
(10, 1, 'Tình trạng'),
(11, 1, 'Xuất xứ'),
(12, 1, 'Màu sắc'),
(13, 1, 'Bảo hành'),
(20, 2, 'Hãng ô tô'),
(21, 2, 'Kiểu dáng'),
(22, 2, 'Hộp số'),
(23, 2, 'Nhiên liệu'),
(24, 2, 'Số chỗ ngồi'),
(25, 2, 'Số Km đã đi'),
(30, 3, 'Hãng xe máy'),
(31, 3, 'Loại xe máy'),
(32, 3, 'Dung tích xe'),
(33, 3, 'Số Km đã đi'),
(40, 4, 'Hãng xe đạp'),
(41, 4, 'Loại xe đạp'),
(42, 4, 'Chất liệu khung'),
(50, 5, 'Hãng xe tải'),
(51, 5, 'Tải trọng'),
(60, 6, 'Tình trạng'),
(61, 6, 'Bảo hành'),
(62, 6, 'Xuất xứ'),
(70, 7, 'Hãng điện thoại'),
(71, 7, 'Dòng máy'),
(72, 7, 'Dung lượng'),
(73, 7, 'Màu sắc'),
(80, 8, 'Hãng Laptop'),
(81, 8, 'Dòng máy'),
(82, 8, 'Vi xử lý (CPU)'),
(83, 8, 'RAM'),
(84, 8, 'Kích cỡ màn hình'),
(85, 8, 'Dung lượng ổ cứng'),
(86, 8, 'Loại ổ cứng'),
(87, 8, 'Card đồ họa (VGA)'),
(90, 9, 'Hãng sản xuất'),
(91, 9, 'Loại loa'),
(92, 9, 'Công suất'),
(100, 10, 'Hãng sản xuất'),
(101, 10, 'Kết nối mạng'),
(110, 11, 'Loại máy'),
(111, 11, 'Hãng sản xuất'),
(120, 12, 'Loại phụ kiện'),
(121, 12, 'Thiết bị'),
(130, 13, 'Hãng sản xuất'),
(131, 13, 'Thể tích'),
(140, 14, 'Hãng sản xuất'),
(141, 14, 'Kiểu máy'),
(142, 14, 'Khối lượng giặt'),
(150, 15, 'Hãng sản xuất'),
(151, 15, 'Công suất');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuoc_tinh_options`
--

CREATE TABLE `thuoc_tinh_options` (
  `id_option` int(11) NOT NULL,
  `id_thuoctinh` int(11) NOT NULL,
  `gia_tri_option` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thuoc_tinh_options`
--

INSERT INTO `thuoc_tinh_options` (`id_option`, `id_thuoctinh`, `gia_tri_option`) VALUES
(1, 10, 'Mới'),
(2, 10, 'Đã sử dụng (Chưa sửa chữa)'),
(3, 10, 'Đã sử dụng (Đã sửa chữa)'),
(4, 11, 'Việt Nam'),
(5, 11, 'Thái Lan'),
(6, 11, 'Nhật Bản'),
(7, 11, 'Trung Quốc'),
(8, 11, 'Hàn Quốc'),
(9, 11, 'Mỹ'),
(10, 11, 'Đức'),
(11, 12, 'Trắng'),
(12, 12, 'Đen'),
(13, 12, 'Đỏ'),
(14, 12, 'Bạc'),
(15, 12, 'Xám'),
(16, 12, 'Xanh dương'),
(17, 12, 'Nâu'),
(18, 12, 'Vàng'),
(19, 13, 'Còn bảo hành'),
(20, 13, 'Hết bảo hành'),
(21, 20, 'Toyota'),
(22, 20, 'Hyundai'),
(23, 20, 'Kia'),
(24, 20, 'Vinfast'),
(25, 20, 'Mazda'),
(26, 20, 'Ford'),
(27, 20, 'Honda'),
(28, 20, 'Mercedes-Benz'),
(29, 20, 'BMW'),
(30, 20, 'Audi'),
(31, 20, 'Lexus'),
(32, 21, 'Sedan'),
(33, 21, 'SUV / Crossover'),
(34, 21, 'Hatchback'),
(35, 21, 'Pick-up (Bán tải)'),
(36, 21, 'Minivan (MPV)'),
(37, 22, 'Số sàn'),
(38, 22, 'Số tự động'),
(39, 22, 'Bán tự động'),
(40, 23, 'Xăng'),
(41, 23, 'Dầu'),
(42, 23, 'Điện'),
(43, 23, 'Hybrid'),
(44, 30, 'Honda'),
(45, 30, 'Yamaha'),
(46, 30, 'Suzuki'),
(47, 30, 'Piaggio'),
(48, 30, 'Sym'),
(49, 30, 'Ducati'),
(50, 30, 'Kawasaki'),
(51, 30, 'Vinfast'),
(52, 31, 'Xe tay ga'),
(53, 31, 'Xe số'),
(54, 31, 'Xe côn tay'),
(55, 31, 'Xe điện'),
(56, 32, 'Dưới 50cc'),
(57, 32, '50 - 100cc'),
(58, 32, '100 - 175cc'),
(59, 32, 'Trên 175cc'),
(60, 40, 'Giant'),
(61, 40, 'Asama'),
(62, 40, 'Thống Nhất'),
(63, 40, 'Trek'),
(64, 40, 'Galaxy'),
(65, 40, 'Trinx'),
(66, 40, 'Jett'),
(67, 41, 'Xe đạp thể thao (Địa hình)'),
(68, 41, 'Xe đạp đua (Road)'),
(69, 41, 'Xe đạp phố (Touring)'),
(70, 41, 'Xe đạp trẻ em'),
(71, 41, 'Xe đạp điện'),
(72, 42, 'Thép'),
(73, 42, 'Nhôm'),
(74, 42, 'Carbon'),
(75, 42, 'Titan'),
(76, 50, 'Hyundai'),
(77, 50, 'Thaco'),
(78, 50, 'Isuzu'),
(79, 50, 'Hino'),
(80, 50, 'Kia'),
(81, 50, 'Suzuki'),
(82, 50, 'Dongben'),
(83, 51, 'Dưới 500kg'),
(84, 51, '500kg - 1 tấn'),
(85, 51, '1 tấn - 3 tấn'),
(86, 51, 'Trên 3 tấn'),
(87, 60, 'Mới 100% (Nguyên Seal)'),
(88, 60, 'Like New 99% (Như mới)'),
(89, 60, 'Cũ 98% (Xước nhẹ)'),
(90, 60, 'Cũ 95% (Cấn móp)'),
(91, 60, 'Xác máy (Hư hỏng/Lấy linh kiện)'),
(92, 61, 'Còn bảo hành chính hãng'),
(93, 61, 'Bảo hành cửa hàng'),
(94, 61, 'Hết bảo hành'),
(95, 62, 'Chính hãng VN/A'),
(96, 62, 'Xách tay Mỹ (LL/A)'),
(97, 62, 'Xách tay Hàn (KH/A)'),
(98, 62, 'Xách tay Nhật (J/A)'),
(99, 62, 'Xách tay Trung Quốc (CN/A)'),
(100, 62, 'Xách tay Singapore (ZA/A)'),
(101, 62, 'Xuất xứ khác'),
(102, 70, 'Apple'),
(103, 70, 'Samsung'),
(104, 70, 'Xiaomi'),
(105, 70, 'Oppo'),
(106, 70, 'Huawei'),
(107, 70, 'Sony'),
(108, 70, 'Google Pixel'),
(109, 70, 'Realme'),
(110, 70, 'Vivo'),
(111, 70, 'Hãng khác'),
(112, 72, '16GB'),
(113, 72, '32GB'),
(114, 72, '64GB'),
(115, 72, '128GB'),
(116, 72, '256GB'),
(117, 72, '512GB'),
(118, 72, '1TB'),
(119, 72, 'Dung lượng khác'),
(120, 73, 'Đen'),
(121, 73, 'Trắng'),
(122, 73, 'Vàng (Gold)'),
(123, 73, 'Bạc (Silver)'),
(124, 73, 'Xám (Space Gray)'),
(125, 73, 'Xanh Pacific'),
(126, 73, 'Tím (Deep Purple)'),
(127, 73, 'Titan Tự nhiên'),
(128, 73, 'Xanh Sierra'),
(129, 73, 'Hồng'),
(130, 73, 'Màu khác'),
(131, 71, 'iPhone 15 Pro Max'),
(132, 71, 'iPhone 15 Pro'),
(133, 71, 'iPhone 14 Pro Max'),
(134, 71, 'iPhone 13 Pro Max'),
(135, 71, 'iPhone 11'),
(136, 71, 'Galaxy S24 Ultra'),
(137, 71, 'Galaxy Z Fold 5'),
(138, 71, 'Xiaomi 14 Ultra'),
(139, 71, 'Dòng máy khác'),
(140, 80, 'MacBook (Apple)'),
(141, 80, 'Dell'),
(142, 80, 'HP'),
(143, 80, 'Asus'),
(144, 80, 'Lenovo'),
(145, 80, 'Acer'),
(146, 80, 'MSI'),
(147, 80, 'Hãng khác'),
(148, 81, 'MacBook Air'),
(149, 81, 'MacBook Pro'),
(150, 81, 'Dell XPS'),
(151, 81, 'Asus ROG'),
(152, 81, 'Dòng máy khác'),
(153, 82, 'Intel Core i5'),
(154, 82, 'Intel Core i7'),
(155, 82, 'Apple M1'),
(156, 82, 'Apple M2'),
(157, 82, 'AMD Ryzen 5'),
(158, 82, 'CPU khác'),
(159, 83, '8 GB'),
(160, 83, '16 GB'),
(161, 83, '32 GB'),
(162, 83, 'Dung lượng khác'),
(163, 84, '13 - 13.9 inch'),
(164, 84, '14 inch'),
(165, 84, '15.6 inch'),
(166, 84, 'Kích thước khác'),
(167, 85, '256 GB'),
(168, 85, '512 GB'),
(169, 85, '1 TB'),
(170, 86, 'SSD'),
(171, 86, 'HDD'),
(172, 87, 'NVIDIA GeForce RTX'),
(173, 87, 'Onboard'),
(174, 87, 'Card khác'),
(175, 90, 'JBL'),
(176, 90, 'Sony'),
(177, 90, 'Marshall'),
(178, 90, 'Hãng khác'),
(179, 91, 'Loa Bluetooth'),
(180, 91, 'Loa Kéo'),
(181, 91, 'Loa Soundbar'),
(182, 91, 'Loại khác'),
(183, 92, 'Dưới 100W'),
(184, 92, '100W - 200W'),
(185, 92, 'Trên 200W'),
(186, 100, 'iPad (Apple)'),
(187, 100, 'Samsung Tab'),
(188, 100, 'Xiaomi Pad'),
(189, 101, 'Wifi Only'),
(190, 101, 'Wifi + 4G/5G'),
(191, 110, 'DSLR'),
(192, 110, 'Mirrorless'),
(193, 110, 'Compact'),
(194, 110, 'Flycam'),
(195, 111, 'Canon'),
(196, 111, 'Sony'),
(197, 111, 'Fujifilm'),
(198, 111, 'Nikon'),
(199, 120, 'Linh kiện Máy tính'),
(200, 120, 'Phụ kiện Điện thoại'),
(201, 121, 'Chuột'),
(202, 121, 'Bàn phím'),
(203, 121, 'Tai nghe'),
(204, 121, 'RAM'),
(205, 121, 'SSD'),
(206, 121, 'Sạc cáp'),
(207, 130, 'Panasonic'),
(208, 130, 'Toshiba'),
(209, 130, 'Hitachi'),
(210, 130, 'Samsung'),
(211, 130, 'LG'),
(212, 131, 'Dưới 150 lít'),
(213, 131, '150 - 300 lít'),
(214, 131, 'Trên 300 lít'),
(215, 140, 'Electrolux'),
(216, 140, 'LG'),
(217, 140, 'Samsung'),
(218, 140, 'Toshiba'),
(219, 141, 'Cửa trước'),
(220, 141, 'Cửa trên'),
(221, 142, 'Dưới 8kg'),
(222, 142, '8kg - 10kg'),
(223, 142, 'Trên 10kg'),
(224, 150, 'Panasonic'),
(225, 150, 'Daikin'),
(226, 150, 'Casper'),
(227, 150, 'LG'),
(228, 151, '1 HP (9000 BTU)'),
(229, 151, '1.5 HP (12000 BTU)'),
(230, 151, '2 HP (18000 BTU)');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id_user` varchar(20) NOT NULL,
  `hoten` varchar(255) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `diachi` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `gioithieu` text DEFAULT NULL,
  `danhgia` decimal(2,1) DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id_user`, `hoten`, `sdt`, `diachi`, `avatar`, `gioithieu`, `danhgia`) VALUES
('US001', 'Quản Trị Viên', '0909000111', 'Hà Nội', 'public/images/1766940298_chắc nền 2.jpg', 'Hệ thống quản trị Chợ Tốt Clone', 0.0),
('US002', 'Nguyễn Văn A', '0912345678', 'Cầu Giấy, Hà Nội', 'user1.jpg', 'Chuyên bán các dòng xe lướt và đồ công nghệ cũ.', 0.0),
('US003', 'Trần Thị B', '0987654321', 'Quận 1, TP.HCM', 'user2.jpg', 'Thanh lý đồ dùng cá nhân, điện thoại like new.', 0.0),
('US004', 'Test User', '0123456789', 'Test Address', NULL, NULL, 0.0),
('US005', 'Test User New', '0123456789', 'Test Address', NULL, NULL, 0.0),
('US006', 'lmao', '0987612851', 'Hà Nội', NULL, NULL, 0.0),
('US007', 'lmao1', '0985721935', 'Thanh Hóa', NULL, NULL, 0.0),
('US008', 'Bùi Đức Hải', '0383572217', 'Điện Biên', '1768047960_IMG_E7973 - Copy.JPG', 'Tôi đang test', 0.0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id_user`);

--
-- Chỉ mục cho bảng `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id_conversation`);

--
-- Chỉ mục cho bảng `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD PRIMARY KEY (`id_conversation`,`id_user`),
  ADD KEY `fk_cu_user` (`id_user`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`id_danhmuc`),
  ADD KEY `id_parent` (`id_parent`);

--
-- Chỉ mục cho bảng `gia_tri_thuoc_tinh`
--
ALTER TABLE `gia_tri_thuoc_tinh`
  ADD PRIMARY KEY (`id_giatri`),
  ADD KEY `id_sanpham` (`id_sanpham`),
  ADD KEY `id_thuoctinh` (`id_thuoctinh`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `fk_messages_conversation` (`id_conversation`),
  ADD KEY `fk_messages_sender` (`sender_id`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id_report`),
  ADD KEY `idx_reporter` (`reporter_id`),
  ADD KEY `idx_reported` (`reported_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `fk_review_user` (`user_id`),
  ADD KEY `fk_review_seller` (`seller_id`);

--
-- Chỉ mục cho bảng `review_images`
--
ALTER TABLE `review_images`
  ADD PRIMARY KEY (`id_image`),
  ADD KEY `fk_img_review` (`review_id`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`id_sanpham`),
  ADD KEY `id_danhmuc` (`id_danhmuc`),
  ADD KEY `id_user` (`id_user`);

--
-- Chỉ mục cho bảng `sanpham_anh`
--
ALTER TABLE `sanpham_anh`
  ADD PRIMARY KEY (`id_anh`),
  ADD KEY `id_sanpham` (`id_sanpham`);

--
-- Chỉ mục cho bảng `thuoc_tinh`
--
ALTER TABLE `thuoc_tinh`
  ADD PRIMARY KEY (`id_thuoctinh`),
  ADD KEY `id_danhmuc` (`id_danhmuc`);

--
-- Chỉ mục cho bảng `thuoc_tinh_options`
--
ALTER TABLE `thuoc_tinh_options`
  ADD PRIMARY KEY (`id_option`),
  ADD KEY `id_thuoctinh` (`id_thuoctinh`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id_conversation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `id_danhmuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `gia_tri_thuoc_tinh`
--
ALTER TABLE `gia_tri_thuoc_tinh`
  MODIFY `id_giatri` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id_report` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `review_images`
--
ALTER TABLE `review_images`
  MODIFY `id_image` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `id_sanpham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `review_images`
--
ALTER TABLE `review_images`
  ADD CONSTRAINT `fk_img_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id_review`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
