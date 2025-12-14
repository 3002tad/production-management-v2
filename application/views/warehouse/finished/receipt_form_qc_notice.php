<!-- QC Approval Notice Component -->
<!-- Display QC status and requirements for finished goods receipt -->

<div class="alert alert-info alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="alert-content">
        <h5 class="alert-heading">
            <i class="fas fa-check-circle" style="color: #28a745;"></i>
            <strong> Yêu Cầu Kiểm Soát Chất Lượng (QC)</strong>
        </h5>
        <hr>
        <p><strong>Quy tắc:</strong> 
            <span style="background-color: #fff3cd; padding: 3px 8px; border-radius: 3px;">
                Kho thành phẩm chỉ được phép nhập sau khi QC đã duyệt (APPROVE)
            </span>
        </p>
        
        <p><strong>Điều kiện để nhập kho:</strong></p>
        <ul style="margin-bottom: 0;">
            <li><i class="fas fa-check" style="color: #28a745;"></i> Ca sản xuất phải có <strong>QC Decision = APPROVE</strong></li>
            <li><i class="fas fa-check" style="color: #28a745;"></i> Trạng thái shift closure phải là <strong>VERIFIED</strong></li>
            <li><i class="fas fa-check" style="color: #28a745;"></i> Flag <code>can_receive_fg</code> phải bật (=1)</li>
            <li><i class="fas fa-check" style="color: #28a745;"></i> Không được phép nhập nếu QC <strong>REJECT</strong></li>
        </ul>

        <p style="margin-top: 10px; margin-bottom: 0;">
            <strong>Chỉ <u>các ca đã được QC duyệt</u> mới xuất hiện trong danh sách dưới đây.</strong>
        </p>
    </div>
</div>

<?php if (empty($batches)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Hiện chưa có ca nào đã được QC duyệt.</strong>
        <br/>
        <small>Vui lòng chờ QC hoàn thành kiểm tra và phê duyệt các ca sản xuất trước khi thực hiện nhập kho.</small>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> 
        <strong><?php echo count($batches); ?> ca sẵn sàng để nhập kho</strong>
        <br/>
        <small>Tất cả các ca sản xuất dưới đây đã được QC duyệt (APPROVE) và có thể nhập kho thành phẩm.</small>
    </div>
<?php endif; ?>

<style>
.alert-content {
    font-size: 0.95rem;
}

.alert-content strong {
    font-weight: 600;
}

.alert-content ul {
    padding-left: 20px;
}

.alert-content li {
    margin-bottom: 5px;
}

code {
    background-color: #f5f5f5;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 0.9rem;
    color: #d73a49;
}
</style>
