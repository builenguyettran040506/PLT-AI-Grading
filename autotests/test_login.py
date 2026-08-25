from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

driver = webdriver.Chrome()
wait = WebDriverWait(driver, 10)

try:
    print("--- BẮT ĐẦU KIỂM THỬ CHỨC NĂNG ĐĂNG NHẬP ---")

    print("\n[TEST CASE 1] Đăng nhập thành công từ giao diện Landing Page")
    
    # 1. Thủ tục chuẩn hóa bắt buộc
    print("1. Access the website.")
    driver.get("http://localhost/PLT-AI-Grading-main/index.php")
    driver.maximize_window()
    time.sleep(1.5)

    print("2. Click vào nút 'Đăng nhập' trên thanh điều hướng.")
    btn_go_login = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Đăng nhập')]")))
    btn_go_login.click()
    time.sleep(2) # Đợi form login hiển thị hoàn toàn

    print("3. Nhập dữ liệu hợp lệ.")
    # ĐÃ FIX: Đổi từ By.NAME, "email" thành By.NAME, "username" theo đúng HTML của bạn
    username_input = wait.until(EC.visibility_of_element_located((By.NAME, "username")))
    username_input.clear()
    username_input.send_keys("admin") # Bạn có thể đổi thành admin hoặc tên user khác
    
    password_input = wait.until(EC.visibility_of_element_located((By.NAME, "password")))
    password_input.clear()
    password_input.send_keys("123456")
    time.sleep(1)

    print("4. Click nút Đăng nhập bên trong form.")
    driver.find_element(By.XPATH, "//button[@type='submit']").click()

    print("5. Verify Expected Result: Đăng nhập thành công.")
    wait.until(EC.url_contains("page=testcase"))
    if "testcase" in driver.current_url or "dashboard" in driver.current_url:
        print("=> [PASS] Đã vào được hệ thống.")
    else:
        print("=> [FAIL] Lỗi chuyển hướng.")

except Exception as e:
    print(f"\n=> [ERROR] Kịch bản bị gián đoạn: {e}")

finally:
    print("\n--- HOÀN TẤT ---")
    time.sleep(2)
    driver.quit()