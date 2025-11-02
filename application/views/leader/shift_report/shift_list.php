<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container-fluid mt-3">
  <div class="card">
    <div class="card-header pb-0 px-3">
      <h6 class="mb-0">Danh sách ca đang chạy</h6>
    </div>
    <div class="card-body pt-3 px-3">
      <?php if (empty($shifts)): ?>
        <div class="alert alert-info">Không có ca đang chạy.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table id="shifts-table" class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ca</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_target'); ?></th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nhân viên</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dây chuyền</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày bắt đầu</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_status'); ?></th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hành động</th>
                    </tr>
                  </thead>
            <tbody>
            <?php foreach ($shifts as $s): ?>
              <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo isset($s['id_shift']) ? $s['id_shift'] : '-'; ?></td>
                <td><?php echo isset($s['qty_target']) ? intval($s['qty_target']) : '-'; ?></td>
                <td><?php echo isset($s['id_staff']) ? $s['id_staff'] : '-'; ?></td>
                <td><?php echo isset($s['machines']) ? htmlspecialchars($s['machines']) : '-'; ?></td>
                <td><?php echo isset($s['start_date']) ? $s['start_date'] : '-'; ?></td>
                <td><?php echo (isset($s['ps_status']) && $s['ps_status']==1) ? '<span class="badge bg-success">Đang chạy</span>' : '<span class="badge bg-secondary">Đã kết thúc</span>'; ?></td>
                <td>
                  <?php
                    // determine first machine id from machine_ids if available
                    $first_mid = null;
                    if (!empty($s['machine_ids'])) {
                      $parts = explode(',', $s['machine_ids']);
                      $first_mid = intval($parts[0]);
                    }
                  ?>
                  <a class="btn btn-sm btn-outline-info" href="<?php echo site_url('leader/shift-report/view/'.$s['id']); ?>">Xem</a>
                  <?php if ($first_mid): ?>
                    <a class="btn btn-sm btn-outline-primary ms-1" href="<?php echo site_url('leader/shift-report/view_machine/'.$s['id'].'/'.$first_mid); ?>">Xem dây chuyền</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // Initialize DataTable when document ready
  document.addEventListener('DOMContentLoaded', function(){
    if (typeof $ !== 'undefined' && $.fn.dataTable) {
      $('#shifts-table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: false
      });
    }
  });
</script>
