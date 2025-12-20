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
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tạo phiếu nhập</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Tạo phiếu nhập</h6>
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
          <h5 class="mb-0">Nhập Kho Thành Phẩm</h5>
          <a href="<?= site_url('warehouse/finished/receipt'); ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
        </div>
        <div class="card-body">
          
          <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Lỗi!</strong> <?= $this->session->flashdata('error'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>Cảnh báo!</strong> <?= $this->session->flashdata('warning'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('warehouse/finished/receipt_save'); ?>" class="needs-validation">
            
            <div class="mb-3">
              <label for="id_finished_report" class="form-label">Chọn Ca/Lô Đạt QC <span class="text-danger">*</span></label>
              <select id="id_finished_report" name="id_finished_report" class="form-select" required onchange="updateBatchInfo()">
                <option value="">-- Chọn ca/lô --</option>
                <?php if (!empty($batches)): ?>
                  <?php foreach ($batches as $batch): ?>
                    <option value="<?= $batch->id_finished; ?>" 
                            data-qty-passed="<?= $batch->qty_passed; ?>"
                            data-qty-received="<?= $batch->qty_already_received; ?>"
                            data-project="<?= $batch->project_name; ?>"
                            data-date="<?= $batch->fdate; ?>">
                      <?= $batch->project_name; ?> (<?= $batch->fdate; ?>) 
                      <?= !empty($batch->qc_session_code) ? " - QC: " . $batch->qc_session_code : ""; ?>
                      - SL: <?= $batch->qty_passed; ?> cái
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option value="" disabled>Không có ca/lô nào đạt QC để nhập</option>
                <?php endif; ?>
              </select>
              <?php if (empty($batches)): ?>
                <small class="text-danger">
                  <i class="material-icons align-middle" style="font-size: 14px;">warning</i>
                  Chưa có ca/lô nào đạt QC. Vui lòng hoàn thành kiểm tra chất lượng trước.
                </small>
              <?php endif; ?>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Thành phẩm đã sản xuất</label>
                <input type="text" id="qty_planned" class="form-control" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Đã Nhập</label>
                <input type="text" id="qty_already_received" class="form-control" readonly>
              </div>
            </div>

            <div class="mb-3">
              <label for="id_project" class="form-label">Dự Án / Đơn Hàng <span class="text-danger">*</span></label>
              <select id="id_project" name="id_project" class="form-select" required onchange="updateProjectInfo()">
                <option value="">-- Chọn dự án --</option>
                <?php if (!empty($projects)): ?>
                  <?php foreach ($projects as $proj): ?>
                    <option value="<?= $proj->id_project; ?>"
                            data-target="<?= $proj->qty_target; ?>"
                            data-received="<?= $proj->qty_received; ?>">
                      <?= $proj->project_name; ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <div class="row" id="project_stats_row" style="display:none;">
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary">Tổng Nhu Cầu Dự Án</label>
                <input type="text" id="proj_qty_target" class="form-control bg-light font-weight-bold" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-success">Tổng Đã Nhập Dự Án</label>
                <input type="text" id="proj_qty_received" class="form-control bg-light font-weight-bold" readonly>
              </div>
            </div>

            <div class="mb-3">
              <label for="quantity_received" class="form-label">Số Lượng Nhập <span class="text-danger">*</span></label>
              <input type="number" id="quantity_received" name="quantity_received" class="form-control" min="1" required placeholder="Nhập số lượng">
              <small class="text-muted">Vui lòng nhập số lượng thực tế</small>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Ghi Chú</label>
              <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Ghi chú (tùy chọn)"></textarea>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="material-icons align-middle">save</i> Lưu Phiếu
              </button>
              <a href="<?= site_url('warehouse/finished/receipt'); ?>" class="btn btn-secondary">
                <i class="material-icons align-middle">cancel</i> Hủy
              </a>
            </div>

          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
function updateBatchInfo() {
  const select = document.getElementById('id_finished_report');
  const option = select.options[select.selectedIndex];
  
  document.getElementById('qty_planned').value = option.getAttribute('data-qty-passed') || '';
  document.getElementById('qty_already_received').value = option.getAttribute('data-qty-received') || '';
  
  // Tự động chọn dự án tương ứng
  const projectName = option.getAttribute('data-project');
  if (projectName) {
    const projSelect = document.getElementById('id_project');
    for (let i = 0; i < projSelect.options.length; i++) {
      if (projSelect.options[i].text.trim() === projectName.trim()) {
        projSelect.selectedIndex = i;
        updateProjectInfo();
        break;
      }
    }
  }
  
  document.getElementById('quantity_received').focus();
}

function updateProjectInfo() {
  const select = document.getElementById('id_project');
  const option = select.options[select.selectedIndex];
  const statsRow = document.getElementById('project_stats_row');
  
  if (select.value) {
    statsRow.style.display = 'flex';
    document.getElementById('proj_qty_target').value = (option.getAttribute('data-target') || '0') + ' cái';
    document.getElementById('proj_qty_received').value = (option.getAttribute('data-received') || '0') + ' cái';
  } else {
    statsRow.style.display = 'none';
  }
}
</script>
