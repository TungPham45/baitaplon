
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ người dùng - C2C Market</title>
    <link rel="stylesheet" href="/baitaplon/public/css/user_layout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include_once 'layouts/header.php'; ?>

    <div class="profile-layout-container">
        <div class="profile-content-wrapper">
            
            <aside class="user-sidebar">
                <div class="user-card-main">
                    <img src="/baitaplon/public/uploads/avatars/<?php echo !empty($user['avatar']) ? $user['avatar'] : 'default.png'; ?>" class="profile-avatar-large">
                    <h3><?php echo $user['hoten']; ?></h3>
                    
                    <div class="user-stats">
                        <span class="rating">5.0 ★ (10 đánh giá)</span>
                        <div class="follow-info" style="margin-top:10px;">
                            <span>Người theo dõi: <b>46</b></span> | <span>Đang theo dõi: <b>0</b></span>
                        </div>
                    </div>

                    <?php if ($isMine): ?>
                        <a href="/baitaplon/User/editProfile" class="btn-action edit">
                            Chỉnh sửa trang cá nhân
                        </a>
                    <?php else: ?>
                        <button class="btn-action follow" onclick="alert('Đã theo dõi!')">+ Theo dõi</button>
                    <?php endif; ?>
                </div>
                <?php
                    // Logic tính thời gian đã tham gia
                    $ngayTao = new DateTime($user['ngaytao']);
                    $ngayHienTai = new DateTime();
                    $interval = $ngayTao->diff($ngayHienTai);

                    $timeDisplay = "";
                    if ($interval->y > 0) {
                        $timeDisplay .= $interval->y . " năm ";
                    }
                    if ($interval->m > 0) {
                        $timeDisplay .= $interval->m . " tháng ";
                    }
                    if ($interval->y == 0 && $interval->m == 0) {
                        $timeDisplay = ($interval->d == 0) ? "Vừa mới tham gia" : $interval->d . " ngày";
                    } else {
                        $timeDisplay = trim($timeDisplay);
                    }
                ?>
                <div class="user-info-details">
                    <p><i class="fas fa-comments"></i> Phản hồi chat: <b>90%</b></p>
                    <p><i class="fas fa-calendar-alt"></i> Đã tham gia: <b><?php echo $timeDisplay; ?></b></p>
                    <p><i class="fas fa-map-marker-alt"></i> Địa chỉ: <b><?php echo !empty($user['diachi']) ? $user['diachi'] : 'Chưa cung cấp'; ?></b></p>
                </div>
            </aside>

            <main class="profile-main-content">
                <div class="tabs-header">
                    <div class="tab-item active" onclick="switchTab(event, 'tab-active')">
                        Đang hiển thị (<?php echo count($mockActiveProducts); ?>)
                    </div>
                    <div class="tab-item" onclick="switchTab(event, 'tab-sold')">
                        Đã bán (<?php echo count($mockSoldProducts); ?>)
                    </div>
                </div>

                <div id="tab-active" class="tab-content active" style="display: block;">
                    <div class="product-grid">
                        <?php foreach($mockActiveProducts as $p): ?>
                            <div class="product-card">
                                <div class="p-img">
                                    <img src="/baitaplon/public/images/products/default-product.jpg" 
                                        onerror="this.src='https://via.placeholder.com/300/eee/ccc?text=No+Image'">
                                    <i class="far fa-heart heart-icon"></i>
                                </div>
                                <div class="p-info">
                                    <h4><?php echo $p['title']; ?></h4>
                                    <p class="price"><?php echo $p['price']; ?></p>
                                    <div class="p-meta">
                                        <i class="far fa-user-circle"></i>
                                        <span><?php echo $p['time']; ?> • <?php echo $p['loc']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="tab-sold" class="tab-content" style="display: none;">
                    <div class="product-grid">
                        <?php if (!empty($mockSoldProducts)): ?>
                            <?php foreach($mockSoldProducts as $p): ?>
                                <div class="product-card" style="opacity: 0.8;"> <div class="p-img">
                                        <img src="/baitaplon/public/images/products/default-product.jpg" 
                                            onerror="this.src='https://via.placeholder.com/300/eee/ccc?text=No+Image'">
                                        <div style="position: absolute; top: 0; left: 0; background: rgba(0,0,0,0.5); color: #fff; padding: 2px 8px; font-size: 0.7rem;">ĐÃ BÁN</div>
                                    </div>
                                    <div class="p-info">
                                        <h4><?php echo $p['title']; ?></h4>
                                        <p class="price" style="color: #777;"><?php echo $p['price']; ?></p>
                                        <div class="p-meta">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Đã giao dịch thành công</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: #999; padding: 50px; grid-column: 1 / -1;">
                                Chưa có sản phẩm nào đã bán.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function switchTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) tabcontent[i].style.display = "none";
        
        tablinks = document.getElementsByClassName("tab-item");
        for (i = 0; i < tablinks.length; i++) tablinks[i].className = tablinks[i].className.replace(" active", "");
        
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    </script>
</body>
</html>