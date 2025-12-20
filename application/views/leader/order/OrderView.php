<?php /** Order detail - Read-only for Leader */ ?>
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card-header d-flex align-items-center justify-content-between bg-gradient-info text-white p-3" style="border-radius:12px;">
        <h6 class="mb-0">Chi tiết Đơn hàng</h6>
        <span class="badge bg-warning text-dark">View Only</span>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-lg-7">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="text-muted">Thông tin Đơn hàng</h6>
          <div class="row">
            <div class="col-sm-6"><small class="text-secondary">Mã đơn hàng</small><div class="text-sm font-weight-bold"><?= htmlspecialchars($order->project_name); ?></div></div>
            <div class="col-sm-6"><small class="text-secondary">Trạng thái</small><div><span class="badge badge-sm <?= ($order->pr_status == 2 ? 'bg-info' : ($order->pr_status == 3 ? 'bg-success' : ($order->pr_status == 1 ? 'bg-secondary' : 'bg-danger'))); ?>"><?= htmlspecialchars($order->status_text); ?></span></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">Ngày nhận</small><div class="text-sm"><?= !empty($order->entry_date) ? date('d/m/Y', strtotime($order->entry_date)) : '-'; ?></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">Hạn giao</small><div class="text-sm"><?= !empty($order->end_date) ? date('d/m/Y', strtotime($order->end_date)) : '-'; ?></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">Yêu cầu</small><div class="text-sm"><?= number_format($order->qty_request ?? 0); ?></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">Đường kính</small><div class="text-sm"><?= htmlspecialchars($order->diameter_display ?? ''); ?></div></div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h6 class="text-muted">Sản phẩm</h6>
          <div class="row">
            <div class="col-sm-6"><small class="text-secondary">Tên sản phẩm</small><div class="text-sm font-weight-bold"><?= htmlspecialchars($order->product_name); ?></div></div>
            <div class="col-sm-6"><small class="text-secondary">Ứng dụng/Màu</small><div class="text-sm"><?= htmlspecialchars($order->product_color); ?></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h6 class="text-muted">Khách hàng</h6>
          <div class="row">
            <div class="col-sm-12"><small class="text-secondary">Tên</small><div class="text-sm font-weight-bold"><?= htmlspecialchars($order->cust_name); ?></div></div>
            <div class="col-sm-12 mt-2"><small class="text-secondary">Địa chỉ</small><div class="text-sm"><?= htmlspecialchars($order->address); ?></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">SĐT</small><div class="text-sm"><?= htmlspecialchars($order->telp); ?></div></div>
            <div class="col-sm-6 mt-2"><small class="text-secondary">Email</small><div class="text-sm"><?= htmlspecialchars($order->email); ?></div></div>
          </div>
        </div>
      </div>
      <div class="card mt-3">
        <div class="card-body">
          <h6 class="text-muted">Kế hoạch liên quan</h6>
          <?php if (!empty($plans)): ?>
            <ul class="list-group">
              <?php foreach ($plans as $pl): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-sm"><?= htmlspecialchars($pl->plan_name ?? ('Kế hoạch #' . $pl->id_plan)); ?></span>
                <span class="badge bg-secondary">SL mục tiêu: <?= number_format($pl->qty_target ?? 0); ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <div class="text-secondary">Không có kế hoạch liên kết cho đơn hàng này.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12">
      <a href="<?= site_url('leader/orders'); ?>" class="btn btn-outline-dark btn-sm">Quay lại danh sách</a>
    </div>
  </div>
</div>
