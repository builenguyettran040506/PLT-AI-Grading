-- =====================================================
-- DATABASE: AI TEST CASE MANAGEMENT
-- =====================================================
DROP DATABASE IF EXISTS ai_test_management;
CREATE DATABASE ai_test_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ai_test_management;

CREATE TABLE roles(
 id INT AUTO_INCREMENT PRIMARY KEY,
 role_name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE users(
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(150) NOT NULL,
 username VARCHAR(50) UNIQUE NOT NULL,
 email VARCHAR(150) UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role_id INT NOT NULL,
 status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(role_id) REFERENCES roles(id)
);

CREATE TABLE projects(
 id INT AUTO_INCREMENT PRIMARY KEY,
 project_code VARCHAR(20) UNIQUE,
 project_name VARCHAR(200) NOT NULL,
 description TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE modules(
 id INT AUTO_INCREMENT PRIMARY KEY,
 project_id INT NOT NULL,
 module_name VARCHAR(120) NOT NULL,
 FOREIGN KEY(project_id) REFERENCES projects(id)
);

CREATE TABLE priorities(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(20) UNIQUE
);

CREATE TABLE test_cases(
 id INT AUTO_INCREMENT PRIMARY KEY,
 tc_code VARCHAR(20) UNIQUE,
 module_id INT NOT NULL,
 title VARCHAR(255) NOT NULL,
 preconditions TEXT,
 priority_id INT,
 created_by INT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(module_id) REFERENCES modules(id),
 FOREIGN KEY(priority_id) REFERENCES priorities(id),
 FOREIGN KEY(created_by) REFERENCES users(id)
);

CREATE TABLE test_case_steps(
 id INT AUTO_INCREMENT PRIMARY KEY,
 testcase_id INT NOT NULL,
 step_no INT NOT NULL,
 action_text TEXT NOT NULL,
 expected_result TEXT NOT NULL,
 FOREIGN KEY(testcase_id) REFERENCES test_cases(id) ON DELETE CASCADE
);

CREATE TABLE defects(
 id INT AUTO_INCREMENT PRIMARY KEY,
 defect_code VARCHAR(20) UNIQUE,
 testcase_id INT,
 title VARCHAR(255),
 description TEXT,
 severity ENUM('Low','Medium','High','Critical'),
 status ENUM('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
 ai_confidence DECIMAL(5,2),
 ai_suggestion TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(testcase_id) REFERENCES test_cases(id)
);

INSERT INTO roles(role_name) VALUES
('Admin'),('QA'),('Tester'),('Manager');

INSERT INTO users(full_name,username,email,password_hash,role_id) VALUES
('System Administrator','admin','admin@plt.local','123456',1),
('QA Lead','qa','qa@plt.local','123456',2),
('Tester 01','tester1','tester1@plt.local','123456',3);

INSERT INTO projects(project_code,project_name,description) VALUES
('PRJ001','AI Test Management','Quản lý Test Case và Defect bằng AI');

INSERT INTO modules(project_id,module_name) VALUES
(1,'Authentication'),
(1,'Checkout'),
(1,'Shopping Cart'),
(1,'Order'),
(1,'Profile');

INSERT INTO priorities(name) VALUES
('Low'),('Medium'),('High'),('Critical');

INSERT INTO test_cases(tc_code,module_id,title,preconditions,priority_id,created_by) VALUES
('TC0001',2,'Xác thực thanh toán qua ví điện tử','Đã đăng nhập và có sản phẩm trong giỏ',3,2),
('TC0002',1,'Đăng nhập bằng tài khoản hợp lệ','Tài khoản tồn tại',2,2),
('TC0003',4,'Tạo đơn hàng thành công','Có địa chỉ giao hàng',3,3);

INSERT INTO test_case_steps(testcase_id,step_no,action_text,expected_result) VALUES
(1,1,'Mở trang Checkout','Trang Checkout hiển thị'),
(1,2,'Chọn Momo','Hiển thị QR Code'),
(1,3,'Thanh toán','Thanh toán thành công'),
(2,1,'Nhập username/password','Cho phép nhập'),
(2,2,'Nhấn Đăng nhập','Đăng nhập thành công'),
(3,1,'Nhấn Đặt hàng','Sinh mã đơn hàng');

INSERT INTO defects(defect_code,testcase_id,title,description,severity,status,ai_confidence,ai_suggestion) VALUES
('BUG-1042',1,'Lỗi timeout khi gọi API Momo','Timeout sau 10s','High','Open',98.5,'Tăng timeout và retry'),
('BUG-1043',1,'Nút thêm vào giỏ lệch trên mobile','CSS Flexbox','Low','In Progress',65.0,'Đổi flex-row thành flex-col'),
('BUG-1044',2,'Đăng nhập không hiển thị thông báo','Thiếu validation','Medium','Resolved',91.2,'Thêm validate phía client');

CREATE INDEX idx_tc_module ON test_cases(module_id);
CREATE INDEX idx_bug_status ON defects(status);

CREATE VIEW vw_dashboard AS
SELECT
 (SELECT COUNT(*) FROM test_cases) total_testcases,
 (SELECT COUNT(*) FROM defects) total_defects,
 (SELECT COUNT(*) FROM defects WHERE status='Open') open_defects;
