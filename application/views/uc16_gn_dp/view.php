<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6>Chi tiết và Điều phối sự cố #<?= $incident->id; ?></h6>
          <div>
            <a href="<?= site_url('uc16_gn_dp'); ?>" class="btn btn-sm btn-secondary">Quay lại</a>
          </div>
        </div>
        <div class="card-body px-5 py-4">
          <div class="row mb-4">
            <div class="col-md-6">
              <h6>Máy</h6>
              <p><?= $incident->machine_name ?? $incident->id_machine; ?></p>
            </div>
            <div class="col-md-6">
              <h6>Trạng thái</h6>
              <p><?= ($incident->status==1)?'Đã hoàn thành':(($incident->status==2)?'Đang báo cáo':'Chưa hoàn thành'); ?></p>
            </div>
          </div>

          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6>Mô tả</h6>
              <p><?= nl2br(htmlspecialchars($incident->incident_description)); ?></p>
            </div>
          </div>

          <!-- Báo cáo từ Technical (Technical Reports) -->
          <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Báo Cáo Sửa Chữa</h6>
            </div>
            <div class="card-body">
              <?php
                // Build a map of user_id => username for quick lookup
                $user_map = [];
                if (!empty($users)) {
                    foreach ($users as $u) {
                        $user_map[$u->user_id] = $u->username;
                    }
                }

                // Filter coordination entries to show technical reports
                $reports = [];
                if (!empty($coordination)) {
                    foreach ($coordination as $c) {
                        if (isset($c->action_type) && $c->action_type === 'assign_technical') {
                            $reports[] = $c;
                        }
                    }
                }

                if (!empty($reports)) : foreach ($reports as $r): ?>
                  <div class="mb-3 border p-3 assignment-item" data-assignee-id="<?= intval($r->assignee_id); ?>" style="background-color: #f9f9f9; border-radius: 4px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <strong><?= htmlspecialchars($user_map[$r->assignee_id] ?? 'N/A'); ?></strong>
                        <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($r->created_at)); ?></small>
                      </div>
                      <span class="badge badge-<?= ($r->status === 'completed') ? 'success' : 'warning'; ?>"><?= ucfirst($r->status ?? 'pending'); ?></span>
                    </div>
                    <div class="mt-2">
                      <strong>Thời gian sửa chữa:</strong>
                      <p class="mb-2"><?= nl2br(htmlspecialchars($r->shift_info)); ?></p>
                    </div>
                    <div>
                      <strong>Chi tiết báo cáo:</strong>
                      <p><?= nl2br(htmlspecialchars($r->notes)); ?></p>
                    </div>
                  </div>
              <?php endforeach; else: ?>
                <div class="alert alert-info">Chưa có báo cáo sửa chữa từ technical</div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($can_assign)): ?>
            <!-- LEADER: Show Coordination Form (id assign is used by Beranda.php anchor) -->
            <div id="assign">
              <?php $this->load->view('uc16_gn_dp/coordination', ['incident' => $incident, 'machines' => $machines]); ?>
            </div>
          <?php else: ?>
            <!-- TECHNICAL: Show Report Form -->
            <?php $this->load->view('uc16_gn_dp/report', ['incident' => $incident]); ?>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
  <script>
    // Toggle fields and filter assignment list
    document.addEventListener('DOMContentLoaded', function() {
      var actionSelect = document.getElementById('action_type');
      var machineWrap = document.getElementById('machine_select_wrap');
      var filterTech = document.getElementById('filter_tech');

      function toggleFields() {
        if (!actionSelect) return;
        var val = actionSelect.value;
        // show machine select only for replace_machine
        if (machineWrap) machineWrap.style.display = (val === 'replace_machine') ? 'block' : 'none';
      }

      if (actionSelect) {
        actionSelect.addEventListener('change', toggleFields);
        toggleFields();
      }

      // Client-side filter of assignment items by selected technician
      if (filterTech) {
        filterTech.addEventListener('change', function() {
          var val = this.value;
          var items = document.querySelectorAll('.assignment-item');
          items.forEach(function(it) {
            var aid = it.getAttribute('data-assignee-id') || '';
            if (!val) {
              it.style.display = '';
            } else {
              if (aid === val) it.style.display = '';
              else it.style.display = 'none';
            }
          });
        });
      }
    });
  </script>
</div>
