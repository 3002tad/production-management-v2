<?php /** Orders list - Read-only for Leader */ ?>
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card-header d-flex align-items-center justify-content-between bg-gradient-info text-white p-3" style="border-radius:12px;">
        <h6 class="mb-0">Danh sách Đơn hàng</h6>
        <span class="badge bg-warning text-dark">View Only</span>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form class="row" method="GET" action="<?= site_url('leader/orders'); ?>">
            <div class="col-md-4 mb-2">
              <label class="form-label">Từ khóa</label>
              <input type="text" name="keyword" class="form-control" placeholder="Mã đơn hàng/khách hàng/sản phẩm" value="<?= htmlspecialchars($filters['keyword'] ?? ''); ?>">
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label">Trạng thái</label>
              <select name="status" class="form-control">
                <option value="">Tất cả</option>
                <option value="1" <?= (isset($filters['status']) && $filters['status']==='1') ? 'selected' : ''; ?>>Đã duyệt</option>
                <option value="2" <?= (isset($filters['status']) && $filters['status']==='2') ? 'selected' : ''; ?>>Đang sản xuất</option>
                <option value="3" <?= (isset($filters['status']) && $filters['status']==='3') ? 'selected' : ''; ?>>Đã sản xuất</option>
                <option value="0" <?= (isset($filters['status']) && $filters['status']==='0') ? 'selected' : ''; ?>>Hủy</option>
              </select>
            </div>
            <div class="col-md-2 mb-2">
              <label class="form-label">Từ ngày</label>
              <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'] ?? ''); ?>">
            </div>
            <div class="col-md-2 mb-2">
              <label class="form-label">Đến ngày</label>
              <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'] ?? ''); ?>">
            </div>
            <div class="col-md-1 mb-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-2">
    <div class="col-12">
      <div class="card">
        <div class="card-body pt-3">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã đơn hàng</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Khách hàng</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sản phẩm</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày nhận</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hạn giao</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Yêu cầu</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Chi tiết</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($orders)): $i=1; foreach ($orders as $o): ?>
                <tr>
                  <td><span class="text-sm"><?= $i++; ?></span></td>
                  <td><span class="text-sm font-weight-bold"><?= htmlspecialchars($o->project_name); ?></span></td>
                  <td><span class="text-sm"><?= htmlspecialchars($o->cust_name); ?></span></td>
                  <td><span class="text-sm"><?= htmlspecialchars($o->product_name); ?></span></td>
                  <td><span class="badge badge-sm <?= ($o->pr_status == 2 ? 'bg-info' : ($o->pr_status == 3 ? 'bg-success' : ($o->pr_status == 1 ? 'bg-secondary' : 'bg-danger'))); ?>"><?= htmlspecialchars($o->status_text); ?></span></td>
                  <td><span class="text-sm"><?= !empty($o->entry_date) ? date('d/m/Y', strtotime($o->entry_date)) : '-'; ?></span></td>
                  <td><span class="text-sm"><?= !empty($o->end_date) ? date('d/m/Y', strtotime($o->end_date)) : '-'; ?></span></td>
                  <td><span class="text-sm"><?= number_format($o->qty_request ?? 0); ?></span></td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('leader/order/'.$o->id_project); ?>" title="Xem chi tiết">Xem</a>
                  </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="9" class="text-center py-4">Chưa có đơn hàng</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
