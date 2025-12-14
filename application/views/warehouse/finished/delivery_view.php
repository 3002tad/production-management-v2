<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Chi Tiết Phiếu Xuất Giao Hàng</h5>
            <small class="text-muted">Mã phiếu: <strong><?= $issue->issue_code; ?></strong></small>
          </div>
          <div>
            <a href="<?= site_url('warehouse/finished/delivery'); ?>" class="btn btn-sm btn-secondary">← Quay lại</a>
            <?php if ($issue->status !== 'cancelled'): ?>
              <a href="<?= site_url('warehouse/finished/delivery_cancel/' . $issue->id_issue); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hủy phiếu này?')">
                <i class="material-icons align-middle">delete</i> Hủy
              </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">

          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title text-muted">Thông Tin Phiếu</h6>
                  <dl class="row">
                    <dt class="col-sm-5">Mã Phiếu:</dt>
                    <dd class="col-sm-7"><strong><?= $issue->issue_code; ?></strong></dd>

                    <dt class="col-sm-5">Trạng Thái:</dt>
                    <dd class="col-sm-7">
                      <?php if ($issue->status === 'full'): ?>
                        <span class="badge bg-success">Giao Đủ</span>
                      <?php elseif ($issue->status === 'partial'): ?>
                        <span class="badge bg-warning">Giao Thiếu</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Hủy</span>
                      <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5">Ngày Tạo:</dt>
                    <dd class="col-sm-7"><?= date('d/m/Y H:i:s', strtotime($issue->created_date)); ?></dd>

                    <dt class="col-sm-5">Người Lập:</dt>
                    <dd class="col-sm-7"><?= $issue->created_by_name; ?></dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title text-muted">Thông Tin Đơn Hàng</h6>
                  <dl class="row">
                    <dt class="col-sm-5">Đơn/Dự Án:</dt>
                    <dd class="col-sm-7"><strong><?= $project->project_name ?? 'N/A'; ?></strong></dd>

                    <dt class="col-sm-5">ID Dự Án:</dt>
                    <dd class="col-sm-7"><?= $issue->id_project; ?></dd>

                    <dt class="col-sm-5">SL Yêu Cầu:</dt>
                    <dd class="col-sm-7"><span class="badge bg-primary"><?= $issue->quantity_requested; ?></span></dd>

                    <dt class="col-sm-5">SL Thực Xuất:</dt>
                    <dd class="col-sm-7"><span class="badge bg-success"><?= $issue->quantity_issued; ?></span></dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="alert alert-info">
            <strong>Tồn Kho Hiện Tại:</strong> <span class="badge bg-info fs-5"><?= $current_stock; ?> cái</span>
          </div>

          <?php if ($issue->notes): ?>
            <div class="mb-4">
              <h6>Ghi Chú</h6>
              <div class="bg-light p-3 rounded">
                <?= nl2br(htmlspecialchars($issue->notes)); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="d-flex gap-2">
            <a href="<?= site_url('warehouse/finished/delivery'); ?>" class="btn btn-secondary">
              <i class="material-icons align-middle">arrow_back</i> Quay lại
            </a>
            <?php if ($issue->status !== 'cancelled'): ?>
              <a href="<?= site_url('warehouse/finished/delivery_cancel/' . $issue->id_issue); ?>" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn hủy phiếu này?')">
                <i class="material-icons align-middle">delete</i> Hủy Phiếu
              </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-primary">
              <i class="material-icons align-middle">print</i> In Phiếu Giao
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>