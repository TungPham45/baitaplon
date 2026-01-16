# 📌 GIẢI THÍCH CHI TIẾT THANH NAVBAR (home.php)

---

## 🔹 **1. CONTAINER NAVBAR CHÍNH**
```php
<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
    <div class="container">
```

### Chức năng:
- `<nav class="navbar">` → Định nghĩa thanh điều hướng
- `navbar-expand-lg` → Thanh sẽ collapse thành menu mobile ở màn hình nhỏ hơn 992px
- `sticky-top` → **QUAN TRỌNG**: Thanh navbar sẽ "dính" (fixed) ở đầu trang khi scroll
- `shadow-sm` → Thêm shadow nhẹ để nổi bật
- `<div class="container">` → Giới hạn chiều rộng nội dung (max 1200px)

---

## 🔹 **2. LOGO VÀ LINK VỀ TRANG CHỦ**

### **2.1. Lấy thông tin User từ Controller:**
```php
<?php
$currentUserId = isset($data['user_id']) ? $data['user_id'] : '';
$homeLink = "/baitaplon/Home" . (!empty($currentUserId) ? "/index/" . urlencode($currentUserId) : "");
?>
```

**Giải thích:**
- `$currentUserId` → Lưu ID người dùng hiện tại từ mảng `$data`
- `$homeLink` → Xây dựng đường dẫn động:
  - **Nếu user đã login**: `/baitaplon/Home/index/{userID}` → Trang chủ riêng của user
  - **Nếu chưa login**: `/baitaplon/Home` → Trang chủ chung

### **2.2. Hiển thị Logo:**
```php
<a class="navbar-brand me-4" href="<?php echo $homeLink; ?>">
    <i class="bi bi-shop"></i> DealNow
</a>
```

**Giải thích:**
- `navbar-brand` → Class Bootstrap cho logo
- `me-4` → Margin phải 24px
- `<i class="bi bi-shop"></i>` → Icon cửa hàng từ Bootstrap Icons
- `DealNow` → Tên ứng dụng
- Link trỏ đến home page với user_id (nếu có)

---

## 🔹 **3. FORM TÌM KIẾM (SEARCH FORM)**

### **3.1. Container Search:**
```php
<div class="mx-auto flex-grow-1 px-3 d-flex justify-content-center">
    <form class="search-container" method="GET" action="/baitaplon/Home/index" id="searchForm">
```

**Giải thích:**
- `mx-auto` → Margin horizontal auto (căn giữa)
- `flex-grow-1` → Form chiếm hết space có sẵn
- `px-3` → Padding horizontal 12px
- `d-flex justify-content-center` → Flexbox + căn giữa nội dung
- `method="GET"` → Gửi dữ liệu qua URL (search parameters)
- `action="/baitaplon/Home/index"` → Gửi request đến controller Home

### **3.2. Lưu User ID (Nếu đã login):**
```php
<?php if(!empty($currentUserId)): ?>
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($currentUserId); ?>">
<?php endif; ?>
```

**Giải thích:**
- `type="hidden"` → Input ẩn, không hiển thị trên giao diện
- Khi submit form, user_id sẽ được gửi kèm trong URL
- `htmlspecialchars()` → Chống XSS (chuyển `<` thành `&lt;`, v.v.)

### **3.3. Lấy các giá trị từ Controller:**
```php
<?php
$keyword  = isset($data['keyword']) ? $data['keyword'] : '';
$category = isset($data['category']) ? $data['category'] : '';
$categoryTree = isset($data['categoryTree']) ? $data['categoryTree'] : [];
$currentCatName = isset($data['currentCatName']) ? $data['currentCatName'] : 'Danh mục';
$address  = isset($data['address']) ? $data['address'] : '';
?>
```

**Giải thích:**
- Kiểm tra dữ liệu có tồn tại trong `$data` không
- Nếu có → Lấy giá trị; Nếu không → Gán chuỗi rỗng
- Dùng để hiển thị lại dữ liệu đã search trong form (persistent search)

---

## 🔹 **4. DROPDOWN DANH MỤC SẢN PHẨM**

### **4.1. Input ẩn lưu category:**
```php
<input type="hidden" name="danhmuc" id="inputDanhmuc" value="<?php echo htmlspecialchars($category); ?>">
```

**Giải thích:**
- Lưu ID danh mục đã chọn
- ID `inputDanhmuc` → Dùng cho JavaScript để update giá trị
- Khi submit form → Danh mục sẽ được gửi kèm

