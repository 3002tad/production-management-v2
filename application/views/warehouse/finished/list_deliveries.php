<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Danh Sách Phiếu Xuất Giao Hàng Thành Phẩm</h5>
          <a href="<?= site_url('warehouse/finished/delivery/form'); ?>" class="btn btn-sm btn-success">
            <i class="material-icons align-middle">add</i> Tạo Phiếu Mới
          </a>
        </div>
        <div class="card-body">

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Thành công!</strong> <?= $this->session->flashdata('success'); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div class="alert alert-info mb-4">
            <strong>Tồn Kho Thành Phẩm Hiện Tại:</strong> 
            <span class="badge bg-info fs-5"><?= $total_stock; ?> cái</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="table-light">
                <tr>
                  <th>Mã Phiếu</th>
                  <th>Đơn Hàng</th>
                  <th>SL Yêu Cầu</th>
                  <th>SL Xuất</th>
                  <th>Trạng Thái</th>
                  <th>Người Lập</th>
                  <th>Ngày Tạo</th>
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
                        <span class="badge bg-primary"><?= $issue->quantity_requested; ?></span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-success"><?= $issue->quantity_issued; ?></span>
                      </td>
                      <td>
                        <?php if ($issue->status === 'full'): ?>
                          <span class="badge bg-success">Giao Đủ</span>
                        <?php elseif ($issue->status === 'partial'): ?>
                          <span class="badge bg-warning text-dark">Giao Một Phần</span>
                        <?php else: ?>
                          <span class="badge bg-danger">Hủy</span>
                        <?php endif; ?>
                      </td>
                      <td><?= $issue->created_by_name; ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($issue->created_date)); ?></td>
                      <td>
                        <a href="<?= site_url('warehouse/finished/delivery/view/' . $issue->id_issue); ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                          <i class="material-icons align-middle">visibility</i>
                        </a>
                        <?php if ($issue->status !== 'cancelled'): ?>
                          <a href="<?= site_url('warehouse/finished/delivery/cancel/' . $issue->id_issue); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hủy phiếu này?')" title="Hủy phiếu">
                            <i class="material-icons align-middle">delete</i>
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      Không có phiếu xuất nào. <a href="<?= site_url('warehouse/finished/delivery/form'); ?>">Tạo phiếu mới</a>
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
                  <li class="page-item <?= ($this->input->get('page', true) == $i || (!$this->input->get('page') && $i == 1)) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
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