document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ === 'undefined') {
        console.error('jQuery chưa được load!');
        return;
    }

    // Xử lý nút dừng bán
    let currentProductId = null;

    $('.stop-selling-btn').on('click', function() {
        currentProductId = $(this).data('id');
        const productName = $(this).data('name');

        $('#stopProductName').text(productName);
        $('#stopReason').val('');
        $('#stopSellingModal').modal('show');
    });

    // Xác nhận dừng bán
    $('#confirmStopSelling').on('click', function() {
        if (!currentProductId) return;

        const reason = $('#stopReason').val().trim();
        if (!reason) {
            alert('Vui lòng nhập lý do dừng bán!');
            return;
        }

        $.ajax({
            url: '/baitaplon/Admin/stopSelling',
            method: 'POST',
            data: {
                id_sanpham: currentProductId,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    // Cập nhật trạng thái trong bảng
                    const row = $(`button[data-id="${currentProductId}"]`).closest('tr');
                    row.find('td:nth-child(6) span').removeClass('badge-success').addClass('badge-danger').text('Dừng bán');

                    // Cập nhật cột thao tác
                    $(`button[data-id="${currentProductId}"].stop-selling-btn`).closest('td').html(`
                        <span class="text-danger" title="${reason}">
                            <i class="fas fa-stop-circle"></i> Đã dừng
                        </span>
                    `);

                    $('#stopSellingModal').modal('hide');
                    alert('Đã dừng bán sản phẩm thành công!');
                } else {
                    alert('Có lỗi xảy ra: ' + (response.error || 'Không xác định'));
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi kết nối đến server!');
            }
        });
    });

    // Xử lý checkboxes trong dropdown filter
    $('.filter-checkbox').on('change', function() {
        updateFilterInput();
    });

    // Xử lý nút áp dụng filters
    $('#applyFilters').on('click', function() {
        updateFilterInput();
        // Đóng dropdown
        $('.dropdown-menu').removeClass('show');
    });

    // Xử lý nút xóa tất cả filters
    $('#clearFilters').on('click', function() {
        $('.filter-checkbox').prop('checked', false);
        updateFilterInput();
    });

    // Hàm cập nhật text trong input
    function updateFilterInput() {
        const selectedFilters = [];

        // Lấy trạng thái đã chọn
        const statusCheckboxes = $('input[name="status[]"]:checked');
        if (statusCheckboxes.length > 0) {
            const statusTexts = [];
            statusCheckboxes.each(function() {
                const label = $(this).closest('.form-check').find('.form-check-label').text().trim();
                statusTexts.push(label);
            });
            selectedFilters.push('Trạng thái: ' + statusTexts.join(', '));
        }

        // Lấy danh mục đã chọn
        const categoryCheckboxes = $('input[name="categories[]"]:checked');
        if (categoryCheckboxes.length > 0) {
            const categoryTexts = [];
            categoryCheckboxes.each(function() {
                const label = $(this).closest('.form-check').find('.form-check-label').text().trim();
                categoryTexts.push(label);
            });
            selectedFilters.push('Danh mục: ' + categoryTexts.join(', '));
        }

        // Cập nhật input text
        if (selectedFilters.length > 0) {
            $('#filterInput').val(selectedFilters.join(' | '));
        } else {
            $('#filterInput').val('');
        }
    }

    // Xử lý submit form
    $('#filterForm').on('submit', function(e) {
        // Đảm bảo checkboxes được gửi với form
        const formData = new FormData(this);

        // Chuyển đổi thành query string
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            params.append(key, value);
        }

        // Redirect với query parameters
        window.location.href = '/baitaplon/Admin/productStatistics?' + params.toString();
        e.preventDefault();
    });

    // Xuất Excel
    $('#exportExcel').on('click', function() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            params.append(key, value);
        }
        window.location.href = '/baitaplon/Admin/exportProductStatistics?' + params.toString();
    });

    // Khởi tạo trạng thái ban đầu của input
    updateFilterInput();
});