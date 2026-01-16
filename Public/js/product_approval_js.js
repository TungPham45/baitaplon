let allProducts = [];
let currentProductId = null;
const ITEMS_PER_PAGE = 8;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();

    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('detailModal').classList.remove('active');
    });

    document.getElementById('closeRejectModal').addEventListener('click', () => {
        document.getElementById('rejectModal').classList.remove('active');
    });

    document.getElementById('cancelReject').addEventListener('click', () => {
        document.getElementById('rejectModal').classList.remove('active');
    });

    document.getElementById('confirmReject').addEventListener('click', rejectProduct);

    document.getElementById('closeLightbox').addEventListener('click', () => {
        document.getElementById('lightbox').classList.remove('active');
    });

    document.getElementById('lightbox').addEventListener('click', (e) => {
        if (e.target === document.getElementById('lightbox')) {
            document.getElementById('lightbox').classList.remove('active');
        }
    });
});

function loadProducts() {
    fetch('/baitaplon/admin/getPendingProducts')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                allProducts = data.data;
                renderPage(1);
            } else {
                alert('Lỗi: ' + data.error);
            }
        })
        .catch(err => alert('Lỗi load sản phẩm: ' + err.message));
}

function renderPage(page) {
    currentPage = page;
    const startIdx = (page - 1) * ITEMS_PER_PAGE;
    const endIdx = startIdx + ITEMS_PER_PAGE;
    const pageProducts = allProducts.slice(startIdx, endIdx);

    displayProducts(pageProducts);
    renderPagination();
}

function displayProducts(products) {
    const productList = document.getElementById('productList');
    const totalCount = document.getElementById('totalCount');

    totalCount.textContent = allProducts.length;

    if (allProducts.length === 0) {
        productList.innerHTML = '<div class="empty-state">Không có sản phẩm nào chờ duyệt</div>';
        return;
    }

    productList.innerHTML = products.map(product => `
        <div class="product-card">
            <img src="/baitaplon/${product.avatar}" alt="${product.ten_sanpham}" class="product-image" onclick="viewDetail(${product.id_sanpham})">
            <div class="product-info">
                <div class="product-name">${product.ten_sanpham}</div>
                <div class="product-price">${formatPrice(product.gia)} VNĐ</div>
                <div class="product-category">${product.ten_danhmuc}</div>
                <div class="product-actions">
                    <button class="btn-small btn-detail" onclick="viewDetail(${product.id_sanpham})">Chi tiết</button>
                    <button class="btn-small btn-approve" onclick="approveProduct(${product.id_sanpham})">Duyệt</button>
                    <button class="btn-small btn-reject" onclick="openRejectModal(${product.id_sanpham}, '${product.ten_sanpham}')">Từ chối</button>
                </div>
            </div>
        </div>
    `).join('');
}

function renderPagination() {
    const totalPages = Math.ceil(allProducts.length / ITEMS_PER_PAGE);
    const pagination = document.getElementById('pagination');

    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    if (currentPage > 1) {
        html += `<button onclick="renderPage(1)">« Đầu</button>`;
        html += `<button onclick="renderPage(${currentPage - 1})">‹ Trước</button>`;
    }

    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        html += `<button onclick="renderPage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
    }

    if (currentPage < totalPages) {
        html += `<button onclick="renderPage(${currentPage + 1})">Sau ›</button>`;
        html += `<button onclick="renderPage(${totalPages})">Cuối »</button>`;
    }

    pagination.innerHTML = html;
}

function viewDetail(id_sanpham) {
    fetch('/baitaplon/admin/getProductDetail&id_sanpham=' + id_sanpham)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayDetail(data);
            } else {
                alert('Lỗi: ' + data.error);
            }
        });
}

function displayDetail(data) {
    const product = data.product;
    const images = data.images || [];
    const attributes = data.attributes || [];

    let html = `
        <div class="detail-header">
            <div class="detail-title">${product.ten_sanpham}</div>
            <div class="detail-price">${formatPrice(product.gia)} VNĐ</div>
        </div>

        <div class="detail-section">
            <div class="section-title">Ảnh sản phẩm:</div>
            <div class="gallery">
    `;

    images.forEach(img => {
        html += `<img src="/baitaplon/${img.url_anh}" alt="Ảnh" onclick="openLightbox('/baitaplon/${img.url_anh}')">`;
    });

    html += `
            </div>
        </div>

        <div class="detail-section">
            <div class="section-title">Danh mục:</div>
            <div class="section-value">${product.ten_danhmuc}</div>
        </div>

        <div class="detail-section">
            <div class="section-title">Mô tả:</div>
            <div class="section-value">${product.mota}</div>
        </div>

        <div class="detail-section">
            <div class="section-title">Người bán:</div>
            <div class="section-value">
                <strong>${product.hoten}</strong><br>
                📱 ${product.sdt || 'Chưa cập nhật'}<br>
                📍 ${product.diachi || 'Chưa cập nhật'}
            </div>
        </div>
    `;

    if (attributes.length > 0) {
        html += `
            <div class="detail-section">
                <div class="section-title">📋 Thuộc tính sản phẩm:</div>
                <div class="attributes-list">
        `;
        attributes.forEach(attr => {
            html += `
                <div class="attribute-item">
                    <span class="attr-name">${attr.ten_thuoctinh}:</span>
                    <span class="attr-value">${attr.giatri || 'Chưa cập nhật'}</span>
                </div>
            `;
        });
        html += `
                </div>
            </div>
        `;
    } else {
        html += `
            <div class="detail-section">
                <div class="section-title">📋 Thuộc tính sản phẩm:</div>
                <div class="section-value" style="color: #999;">Sản phẩm chưa có thuộc tính</div>
            </div>
        `;
    }

    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('detailModal').classList.add('active');
}

function openLightbox(imageSrc) {
    document.getElementById('lightboxImage').src = imageSrc;
    document.getElementById('lightbox').classList.add('active');
}

function approveProduct(id_sanpham) {
    if (!confirm('Duyệt sản phẩm này?')) return;

    const formData = new FormData();
    formData.append('id_sanpham', id_sanpham);

    fetch('/baitaplon/admin/approve', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadProducts();
        } else {
            alert('Lỗi: ' + data.error);
        }
    })
    .catch(err => alert('Lỗi: ' + err.message));
}

function approveAllProducts() {
    if (!confirm('Bạn có chắc muốn duyệt TẤT CẢ sản phẩm chờ duyệt?')) return;

    fetch('/baitaplon/admin/approveAll', {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadProducts();
        } else {
            alert('Lỗi: ' + data.error);
        }
    })
    .catch(err => alert('Lỗi: ' + err.message));
}

function openRejectModal(id_sanpham, product_name) {
    currentProductId = id_sanpham;
    document.getElementById('rejectProductName').textContent = 'Sản phẩm: ' + product_name;
    document.getElementById('rejectModal').classList.add('active');
}

function rejectProduct() {
    if (!currentProductId) return;

    const reason = document.getElementById('rejectReason').value;

    const formData = new FormData();
    formData.append('id_sanpham', currentProductId);
    formData.append('reason', reason);

    fetch('/baitaplon/admin/reject', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('rejectModal').classList.remove('active');
            document.getElementById('rejectReason').value = '';
            loadProducts();
        } else {
            alert('Lỗi: ' + data.error);
        }
    })
    .catch(err => alert('Lỗi: ' + err.message));
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}
