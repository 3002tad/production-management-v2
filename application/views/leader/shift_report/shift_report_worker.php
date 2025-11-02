<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container-fluid py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Trang</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Báo cáo máy</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Báo cáo máy trong ca</h6>
  </nav>

  <div class="card mt-3">
    <div class="card-body p-3">
      <?php if (empty($row)): ?>
        <div class="alert alert-info">Chưa có dữ liệu cho máy này trong ca.</div>
      <?php else: ?>
        <table class="table align-items-center mb-0">
          <tr><th>Máy</th><td><?= htmlspecialchars(isset($row['machine_name']) ? $row['machine_name'] : $row['machine_id']); ?></td></tr>
          <tr><th>Sản lượng</th><td><?= intval($row['produced_qty']); ?></td></tr>
          <tr><th>Mục tiêu</th><td><?= $row['target_qty'] !== null ? intval($row['target_qty']) : '-'; ?></td></tr>
          <tr><th>Thời gian dừng (s)</th><td><?= intval($row['downtime_seconds']); ?></td></tr>
          <tr><th>Cập nhật lần cuối</th><td><?= htmlspecialchars($row['last_updated_at'] ?? '-'); ?></td></tr>
        </table>
        <div class="card mt-3">
          <div class="card-header"><strong>Sự kiện</strong></div>
          <div class="card-body">
            <?php if (isset($events) && is_array($events)): ?>
              <?php if (count($events) === 0): ?>
                <div class="alert alert-info">Không có sự kiện nào.</div>
              <?php else: ?>
                <ul class="list-group">
                  <?php foreach ($events as $ev): ?>
                    <li class="list-group-item">
                      <div><strong><?= htmlspecialchars($ev['event_type'] ?? '') ?></strong>
                        <small class="text-muted">(<?= htmlspecialchars($ev['ts'] ?? '') ?>)</small>
                      </div>
                      <div><?= nl2br(htmlspecialchars($ev['detail'] ?? '')) ?></div>
                      <div class="text-muted small">Người tạo: <?= htmlspecialchars($ev['created_by'] ?? '') ?></div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            <?php else: ?>
              <div class="alert alert-warning">Không thể tải sự kiện.</div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
