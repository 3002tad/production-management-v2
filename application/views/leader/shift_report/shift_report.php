<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $__events_json = isset($events_map) ? json_encode($events_map) : json_encode([]); ?>
<script>window.prefetchedEvents = <?= $__events_json ?>;</script>
<div class="container-fluid py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Trang</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('label_shift_report_page'); ?></li>
    </ol>
  <h6 class="font-weight-bolder mb-0"><?= lang('label_shift_report_header'); ?> <?= htmlspecialchars(isset($shift_label) && $shift_label ? $shift_label : $shift_id); ?></h6>
  </nav>

  <div class="card mt-3">
    <div class="card-body p-3">
      <?php if (empty($report)): ?>
        <div class="alert alert-info"><?= lang('msg_no_data_for_shift'); ?></div>
      <?php else: ?>
        <div class="d-flex justify-content-end mb-2">
          <button id="sortAsc" class="btn btn-sm btn-outline-secondary me-2"><?= lang('btn_sort_asc'); ?></button>
          <button id="sortDesc" class="btn btn-sm btn-outline-secondary"><?= lang('btn_sort_desc'); ?></button>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_machine'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_produced'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_target'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_downtime'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_status'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_last_update'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_events'); ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"><?= lang('sr_table_actions'); ?></th>
              </tr>
            </thead>
            <tbody>
            <?php $i=1; foreach ($report as $r_raw): 
                // normalize record: allow object or array callers
                $r = is_array($r_raw) ? $r_raw : (is_object($r_raw) ? (array)$r_raw : []);
                $machine_name = isset($r['machine_name']) ? $r['machine_name'] : (isset($r['machine_id']) ? $r['machine_id'] : '-');
                $produced = isset($r['produced_qty']) ? intval($r['produced_qty']) : 0;
                $target = array_key_exists('target_qty', $r) && $r['target_qty'] !== null ? intval($r['target_qty']) : '-';
                $downtime = isset($r['downtime_seconds']) ? intval($r['downtime_seconds']) : 0;
                $status = isset($r['status']) ? $r['status'] : '-';
                $last = isset($r['last_updated_at']) ? $r['last_updated_at'] : '-';
                $mid = isset($r['machine_id']) ? intval($r['machine_id']) : '';
            ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($machine_name); ?></td>
                <td><?= $produced; ?></td>
                <td><?= $target; ?></td>
                <td><?= $downtime; ?></td>
                <td><?= htmlspecialchars($status); ?></td>
                <td><?= htmlspecialchars($last); ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-events" data-shift="<?= $shift_id ?>" data-machine="<?= $mid ?>"><?= lang('btn_view_events'); ?></button>
                </td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="<?= site_url('leader/shift-report/view_machine/'.$shift_id.'/'.$mid); ?>"><?= lang('btn_detail'); ?></a>
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

<!-- Events modal -->
<div class="modal fade" id="eventsModal" tabindex="-1" aria-labelledby="eventsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventsModalLabel"><?= lang('modal_events_title'); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="eventsList"><?= lang('text_loading'); ?></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('btn_close'); ?></button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
  // helper to show modal compatible with Bootstrap v4/v5
    function showModal() {
      var modalEl = document.getElementById('eventsModal');
      if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var m = new bootstrap.Modal(modalEl);
        m.show();
        return;
      }
      // fallback to jQuery modal if available (Bootstrap 4)
      if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
        $('#eventsModal').modal('show');
        return;
      }
      // last resort: make modal visible
      modalEl.style.display = 'block';
    }

    // Sort table rows by produced_qty column (index 2) asc/desc
    function sortTableByProduced(order) {
      var table = document.querySelector('.table-responsive table');
      if (!table) return;
      var tbody = table.querySelector('tbody');
      var rows = Array.from(tbody.querySelectorAll('tr'));
      rows.sort(function(a,b){
        var aVal = parseInt(a.cells[2].textContent.trim()) || 0;
        var bVal = parseInt(b.cells[2].textContent.trim()) || 0;
        return order === 'asc' ? aVal - bVal : bVal - aVal;
      });
      rows.forEach(function(r, i){
        // update index (first column)
        if (r.cells && r.cells[0]) r.cells[0].textContent = (i+1);
        tbody.appendChild(r);
      });
    }

    var btnAsc = document.getElementById('sortAsc');
    var btnDesc = document.getElementById('sortDesc');
    if (btnAsc) btnAsc.addEventListener('click', function(){ sortTableByProduced('asc'); });
    if (btnDesc) btnDesc.addEventListener('click', function(){ sortTableByProduced('desc'); });

    // event button click -> load events via API and show modal
    document.querySelectorAll('.btn-events').forEach(function(btn){
      btn.addEventListener('click', function(e){
        var shift = this.getAttribute('data-shift');
        var machine = this.getAttribute('data-machine');
        var url = '<?= site_url('leader/shift-report/api/get_machine_events') ?>/' + shift + '/' + machine;
        var eventsList = document.getElementById('eventsList');
  eventsList.innerHTML = '<div class="text-muted"><?= lang('text_loading'); ?></div>';

        // If we have prefetched events from server-side, use them and avoid AJAX.
        try {
          var pref = window.prefetchedEvents && window.prefetchedEvents[machine];
          // If pref is defined (could be empty array), handle accordingly.
            if (typeof pref !== 'undefined') {
            if (!pref || pref.length === 0) {
              eventsList.innerHTML = '<div class="alert alert-info"><?= lang('msg_no_events'); ?></div>';
              showModal();
              return;
            }
            var html = '<ul class="list-group">';
            pref.forEach(function(ev){
              html += '<li class="list-group-item">';
              html += '<strong>'+ (ev.event_type || '') +'</strong> <small class="text-muted">('+ (ev.ts || '') +')</small><div>'+ (ev.detail || '') +'</div>';
              html += '</li>';
            });
            html += '</ul>';
            eventsList.innerHTML = html;
            showModal();
            return;
          }
        } catch (e) {
          // if prefetched data can't be used, fall back to AJAX
        }

        fetch(url, { credentials: 'same-origin' })
          .then(function(res){
            if (res.status === 401) {
              eventsList.innerHTML = '<div class="alert alert-warning"><?= lang('msg_not_logged_in'); ?></div>';
              showModal();
              throw new Error('unauthorized');
            }
            var ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
              // might be HTML (redirect to login) -> show error
              return res.text().then(function(txt){
                eventsList.innerHTML = '<div class="alert alert-danger"><?= lang('msg_server_error_loading_events'); ?></div>';
                showModal();
                throw new Error('invalid_response');
              });
            }
            return res.json();
          })
          .then(function(data){
            if (!data || data.length === 0) {
              eventsList.innerHTML = '<div class="alert alert-info"><?= lang('msg_no_events'); ?></div>';
              showModal();
              return;
            }
            var html = '<ul class="list-group">';
            data.forEach(function(ev){
              html += '<li class="list-group-item">';
              html += '<strong>'+ (ev.event_type || '') +'</strong> <small class="text-muted">('+ (ev.ts || '') +')</small><div>'+ (ev.detail || '') +'</div>';
              html += '</li>';
            });
            html += '</ul>';
            eventsList.innerHTML = html;
            showModal();
          })
          .catch(function(err){
            if (err.message === 'unauthorized' || err.message === 'invalid_response') return;
            eventsList.innerHTML = '<div class="alert alert-danger"><?= lang('msg_error_loading_events'); ?></div>';
            showModal();
          });
      });
    });
  });
</script>
