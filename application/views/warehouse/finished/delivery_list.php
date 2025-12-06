<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Danh Sách Phiếu Xuất Giao Hàng Thành Phẩm</h5>
            <small class="text-muted">Tồn Kho Hiện Tại: <strong class="badge bg-info"><?= $total_stock; ?> cái</strong></small>
          </div>
          <a href="<?= site_url('warehouse/finished/delivery_form'); ?>" class="btn btn-sm btn-success">
            <i class="material-icons align-middle">add</i> Tạo Phiếu Xuất
          </a>
        </div>
        <div class="card-body">

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Thành công!</strong> <?= $this->session->flashdata('success'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="table-light">
                <tr>
                  <th>Mã Phiếu</th>
                  <th>Đơn Hàng</th>
                  <th>Yêu Cầu</th>
                  <th>Xuất</th>
                  <th>Người Lập</th>
                  <th>Ngày Tạo</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($issues)): ?>
                  <?php foreach ($issues as $issue): ?>
                    <tr>
                      <td><strong><?= $issue->issue_code; ?></strong></td>
                      <td><?= $issue->project_name ?? 'N/A'; ?></td>
                      <td class="text-center">
                        <span class="badge bg-secondary"><?= $issue->quantity_requested; ?></span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-warning"><?= $issue->quantity_issued; ?></span>
                      </td>
                      <td><?= $issue->created_by_name; ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($issue->created_date)); ?></td>
                      <td>
                        <?php 
                          $status_class = match($issue->status) {
                            'full' => 'bg-success',
                            'partial' => 'bg-warning',
                            'cancelled' => 'bg-danger',
                            default => 'bg-secondary'
                          };
                          $status_text = match($issue->status) {
                            'full' => 'Giao Đủ',
                            'partial' => 'Giao Một Phần',
                            'cancelled' => 'Hủy',
                            default => 'Không Xác Định'
                          };
                        ?>
                        <span class="badge <?= $status_class; ?>"><?= $status_text; ?></span>
                      </td>
                      <td>
                        <a href="<?= site_url('warehouse/finished/delivery_view/' . $issue->id_issue); ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                          <i class="material-icons align-middle">visibility</i>
                        </a>
                        <?php if ($issue->status !== 'cancelled'): ?>
                          <a href="<?= site_url('warehouse/finished/delivery_cancel/' . $issue->id_issue); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hủy phiếu này?')" title="Hủy phiếu">
                            <i class="material-icons align-middle">delete</i>
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      Không có phiếu xuất nào. <a href="<?= site_url('warehouse/finished/delivery_form'); ?>">Tạo phiếu mới</a>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <?php if ($total > $limit): ?>
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ceil($total / $limit); $i++): ?>
                  <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?= site_url('warehouse/finished/delivery/' . $i); ?>"><?= $i; ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
