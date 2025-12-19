<!-- Danh sách phiếu xuất giao hàng -->
<div class="card h-100">
        <div class="card-header card-header-success py-3 px-4 d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="material-icons align-middle">local_shipping</i>
            Danh Sách Phiếu Xuất
          </h5>
          <button onclick="showDeliveryForm()" class="btn btn-sm btn-success">
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

          <div class="alert alert-info mb-3" style="font-size: 0.875rem;">
            <strong>Tồn kho hiện tại:</strong> 
            <?php 
              $current_stock = 0;
              if (!empty($deliveries_data)) {
                // Lấy tồn kho từ model
                $stock = $this->FinishedIssueModel->getCurrentStock();
                if (!empty($stock)) {
                  $current_stock = $stock->available_quantity ?? 0;
                }
              }
            ?>
            <span class="badge bg-success"><?= $current_stock ?> cái</span>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-hover">
              <thead class="table-light">
                <tr>
                  <th>Mã Phiếu</th>
                  <th>Đơn Hàng</th>
                  <th>SL</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($deliveries_data)): ?>
                  <?php foreach ($deliveries_data as $delivery): ?>
                    <tr>
                      <td><strong><?= isset($delivery->id_issue) ? $delivery->id_issue : 'N/A' ?></strong></td>
                      <td>
                        <small><?= isset($delivery->project_name) ? $delivery->project_name : 'N/A' ?></small>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-info"><?= isset($delivery->quantity_issued) ? $delivery->quantity_issued : 0 ?></span>
                      </td>
                      <td>
                        <?php 
                          $status = isset($delivery->status) ? $delivery->status : 'pending';
                          $badge_class = ($status === 'completed' || $status === 'posted') ? 'badge-success' : (($status === 'cancelled') ? 'badge-danger' : 'badge-warning');
                        ?>
                        <span class="badge <?= $badge_class ?> text-white text-xs"><?= ucfirst($status) ?></span>
                      </td>
                      <td>
                        <button onclick="showDeliveryDetail(<?= isset($delivery->id_issue) ? $delivery->id_issue : 0 ?>)" class="btn btn-sm btn-info" title="Xem chi tiết">
                          <i class="material-icons" style="font-size: 16px;">visibility</i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                      Không có phiếu xuất nào
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
