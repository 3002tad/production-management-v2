<!-- Danh sách phiếu nhập thành phẩm -->
<div class="card h-100">
  <div class="card-header card-header-info py-3 px-4 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="material-icons align-middle">receipt</i>
      Danh Sách Phiếu Nhập
    </h5>
    <button onclick="showReceiptForm()" class="btn btn-sm btn-success">
      <i class="material-icons align-middle">add_circle</i> Tạo Phiếu
    </button>
  </div>
  <div class="card-body pt-3 px-4" style="max-height: 600px; overflow-y: auto;">

    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> <?= $this->session->flashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-sm table-hover">
        <thead class="table-light">
          <tr>
            <th>Mã Phiếu</th>
            <th>Dự Án</th>
            <th>SL</th>
            <th>Trạng Thái</th>
            <th>Hành Động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($receipts_data)): ?>
            <?php foreach ($receipts_data as $receipt): ?>
              <tr>
                <td><strong><?= isset($receipt->id_receipt) ? $receipt->id_receipt : 'N/A' ?></strong></td>
                <td>
                  <small><?= isset($receipt->project_name) ? $receipt->project_name : 'N/A' ?></small>
                </td>
                <td class="text-center">
                  <span class="badge bg-info"><?= isset($receipt->quantity_received) ? $receipt->quantity_received : 0 ?></span>
                </td>
                <td>
                  <?php 
                    $status = isset($receipt->status) ? $receipt->status : 'pending';
                    $badge_class = ($status === 'completed' || $status === 'posted') ? 'badge-success' : (($status === 'cancelled') ? 'badge-danger' : 'badge-warning');
                  ?>
                  <span class="badge <?= $badge_class ?> text-white text-xs"><?= ucfirst($status) ?></span>
                </td>
                <td>
                  <button onclick="showReceiptDetail(<?= isset($receipt->id_receipt) ? $receipt->id_receipt : 0 ?>)" class="btn btn-sm btn-info" title="Xem chi tiết">
                    <i class="material-icons" style="font-size: 16px;">visibility</i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-3">
                Không có phiếu nhập nào
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
