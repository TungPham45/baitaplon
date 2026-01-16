<div class="admin-page-header">
    <h2 style="margin: 0;">🛍️ Quản lý Sản phẩm</h2>
    <div class="header-actions">
        <button onclick="approveAllProducts()" style="padding: 10px 15px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px; transition: background 0.2s;" onmouseover="this.style.background='#229954'" onmouseout="this.style.background='#27ae60'">✓ Duyệt tất cả</button>
    </div>
</div>

<link rel="stylesheet" href="/baitaplon/public/css/product_approval_css.css">

<div class="admin-stats">
    <div class="stat-item">
        <span>Sản phẩm chờ duyệt:</span>
        <span class="stat-number" id="totalCount">0</span>
    </div>
</div>

<div class="product-grid" id="productList">
    <!-- Sản phẩm sẽ được load qua JS -->
</div>

<div class="pagination" id="pagination">
    <!-- Pagination sẽ được tạo qua JS -->
</div>

<!-- Modal chi tiết sản phẩm -->
<div class="modal" id="detailModal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <div id="modalBody"></div>
    </div>
</div>

<!-- Modal từ chối -->
<div class="modal" id="rejectModal">
    <div class="modal-content">
        <button class="modal-close" id="closeRejectModal">&times;</button>
        <h3>Từ chối sản phẩm</h3>
        <p id="rejectProductName" style="margin: 15px 0; color: #666;"></p>
        <p style="color: #666; margin-bottom: 20px;">Bạn có chắc muốn từ chối sản phẩm này?</p>
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <button class="btn-small btn-reject" id="confirmReject">Xác nhận từ chối</button>
            <button class="btn-small" style="background: #95a5a6; color: white;" id="cancelReject">Hủy</button>
        </div>
    </div>
</div>

<!-- Lightbox ảnh -->
<div class="lightbox" id="lightbox">
    <div class="lightbox-content">
        <span class="lightbox-close" id="closeLightbox">&times;</span>
        <img id="lightboxImage" class="lightbox-image" src="" alt="">
    </div>
</div>

<script src="/baitaplon/public/js/product_approval_js.js"></script>
