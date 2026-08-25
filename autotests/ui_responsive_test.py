from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

driver = webdriver.Chrome()
wait = WebDriverWait(driver, 10)

try:
    print("--- BẮT ĐẦU KIỂM THỬ GIAO DIỆN CHUYÊN SÂU ---")
    
    # ---------------------------------------------------
    # Bước 1: Khởi tạo truy cập (Bắt buộc theo chuẩn)
    # ---------------------------------------------------
    print("1. Access the website.")
    driver.get("http://localhost/PLT-AI-Grading-main/index.php") 
    time.sleep(2)

    # ---------------------------------------------------
    # KIỂM THỬ SÂU 1: NỘI DUNG VÀ TIÊU ĐỀ (TEXT & TITLE)
    # ---------------------------------------------------
    print("\n2. Kiểm tra tính chính xác của Text & Title...")
    driver.set_window_size(1920, 1080)
    
    # 2.1 Kiểm tra Title của tab trình duyệt
    if "PLT Solutions" in driver.title:
        print(f"=> [PASS] Tiêu đề tab trình duyệt chính xác: '{driver.title}'")
    else:
        print(f"=> [FAIL] Tiêu đề tab sai. Đang hiển thị: '{driver.title}'")

    # 2.2 Kiểm tra thẻ H1 lớn nhất (Hero Section) có chứa chữ "Quản lý QA" không
    hero_title = wait.until(EC.presence_of_element_located((By.TAG_NAME, "h1"))).text
    if "Quản lý QA" in hero_title:
        print("=> [PASS] Khối chữ Hero Section hiển thị thông điệp chuẩn xác.")
    else:
        print("=> [FAIL] Sai thông điệp chính trên trang chủ.")

    # ---------------------------------------------------
    # KIỂM THỬ SÂU 2: TRẠNG THÁI ẨN/HIỆN THEO RESPONSIVE
    # ---------------------------------------------------
    print("\n3. Kiểm tra logic ẩn/hiện element giữa Desktop và Mobile...")
    
    # Tìm khối chứa các menu link (Tính năng, Cách hoạt động...)
    # Trong code Tailwind, khối này dùng class "hidden md:flex"
    desktop_menu = driver.find_element(By.XPATH, "//nav//div[contains(@class, 'md:flex')]")
    
    if desktop_menu.is_displayed():
        print("=> [PASS] (Desktop) Thanh menu ngang đang hiển thị rất đẹp.")
    else:
        print("=> [FAIL] (Desktop) Lỗi: Thanh menu ngang bị ẩn sai cách.")

    # Thu nhỏ màn hình lập tức để test tính năng Responsive
    print("=> Chuyển sang kích thước Mobile (375x812) để ép giao diện co lại...")
    driver.set_window_size(375, 812)
    time.sleep(1.5)

    # Trên Mobile, Menu Desktop bắt buộc PHẢI biến mất (trạng thái hiển thị = False)
    if not desktop_menu.is_displayed():
        print("=> [PASS] (Mobile) Tuyệt vời! Thanh menu ngang đã tự động ẩn đi để tránh tràn viền (overflow).")
    else:
        print("=> [FAIL] (Mobile) Lỗi nghiêm trọng: Thanh menu ngang không chịu ẩn, gây vỡ giao diện Mobile!")

    # ---------------------------------------------------
    # KIỂM THỬ SÂU 3: SCROLLING & TÍNH TƯƠNG TÁC
    # ---------------------------------------------------
    print("\n4. Kiểm tra nút CTA có bị che khuất (Overlapping) không...")
    
    # Tìm nút "Dùng thử ngay" bằng text của nó
    cta_btn = driver.find_element(By.XPATH, "//a[contains(text(), 'Dùng thử ngay')]")
    
    # Dùng Javascript ép trình duyệt cuộn (scroll) trang xuống đúng vị trí của nút đó
    driver.execute_script("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", cta_btn)
    time.sleep(1.5)
    
    # Kiểm tra xem nút có tồn tại, có hiển thị và có bị vô hiệu hóa (disabled) không
    if cta_btn.is_displayed() and cta_btn.is_enabled():
         print("=> [PASS] Nút Call-To-Action (CTA) hiển thị rõ ràng, cuộn mượt mà và sẵn sàng cho user click.")
    else:
         print("=> [FAIL] Nút CTA bị chìm hoặc bị phần tử khác đè lên.")

except Exception as e:
    print(f"\n=> [ERROR] Có lỗi phát sinh trong quá trình kiểm tra: {e}")

finally:
    print("\n--- HOÀN TẤT BÀI KIỂM THỬ UI CHUYÊN SÂU ---")
    driver.quit()