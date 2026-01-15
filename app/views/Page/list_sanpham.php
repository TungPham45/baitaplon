<?php
// app/views/Page/list_sanpham.php

$products      = isset($data['products']) ? $data['products'] : [];
$keyword       = isset($data['keyword']) ? $data['keyword'] : '';
$category      = isset($data['category']) ? $data['category'] : '';
$address       = isset($data['address']) ? $data['address'] : '';
$status        = isset($data['status']) ? $data['status'] : '';
$page          = isset($data['pageNum']) ? $data['pageNum'] : 1;
$totalPages    = isset($data['totalPages']) ? $data['totalPages'] : 1;
$totalProducts = isset($data['totalProducts']) ? $data['totalProducts'] : 0;

if (!function_exists('buildHomeUrl')) {
    function buildHomeUrl($page, $keyword, $category, $address, $status = '') {
        $params = [];
        if (isset($_GET['url'])) $params['url'] = $_GET['url'];
        if ($keyword !== '') $params['q'] = $keyword;
        if ($category !== '') $params['danhmuc'] = $category;
        if ($address !== '') $params['diachi'] = $address;
        if ($status !== '') $params['trangthai'] = $status;
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        <?php if ($keyword !== '' || $category !== '' || $address !== ''): ?>
            Kết quả tìm kiếm: <small class="text-muted"><?php echo number_format($totalProducts); ?> sản phẩm</small>
        <?php else: ?>
            Tất cả tin đăng <small class="text-muted">(<?php echo number_format($totalProducts); ?> sản phẩm)</small>
        <?php endif; ?>
    </h5>
    <?php if ($keyword !== '' || $category !== '' || $address !== '' || $status !== ''): ?>
        <a href="./" class="btn btn-sm btn-link">Xóa lọc</a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info">Không tìm thấy sản phẩm nào phù hợp.</div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="col-6 col-md-4 col-lg-3">
    <div class="card h-100 product-card">
        
        <?php
        $img = isset($p['anh_hienthi']) && $p['anh_hienthi']
            ? $p['anh_hienthi']
            : (isset($p['anh_dai_dien']) && $p['anh_dai_dien']
                ? $p['anh_dai_dien']
                : 'https://via.placeholder.com/300x200?text=No+Image');
        ?>
        <img src="/baitaplon/<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="Ảnh sản phẩm" style="width:100%;height:180px;object-fit:cover;">

        <div class="card-body d-flex flex-column">
            
            <?php
            $isAdmin = false;
            if (isset($p['role'])) {
                $role = trim(strtolower($p['role']));
                $isAdmin = in_array($role, ['quản lý', 'admin', 'administrator', 'quanly', 'ad']);
            }
            ?>

            <?php if ($isAdmin): ?>
                <div class="mb-1">
                    <span class="badge bg-warning text-dark border border-light shadow-sm" style="font-size: 0.7rem;">
                        <i class="bi bi-patch-check-fill text-primary"></i> Được bán bởi Admin
                    </span>
                </div>
            <?php endif; ?>
            <div class="mb-1">
                <?php if (!empty($p['ten_danhmuc'])): ?>
                    <span class="badge badge-category">
                        <?php echo htmlspecialchars($p['ten_danhmuc']); ?>
                    </span>
                <?php endif; ?>
            </div>

            <h6 class="card-title" title="<?php echo htmlspecialchars($p['ten_sanpham']); ?>">
                <?php
                $name = $p['ten_sanpham'];
                if (mb_strlen($name, 'UTF-8') > 40) {
                    $name = mb_substr($name, 0, 40, 'UTF-8') . '...';
                }
                echo htmlspecialchars($name);
                ?>
            </h6>

            <p class="price mb-1" style="color:#d0021b;font-weight:600;font-size:1.1rem;">
                <?php echo number_format($p['gia']); ?> đ
            </p>

            <p class="text-muted small mb-1">
                <i class="bi bi-geo-alt-fill"></i> 
                <?php echo htmlspecialchars(isset($p['khu_vuc_ban']) && $p['khu_vuc_ban'] ? $p['khu_vuc_ban'] : 'Toàn quốc'); ?>
            </p>

            <p class="text-muted small mb-2">
                <i class="bi bi-clock"></i> 
                <?php echo htmlspecialchars($p['ngaydang']); ?>
            </p>

            <?php
                $currentUserId = isset($data['user_id']) ? $data['user_id'] : '';
                $detailLink = "/baitaplon/Home/detail_Sanpham/" . $p['id_sanpham'];
                if (!empty($currentUserId)) {
                    $detailLink .= "/" . $currentUserId;
                }
            ?>

            <a href="<?php echo htmlspecialchars($detailLink); ?>" class="btn btn-sm btn-outline-primary mt-auto">
                Xem chi tiết
            </a>
        </div>
    </div>
</div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo ($page > 1) ? buildHomeUrl($page - 1, $keyword, $category, $address, $status) : '#'; ?>">
                    &laquo; Trước
                </a>
            </li>
            <?php
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo buildHomeUrl($i, $keyword, $category, $address, $status); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo ($page < $totalPages) ? buildHomeUrl($page + 1, $keyword, $category, $address, $status) : '#'; ?>">
                    Sau &raquo;
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>