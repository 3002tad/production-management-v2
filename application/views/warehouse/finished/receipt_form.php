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
                      <?= $batch->project_name; ?> (<?= $batch->fdate; ?>) - SL: <?= $batch->qty_passed; ?> cái
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
                <label class="form-label">Dự Kiến Sản Xuất</label>
                <input type="text" id="qty_planned" class="form-control" readonly>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Đã Nhập</label>
                <input type="text" id="qty_already_received" class="form-control" readonly>
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
  document.getElementById('quantity_received').focus();
}
</script>
