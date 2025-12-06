<div class="container-fluid py-4">
  <h3>Nhập kho thành phẩm</h3>

  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
  <?php endif; ?>

  <form action="<?= site_url('warehouse/finished_receipt_save'); ?>" method="post">
    <div class="mb-3">
      <label for="id_finished_report" class="form-label">Chọn lô/phiên (QC Passed)</label>
      <select name="id_finished_report" id="id_finished_report" class="form-control" required>
        <option value="">-- Chọn --</option>
        <?php foreach($candidates as $c): ?>
          <option value="<?= $c->id_finished ?? $c->id; ?>">
            <?= $c->code ?? ('Batch '.$c->id_finished); ?> - <?= $c->project_name ?? ''; ?>
            (<?= $c->qty_passed ?? $c->total_finished; ?> sản phẩm)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="quantity_received" class="form-label">Số lượng nhập</label>
      <input type="number" name="quantity_received" id="quantity_received" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
      <label for="notes" class="form-label">Ghi chú</label>
      <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="<?= site_url('warehouse/finished'); ?>" class="btn btn-secondary">Hủy</a>
  </form>
</div>