### **4.2. Button Dropdown:**
```php
<div class="dropdown">
    <button class="btn btn-sm fw-bold text-secondary border-0 dropdown-toggle text-truncate" 
            type="button" data-bs-toggle="dropdown" style="max-width: 150px;">
        <i class="bi bi-list"></i> <span id="catDisplay">
            <?php echo htmlspecialchars($currentCatName); ?>
        </span>
    </button>
```

**Giải thích:**
- `data-bs-toggle="dropdown"` → Bootstrap sự kiện mở dropdown menu
- `fw-bold` → Font weight bold
- `text-secondary` → Màu chữ xám
- `border-0` → Xóa border button
- `text-truncate` → Chữ dài sẽ bị cắt (...)
- `max-width: 150px` → Giới hạn độ rộng button
- `id="catDisplay"` → **QUAN TRỌNG**: ID dùng cho JavaScript để update tên danh mục
- `<i class="bi bi-list"></i>` → Icon danh sách

### **4.3. Menu Dropdown:**
```php
<ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#" onclick="selectCategory('', 'Tất cả danh mục'); return false;">
        Tất cả danh mục
    </a></li>
    <li><hr class="dropdown-divider"></li>
```

**Giải thích:**
- `dropdown-menu` → Bootstrap style cho dropdown menu
- "Tất cả danh mục" → Option đầu tiên để xem tất cả sản phẩm
- `onclick="selectCategory('', 'Tất cả danh mục'); return false;"` → Khi click:
  - Gọi function `selectCategory()`
  - Truyền ID rỗng (không filter by category)
  - Truyền tên hiển thị là "Tất cả danh mục"
  - `return false` → Ngăn link default action

### **4.4. Danh mục cha-con:**
```php
<?php if (!empty($categoryTree)): foreach ($categoryTree as $parent): ?>
    <?php if (!empty($parent['children'])): ?>
        <li class="dropdown-item-parent">
            <a class="dropdown-item d-flex justify-content-between align-items-center" 
               href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', 
               '<?php echo $parent['ten_danhmuc']; ?>'); return false;">
                <?php echo htmlspecialchars($parent['ten_danhmuc']); ?> 
                <i class="bi bi-chevron-right small"></i>
            </a>
            <ul class="submenu shadow">
                <?php foreach ($parent['children'] as $child): ?>
                    <li><a class="dropdown-item" href="#" 
                           onclick="selectCategory('<?php echo $child['id_danhmuc']; ?>', 
                           '<?php echo $child['ten_danhmuc']; ?>'); return false;">
                        <?php echo htmlspecialchars($child['ten_danhmuc']); ?>
                    </a></li>
                <?php endforeach; ?>
            </ul>
        </li>
```

**Giải thích:**
- `$categoryTree` → Mảng chứa danh mục cha-con từ controller
- **Vòng lặp cha**: `foreach ($categoryTree as $parent)`
  - Lặp qua từng danh mục cha
- **Kiểm tra con**: `if (!empty($parent['children']))`
  - Nếu danh mục cha có danh mục con → Hiển thị submenu
  - Nếu không có con → Hiển thị như item thường
- **Submenu**: 
  - `<ul class="submenu shadow">` → Dropdown phụ ẩn ban đầu (CSS: `display: none`)
  - Khi hover vào danh mục cha → Hiển thị submenu
  - **Vòng lặp con**: `foreach ($parent['children'] as $child)` → Lặp các danh mục con

---

## 🔹 **5. ĐƯỜNG KẺ NGĂN CÁCH (DIVIDER)**
```php
<div class="vr mx-2"></div>
```

**Giải thích:**
- `vr` → Vertical Rule (đường kẻ dọc)
- `mx-2` → Margin left/right 8px
- Tạo khoảng trắng và phân chia giữa các phần của form

---

## 🔹 **6. INPUT TÌM KIẾM (KEYWORD)**
```php
<input class="search-input" type="text" name="q" placeholder="Tìm sản phẩm..." 
       value="<?php echo htmlspecialchars($keyword); ?>" style="flex: 1;">
```

**Giải thích:**
- `name="q"` → Tên parameter trong URL (sẽ gửi là `?q=...`)
- `type="text"` → Input text bình thường
- `placeholder="Tìm sản phẩm..."` → Văn bản mặc định khi input rỗng
- `value="<?php echo htmlspecialchars($keyword); ?>"` → Hiển thị từ khóa đã search trước đó
- `style="flex: 1;"` → Chiếm toàn bộ space có sẵn trong flex container

