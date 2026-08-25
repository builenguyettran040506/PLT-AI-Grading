-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th8 20, 2026 lúc 06:26 PM
-- Phiên bản máy phục vụ: 5.7.31
-- Phiên bản PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ai_test_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `defects`
--

DROP TABLE IF EXISTS `defects`;
CREATE TABLE IF NOT EXISTS `defects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `defect_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `testcase_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` enum('Low','Medium','High','Critical') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') COLLATE utf8mb4_unicode_ci DEFAULT 'Open',
  `ai_confidence` decimal(5,2) DEFAULT NULL,
  `ai_suggestion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `defect_code` (`defect_code`),
  KEY `testcase_id` (`testcase_id`),
  KEY `idx_bug_status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `defects`
--

INSERT INTO `defects` (`id`, `defect_code`, `testcase_id`, `title`, `description`, `severity`, `status`, `ai_confidence`, `ai_suggestion`, `created_at`, `updated_at`) VALUES
(1, 'BUG-1042', NULL, 'Lỗi timeout khi gọi API cổng thanh toán Momo', 'Timeout sau 10s', 'High', 'Open', '98.50', 'Tăng timeout và retry', '2026-08-01 05:16:03', '2026-08-03 08:26:23'),
(2, 'BUG-1043', NULL, 'Nút \'Thêm vào giỏ hàng\' bị lệch trên mobile UI', 'CSS Flexbox', 'Low', 'In Progress', '65.00', 'Đổi flex-row thành flex-col', '2026-08-01 05:16:03', '2026-08-03 08:26:29'),
(3, 'BUG-1044', NULL, '', 'Thiếu validation', 'Medium', 'Resolved', '91.20', 'Thêm validate phía client', '2026-08-01 05:16:03', '2026-08-03 08:26:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `modules`
--

INSERT INTO `modules` (`id`, `project_id`, `module_name`) VALUES
(1, 1, 'Authentication'),
(2, 1, 'Checkout'),
(3, 1, 'Shopping Cart'),
(4, 1, 'Order'),
(5, 1, 'Profile');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `priorities`
--

DROP TABLE IF EXISTS `priorities`;
CREATE TABLE IF NOT EXISTS `priorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `priorities`
--

INSERT INTO `priorities` (`id`, `name`) VALUES
(1, 'Low'),
(2, 'Medium'),
(3, 'High'),
(4, 'Critical');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_code` (`project_code`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `projects`
--

INSERT INTO `projects` (`id`, `project_code`, `project_name`, `description`, `created_at`) VALUES
(1, 'PRJ001', 'AI Test Management', 'Quản lý Test Case và Defect bằng AI', '2026-08-01 05:16:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'QA'),
(3, 'Tester'),
(4, 'Manager');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `test_cases`
--

DROP TABLE IF EXISTS `test_cases`;
CREATE TABLE IF NOT EXISTS `test_cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tc_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preconditions` text COLLATE utf8mb4_unicode_ci,
  `priority_id` int(11) DEFAULT NULL,
  `result_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Untested',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `procedure_steps` text COLLATE utf8mb4_unicode_ci,
  `expected_results` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tc_code` (`tc_code`),
  KEY `priority_id` (`priority_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_tc_module` (`module_id`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `test_cases`
--

INSERT INTO `test_cases` (`id`, `tc_code`, `module_id`, `title`, `estimation`, `area`, `preconditions`, `priority_id`, `result_status`, `created_by`, `created_at`, `updated_at`, `procedure_steps`, `expected_results`) VALUES
(6, 'TC-SPTH-001', NULL, 'Kiểm tra tải trang “Sản phẩm tổng hợp”', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Danh sách', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập menu “Sản phẩm tổng hợp”\n3. Quan sát quá trình tải màn hình danh sách'),
(2, 'TC0002', 1, 'Đăng nhập bằng tài khoản hợp lệ', '2h', 'Checkout', 'Tài khoản tồn tại', 2, 'Passed', 2, '2026-08-01 05:16:03', '2026-08-16 15:37:19', '1. Access the website.\n2. form đăng nhập xuất hiện.\n3. Nhập thông tin hợp lệ.\n4. Quan sát trang web.', '1.Truy cập trang web thành công.\n2.form đăng nhập xuất hiện thành công.\n3.Nhập đầy đủ thông tin hợp lệ.\n4. Đăng nhập thành công giao diện trang chủ hiện ra.'),
(127, 'TC0001', NULL, 'ktra nút mua', '', '', NULL, 2, 'Passed', 2, '2026-08-20 02:49:25', '2026-08-20 03:06:08', '1. Access the website.\n2. nhấn vào nút mua', '1.Đăng nhập thành công'),
(8, 'TC-SPTH-003', NULL, 'Kiểm tra 4 thẻ thống kê ở trạng thái mặc định', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thống kê', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp” khi chưa có dữ liệu\n3. Quan sát 4 thẻ thống kê phía trên màn hình'),
(9, 'TC-SPTH-004', NULL, 'Kiểm tra cập nhật thẻ thống kê khi có dữ liệu', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thống kê', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Thêm mới dữ liệu sản phẩm tổng hợp vào hệ thống\n3. Quay lại màn hình danh sách “Sản phẩm tổng hợp”\n4. Quan sát các thẻ thống kê'),
(10, 'TC-SPTH-005', NULL, 'Kiểm tra UI các cột trong bảng danh sách', '4', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Danh sách', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Quan sát tiêu đề và dữ liệu các cột trong bảng'),
(11, 'TC-SPTH-006', NULL, 'Kiểm tra phân trang (Pagination)', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Pagination', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp” có nhiều hơn 10 dữ liệu\n3. Quan sát thanh phân trang phía dưới bảng4. Click nút “>” để chuyển trang\n5. Chọn tùy chọn “10/page”'),
(12, 'TC-SPTH-007', NULL, 'Kiểm tra nút “+ Thêm sản phẩm tổng hợp”', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Button/Modal', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Quan sát nút “+ Thêm sản phẩm tổng hợp”\n4. Click nút “+ Thêm sản phẩm tổng hợp”'),
(13, 'TC-SPTH-008', NULL, 'Kiểm tra checkbox trên tiêu đề bảng', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Checkbox', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp” có dữ liệu\n3. Click checkbox tại tiêu đề bảng\n4. Quan sát trạng thái các dòng dữ liệu\n5. Click lại checkbox lần nữa'),
(14, 'TC-SPTH-009', NULL, 'Kiểm tra UI mặc định của Modal “Thêm sản phẩm tổng hợp”', '4', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Modal Thêm', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Quan sát giao diện Modal hiển thị'),
(15, 'TC-SPTH-010', NULL, 'Kiểm tra hiển thị mặc định phần “Các sản phẩm con”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Modal Thêm', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Quan sát khu vực “Các sản phẩm con” khi chưa thêm dữ liệu'),
(16, 'TC-SPTH-011', NULL, 'Kiểm tra nút “X” đóng Modal', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Modal Thêm', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập thử dữ liệu bất kỳ vào form\n5. Click nút “X” ở góc phải modal'),
(17, 'TC-SPTH-012', NULL, 'Kiểm tra nút “Hủy” trên Modal', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Modal Thêm', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình “Sản phẩm tổng hợp”3. Click nút “+ Thêm sản phẩm tổng hợp”4. Nhập dữ liệu vào các field trong form\n5. Click nút “Hủy”'),
(18, 'TC-SPTH-013', NULL, 'Kiểm tra nút “Tạo” khi form trống', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Modal Thêm', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Không nhập bất kỳ dữ liệu nào vào form\n5. Click nút “Tạo”'),
(19, 'TC-SPTH-014', NULL, 'Kiểm tra để trống trường “Mã sản phẩm” khi tạo mới', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Để trống trường “Mã sản phẩm”\n5. Nhập hợp lệ các trường còn lại\n6. Click nút “Tạo”'),
(20, 'TC-SPTH-015', NULL, 'Kiểm tra nhập “Mã sản phẩm” dưới 3 ký tự', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập “SP” vào trường “Mã sản phẩm”\n5. Nhập hợp lệ các trường còn lại\n6. Click nút “Tạo”'),
(21, 'TC-SPTH-016', NULL, 'Kiểm tra nhập “Mã sản phẩm” hợp lệ', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập giá trị “COMBO-01” vào trường “Mã sản phẩm”\n5. Quan sát trạng thái validation của field'),
(22, 'TC-SPTH-017', NULL, 'Kiểm tra nhập “Mã sản phẩm” vượt quá 50 ký tự', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập chuỗi vượt quá 50 ký tự vào trường “Mã sản phẩm”\n5. Quan sát phản hồi của hệ thống'),
(23, 'TC-SPTH-018', NULL, 'Kiểm tra nhập “Mã sản phẩm” đã tồn tại', '5', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập mã sản phẩm đã tồn tại trong hệ thống\n5. Nhập hợp lệ các trường còn lại\n6. Click nút “Tạo”'),
(24, 'TC-SPTH-019', NULL, 'Kiểm tra nhập “Mã sản phẩm” chứa ký tự đặc biệt bị cấm', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập mã sản phẩm chứa ký tự đặc biệt bị cấm (VD. @#$%)\n5. Click nút “Tạo”'),
(25, 'TC-SPTH-020', NULL, 'Kiểm tra nhập “Mã sản phẩm” chỉ chứa khoảng trắng', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập nhiều khoảng trắng vào trường “Mã sản phẩm”\n5. Click nút “Tạo”'),
(26, 'TC-SPTH-021', NULL, 'Kiểm tra để trống trường “Tên sản phẩm” khi tạo mới', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Để trống trường “Tên sản phẩm”\n5. Nhập hợp lệ các trường còn lại trong form\n6. Click nút “Tạo”'),
(27, 'TC-SPTH-022', NULL, 'Kiểm tra nhập “Tên sản phẩm” dưới 3 ký tự', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập giá trị dưới 3 ký tự vào trường “Tên sản phẩm”\n5. Nhập hợp lệ các trường còn lại trong form\n6. Click nút “Tạo”'),
(28, 'TC-SPTH-023', NULL, 'Kiểm tra nhập “Tên sản phẩm” hợp lệ', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập giá trị “Combo Gà Rán” vào trường “Tên sản phẩm”\n5. Quan sát trạng thái validation của field'),
(29, 'TC-SPTH-024', NULL, 'Kiểm tra nhập “Tên sản phẩm” vượt quá 100 ký tự', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập chuỗi vượt quá 100 ký tự vào trường “Tên sản phẩm”\n5. Quan sát phản hồi của hệ thống'),
(30, 'TC-SPTH-025', NULL, 'Kiểm tra nhập “Tên sản phẩm” chỉ chứa khoảng trắng', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập nhiều khoảng trắng vào trường “Tên sản phẩm”\n5. Click nút “Tạo”'),
(31, 'TC-SPTH-026', NULL, 'Kiểm tra để trống trường “Mô tả” khi tạo mới', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Để trống trường “Mô tả”\n5. Nhập hợp lệ các trường bắt buộc khác\n6. Click nút “Tạo”'),
(32, 'TC-SPTH-027', NULL, 'Kiểm tra nhập “Mô tả” với văn bản dài', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập đoạn văn bản dài vào trường “Mô tả”\n5. Quan sát hiển thị dữ liệu trong field'),
(33, 'TC-SPTH-028', NULL, 'Kiểm tra mở dropdown “Chọn công thức”', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Click dropdown “Chọn công thức”'),
(34, 'TC-SPTH-029', NULL, 'Kiểm tra tìm kiếm trong dropdown “Chọn công thức”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Click dropdown “Chọn công thức”\n5. Nhập từ khóa tìm kiếm vào ô tìm kiếm công thức'),
(35, 'TC-SPTH-030', NULL, 'Kiểm tra nút “Tính giá” khi chưa chọn công thức', '3', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Không chọn công thức trong dropdown “Chọn công thức”\n5. Click nút “Tính giá”'),
(36, 'TC-SPTH-031', NULL, 'Kiểm tra nút “Tính giá” sau khi chọn công thức', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Chọn 1 công thức trong dropdown “Chọn công thức”\n5. Click nút “Tính giá”'),
(37, 'TC-SPTH-032', NULL, 'Kiểm tra dữ liệu “Thông tin công thức đã chọn”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Mở modal thêm mới sản phẩm tổng hợp\n4. Chọn công thức và click nút “Tính giá”\n5. Quan sát khối “Thông tin công thức đã chọn”'),
(38, 'TC-SPTH-033', NULL, 'Kiểm tra dữ liệu “Giá công thức đã tính”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Mở modal thêm mới sản phẩm tổng hợp\n4. Chọn công thức và click nút “Tính giá”\n5. Quan sát khối “Giá công thức đã tính”'),
(39, 'TC-SPTH-034', NULL, 'Kiểm tra thay đổi công thức và tính giá lại', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Mở modal thêm mới sản phẩm tổng hợp\n4. Chọn công thức thứ nhất và click “Tính giá”\n5. Thay đổi sang công thức khác\n6. Click nút “Tính giá” lần nữa'),
(40, 'TC-SPTH-035', NULL, 'Kiểm tra xóa trắng ô “Chọn công thức”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Mở modal thêm mới sản phẩm tổng hợp\n4. Chọn công thức và click “Tính giá”\n5. Xóa trắng giá trị trong ô “Chọn công thức”\n6. Quan sát các khối thông tin tính giá'),
(41, 'TC-SPTH-036', NULL, 'Kiểm tra nút “+ Thêm sản phẩm con”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Click nút “+ Thêm sản phẩm con”'),
(42, 'TC-SPTH-037', NULL, 'Kiểm tra dropdown “Sản phẩm” trong block sản phẩm con', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm con”\n4. Click dropdown “Sản phẩm” trong block sản phẩm con'),
(43, 'TC-SPTH-038', NULL, 'Kiểm tra tự động fill dữ liệu khi chọn sản phẩm con', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm con”\n4. Chọn 1 sản phẩm trong dropdown “Sản phẩm”\n5. Quan sát các trường dữ liệu liên quan'),
(44, 'TC-SPTH-039', NULL, 'Kiểm tra nhập số nguyên dương vào trường “Số lượng mỗi suất”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Nhập giá trị số nguyên dương (VD: 2) vào trường “Số lượng mỗi suất”\n5. Quan sát kết quả tính toán'),
(45, 'TC-SPTH-040', NULL, 'Kiểm tra nhập số thập phân vào trường “Số lượng mỗi suất”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Nhập giá trị số thập phân (VD: 1.5) vào trường “Số lượng mỗi suất”\n5. Quan sát kết quả tính toán'),
(46, 'TC-SPTH-041', NULL, 'Kiểm tra nhập giá trị 0 hoặc số âm vào trường “Số lượng mỗi suất”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Nhập giá trị 0 hoặc số âm vào trường “Số lượng mỗi suất”\n5. Click ra ngoài field nhập liệu'),
(47, 'TC-SPTH-042', NULL, 'Kiểm tra để trống trường “Số lượng mỗi suất”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Để trống trường “Số lượng mỗi suất”\n5. Click nút “Tạo”'),
(48, 'TC-SPTH-043', NULL, 'Kiểm tra dropdown “Đơn vị” của sản phẩm con', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Click dropdown “Đơn vị”'),
(49, 'TC-SPTH-044', NULL, 'Kiểm tra trường “Giá vốn” của sản phẩm con', '4', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm và chọn 1 sản phẩm con\n4. Quan sát trường “Giá vốn”\n5. Thử nhập dữ liệu vào field “Giá vốn”'),
(50, 'TC-SPTH-045', NULL, 'Kiểm tra chỉnh sửa trường “Giá bán” của sản phẩm con', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm và chọn 1 sản phẩm con\n4. Nhập giá trị số nguyên >= 0 vào trường “Giá bán”'),
(51, 'TC-SPTH-046', NULL, 'Kiểm tra nhập ký tự chữ vào trường “Giá bán”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm 1 sản phẩm con\n4. Nhập ký tự chữ vào trường “Giá bán”'),
(52, 'TC-SPTH-047', NULL, 'Kiểm tra thêm nhiều sản phẩm con', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm con” nhiều lần để thêm sản phẩm con thứ 2 và thứ 3\n4. Quan sát giao diện form'),
(53, 'TC-SPTH-048', NULL, 'Kiểm tra icon Thùng rác xóa sản phẩm con', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm nhiều sản phẩm con\n4. Click icon Thùng rác tại 1 dòng sản phẩm con'),
(54, 'TC-SPTH-049', NULL, 'Kiểm tra khối “Tóm tắt tài chính”', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập modal “Thêm sản phẩm tổng hợp”\n3. Thêm nhiều sản phẩm con với dữ liệu giá khác nhau\n4. Quan sát khối “Tóm tắt tài chính” ở cuối form'),
(55, 'TC-SPTH-050', NULL, 'Kiểm tra tạo mới sản phẩm tổng hợp chỉ có Công thức', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập hợp lệ trường “Mã sản phẩm” và “Tên sản phẩm”\n5. Chọn 1 công thức trong dropdown “Chọn công thức”\n6. Click nút “Tính giá”\n7. Không thêm sản phẩm con\n8. Click nút “Tạo”'),
(56, 'TC-SPTH-051', NULL, 'Kiểm tra tạo mới sản phẩm tổng hợp chỉ có sản phẩm con', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập hợp lệ trường “Mã sản phẩm” và “Tên sản phẩm”\n5. Không chọn công thức\n6. Thêm ít nhất 1 sản phẩm con hợp lệ\n7. Click nút “Tạo”'),
(57, 'TC-SPTH-052', NULL, 'Kiểm tra tạo mới sản phẩm tổng hợp có cả Công thức và sản phẩm con', '6', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Thêm mới', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click nút “+ Thêm sản phẩm tổng hợp”\n4. Nhập hợp lệ trường “Mã sản phẩm” và “Tên sản phẩm”\n5. Chọn 1 công thức trong dropdown “Chọn công thức”\n6. Click nút “Tính giá”\n7. Thêm ít nhất 1 sản phẩm con hợp lệ\n8. Click nút “Tạo”'),
(58, 'TC-SPTH-053', NULL, 'Kiểm tra hiển thị bản ghi vừa tạo trên màn hình Danh sách', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Danh sách', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Tạo mới thành công 1 sản phẩm tổng hợp\n3. Quay lại màn hình danh sách “Sản phẩm tổng hợp”\n4. Tìm kiếm hoặc quan sát bản ghi vừa tạo'),
(59, 'TC-SPTH-054', NULL, 'Kiểm tra mở modal chỉnh sửa sản phẩm tổng hợp', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Chọn 1 sản phẩm tổng hợp đã tồn tại trong danh sách\n4. Click icon “Sửa” (cây bút) tại dòng dữ liệu tương ứng'),
(60, 'TC-SPTH-055', NULL, 'Kiểm tra trường “Mã sản phẩm” trong modal Sửa', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Quan sát trường “Mã sản phẩm”\n5. Thử nhập/chỉnh sửa dữ liệu tại field này'),
(61, 'TC-SPTH-056', NULL, 'Kiểm tra chỉnh sửa “Tên sản phẩm” hợp lệ', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Chỉnh sửa trường “Tên sản phẩm” bằng giá trị hợp lệ\n5. Click nút “Lưu”\n6. Quay lại màn hình danh sách sản phẩm'),
(62, 'TC-SPTH-057', NULL, 'Kiểm tra đổi công thức và tính giá lại khi chỉnh sửa', '6', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Chọn công thức khác trong dropdown “Chọn công thức”\n5. Click nút “Tính giá”\n6. Click nút “Lưu”'),
(63, 'TC-SPTH-058', NULL, 'Kiểm tra thêm sản phẩm con mới khi chỉnh sửa', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Click nút “+ Thêm sản phẩm con”\n5. Nhập dữ liệu hợp lệ cho sản phẩm con mới\n6. Click nút “Lưu”'),
(64, 'TC-SPTH-059', NULL, 'Kiểm tra xóa sản phẩm con khi chỉnh sửa', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp có sản phẩm con\n4. Click icon Thùng rác tại 1 dòng sản phẩm con\n5. Click nút “Lưu”'),
(65, 'TC-SPTH-060', NULL, 'Kiểm tra để trống “Tên sản phẩm” khi chỉnh sửa', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Xóa trắng dữ liệu tại trường “Tên sản phẩm”\n5. Click nút “Lưu”'),
(66, 'TC-SPTH-061', NULL, 'Kiểm tra nút “Hủy” khi đang chỉnh sửa', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Sửa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Thay đổi dữ liệu trong form chỉnh sửa\n5. Click nút “Hủy”'),
(67, 'TC-SPTH-062', NULL, 'Kiểm tra hiển thị popup xác nhận khi xóa sản phẩm tổng hợp', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa đơn', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Chọn một sản phẩm tổng hợp trong danh sách\n4. Click icon “Xóa” (thùng rác màu đỏ) tại dòng dữ liệu'),
(68, 'TC-SPTH-063', NULL, 'Kiểm tra nút “Hủy” trên popup xác nhận xóa', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa đơn', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Xóa” trên một sản phẩm tổng hợp\n4. Click nút “Hủy” trên popup xác nhận xóa'),
(69, 'TC-SPTH-064', NULL, 'Kiểm tra xác nhận xóa sản phẩm tổng hợp', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa đơn', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Xóa” trên một sản phẩm tổng hợp\n4. Click nút “Xác nhận” trên popup cảnh báo'),
(70, 'TC-SPTH-065', NULL, 'Kiểm tra xóa sản phẩm tổng hợp đang liên kết với hóa đơn bán hàng', '5', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa đơn', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Chọn sản phẩm tổng hợp đang liên kết với hóa đơn bán hàng\n4. Click icon “Xóa”\n5. Click nút “Xác nhận” trên popup cảnh báo'),
(71, 'TC-SPTH-066', NULL, 'Kiểm tra hiển thị nút “Xóa đã chọn” khi chọn nhiều bản ghi', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa nhiều', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Check vào nhiều ô vuông ở cột đầu tiên của bảng dữ liệu\n4. Quan sát thanh công cụ phía trên danh sách'),
(72, 'TC-SPTH-067', NULL, 'Kiểm tra hủy thao tác “Xóa đã chọn”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa nhiều', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Check chọn nhiều bản ghi trong danh sách\n4. Click nút “Xóa đã chọn”\n5. Click nút “Hủy” trên popup xác nhận'),
(73, 'TC-SPTH-068', NULL, 'Kiểm tra xác nhận “Xóa đã chọn” nhiều bản ghi', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Xóa nhiều', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Check chọn nhiều bản ghi trong danh sách\n4. Click nút “Xóa đã chọn”\n5. Click nút “Xác nhận” trên popup xác nhận xóa'),
(74, 'TC-SPTH-069', NULL, 'Kiểm tra mở modal “Chuẩn bị sản phẩm tổng hợp”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Chọn một sản phẩm tổng hợp trong danh sách\n4. Click icon “Chuẩn bị sản phẩm” (hình giọt nước)'),
(75, 'TC-SPTH-070', NULL, 'Kiểm tra giao diện modal “Chuẩn bị sản phẩm tổng hợp”', '4', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Chuẩn bị sản phẩm” trên một sản phẩm tổng hợp\n4. Quan sát thông tin hiển thị trên modal'),
(76, 'TC-SPTH-071', NULL, 'Kiểm tra nhập số thập phân vào trường “Số lượng chuẩn bị”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n3. Nhập giá trị số thập phân (VD: 1.5) vào trường “Số lượng chuẩn bị”\n4. Click ra ngoài field hoặc click nút thao tác'),
(77, 'TC-SPTH-072', NULL, 'Kiểm tra nhập giá trị 0 hoặc số âm vào trường “Số lượng chuẩn bị”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n3. Nhập giá trị 0 hoặc số âm vào trường “Số lượng chuẩn bị”\n4. Click ra ngoài field hoặc thực hiện thao tác chuẩn bị'),
(78, 'TC-SPTH-073', NULL, 'Kiểm tra nhập số nguyên dương hợp lệ vào trường “Số lượng chuẩn bị”', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n3. Nhập giá trị số nguyên dương hợp lệ vào trường “Số lượng chuẩn bị”\n4. Quan sát khối “Tóm tắt chuẩn bị”'),
(79, 'TC-SPTH-074', NULL, 'Kiểm tra vùng “Nguyên liệu cần thiết”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n3. Nhập số lượng chuẩn bị hợp lệ\n4. Quan sát vùng “Nguyên liệu cần thiết”'),
(80, 'TC-SPTH-075', NULL, 'Kiểm tra chuẩn bị sản phẩm khi kho đủ nguyên liệu', '6', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Đảm bảo kho có đủ nguyên liệu cho sản phẩm tổng hợp\n3. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n4. Nhập số lượng chuẩn bị hợp lệ\n5. Click nút “Bắt đầu chuẩn bị”'),
(81, 'TC-SPTH-076', NULL, 'Kiểm tra chuẩn bị sản phẩm khi kho thiếu nguyên liệu', '5', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Đảm bảo kho không đủ nguyên liệu cho sản phẩm tổng hợp\n3. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n4. Nhập số lượng chuẩn bị\n5. Quan sát trạng thái hệ thống hoặc click nút “Bắt đầu chuẩn bị”'),
(82, 'TC-SPTH-077', NULL, 'Kiểm tra nút “Hủy” trên modal chuẩn bị sản phẩm', '3', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Chuẩn bị', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Truy cập modal “Chuẩn bị sản phẩm tổng hợp”\n3. Nhập dữ liệu chuẩn bị sản phẩm\n4. Click nút “Hủy”'),
(83, 'TC-SPTH-078', NULL, 'Kiểm tra cập nhật tồn kho sau khi chuẩn bị sản phẩm tổng hợp', '6', 'Business Workflow', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Nghiệp vụ', '1. Đăng nhập hệ thống PRS bằng tài khoản có quyền kho\n2. Thực hiện chuẩn bị thành công một sản phẩm tổng hợp\n3. Truy cập module “Quản lý tồn kho”\n4. Tìm kiếm sản phẩm tổng hợp vừa chuẩn bị'),
(84, 'TC-SPTH-079', NULL, 'Kiểm tra quyền truy cập của tài khoản Nhân viên bán hàng', '5', 'Authorization', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Quyền', '1. Đăng xuất tài khoản hiện tại\n2. Đăng nhập hệ thống bằng tài khoản Nhân viên bán hàng không có quyền kho\n3. Quan sát menu hệ thống\n4. Truy cập màn hình “Sản phẩm tổng hợp” nếu menu còn hiển thị'),
(85, 'TC-SPTH-080', NULL, 'Kiểm tra xử lý lỗi khi mất kết nối mạng lúc tạo/lưu sản phẩm', '5', 'Error Handling', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Luồng lỗi', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình thêm mới hoặc chỉnh sửa sản phẩm tổng hợp\n3. Nhập dữ liệu hợp lệ vào form\n4. Ngắt kết nối mạng thiết bị\n5. Click nút “Tạo” hoặc “Lưu”'),
(86, 'TC-SPTH-081', NULL, 'Kiểm tra tiêu đề của Modal “Sửa sản phẩm tổng hợp”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp bất kỳ\n4. Quan sát phần tiêu đề của modal'),
(87, 'TC-SPTH-082', NULL, 'Kiểm tra icon đóng (X) trên góc phải modal', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Quan sát icon “X” trên góc phải modal\n5. Click icon “X”'),
(88, 'TC-SPTH-083', NULL, 'Kiểm tra load dữ liệu trường “Mã sản phẩm”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Load dữ liệu', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp đã có dữ liệu\n4. Quan sát trường “Mã sản phẩm”\n5. Thử click hoặc chỉnh sửa field này'),
(89, 'TC-SPTH-084', NULL, 'Kiểm tra load dữ liệu trường “Tên sản phẩm”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Load dữ liệu', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Quan sát trường “Tên sản phẩm”\n5. Click vào field “Tên sản phẩm”'),
(90, 'TC-SPTH-085', NULL, 'Kiểm tra load dữ liệu trường “Mô tả”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Load dữ liệu', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp có dữ liệu mô tả\n4. Quan sát trường “Mô tả”\n5. Thử nhập thêm dữ liệu vào textbox'),
(91, 'TC-SPTH-086', NULL, 'Kiểm tra UI khối “Thông tin công thức”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp có công thức\n3. Quan sát khối “Thông tin công thức”'),
(92, 'TC-SPTH-087', NULL, 'Kiểm tra load thông tin chi tiết của công thức hiện tại', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Load dữ liệu', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp có công thức\n3. Quan sát khối “Thông tin công thức”'),
(93, 'TC-SPTH-088', NULL, 'Kiểm tra UI khối “Thông tin chi phí công thức”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp có công thức\n3. Quan sát khối “Thông tin chi phí công thức”'),
(94, 'TC-SPTH-089', NULL, 'Kiểm tra Box “Chi phí công thức mỗi suất”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Quan sát Box “Chi phí công thức mỗi suất”'),
(95, 'TC-SPTH-090', NULL, 'Kiểm tra Box “Giá sản phẩm hiện tại”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Quan sát Box “Giá sản phẩm hiện tại”'),
(96, 'TC-SPTH-091', NULL, 'Kiểm tra tiêu đề khối “Các sản phẩm con”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Quan sát tiêu đề khối “Các sản phẩm con”'),
(97, 'TC-SPTH-092', NULL, 'Kiểm tra tooltip icon (i) tại khối “Các sản phẩm con”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Hiển thị', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Hover chuột vào icon “(i)” tại tiêu đề sản phẩm con'),
(98, 'TC-SPTH-093', NULL, 'Kiểm tra hiển thị khi không có sản phẩm con', '4', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Khối rỗng', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập chỉnh sửa sản phẩm tổng hợp không có sản phẩm con\n3. Quan sát khu vực “Các sản phẩm con”'),
(99, 'TC-SPTH-094', NULL, 'Kiểm tra trạng thái nút “Cập nhật” khi chưa chỉnh sửa dữ liệu', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Button', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Không thay đổi bất kỳ dữ liệu nào trên form\n4. Quan sát nút “Cập nhật”'),
(100, 'TC-SPTH-095', NULL, 'Kiểm tra trạng thái nút “Hủy”', '3', 'UI Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. UI/Button', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Quan sát nút “Hủy”\n4. Click nút “Hủy”'),
(101, 'TC-SPTH-096', NULL, 'Kiểm tra không cho phép chỉnh sửa trường “Mã sản phẩm”', '3', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Mã SP', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình “Sản phẩm tổng hợp”\n3. Click icon “Sửa” trên một sản phẩm tổng hợp\n4. Dùng chuột bôi đen dữ liệu trường “Mã sản phẩm”\n5. Nhấn phím Backspace hoặc thử nhập dữ liệu mới'),
(102, 'TC-SPTH-097', NULL, 'Kiểm tra để trống trường “Tên sản phẩm”', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP (BVA)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp3. Xóa toàn bộ dữ liệu tại trường “Tên sản phẩm”4. Click ra ngoài field hoặc click nút “Cập nhật”'),
(103, 'TC-SPTH-098', NULL, 'Kiểm tra nhập “Tên sản phẩm” dưới 3 ký tự', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP (BVA)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp3. Nhập giá trị có 1 hoặc 2 ký tự vào trường “Tên sản phẩm” (VD: “Sư”)4. Click ra ngoài field hoặc click nút “Cập nhật”'),
(104, 'TC-SPTH-099', NULL, 'Kiểm tra nhập “Tên sản phẩm” đúng 3 ký tự hợp lệ', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP (BVA)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp3. Nhập giá trị hợp lệ đúng 3 ký tự vào trường “Tên sản phẩm”4. Quan sát trạng thái validation và nút “Cập nhật”'),
(105, 'TC-SPTH-100', NULL, 'Kiểm tra nhập “Tên sản phẩm” đúng 100 ký tự', '4', 'Boundary Value', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP (BVA)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp3. Nhập chuỗi đúng 100 ký tự vào trường “Tên sản phẩm”4. Quan sát phản hồi của hệ thống'),
(106, 'TC-SPTH-101', NULL, 'Kiểm tra nhập “Tên sản phẩm” vượt quá 100 ký tự', '4', 'Boundary Value', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP (BVA)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp3. Nhập chuỗi dài 101 ký tự vào trường “Tên sản phẩm”4. Quan sát phản hồi của hệ thống'),
(107, 'TC-SPTH-102', NULL, 'Kiểm tra nhập “Tên sản phẩm” chứa ký tự đặc biệt', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhập giá trị chứa ký tự đặc biệt vào trường “Tên sản phẩm” (VD: “Sữa @#$!”)\n4. Quan sát trạng thái validation'),
(108, 'TC-SPTH-103', NULL, 'Kiểm tra nhập “Tên sản phẩm” chỉ chứa dấu cách', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhập toàn dấu cách (Space) vào trường “Tên sản phẩm”\n4. Click ra ngoài field hoặc click nút “Cập nhật”'),
(109, 'TC-SPTH-104', NULL, 'Kiểm tra nhập “Tên sản phẩm” trùng với sản phẩm tổng hợp khác', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Tên SP', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhập tên trùng với một sản phẩm tổng hợp khác đã tồn tại\n4. Quan sát phản hồi của hệ thống'),
(110, 'TC-SPTH-105', NULL, 'Kiểm tra nhập văn bản dài vào trường “Mô tả”', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Mô tả', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhập đoạn văn bản rất dài (>1000 từ) vào trường “Mô tả”\n4. Quan sát hiển thị của textarea'),
(111, 'TC-SPTH-106', NULL, 'Kiểm tra bảo mật XSS tại trường “Mô tả”', '5', 'Security Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Mô tả', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Paste đoạn mã HTML/Script vào trường “Mô tả”\n4. Click nút “Cập nhật” hoặc reload lại dữ liệu'),
(112, 'TC-SPTH-107', NULL, 'Kiểm tra không cho phép chỉnh sửa thông tin công thức hiện tại', '4', 'Validation', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Công thức', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp có công thức\n3. Click vào các dòng “Tên công thức”, “Nguyên liệu”, “Sản lượng” trong khối công thức\n4. Quan sát phản hồi của hệ thống'),
(113, 'TC-SPTH-108', NULL, 'Kiểm tra bảo mật khi chỉnh sửa DOM của block công thức', '6', 'Security Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Công thức', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Mở DevTools bằng phím F12\n4. Inspect Element và xóa thuộc tính disabled/read-only của block công thức\n5. Thay đổi dữ liệu công thức từ DOM6. Click nút “Cập nhật”'),
(114, 'TC-SPTH-109', NULL, 'Kiểm tra dữ liệu ô “Chi phí công thức mỗi suất”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Công thức', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp có công thức\n3. Quan sát ô “Chi phí công thức mỗi suất”\n4. Thử click và nhập dữ liệu vào ô này'),
(115, 'TC-SPTH-110', NULL, 'Kiểm tra dữ liệu ô “Giá sản phẩm hiện tại”', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Công thức', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Quan sát ô “Giá sản phẩm hiện tại”\n4. Thử click và nhập dữ liệu trực tiếp vào ô giá'),
(116, 'TC-SPTH-111', NULL, 'Kiểm tra nút “Hủy” khi đã thay đổi dữ liệu', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Hủy bỏ', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Thay đổi dữ liệu trường “Tên sản phẩm” và “Mô tả”\n4. Click nút “Hủy”\n5. Quay lại màn hình danh sách để kiểm tra dữ liệu'),
(117, 'TC-SPTH-112', NULL, 'Kiểm tra icon “X” khi đang chỉnh sửa dữ liệu', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Hủy bỏ', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Thay đổi dữ liệu trên form chỉnh sửa\n4. Click icon “X” tại góc phải trên cùng modal'),
(118, 'TC-SPTH-113', NULL, 'Kiểm tra click ra ngoài modal khi có dữ liệu chưa lưu', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Hủy bỏ', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Thay đổi dữ liệu trên form\n4. Click chuột ra vùng xám bên ngoài modal'),
(119, 'TC-SPTH-114', NULL, 'Kiểm tra cập nhật tên sản phẩm thành công', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Lưu (Happy)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Đổi “Tên sản phẩm” từ “Sữa” thành “Sữa tươi 100%”\n4. Click nút “Cập nhật”'),
(120, 'TC-SPTH-115', NULL, 'Kiểm tra dữ liệu ngoài màn hình danh sách sau khi cập nhật', '4', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Lưu (Happy)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Thực hiện cập nhật tên sản phẩm thành công\n3. Quay lại màn hình danh sách sản phẩm tổng hợp\n4. Tìm dòng dữ liệu có mã sản phẩm “242”'),
(121, 'TC-SPTH-116', NULL, 'Kiểm tra cập nhật trường “Mô tả” thành công', '5', 'Functional', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Lưu (Happy)', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhập/chỉnh sửa dữ liệu tại trường “Mô tả”\n4. Click nút “Cập nhật”\n5. Mở lại form chỉnh sửa sản phẩm'),
(122, 'TC-SPTH-117', NULL, 'Kiểm tra xử lý lỗi mạng khi cập nhật dữ liệu', '5', 'Error Handling', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Lỗi Mạng', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Thay đổi dữ liệu bất kỳ trên form\n4. Ngắt kết nối Internet/Wifi của thiết bị\n5. Click nút “Cập nhật”'),
(123, 'TC-SPTH-118', NULL, 'Kiểm tra xử lý cập nhật đồng thời nhiều tab trình duyệt', '7', 'Concurrency Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Cập nhật kép', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Mở cùng một sản phẩm tổng hợp trên 2 tab trình duyệt khác nhau\n3. Tại Tab 1: sửa “Tên sản phẩm” và click “Cập nhật”\n4. Tại Tab 2: sửa “Mô tả” và click “Cập nhật”'),
(124, 'TC-SPTH-119', NULL, 'Kiểm tra cập nhật khi sản phẩm đã bị xóa khỏi hệ thống', '6', 'Error Handling', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. SP đã bị xóa', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Mở form chỉnh sửa của một sản phẩm tổng hợp\n3. Nhờ Admin xóa bản ghi này trực tiếp trong Database/hệ thống khác\n4. Quay lại form chỉnh sửa và click nút “Cập nhật”'),
(125, 'TC-SPTH-120', NULL, 'Kiểm tra submit form bằng phím Enter', '4', 'Usability Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Bàn phím', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Chỉnh sửa dữ liệu “Tên sản phẩm” hợp lệ\n4. Nhấn phím “Enter” trên bàn phím'),
(126, 'TC-SPTH-121', NULL, 'Kiểm tra đóng form bằng phím ESC', '3', 'Usability Testing', NULL, 2, 'Untested', 2, '2026-08-07 13:11:28', '2026-08-07 13:11:28', '1. Access the website.\n2. Bàn phím', '1. Đăng nhập hệ thống PRS bằng tài khoản hợp lệ\n2. Truy cập màn hình chỉnh sửa sản phẩm tổng hợp\n3. Nhấn phím “ESC” trên bàn phím');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `test_case_steps`
--

DROP TABLE IF EXISTS `test_case_steps`;
CREATE TABLE IF NOT EXISTS `test_case_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `testcase_id` int(11) NOT NULL,
  `step_no` int(11) NOT NULL,
  `action_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `expected_result` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `testcase_id` (`testcase_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `test_case_steps`
--

INSERT INTO `test_case_steps` (`id`, `testcase_id`, `step_no`, `action_text`, `expected_result`) VALUES
(1, 1, 1, 'Access the website - Điều hướng đến trang Checkout', 'Trang Checkout hiển thị'),
(2, 1, 2, 'Access the website - Chọn phương thức thanh toán Momo', 'Hiển thị QR Code'),
(3, 1, 3, 'Access the website - Thực hiện quét mã và thanh toán', 'Thanh toán thành công'),
(4, 2, 1, 'Access the website - Nhập username/password hợp lệ', 'Cho phép nhập'),
(5, 2, 2, 'Access the website - Nhấn nút Đăng nhập', 'Đăng nhập thành công, chuyển hướng trang'),
(6, 3, 1, 'Access the website - Nhấn nút Đặt hàng trong giỏ', 'Sinh mã đơn hàng thành công');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password_hash`, `role_id`, `status`, `created_at`) VALUES
(1, 'System Administrator', 'admin', 'admin@plt.local', '$2y$10$L4UVm3w15QGq.TATRL5.ye2SkjpQdQani9.pkhp0U5b1P52LgY7/i', 1, 'ACTIVE', '2026-08-01 05:16:03'),
(2, 'QA Lead', 'qa', 'qa@plt.local', '$2y$10$L4UVm3w15QGq.TATRL5.ye2SkjpQdQani9.pkhp0U5b1P52LgY7/i', 2, 'ACTIVE', '2026-08-01 05:16:03'),
(3, 'Tester 01', 'tester1', 'tester1@plt.local', '$2y$10$L4UVm3w15QGq.TATRL5.ye2SkjpQdQani9.pkhp0U5b1P52LgY7/i', 3, 'ACTIVE', '2026-08-01 05:16:03');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `vw_dashboard`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_dashboard`;
CREATE TABLE IF NOT EXISTS `vw_dashboard` (
`total_testcases` bigint(21)
,`total_defects` bigint(21)
,`open_defects` bigint(21)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `vw_dashboard`
--
DROP TABLE IF EXISTS `vw_dashboard`;

DROP VIEW IF EXISTS `vw_dashboard`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_dashboard`  AS  select (select count(0) from `test_cases`) AS `total_testcases`,(select count(0) from `defects`) AS `total_defects`,(select count(0) from `defects` where (`defects`.`status` = 'Open')) AS `open_defects` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
