<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Leader Coordination & Dispatch Form -->
<!-- This form is shown ONLY to leader/line_manager users -->
<!-- Submitting this WILL change incident status to in-progress (2), hiding it from dashboard new incidents -->

<div class="card mb-4" id="coordination-form-wrapper">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Xác Nhận Điều Phối Hành Động (Leader)</h6>
  </div>
  <div class="card-body">
    <form method="post" action="<?= site_url('uc16_gn_dp/confirm_dispatch'); ?>">
      <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
      
      <div class="row mb-3">
        <div class="col-md-4">
          <label>Loại hành động</label>
          <select id="action_type" name="action_type" class="form-control" required>
            <option value="">-- Chọn hành động --</option>
            <option value="replace_machine">Đổi máy</option>
            <option value="adjust_shift">Điều chỉnh lịch ca</option>
            <option value="overtime">Tăng ca</option>
          </select>
        </div>
        <div class="col-md-4" id="dummy_wrap">
          <!-- placeholder column to keep layout aligned when machine select hidden -->
        </div>
        <div class="col-md-4" id="machine_select_wrap" style="display:none;">
          <label>Mã máy (chọn máy cần đổi)</label>
          <select name="machine_id" class="form-control">
            <option value="">-- Chọn máy --</option>
            <?php if (!empty($machines)): foreach ($machines as $m): ?>
              <option value="<?= htmlspecialchars($m->id_machine); ?>">
                <?= htmlspecialchars($m->machine_name . ' (' . $m->id_machine . ')'); ?>
              </option>
            <?php endforeach; endif; ?>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <label>Ghi chú điều phối</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Nhập ghi chú hành động điều phối..."></textarea>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-warning">
          <i class="fas fa-check-circle"></i> Xác Nhận Điều Phối
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Mark completed (separate form) -->
<div class="card mb-4">
  <div class="card-header bg-light">
    <h6 class="mb-0">Đánh Dấu Hoàn Tất</h6>
  </div>
  <div class="card-body">
    <form method="post" action="<?= site_url('uc16_gn_dp/mark_completed'); ?>">
      <input type="hidden" name="incident_id" value="<?= $incident->id; ?>">
      <div class="row">
        <div class="col-md-10">
          <input type="text" name="notes" class="form-control" placeholder="Ghi chú hoàn tất (tùy chọn)">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-success w-100">
            <i class="fas fa-check"></i> Hoàn Tất
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var actionSelect = document.getElementById('action_type');
    var machineWrap = document.getElementById('machine_select_wrap');

    function toggleFields() {
      if (!actionSelect) return;
      var val = actionSelect.value;
      if (machineWrap) machineWrap.style.display = (val === 'replace_machine') ? 'block' : 'none';
    }

    if (actionSelect) {
      actionSelect.addEventListener('change', toggleFields);
      toggleFields();
    }
  });
</script>