---

## 🔹 **7. INPUT ĐỊA CHỈ (ADDRESS)**
```php
<div style="position: relative; width: 180px;">
    <input class="search-input" type="text" id="nav-address-input" name="diachi" 
           placeholder="Toàn quốc" autocomplete="off" 
           value="<?php echo htmlspecialchars($address); ?>">
    <div id="nav-address-list" class="address-suggestions"></div>
</div>
```

**Giải thích:**
- `position: relative; width: 180px;` → Container với chiều rộng cố định
- `id="nav-address-input"` → ID dùng cho JavaScript để theo dõi input
- `name="diachi"` → Tên parameter (`?diachi=...`)
- `autocomplete="off"` → Tắt autocomplete trình duyệt (để dùng custom suggestions)
- `id="nav-address-list"` → Container cho danh sách gợi ý địa chỉ
- `class="address-suggestions"` → CSS hide by default, show khi có gợi ý

---

## 🔹 **8. NÚT TÌM KIẾM**
```php
<button class="btn btn-warning rounded-circle ms-2" type="submit" 
        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-search text-white"></i>
</button>
```

**Giải thích:**
- `btn btn-warning` → Button màu vàng Bootstrap
- `rounded-circle` → Làm cho button tròn (border-radius 50%)
- `ms-2` → Margin trái 8px
- `width/height: 35px` → Kích thước 35x35 pixel
- `display: flex; align-items: center; justify-content: center;` → Căn icon vào giữa
- `type="submit"` → Button này sẽ submit form
- `<i class="bi bi-search text-white"></i>` → Icon kính lúp trắng

---

## 🔹 **9. PHẦN BÊN PHẢI (RIGHT SIDE)**

### **9.1. Nút Đăng tin (Chỉ hiển thị khi login):**
```php
<div class="d-flex align-items-center gap-3">
    <?php if (isset($data['isLoggedIn']) && $data['isLoggedIn']): ?>
        <button class="btn btn-warning fw-bold text-dark btn-sm" 
                data-bs-toggle="modal" data-bs-target="#postModal">
            <i class="bi bi-plus-lg"></i> Đăng tin
        </button>
```

**Giải thích:**
- `d-flex align-items-center gap-3` → Flexbox với căn giữa và khoảng cách 16px
- `isset($data['isLoggedIn'])` → Kiểm tra user đã login chưa
- `data-bs-toggle="modal" data-bs-target="#postModal"` → Click sẽ mở modal với ID `postModal`
- `btn-sm` → Button nhỏ
- `fw-bold` → Bold text
- `text-dark` → Chữ tối màu

### **9.2. Icon Chat:**
```php
<a href="/baitaplon/Chat/index/0/<?php echo $currentUserId; ?>" class="text-secondary fs-5">
    <i class="bi bi-chat-dots-fill"></i>
</a>
```

**Giải thích:**
- Link tới trang Chat của user hiện tại
- `/baitaplon/Chat/index/0/` → Route để xem tất cả conversations
- `<?php echo $currentUserId; ?>` → Truyền user ID vào URL
- `text-secondary` → Màu xám
- `fs-5` → Font size 1rem
- `bi bi-chat-dots-fill` → Icon chat tròn đầy

### **9.3. Dropdown User Menu:**
```php
<div class="dropdown">
    <a href="#" class="text-secondary fs-5" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle"></i>
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end shadow">
```

**Giải thích:**
- `data-bs-toggle="dropdown"` → Click icon mở dropdown menu
- `dropdown-menu-end` → Menu sẽ kéo ra bên phải (align right)
- `shadow` → Thêm shadow cho menu

### **9.4. Menu Item - Admin Dashboard (Chỉ cho Admin):**
```php
<?php 
$role = isset($_SESSION['role']) ? trim($_SESSION['role']) : '';
if ($role === 'Quản lý'): 
?>
    <li>
        <a class="dropdown-item fw-bold text-primary" href="/baitaplon/Admin/dashboard">
            <i class="bi bi-speedometer2"></i> Quản lý Web
        </a>
    </li>
    <li><hr class="dropdown-divider"></li>
<?php endif; ?>
```

