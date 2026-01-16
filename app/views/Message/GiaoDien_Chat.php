<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Hệ Thống</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="/baitaplon/public/css/GiaoDien_Chat.css">
</head>
<body>

<div class="chat-container">

    <div class="chat-list">
        <a href="/baitaplon/Home/index/<?= htmlspecialchars($my_id) ?>" class="btn-back-home">
            <i class="bi bi-arrow-left-circle-fill"></i> 
            <span>Quay lại Trang chủ</span>
        </a>

        <form method="post" action="/baitaplon/Chat/search">
            <div class="chat-search">
                <input 
                    type="text" 
                    name="keyword" 
                    autocomplete="off"
                    placeholder="🔍 Tìm người dùng..."
                    value="<?= htmlspecialchars($_POST['keyword'] ?? '') ?>"
                >
            </div>
        </form>

        <div class="chat-users">
            <?php if (!empty($conversations)): ?>
                <?php foreach ($conversations as $c): ?>
                    <div class="chat-user <?= ($c['id_conversation'] == ($active_conversation_id ?? 0)) ? 'active' : '' ?>"
                         onclick="window.location.href='/baitaplon/Chat/start/<?= $c['id_conversation'] ?>'" >

                        <div class="avatar">
                            <?php if (!empty($c['avatar'])): ?>
                                <img src="/baitaplon/<?= htmlspecialchars($c['avatar']) ?>" alt="Avt">
                            <?php else: ?>
                                <?= strtoupper(substr($c['hoten'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>

                        <div class="chat-user-info">
                            <div class="username"><?= htmlspecialchars($c['hoten']) ?></div>
                            <div class="last-message">
                                <?= htmlspecialchars($c['last_message'] ?? 'Bắt đầu cuộc trò chuyện') ?>
                            </div>
                        </div>

                        <div class="chat-time">
                            <?= isset($c['last_message_at']) ? formatChatTime($c['last_message_at']) : '' ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 20px; color: #999;">Không tìm thấy cuộc trò chuyện</div>
            <?php endif; ?>
        </div>
    </div>


    <div class="chat-main">
        
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-header-avatar">
                    <?php if (!empty($sender_avatar)): ?>
                        <img src="/baitaplon/<?= htmlspecialchars($sender_avatar) ?>" alt="Avt">
                    <?php else: ?>
                        <?= strtoupper(substr($sender_name ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="chat-title">
                    <?= htmlspecialchars($sender_name) ?>
                    </div>
            </div>

            <div class="chat-header-right">
                <button type="button" class="btn-search-message" onclick="toggleSearchMessage()" title="Tìm tin nhắn">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        
        <?php require __DIR__ . '/SearchMessage_Chat.php'; ?>

        <?php if (!empty($product_context)): ?>
        <div class="product-pinned-bar">
            <div class="pinned-left">
                <div class="pinned-img">
                    <?php 
                        $imgSrc = !empty($product_context['image']) ? "/baitaplon/" . $product_context['image'] : "/baitaplon/public/images/default-product.png";
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Product">
                </div>
                <div class="pinned-info">
                    <div class="pinned-title">Đang trao đổi về: <?= htmlspecialchars($product_context['name']) ?></div>
                    <div class="pinned-price"><?= number_format($product_context['price']) ?> đ</div>
                </div>
            </div>
            
            <div class="pinned-right">
                <a href="/baitaplon/Home/detail_Sanpham/<?= $product_context['id'] ?? 0 ?>/<?= $product_context['seller_id'] ?? 0 ?>" 
                   target="_blank" 
                   class="btn-back-home" style="padding: 5px 10px; font-size: 13px; border: 1px solid #ddd;">
                   Xem chi tiết
                </a>
            </div>
        </div>
        <?php endif; ?>

        <div class="chat-messages <?= !empty($product_context) ? 'has-pinned' : '' ?>">
            <?php if (!empty($messages)): ?>
                <?php $prevTime = null; ?>
                <?php foreach ($messages as $msg): ?>
                    <?php
                        $currentTime = strtotime($msg['created_at']);
                        $showTime = ($prevTime === null || ($currentTime - $prevTime) >= 300); // 5 phút mới hiện giờ 1 lần
                        $prevTime = $currentTime;
                        $isMine = ($msg['sender_id'] == $my_id);
                    ?>

                    <div class="message <?= $isMine ? 'message-right' : 'message-left' ?>">
                        
                        <?php if (!$isMine): ?>
                            <div class="message-avatar">
                                <?php if (!empty($sender_avatar)): ?>
                                    <img src="/baitaplon/<?= htmlspecialchars($sender_avatar) ?>" alt="Avt">
                                <?php else: ?>
                                    <?= strtoupper(substr($sender_name ?? 'U', 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="message-body">
                            <?php if ($isMine): ?>
                                <div class="message-actions">
                                    <i class="bi bi-three-dots-vertical"></i>
                                    <ul class="message-menu">
                                        <li onclick="editMessage(<?= $msg['id_message'] ?>)">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </li>
                                        <li onclick="deleteMessage(<?= $msg['id_message'] ?>)" style="color: red;">
                                            <i class="bi bi-trash"></i> Xóa
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="message-content" data-id="<?= $msg['id_message'] ?>">
                                <?= htmlspecialchars($msg['content']) ?>
                            </div>

                            <?php if ($showTime): ?>
                                <div class="message-time">
                                    <?= date('H:i', $currentTime) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; margin-top: 50px; color: #ccc;">
                    <i class="bi bi-chat-dots" style="font-size: 40px;"></i>
                    <p>Hãy bắt đầu cuộc trò chuyện!</p>
                </div>
            <?php endif; ?>
        </div>

        <form class="chat-input" method="post" action="/baitaplon/Chat/send" id="chatForm">
            <input type="hidden" name="conversation_id" value="<?php echo $active_conversation_id ?? 0; ?>">
            
            <?php if (!empty($product_context)): ?>
                <input type="hidden" name="ref_product_id" value="<?= $product_context['id'] ?>">
            <?php endif; ?>
            
            <input type="hidden" name="message_id" id="editMessageId">
            
            <input type="text" name="message" id="chatInput" autocomplete="off" placeholder="Nhập tin nhắn..." required>
            
            <button type="submit">
                <i class="bi bi-send-fill"></i>
            </button>
         </form>
    </div>

    <div class="chat-info">
        <div class="avatar-large">
            <?php if (!empty($sender_avatar)): ?>
                <img src="/baitaplon/<?= htmlspecialchars($sender_avatar) ?>" alt="Avt">
            <?php else: ?>
                <?= strtoupper(substr($sender_name ?? 'U', 0, 1)) ?>
            <?php endif; ?>
        </div>

        <h4><?= htmlspecialchars($sender_name) ?></h4>

        <ul class="chat-info-list">
            <li onclick="window.location.href='/baitaplon/User/Profile/<?= $sender_id ?>/<?= $my_id ?>'">
                <i class="bi bi-person-circle" style="color: var(--primary-color);"></i>  Xem trang cá nhân
            </li>
            
            <li onclick="toggleSearchMessage()">
                <i class="bi bi-search" style="color: #6610f2;"></i>  Tìm kiếm tin nhắn
            </li>
            
            <li data-partner-id="<?= $sender_id ?>" onclick="openVoteDialog(this)">
                <i class="bi bi-star-fill" style="color: #fd7e14;"></i>  Đánh giá người dùng
            </li>
          
            <li onclick="confirmDeleteConversation(<?= $active_conversation_id ?>)" style="color: var(--danger);">
                <i class="bi bi-trash3-fill"></i> Xóa cuộc trò chuyện
            </li>
        </ul>

        <form id="formDeleteConversation" action="/baitaplon/Chat/deleteConversation" method="POST" style="display: none;">
            <input type="hidden" name="conversation_id" id="inputDeleteConvId">
        </form>
    </div>

</div>

<script src="/baitaplon/public/js/openConversation.js"></script>
<script src="/baitaplon/public/js/OpenSearchMessage.js"></script>
<script src="/baitaplon/public/js/openDialogVote.js"></script>
<script src="/baitaplon/public/js/deleteMessage.js"></script>

</body>
</html>