<!-- Breadcrumb Navigation -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a>
                </li>
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="<?= site_url('warehouse/finished'); ?>">Kho thành phẩm</a>
                </li>
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="<?= site_url('warehouse/finished/receipt'); ?>">Danh sách phiếu nhập</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chi tiết phiếu nhập</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Chi tiết phiếu nhập</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
                <div class="col-6 d-flex text-end">
                    <a href="<?= site_url('warehouse/logout'); ?>" class="btn gradient-dark mb-0">|  <?= lang('btn_logout'); ?>
                    <i class="material-icons">arrow_forward</i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Chi Tiết Phiếu Nhập Thành Phẩm</h5>
          <a href="<?= site_url('warehouse/finished/receipt'); ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
        </div>
        <div class="card-body">
          <?php if (!$receipt): ?>
            <div class="alert alert-danger">
              Không tìm thấy thông tin phiếu nhập.
            </div>
          <?php else: ?>
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title text-muted">Thông Tin Phiếu</h6>
                  <dl class="row">
                    <dt class="col-sm-5">Mã Phiếu:</dt>
                    <dd class="col-sm-7"><strong><?= $receipt->receipt_code ?? 'N/A'; ?></strong></dd>

                    <dt class="col-sm-5">Trạng Thái:</dt>
                    <dd class="col-sm-7">
                      <?php if (($receipt->status ?? '') === 'posted'): ?>
                        <span class="badge bg-success">Đã Lưu</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Hủy</span>
                      <?php endif; ?>
                    </dd>

                    <dt class="col-sm-5">Ngày Tạo:</dt>
                    <dd class="col-sm-7"><?= !empty($receipt->created_date) ? date('d/m/Y H:i:s', strtotime($receipt->created_date)) : 'N/A'; ?></dd>

                    <dt class="col-sm-5">Người Lập:</dt>
                    <dd class="col-sm-7"><?= $receipt->created_by_name ?? 'N/A'; ?></dd>
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
                    <dd class="col-sm-7"><?= $receipt->id_project ?? 'N/A'; ?></dd>

                    <dt class="col-sm-5">SL Kế Hoạch:</dt>
                    <dd class="col-sm-7"><span class="badge bg-primary"><?= $receipt->quantity_planned ?? 0; ?></span></dd>

                    <dt class="col-sm-5">SL Thực Nhập:</dt>
                    <dd class="col-sm-7"><span class="badge bg-success"><?= $receipt->quantity_received ?? 0; ?></span></dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <?php if (!empty($receipt->notes)): ?>
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
            <?php if (($receipt->status ?? '') === 'posted'): ?>
              <a href="<?= site_url('warehouse/finished/receipt_cancel/' . ($receipt->id_receipt ?? 0)); ?>" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn hủy phiếu này?')">
                <i class="material-icons align-middle">delete</i> Hủy Phiếu
              </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-primary">
              <i class="material-icons align-middle">print</i> In Phiếu
            </button>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>