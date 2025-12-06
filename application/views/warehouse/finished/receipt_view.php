<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Chi Tiết Phiếu Nhập Thành Phẩm</h5>
          <a href="<?= site_url('warehouse/finished/receipt'); ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
        </div>
        <div class="card-body">

          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title text-muted">Thông Tin Phiếu</h6>
                  <dl class="row">
                    <dt class="col-sm-5">Mã Phiếu:</dt>
                    <dd class="col-sm-7"><strong><?= $receipt->receipt_code; ?></strong></dd>

                    <dt class="col-sm-5">Trạng Thái:</dt>
                    <dd class="col-sm-7">
                      <?php if ($receipt->status === 'posted'): ?>
                        <span class="badge bg-success">Đã Lưu</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Hủy</span>
                      <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5">Ngày Tạo:</dt>
                    <dd class="col-sm-7"><?= date('d/m/Y H:i:s', strtotime($receipt->created_date)); ?></dd>

                    <dt class="col-sm-5">Người Lập:</dt>
                    <dd class="col-sm-7"><?= $receipt->created_by_name; ?></dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title text-muted">Thông Tin Dự Án</h6>
                  <dl class="row">
                    <dt class="col-sm-5">Dự Án:</dt>
                    <dd class="col-sm-7"><strong><?= $project->project_name ?? 'N/A'; ?></strong></dd>

                    <dt class="col-sm-5">ID Dự Án:</dt>
                    <dd class="col-sm-7"><?= $receipt->id_project; ?></dd>

                    <dt class="col-sm-5">SL Kế Hoạch:</dt>
                    <dd class="col-sm-7"><span class="badge bg-primary"><?= $receipt->quantity_planned; ?></span></dd>

                    <dt class="col-sm-5">SL Thực Nhập:</dt>
                    <dd class="col-sm-7"><span class="badge bg-success"><?= $receipt->quantity_received; ?></span></dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <?php if ($receipt->notes): ?>
            <div class="mb-4">
              <h6>Ghi Chú</h6>
              <div class="bg-light p-3 rounded">
                <?= nl2br(htmlspecialchars($receipt->notes)); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="d-flex gap-2">
            <a href="<?= site_url('warehouse/finished/receipt'); ?>" class="btn btn-secondary">
              <i class="material-icons align-middle">arrow_back</i> Quay lại
            </a>
            <?php if ($receipt->status === 'posted'): ?>
              <a href="<?= site_url('warehouse/finished/receipt_cancel/' . $receipt->id_receipt); ?>" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn hủy phiếu này?')">
                <i class="material-icons align-middle">delete</i> Hủy Phiếu
              </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-primary">
              <i class="material-icons align-middle">print</i> In Phiếu
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>