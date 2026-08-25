from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import os

driver = webdriver.Chrome()
wait = WebDriverWait(driver, 10)

# Chuẩn bị đường dẫn file test
current_dir = os.getcwd()
file_path = os.path.join(current_dir, "test_data.csv")

try:
    print("--- BẮT ĐẦU KIỂM THỬ E2E: ĐĂNG NHẬP VÀ UPLOAD FILE ---")
    
    # Tự động sinh file dữ liệu test (CSV)
    if not os.path.exists(file_path):
        with open(file_path, "w", encoding="utf-8") as f:
            f.write("TenLoi,TrangThai\nLoi_Giao_Dien_01,Open\nLoi_Logic_02,In Progress")
    
    # ---------------------------------------------------
    # GIAI ĐOẠN 1: THỰC HIỆN ĐĂNG NHẬP
    # ---------------------------------------------------
    print("\n[PHẦN 1] Đăng nhập vào hệ thống")
    
    # Thủ tục chuẩn hóa bắt buộc
    print("1. Access the website.")
    driver.get("http://localhost/PLT-AI-Grading-main/index.php")
    driver.maximize_window()
    time.sleep(1.5)

    print("2. Mở form đăng nhập và điền thông tin.")
    wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Đăng nhập')]"))).click()
    time.sleep(2) # Chờ form load hoàn toàn

    # Nhập Username (Sử dụng đúng name="username" của hệ thống)
    username_input = wait.until(EC.visibility_of_element_located((By.NAME, "username")))
    username_input.clear()
    username_input.send_keys("admin") 
    
    # Nhập Password
    password_input = wait.until(EC.visibility_of_element_located((By.NAME, "password")))
    password_input.clear()
    password_input.send_keys("123456") 
    time.sleep(1)

    print("3. Submit form đăng nhập.")
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    
    # Xác nhận đăng nhập thành công trước khi đi tiếp
    wait.until(EC.url_contains("page="))
    print("=> [PASS] Đã đăng nhập thành công!")
    time.sleep(1.5)

    # ---------------------------------------------------
    # GIAI ĐOẠN 2: THỰC HIỆN UPLOAD FILE
    # ---------------------------------------------------
    print("\n[PHẦN 2] Chuyển đến trang Quản lý lỗi và nạp file")
    
    print("4. Điều hướng tới trang defect.")
    driver.get("http://localhost/PLT-AI-Grading-main/index.php?page=testcase")
    time.sleep(2)

    print("5. Truyền file dữ liệu vào hệ thống (Xuyên qua lớp UI ẩn).")
    # Định vị thẻ input ẩn và truyền thẳng file vào
    file_input = wait.until(EC.presence_of_element_located((By.ID, "excelFileInput")))
    file_input.send_keys(file_path)
    
    print("6. Chờ thông báo xử lý thành công từ server.")
    # Chờ Toast message xuất hiện (do onchange tự submit form)
    toast_message = wait.until(EC.visibility_of_element_located((By.ID, "php-toast")))
    
    if "thành công" in toast_message.text.lower():
        print(f"=> [PASS] Upload hoàn tất! Trình duyệt báo: '{toast_message.text}'")
    else:
        print(f"=> [FAIL] Hệ thống báo lỗi: '{toast_message.text}'")
        
    time.sleep(4) 

except Exception as e:
    print(f"\n=> [ERROR] Kịch bản bị gián đoạn: {e}")

finally:
    print("\n--- HOÀN TẤT KIỂM THỬ ---")
    driver.quit()
    
    # Dọn dẹp: Xóa file test sau khi chạy xong
    if os.path.exists(file_path):
        os.remove(file_path)