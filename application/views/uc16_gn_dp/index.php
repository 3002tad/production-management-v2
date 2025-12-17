<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Danh sách báo cáo sự cố</h6>
          <?php if (isset($user_role) && in_array($user_role, ['leader','line_manager'])): ?>
            <a href="<?= site_url('uc16_gn_dp'); ?>" class="btn btn-sm btn-primary">Lọc: Sự cố mới</a>
          <?php endif; ?>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="dataTableUC16">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Máy</th>
                  <th>Loại</th>
                  <th>Mức độ</th>
                  <th>Trạng thái</th>
                  <th>Ngày tạo</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($incidents)): foreach ($incidents as $incident): ?>
                  <tr>
                    <td><?= $incident->id; ?></td>
                    <td>
                      <?php if (!empty($incident->zone_name)): ?>
                        <small class="text-muted"><?= htmlspecialchars($incident->zone_name); ?></small> &rarr; 
                      <?php endif; ?>
                      <?php if (!empty($incident->line_code)): ?>
                        <span class="badge badge-secondary"><?= htmlspecialchars($incident->line_code); ?></span>
                      <?php endif; ?>
                      <?php if (!empty($incident->machine_code)): ?>
                        &rarr; <strong><?= htmlspecialchars($incident->machine_code); ?></strong>
                      <?php else: ?>
                        <em class="text-muted">(Toàn line)</em>
                      <?php endif; ?>
                    </td>
                    <td><?= $incident->category; ?></td>
                    <td><?= $incident->severity_level; ?></td>
                    <td><?= ($incident->status == 1) ? 'Đã hoàn thành' : (($incident->status==2)?'Đang báo cáo':'Chưa hoàn thành'); ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($incident->created_at)); ?></td>
                    <td>
                      <a href="<?= site_url('uc16_gn_dp/view/' . $incident->id); ?>" class="btn btn-sm btn-info">Chi tiết / Điều phối</a>
                      <?php if (!empty($can_assign)): ?>
                        <a href="<?= site_url('uc16_gn_dp/view/' . $incident->id); ?>#assign" class="btn btn-sm btn-warning">Thông báo sự cố</a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="7" class="text-center">Không có báo cáo sự cố</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('#dataTableUC16').DataTable();
  });
</script>
