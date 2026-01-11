// ==========================================
// 1. BIẾN TOÀN CỤC & TRẠNG THÁI
// ==========================================
let selectedTags = [];
let selectedFiles = [];

const feedbackOptions = {
    positive: ["Nhiệt tình", "Đúng giờ", "Sản phẩm tốt", "Trả lời nhanh", "Thân thiện"],
    negative: ["Bom hàng", "Thái độ kém", "Sai mô tả", "Ép giá", "Spam", "Không đến hẹn"]
};

// Hàm reset dữ liệu khi mở dialog mới
function resetDialogState() {
    selectedTags = [];
    selectedFiles = [];
}

// ==========================================
// 2. HÀM MỞ DIALOG (Đánh giá User)
// ==========================================
function openVoteDialog(el) {
    let partnerId = el.getAttribute('data-partner-id'); 
    
    if (partnerId) partnerId = partnerId.trim(); 

    if (!partnerId) {
        alert('Lỗi: Không xác định được người dùng cần đánh giá!');
        return;
    }

    // Xóa modal cũ nếu còn tồn tại
    const oldModal = document.getElementById('reviewModal');
    if (oldModal) oldModal.remove();

    // Reset trạng thái
    resetDialogState();

    // Gọi Fetch lấy HTML dialog
    // Đảm bảo đường dẫn này đúng với Router của bạn
    const url = '/baitaplon/Vote/dialog/' + encodeURIComponent(partnerId);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(response.statusText);
            return response.text();
        })
        .then(html => {
            if (html.includes("Fatal error") || html.includes("Warning")) {
                alert("Lỗi Server: " + html);
                return;
            }

            // Chèn HTML vào cuối body
            document.body.insertAdjacentHTML('beforeend', html);
            document.body.style.overflow = 'hidden'; // Khóa cuộn trang
        })
        .catch(err => {
            console.error(err);
            alert("Không thể mở hộp thoại: " + err.message);
        });
}

// ==========================================
// 3. XỬ LÝ RATING & TAGS
// ==========================================

// Hàm xử lý khi bấm chọn sao
function setRating(star) {
    const ratingInput = document.getElementById('voteRating');
    const errorMsg = document.getElementById('starError');
    
    if(ratingInput) ratingInput.value = star;
    if(errorMsg) errorMsg.style.display = 'none';

    // 1. Tô màu sao (Dựa trên class .filled trong CSS mới)
    const stars = document.querySelectorAll('.star-group span');
    stars.forEach((s, index) => {
        // Dùng classList.toggle hoặc add/remove
        if (index < star) {
            s.classList.add('filled');
            // Cập nhật icon thành sao đặc (solid) nếu dùng FontAwesome
            s.innerHTML = '<i class="fas fa-star"></i>';
        } else {
            s.classList.remove('filled');
            // Sao rỗng (regular) nếu muốn, hoặc giữ nguyên icon
            s.innerHTML = '<i class="fas fa-star"></i>'; 
        }
    });

    // 2. Hiện label cảm xúc
    const labels = ["Rất tệ", "Tệ", "Bình thường", "Tốt", "Tuyệt vời"];
    const labelEl = document.getElementById('ratingLabel');
    if(labelEl) {
        labelEl.innerText = labels[star - 1];
        labelEl.style.color = (star >= 4) ? '#22c1b5' : '#ff9900';
    }

    // 3. Render tags phù hợp
    renderTags(star);
}

// Hàm render các nút tag gợi ý
function renderTags(star) {
    const container = document.getElementById('feedbackTags');
    if(!container) return;

    container.style.display = 'flex';
    container.innerHTML = '';
    selectedTags = []; // Reset chọn tag khi đổi sao

    // Nếu >= 4 sao: Hiện tag tích cực, < 4 sao: Hiện tag tiêu cực
    const options = star >= 4 ? feedbackOptions.positive : feedbackOptions.negative;

    options.forEach(text => {
        const tag = document.createElement('span');
        tag.className = 'tag-item';
        tag.innerText = text;
        tag.onclick = function() { toggleTag(this, text); };
        container.appendChild(tag);
    });
}

// Hàm chọn/bỏ chọn tag
function toggleTag(element, text) {
    if (selectedTags.includes(text)) {
        selectedTags = selectedTags.filter(t => t !== text);
        element.classList.remove('active');
    } else {
        selectedTags.push(text);
        element.classList.add('active');
    }
}

