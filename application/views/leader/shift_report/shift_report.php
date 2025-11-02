<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $__events_json = isset($events_map) ? json_encode($events_map) : json_encode([]); ?>
<script>window.prefetchedEvents = <?= $__events_json ?>;</script>
<div class="container-fluid py-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= site_url('leader/'); ?>">Trang</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Báo cáo sản lượng</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Báo cáo ca: <?= htmlspecialchars($shift_id); ?></h6>
  </nav>

  <div class="card mt-3">
    <div class="card-body p-3">
      <?php if (empty($report)): ?>
        <div class="alert alert-info">Chưa có dữ liệu cho ca này.</div>
      <?php else: ?>
        <div class="d-flex justify-content-end mb-2">
          <button id="sortAsc" class="btn btn-sm btn-outline-secondary me-2">Sắp xếp tăng dần</button>
          <button id="sortDesc" class="btn btn-sm btn-outline-secondary">Sắp xếp giảm dần</button>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Máy</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sản lượng</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mục tiêu</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Thời gian dừng (s)</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cập nhật</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sự kiện</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hành động</th>
              </tr>
            </thead>
            <tbody>
            <?php $i=1; foreach ($report as $r): ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars(isset($r['machine_name']) ? $r['machine_name'] : $r['machine_id']); ?></td>
                <td><?= intval($r['produced_qty']); ?></td>
                <td><?= $r['target_qty'] !== null ? intval($r['target_qty']) : '-'; ?></td>
                <td><?= intval($r['downtime_seconds']); ?></td>
                <td><?= htmlspecialchars($r['status']); ?></td>
                <td><?= htmlspecialchars($r['last_updated_at'] ?? '-'); ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-events" data-shift="<?= $shift_id ?>" data-machine="<?= intval($r['machine_id']) ?>">Xem sự kiện</button>
                </td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="<?= site_url('leader/shift-report/view_machine/'.$shift_id.'/'.intval($r['machine_id'])); ?>">Chi tiết</a>
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
        <h5 class="modal-title" id="eventsModalLabel">Sự kiện máy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="eventsList">Đang tải...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
        eventsList.innerHTML = '<div class="text-muted">Đang tải...</div>';

        // If we have prefetched events from server-side, use them and avoid AJAX.
        try {
          var pref = window.prefetchedEvents && window.prefetchedEvents[machine];
          // If pref is defined (could be empty array), handle accordingly.
          if (typeof pref !== 'undefined') {
            if (!pref || pref.length === 0) {
              eventsList.innerHTML = '<div class="alert alert-info">Không có sự kiện nào.</div>';
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
              eventsList.innerHTML = '<div class="alert alert-warning">Bạn chưa đăng nhập hoặc hết phiên làm việc. Vui lòng đăng nhập lại.</div>';
              showModal();
              throw new Error('unauthorized');
            }
            var ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
              // might be HTML (redirect to login) -> show error
              return res.text().then(function(txt){
                eventsList.innerHTML = '<div class="alert alert-danger">Lỗi server khi tải sự kiện.</div>';
                showModal();
                throw new Error('invalid_response');
              });
            }
            return res.json();
          })
          .then(function(data){
            if (!data || data.length === 0) {
              eventsList.innerHTML = '<div class="alert alert-info">Không có sự kiện nào.</div>';
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
            eventsList.innerHTML = '<div class="alert alert-danger">Lỗi khi tải sự kiện.</div>';
            showModal();
          });
      });
    });
  });
</script>
