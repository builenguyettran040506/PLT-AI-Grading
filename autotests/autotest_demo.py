from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

# Khởi tạo WebDriver cho trình duyệt bản mới nhất
# (Nếu dùng Chrome, thay Edge() bằng Chrome())
driver = webdriver.Edge() 
wait = WebDriverWait(driver, 10)

try:
    print("Bắt đầu chạy kịch bản kiểm thử tự động...")
    
    # ---------------------------------------------------
    # Bước 1: BẮT BUỘC - Khởi tạo truy cập
    # ---------------------------------------------------
    print("1. Access the website.")
    driver.get("http://localhost/PLT-AI-Grading-main/index.php?page=login")
    driver.maximize_window()
    time.sleep(2) # Dừng 2s để demo hiển thị rõ ràng

    # ---------------------------------------------------
    # Bước 2: Form đăng nhập xuất hiện & Nhập thông tin
    # ---------------------------------------------------
    print("2. form đăng nhập xuất hiện & Nhập thông tin hợp lệ.")
    
    # Tìm ô Email và điền dữ liệu
    email_input = wait.until(EC.presence_of_element_located((By.NAME, "email")))
    email_input.send_keys("admin")
    time.sleep(1)
    
    # Tìm ô Mật khẩu và điền dữ liệu
    password_input = driver.find_element(By.NAME, "password")
    password_input.send_keys("123456")
    time.sleep(1)

    # Click nút Đăng nhập
    login_button = driver.find_element(By.XPATH, "//button[@type='submit']")
    login_button.click()

    # ---------------------------------------------------
    # Bước 3: Quan sát trang web (Kiểm tra kết quả)
    # ---------------------------------------------------
    print("3. Quan sát trang web (Verify Expected Result).")
    
    # Chờ xem hệ thống có chuyển hướng vào trang Dashboard/Testcase không
    wait.until(EC.url_contains("page=testcase"))
    
    current_url = driver.current_url
    if "testcase" in current_url or "dashboard" in current_url:
        print("=> [PASS] Đăng nhập thành công! Đã chuyển hướng vào hệ thống.")
    else:
        print("=> [FAIL] Lỗi chuyển hướng.")
        
    time.sleep(3) # Dừng lại 3s để người xem demo kịp nhìn thấy thành quả

except Exception as e:
    print(f"=> [ERROR] Kịch bản bị gián đoạn: {e}")

finally:
    # Đóng trình duyệt sau khi test xong
    driver.quit()
    print("Đã hoàn tất phiên kiểm thử tự động.")