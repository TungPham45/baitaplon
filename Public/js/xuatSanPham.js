/**
 * public/js/xuatSanPham.js
 * Xử lý lọc và xuất Excel cho trang Profile
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('statusFilter');
    const btnExport = document.getElementById('btnExportExcel');
    const productItems = document.querySelectorAll('.product-item-wrapper');

    // 1. CHỨC NĂNG LỌC SẢN PHẨM
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const status = this.value; // 'all', 'hienthi', 'daban', 'choduyet'

            productItems.forEach(item => {
                const itemStatus = item.getAttribute('data-status');
                
                if (status === 'all' || itemStatus === status) {
                    // [QUAN TRỌNG] Chỉ xóa class d-none để nó hiện lại theo style gốc (block/flex)
                    item.classList.remove('d-none'); 
                } else {
                    item.classList.add('d-none'); // Ẩn đi
                }
            });
        });
    }

    // 2. CHỨC NĂNG XUẤT EXCEL (CSV)
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            // Thêm BOM (\uFEFF) để Excel mở không bị lỗi font tiếng Việt
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; 
            
            // Header cột
            csvContent += "STT,Tên sản phẩm,Giá bán (VNĐ),Ngày đăng,Trạng thái\n";

            let count = 0;

            productItems.forEach(item => {
                // Chỉ export những item đang hiển thị (không có class d-none)
                if (!item.classList.contains('d-none')) {
                    count++;
                    
                    // Lấy dữ liệu và xử lý dấu phẩy (vì CSV ngăn cách bằng phẩy)
                    const name = (item.getAttribute('data-name') || "").replace(/,/g, " "); 
                    const price = item.getAttribute('data-price') || "0";
                    const date = item.getAttribute('data-date') || "";
                    
                    // Chuyển mã trạng thái sang tiếng Việt
                    let statusText = "Đang bán";
                    const status = item.getAttribute('data-status');
                    if (status === 'daban') statusText = "Đã bán";
                    if (status === 'choduyet') statusText = "Chờ duyệt";

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