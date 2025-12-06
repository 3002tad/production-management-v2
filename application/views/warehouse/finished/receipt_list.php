<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Danh Sách Phiếu Nhập Thành Phẩm</h5>
          <a href="<?= site_url('warehouse/finished/receipt_form'); ?>" class="btn btn-sm btn-success">
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

          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="table-light">
                <tr>
                  <th>Mã Phiếu</th>
                  <th>Dự Án</th>
                  <th>SL Nhập</th>
                  <th>Người Lập</th>
                  <th>Ngày Tạo</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($receipts)): ?>
                  <?php foreach ($receipts as $receipt): ?>
                    <tr>
                      <td><strong><?= $receipt->receipt_code; ?></strong></td>
                      <td><?= $receipt->project_name ?? 'N/A'; ?></td>
                      <td class="text-center">
                        <span class="badge bg-info"><?= $receipt->quantity_received; ?></span>
                      </td>
                      <td><?= $receipt->created_by_name; ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($receipt->created_date)); ?></td>
                      <td>
                        <?php if ($receipt->status === 'posted'): ?>
                          <span class="badge bg-success">Đã Lưu</span>
                        <?php else: ?>
                          <span class="badge bg-danger">Hủy</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="<?= site_url('warehouse/finished/receipt_view/' . $receipt->id_receipt); ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                          <i class="material-icons align-middle">visibility</i>
                        </a>
                        <?php if ($receipt->status === 'posted'): ?>
                          <a href="<?= site_url('warehouse/finished/receipt_cancel/' . $receipt->id_receipt); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hủy phiếu này?')" title="Hủy phiếu">
                            <i class="material-icons align-middle">delete</i>
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      Không có phiếu nhập nào. <a href="<?= site_url('warehouse/finished/receipt_form'); ?>">Tạo phiếu mới</a>
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
