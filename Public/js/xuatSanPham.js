/**
 * public/js/xuatSanPham.js
 * Xử lý xuất Excel cho trang Profile theo bộ lọc hiện tại
 */

document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const btnExport = document.getElementById('btnExportExcel');
    const productItems = document.querySelectorAll('.product-item-wrapper');

    // CHỨC NĂNG XUẤT EXCEL (CSV)
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            // Lấy bộ lọc hiện tại (từ select hoặc URL)
            let currentFilter = 'all';
            if (statusFilter) {
                currentFilter = statusFilter.value;
            } else {
                // Fallback: lấy từ URL parameter
                const params = new URLSearchParams(window.location.search);
                currentFilter = params.get('trang_thai') || 'all';
            }

            // Thêm BOM (\uFEFF) để Excel mở không bị lỗi font tiếng Việt
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; 
            
            // Header cột
            csvContent += "STT,Tên sản phẩm,Giá bán (VNĐ),Ngày đăng,Trạng thái\n";

            let count = 0;

            productItems.forEach(item => {
                const itemStatus = item.getAttribute('data-status') || '';
                
                // Chỉ export những item theo bộ lọc hiện tại
                // Mapping: trangthai DB -> jsStatus trong data-status
                let shouldInclude = false;
                
                if (currentFilter === 'all') {
                    shouldInclude = true;
                } else if (currentFilter === 'Đã duyệt' && itemStatus === 'hienthi') {
                    shouldInclude = true;
                } else if (currentFilter === 'Đã bán' && itemStatus === 'daban') {
                    shouldInclude = true;
                } else if (currentFilter === 'Chờ duyệt' && itemStatus === 'choduyet') {
                    shouldInclude = true;
                } else if (currentFilter === 'Từ chối' && itemStatus === 'tuchoi') {
                    shouldInclude = true;
                } else if (currentFilter === 'Dừng bán' && itemStatus === 'dungban') {
                    shouldInclude = true;
                }

                if (shouldInclude) {
                    count++;
                    
                    // Lấy dữ liệu từ data attributes
                    const name = (item.getAttribute('data-name') || "").replace(/,/g, " "); 
                    const price = item.getAttribute('data-price') || "0";
                    const date = item.getAttribute('data-date') || "";
                    
                    // Chuyển mã trạng thái sang tiếng Việt
                    let statusText = "Đang bán";
                    if (itemStatus === 'daban') statusText = "Đã bán";
                    if (itemStatus === 'choduyet') statusText = "Chờ duyệt";
                    if (itemStatus === 'tuchoi') statusText = "Từ chối";
                    if (itemStatus === 'dungban') statusText = "Dừng bán";

                    // Tạo dòng CSV
                    const row = `${count},${name},${price},${date},${statusText}`;
                    csvContent += row + "\n";
                }
            });

            if (count === 0) {
                alert("Không có sản phẩm nào để xuất!");
                return;
            }

            // Tạo link tải ảo và click
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            const fileName = `Danh_sach_san_pham_${new Date().toISOString().slice(0,10)}.csv`;
            
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", fileName);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
});