<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container-fluid py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Trang</a></li>
  <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('sr_worker_page'); ?></li>
    </ol>
  <h6 class="font-weight-bolder mb-0"><?= lang('sr_worker_header'); ?></h6>
  </nav>

  <div class="card mt-3">
    <div class="card-body p-3">
        <?php
          // normalize $row to array if controller passed object or array
          $r = null;
          if (isset($row)) {
              if (is_array($row)) $r = $row;
              elseif (is_object($row)) $r = (array)$row;
          }
        ?>
        <?php if (empty($r)): ?>
          <div class="alert alert-info"><?= lang('sr_no_data_machine'); ?></div>
        <?php else: ?>
          <table class="table align-items-center mb-0">
            <tr><th><?= lang('sr_table_machine'); ?></th><td><?= htmlspecialchars(isset($r['machine_name']) ? $r['machine_name'] : ($r['machine_id'] ?? '-')); ?></td></tr>
            <tr><th><?= lang('sr_table_produced'); ?></th><td><?= isset($r['produced_qty']) ? intval($r['produced_qty']) : 0; ?></td></tr>
            <tr><th><?= lang('sr_table_target'); ?></th><td><?= array_key_exists('target_qty', $r) && $r['target_qty'] !== null ? intval($r['target_qty']) : '-'; ?></td></tr>
            <tr><th><?= lang('sr_table_downtime'); ?></th><td><?= isset($r['downtime_seconds']) ? intval($r['downtime_seconds']) : 0; ?></td></tr>
            <tr><th><?= lang('sr_table_last_update'); ?></th><td><?= htmlspecialchars($r['last_updated_at'] ?? '-'); ?></td></tr>
          </table>
        <div class="card mt-3">
          <div class="card-header"><strong><?= lang('modal_events_title'); ?></strong></div>
          <div class="card-body">
            <?php
              // normalize events: may be array, object, or prefetched map keyed by machine
              $evs = [];
              if (isset($events)) {
                if (is_array($events)) $evs = $events;
                elseif (is_object($events)) $evs = (array)$events;
              }
            ?>
            <?php if (is_array($evs)): ?>
              <?php if (count($evs) === 0): ?>
                <div class="alert alert-info"><?= lang('msg_no_events'); ?></div>
              <?php else: ?>
                <ul class="list-group">
                  <?php foreach ($evs as $ev): ?>
                    <?php $ev = is_array($ev) ? $ev : (is_object($ev) ? (array)$ev : []); ?>
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
              <div class="alert alert-warning"><?= lang('msg_error_loading_events'); ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