// ==========================================
// 4. XỬ LÝ UPLOAD ẢNH (Quan trọng)
// ==========================================
function handleImageSelect(event) {
    const files = Array.from(event.target.files);
    const container = document.getElementById('imagePreviewContainer');
    
    // Kiểm tra giới hạn số lượng (tối đa 3 ảnh)
    if (selectedFiles.length + files.length > 3) {
        alert("Bạn chỉ được chọn tối đa 3 ảnh.");
        return;
    }

    files.forEach(file => {
        // Chỉ nhận file ảnh
        if (!file.type.startsWith('image/')) return;

        // Thêm vào mảng quản lý
        selectedFiles.push(file);

        // Tạo element xem trước
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            // HTML khớp với CSS .preview-thumb
            previewItem.innerHTML = `
                <img src="${e.target.result}" class="preview-thumb">
                <button class="btn-remove-img" onclick="removeImage('${file.name}', this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(previewItem);
        }
        reader.readAsDataURL(file);
    });

    // Reset input để chọn lại được file cũ nếu cần
    event.target.value = '';
}

// Xóa ảnh khỏi danh sách chờ upload
function removeImage(fileName, buttonElement) {
    // Xóa khỏi mảng quản lý
    selectedFiles = selectedFiles.filter(f => f.name !== fileName);
    // Xóa khỏi giao diện (remove thẻ cha .preview-item)
    buttonElement.closest('.preview-item').remove();
}

// ==========================================
// 5. HÀM GỬI ĐÁNH GIÁ (SUBMIT)
// ==========================================
function submitVote() {
    const targetId = document.getElementById('voteTargetId').value;
    const rating = document.getElementById('voteRating').value;
    let comment = document.getElementById('voteComment').value.trim();
    
    // Checkbox xác nhận giao dịch
    const isTransactedEl = document.getElementById('isTransacted');
    const isTransacted = (isTransactedEl && isTransactedEl.checked) ? 1 : 0;

    // Validate
    if (rating == 0) {
        document.getElementById('starError').style.display = 'block';
        return;
    }

    // Gộp tags vào comment
    if (selectedTags.length > 0) {
        const tagString = selectedTags.map(t => `[${t}]`).join(' ');
        comment = tagString + "\n" + comment;
    }

    // --- DÙNG FORMDATA ĐỂ GỬI FILE & DỮ LIỆU ---
    const formData = new FormData();
    formData.append('target_id', targetId);
    formData.append('rating', rating);
    formData.append('comment', comment);
    formData.append('is_transacted', isTransacted); // Gửi 0 hoặc 1

    // Append từng file ảnh vào formData
    // Key 'review_images[]' phải khớp với $_FILES['review_images'] trong PHP
    selectedFiles.forEach((file) => {
        formData.append('review_images[]', file);
    });

    // Gửi Ajax
    fetch('/baitaplon/Vote/submit', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Kiểm tra xem server có trả về JSON không
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            return response.text().then(text => { throw new Error(text); });
        }
    })
    .then(data => {
        if (data.success) { 
            closeReview(); 
            
            // Logic gợi ý báo cáo nếu đánh giá thấp
            if (parseInt(rating) <= 2) {
                setTimeout(() => {
                    showReportSuggestion(targetId);
                }, 500);
            } else {
                alert('Cảm ơn bạn đã đánh giá!');
            }
        } else {
            alert('Lỗi: ' + (data.message || 'Không rõ nguyên nhân'));
        }
    })
    .catch(error => {
        console.error('Lỗi hệ thống:', error);
        // Hiển thị lỗi ngắn gọn
        alert('Có lỗi xảy ra. Vui lòng kiểm tra Console (F12) để biết chi tiết.'); 
    });
}

// ==========================================
// 6. CÁC HÀM PHỤ TRỢ
// ==========================================

function showReportSuggestion(targetId) {
    const choice = confirm(
        "⚠️ CẢNH BÁO AN TOÀN \n\n" +
        "Bạn đã đánh giá thấp người dùng này.\n" +
        "Nếu bạn nghi ngờ có hành vi LỪA ĐẢO hoặc VI PHẠM, hãy báo cáo ngay.\n\n" +
        "Chuyển đến trang Báo cáo?"
    );

    if (choice) {
        window.location.href = '/baitaplon/Report/create?target_id=' + targetId;
    }
}

function closeReview() {
    const modal = document.getElementById('reviewModal');
    if (modal) modal.remove();
    document.body.style.overflow = ''; // Mở lại cuộn trang
    resetDialogState();
}