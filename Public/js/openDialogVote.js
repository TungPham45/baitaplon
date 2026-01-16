

var selectedTags = selectedTags || [];
var selectedFiles = selectedFiles || [];

var feedbackOptions = feedbackOptions || {
    positive: ["Nhiệt tình", "Đúng giờ", "Sản phẩm tốt", "Trả lời nhanh", "Thân thiện"],
    negative: ["Bom hàng", "Thái độ kém", "Sai mô tả", "Ép giá", "Spam", "Không đến hẹn"]
};

// Hàm reset dữ liệu khi mở dialog mới
// Kiểm tra if (!window.resetDialogState) để tránh định nghĩa lại hàm nhiều lần
if (!window.resetDialogState) {
    window.resetDialogState = function() {
        // 1. Reset biến
        selectedTags = [];
        selectedFiles = [];

        // 2. Reset Input & Textarea
        if(document.getElementById('voteRating')) document.getElementById('voteRating').value = "0";
        if(document.getElementById('voteComment')) document.getElementById('voteComment').value = "";
        if(document.getElementById('isTransacted')) document.getElementById('isTransacted').checked = false;
        if(document.getElementById('reviewImages')) document.getElementById('reviewImages').value = ""; 

        // 3. Reset Giao diện Sao
        document.querySelectorAll('.star-rating span').forEach(s => {
            s.classList.remove('filled');
            s.innerHTML = '<i class="far fa-star"></i>'; 
        });
        if(document.getElementById('ratingLabel')) document.getElementById('ratingLabel').innerText = "";
        if(document.getElementById('starError')) document.getElementById('starError').style.display = 'none';

        // 4. Reset Tags & Ảnh preview
        const tagContainer = document.getElementById('feedbackTags');
        if (tagContainer) {
            tagContainer.innerHTML = '';
            tagContainer.style.display = 'none';
        }
        const imgContainer = document.getElementById('imagePreviewContainer');
        if(imgContainer) imgContainer.innerHTML = '';
    }
}

