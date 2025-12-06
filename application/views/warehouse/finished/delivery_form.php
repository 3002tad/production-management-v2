<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Xuất Kho Thành Phẩm Giao Hàng</h5>
          <a href="<?= site_url('warehouse/finished/delivery'); ?>" class="btn btn-sm btn-secondary">← Quay lại danh sách</a>
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

          <!-- Bảng đối chiếu tồn kho -->
          <div class="alert alert-info mb-4">
            <h6 class="alert-heading">Thông Tin Tồn Kho Hiện Tại</h6>
            <p class="mb-0">
              <strong>Tồn Kho Thành Phẩm Khả Dụng:</strong> 
              <span class="badge bg-success fs-5"><?= $current_stock; ?> cái</span>
            </p>
          </div>

          <form method="post" action="<?= site_url('warehouse/finished/delivery_save'); ?>" class="needs-validation">

            <div class="mb-3">
              <label for="id_project" class="form-label">Chọn Đơn Hàng / Dự Án <span class="text-danger">*</span></label>
              <select id="id_project" name="id_project" class="form-select" required onchange="updateProjectInfo()">
                <option value="">-- Chọn đơn hàng --</option>
                <?php foreach ($projects as $proj): ?>
                  <option value="<?= $proj->id_project; ?>"
                          data-qty-req="<?= $proj->qty_request; ?>"
                          data-qty-avail="<?= $proj->qty_available; ?>"
                          data-qty-issued="<?= $proj->qty_already_issued; ?>"
                          data-qty-remaining="<?= $proj->qty_remaining; ?>"
                          data-name="<?= $proj->project_name; ?>">
                    <?= $proj->project_name; ?> (Còn: <?= $proj->qty_remaining; ?>/<?= $proj->qty_request; ?> cái)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="row mb-4">
              <div class="col-md-3">
                <label class="form-label">Yêu Cầu</label>
                <div class="input-group">
                  <input type="text" id="qty_request" class="form-control fw-bold" readonly>
                  <span class="input-group-text">cái</span>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">Đã Giao</label>
                <div class="input-group">
                  <input type="text" id="qty_issued" class="form-control fw-bold" readonly>
                  <span class="input-group-text">cái</span>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">Còn Lại</label>
                <div class="input-group">
                  <input type="text" id="qty_remaining" class="form-control fw-bold text-success" readonly>
                  <span class="input-group-text">cái</span>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">Tồn Kho</label>
                <div class="input-group">
                  <input type="text" id="qty_avail" class="form-control fw-bold text-info" readonly value="<?= $current_stock; ?>">
                  <span class="input-group-text">cái</span>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="quantity_issued" class="form-label">Số Lượng Xuất <span class="text-danger">*</span></label>
              <input type="number" id="quantity_issued" name="quantity_issued" class="form-control" min="1" required placeholder="Nhập số lượng xuất">
              <small class="text-muted">Nhập số lượng thực tế giao (tối đa = tồn kho hiện tại)</small>
              <div id="stock-warning" class="alert alert-warning mt-2" style="display: none;">
                ⚠️ Không đủ hàng - sẽ giao một phần
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Ghi Chú</label>
              <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Ghi chú (tùy chọn)"></textarea>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="material-icons align-middle">save</i> Xuất & Lưu Phiếu
              </button>
              <a href="<?= site_url('warehouse/finished/delivery'); ?>" class="btn btn-secondary">
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
function updateProjectInfo() {
  const select = document.getElementById('id_project');
  const option = select.options[select.selectedIndex];
  const qtyReq = parseInt(option.getAttribute('data-qty-req')) || 0;
  const qtyAvail = parseInt(option.getAttribute('data-qty-avail')) || 0;
  const qtyIssued = parseInt(option.getAttribute('data-qty-issued')) || 0;
  const qtyRemaining = parseInt(option.getAttribute('data-qty-remaining')) || 0;
  const currentStock = <?= $current_stock; ?>;

  document.getElementById('qty_request').value = qtyReq;
  document.getElementById('qty_issued').value = qtyIssued;
  document.getElementById('qty_remaining').value = qtyRemaining;
  document.getElementById('quantity_issued').value = Math.min(qtyRemaining, currentStock);
  document.getElementById('quantity_issued').max = currentStock;
  document.getElementById('quantity_issued').focus();

  // Show warning if not enough stock
  if (qtyRemaining > currentStock) {
    document.getElementById('stock-warning').style.display = 'block';
  } else {
    document.getElementById('stock-warning').style.display = 'none';
  }
}

// Validate quantity on input
document.getElementById('quantity_issued')?.addEventListener('input', function() {
  const max = <?= $current_stock; ?>;
  const warning = document.getElementById('stock-warning');
  if (parseInt(this.value) > max) {
    warning.style.display = 'block';
  }
});
</script>