**Giải thích:**
- Lấy role từ Session
- `trim()` → Xóa khoảng trắng thừa
- Kiểm tra nếu role = "Quản lý" (admin) → Hiển thị option "Quản lý Web"
- Link tới dashboard admin
- `text-primary` → Màu xanh để nổi bật

### **9.5. Menu Item - Trang Cá Nhân:**
```php
<li><a class="dropdown-item" href="/baitaplon/User/Profile/<?php echo urlencode($currentUserId); ?>">
    Trang cá nhân
</a></li>
```

**Giải thích:**
- Link tới trang profile của user
- `urlencode()` → Chuyển đổi user ID để an toàn trong URL

### **9.6. Menu Item - Đổi Mật Khẩu:**
```php
<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
    Đổi mật khẩu
</a></li>
```

**Giải thích:**
- Click mở modal "changePasswordModal"
- Modal này có form đổi mật khẩu (xuất hiện dưới trong file)

### **9.7. Menu Item - Đăng Xuất:**
```php
<li><a class="dropdown-item text-danger" href="/baitaplon/Home?logout=1">
    Đăng xuất
</a></li>
```

**Giải thích:**
- Link đăng xuất với parameter `?logout=1`
- Controller sẽ nhận parameter này → Xóa session → Chuyển về trang chủ
- `text-danger` → Chữ màu đỏ để cảnh báo

---

## 🔹 **10. JAVASCRIPT - HÀM SELECT CATEGORY**

Ở cuối file, có hàm JavaScript này:
```javascript
<script>
    function selectCategory(id, name) {
        document.getElementById('inputDanhmuc').value = id;
        document.getElementById('catDisplay').innerText = name;
        document.getElementById('searchForm').submit();
    }
</script>
```

**Giải thích:**
- **Hàm này được gọi khi click vào danh mục**
- `document.getElementById('inputDanhmuc').value = id;` → Set category ID vào input ẩn
- `document.getElementById('catDisplay').innerText = name;` → Cập nhật hiển thị tên danh mục trên button
- `document.getElementById('searchForm').submit();` → Submit form để search

**Luồng hoạt động:**
```
User click vào danh mục
    ↓
Gọi selectCategory(categoryId, categoryName)
    ↓
Set giá trị vào input ẩn + hiển thị tên danh mục
    ↓
Submit form GET request → /baitaplon/Home/index?danhmuc={categoryId}&...
    ↓
Controller nhận parameter → Filter products → Trả về trang kết quả
```

---

## 📊 **TÓNG KẾT LUỒNG HOẠT ĐỘNG NAVBAR**

```
┌─────────────────────────────────────┐
│     USER INTERACTION FLOW            │
└─────────────────────────────────────┘

1. USER CLICK CATEGORY
   ↓
   selectCategory(id, name) được gọi
   ↓
   Update input hidden + display text
   ↓
   searchForm.submit()
   ↓
   GET request: /baitaplon/Home/index?danhmuc={id}&q={keyword}&diachi={address}&user_id={userId}
   ↓
   Controller lọc products
   ↓
   Trả về danh sách sản phẩm

2. USER TYPE KEYWORD & SUBMIT
   ↓
   Form submit → GET request dengan q={keyword}
   ↓
   Controller search by keyword
   ↓
   Trả về kết quả

3. USER SELECT ADDRESS
   ↓
   JavaScript lắng nghe input → Gợi ý địa chỉ
   ↓
   User chọn → Form submit với diachi={address}
   ↓
   Controller filter by location

4. USER CLICK SEARCH BUTTON
   ↓
   Gửi ALL parameters: danhmuc, q, diachi, user_id
   ↓
   Controller filter with multiple conditions
   ↓
   Trả về kết quả hợp nhất
```

---

## 🎯 **KEY POINTS**

✅ **Navbar sticky-top** → Luôn hiển thị khi scroll  
✅ **Dynamic home link** → Link khác nhau tùy user login hay không  
✅ **Hidden inputs** → Lưu giá trị category & user_id  
✅ **Dropdown categories** → Menu cha-con 2 cấp  
✅ **Address suggestions** → Gợi ý địa chỉ từ JavaScript  
✅ **Persistent values** → Giữ lại từ khóa/danh mục đã search  
✅ **Role-based menu** → Menu khác nhau cho admin vs user thường  
✅ **XSS protection** → Dùng htmlspecialchars() & urlencode()

---

**Hy vọng giải thích này rõ ràng! Có phần nào cần hỏi thêm không?** 🚀