if (!window.openVoteDialog) {
    window.openVoteDialog = function(element) {
        // 1. Lấy dữ liệu từ nút bấm
        const partnerId = element.getAttribute('data-partner-id');
        const partnerName = element.getAttribute('data-partner-name') || 'Người dùng';
        const avatarSrc = element.getAttribute('data-avatar'); 

        if (!partnerId) {
            alert("Lỗi: Không tìm thấy ID người dùng.");
            return;
        }

        // 2. Reset form
        resetDialogState();

        // 3. Điền ID và Tên vào Modal
        document.getElementById('voteTargetId').value = partnerId;
        
        const nameEl = document.querySelector('#reviewModal .user-name');
        if (nameEl) nameEl.textContent = partnerName;

        // 4. XỬ LÝ HIỆN AVATAR
        const avatarContainer = document.querySelector('#reviewModal .avatar-circle');
        
        if (avatarContainer) {
            avatarContainer.innerHTML = ''; 

            if (avatarSrc && avatarSrc.trim() !== '') {
                const imgPath = avatarSrc.startsWith('/') || avatarSrc.startsWith('http') 
                                ? avatarSrc 
                                : '/baitaplon/' + avatarSrc;
                
                avatarContainer.innerHTML = `<img src="${imgPath}" alt="Avt" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
            } else {
                const firstLetter = partnerName.charAt(0).toUpperCase();
                avatarContainer.textContent = firstLetter;
            }
        }

        // 5. Hiện Modal
        const modal = document.getElementById('reviewModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            console.error("Không tìm thấy modal #reviewModal");
        }
    }
}


if (!window.closeReview) {
    window.closeReview = function() {
        const modal = document.getElementById('reviewModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

if (!window.setRating) {
    window.setRating = function(star) {
        const ratingInput = document.getElementById('voteRating');
        const errorMsg = document.getElementById('starError');
        
        if(ratingInput) ratingInput.value = star;
        if(errorMsg) errorMsg.style.display = 'none';

        const stars = document.querySelectorAll('.star-rating span'); 
        stars.forEach((s, index) => {
            if (index < star) {
                s.classList.add('filled');
                s.innerHTML = '<i class="fas fa-star"></i>'; 
            } else {
                s.classList.remove('filled');   
                s.innerHTML = '<i class="far fa-star"></i>'; 
            }
        });

        const labels = ["Rất tệ", "Tệ", "Bình thường", "Tốt", "Tuyệt vời"];
        const labelEl = document.getElementById('ratingLabel');
        if(labelEl) {
            labelEl.innerText = labels[star - 1];
            labelEl.style.color = (star >= 4) ? '#22c1b5' : '#ff9900';
        }

        renderTags(star);
    }
}

if (!window.renderTags) {
    window.renderTags = function(star) {
        const container = document.getElementById('feedbackTags');
        if(!container) return;

        container.style.display = 'flex';
        container.innerHTML = '';
        selectedTags = []; 

        const options = star >= 4 ? feedbackOptions.positive : feedbackOptions.negative;

        options.forEach(text => {
            const tag = document.createElement('span');
            tag.className = 'tag-item';
            tag.innerText = text;
            tag.onclick = function() { toggleTag(this, text); };
            container.appendChild(tag);
        });
    }
}

if (!window.toggleTag) {
    window.toggleTag = function(element, text) {
        if (selectedTags.includes(text)) {
            selectedTags = selectedTags.filter(t => t !== text);
            element.classList.remove('active');
        } else {
            selectedTags.push(text);
            element.classList.add('active');
        }
    }
}

// Xử lý upload ảnh
if (!window.handleImageSelect) {
    window.handleImageSelect = function(event) {
        const files = Array.from(event.target.files);
        const container = document.getElementById('imagePreviewContainer');
        
        if (selectedFiles.length + files.length > 3) {
            alert("Bạn chỉ được chọn tối đa 3 ảnh.");
            return;
        }

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            selectedFiles.push(file);

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" class="preview-thumb">
                    <button class="btn-remove-img" type="button" onclick="removeImage('${file.name}', this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(previewItem);
            }
            reader.readAsDataURL(file);
        });

        event.target.value = '';
    }
}

if (!window.removeImage) {
    window.removeImage = function(fileName, buttonElement) {
        selectedFiles = selectedFiles.filter(f => f.name !== fileName);
        buttonElement.closest('.preview-item').remove();
    }
}

// Gửi đánh giá
if (!window.submitVote) {
    window.submitVote = function() {
        const targetId = document.getElementById('voteTargetId').value;
        const rating = document.getElementById('voteRating').value;
        let comment = document.getElementById('voteComment').value.trim();
        const isTransactedEl = document.getElementById('isTransacted');
        const isTransacted = (isTransactedEl && isTransactedEl.checked) ? 1 : 0;

        if (rating == 0) {
            document.getElementById('starError').style.display = 'block';
            return;
        }

        if (selectedTags.length > 0) {
            const tagString = selectedTags.map(t => `[${t}]`).join(' ');
            comment = tagString + "\n" + comment;
        }

        const formData = new FormData();
        formData.append('target_id', targetId);
        formData.append('rating', rating);
        formData.append('comment', comment);
        formData.append('is_transacted', isTransacted);

        selectedFiles.forEach((file) => {
            formData.append('review_images[]', file);
        });

        // Gửi Ajax
        fetch('/baitaplon/Vote/submit', { 
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) { 
                closeReview(); 
                if (parseInt(rating) <= 2) {
                    setTimeout(() => { showReportSuggestion(targetId); }, 500);
                } else {
                    alert('Cảm ơn bạn đã đánh giá!');
                }
            } else {
                alert('Lỗi: ' + (data.message || 'Không rõ nguyên nhân'));
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Lỗi hệ thống hoặc Server không phản hồi JSON.'); 
        });
    }
}

if (!window.showReportSuggestion) {
    window.showReportSuggestion = function(targetId) {
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
}