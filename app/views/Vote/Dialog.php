<link rel="stylesheet" href="/baitaplon/public/css/DialogVote.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="/baitaplon/public/js/openDialogVote.js"></script>



<div class="modal-overlay" id="reviewModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Đánh giá trải nghiệm</h3>
            <button class="btn-close" onclick="closeReview()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <input type="hidden" id="voteTargetId" value="<?= htmlspecialchars($target_id ?? '') ?>">
            <input type="hidden" id="voteRating" value="0">

            <div class="profile-section">
                <div class="avatar-circle">
                    <?= strtoupper(substr($target_name ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-name"><?= htmlspecialchars($target_name ?? 'Người dùng') ?></div>
                <div class="user-context">Bạn cảm thấy thế nào về cuộc trò chuyện này?</div>
            </div>

            <div class="rating-section">
                <div class="star-rating">
                    <span onclick="setRating(1)" data-star="1"><i class="fas fa-star"></i></span>
                    <span onclick="setRating(2)" data-star="2"><i class="fas fa-star"></i></span>
                    <span onclick="setRating(3)" data-star="3"><i class="fas fa-star"></i></span>
                    <span onclick="setRating(4)" data-star="4"><i class="fas fa-star"></i></span>
                    <span onclick="setRating(5)" data-star="5"><i class="fas fa-star"></i></span>
                </div>
                <div id="ratingLabel" class="rating-text"></div>
                <small id="starError" style="color:#ef4444; display:none;">Vui lòng chọn số sao</small>
            </div>

            <div class="transaction-badge">
                <label>
                    <input type="checkbox" id="isTransacted">
                    <span><i class="fas fa-shield-alt"></i> Xác nhận đã giao dịch</span>
                </label>
            </div>

            <div class="feedback-tags" id="feedbackTags" style="display:none;"></div>

            <div class="input-group">
                <textarea id="voteComment" rows="3" placeholder="Chia sẻ thêm chi tiết (Thái độ, chất lượng sản phẩm...)..."></textarea>
            </div>

            <div class="upload-zone">
                <input type="file" id="reviewImages" multiple accept="image/*" style="display: none;" onchange="handleImageSelect(event)">
                
                <label for="reviewImages" class="btn-upload-box">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="upload-title">Thêm hình ảnh/video xác thực</span>
                    <span class="upload-desc">Nhấn để tải lên (Tối đa 3 ảnh)</span>
                </label>
                
                <div id="imagePreviewContainer" class="preview-list"></div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-outline" onclick="closeReview()">Để sau</button>
            <button class="btn-solid" onclick="submitVote()">Gửi đánh giá</button>
        </div>
    </div>
</div>