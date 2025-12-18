<?php
// Danh sách Kế hoạch cho vai trò Leader - dựa trên layout của BOD
?>
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-8 d-flex align-items-center">
                            <i class="material-icons text-white opacity-10 me-2">calendar_month</i>
                            <h6 class="text-white mb-0">Danh sách Kế hoạch</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-3">
                    <table id="table-plans" class="table align-items-center justify-content-center mb-0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên kế hoạch</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Ngày kết thúc</th>
                                <th class="text-center">Đơn hàng</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($planning)): ?>
                                <?php $i = 1; foreach ($planning as $plan): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= !empty($plan->plan_name) ? $plan->plan_name : ($plan->project_name ?? 'Không tên'); ?></p>
                                            <p class="text-xs text-secondary mb-0">ID: <?= $plan->id_plan; ?></p>
                                        </td>
                                        <td class="text-center"><span class="text-sm font-weight-bold"><?= number_format($plan->qty_target ?? 0); ?></span></td>
                                        <td class="text-center"><span class="text-xs"><?= !empty($plan->end_date) ? date('d/m/Y', strtotime($plan->end_date)) : '-'; ?></span></td>
                                        <td class="text-center text-sm"><?= $plan->project->project_name ?? $plan->project_name ?? $plan->id_project ?? '-'; ?></td>
                                        <td class="text-center text-sm">
                                            <?php if (isset($plan->pl_status) && $plan->pl_status == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success">Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary view-plan" data-plan="<?= htmlspecialchars(json_encode($plan), ENT_QUOTES); ?>">Xem</button>
                                                <a href="<?= site_url('leader/ChangePlanning/' . ($plan->id_plan ?? '')); ?>" class="btn btn-sm btn-outline-secondary">Sửa</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4">Chưa có kế hoạch nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal: Plan Details -->
            <div class="modal fade" id="planDetailModal" tabindex="-1" aria-labelledby="planDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="planDetailModalLabel">Chi tiết kế hoạch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <style>
                                .pd-materials-content {
                                    max-height: 200px;
                                    overflow: auto;
                                    white-space: pre-wrap;
                                    word-break: break-word;
                                    padding: 6px 8px;
                                    border-radius: 4px;
                                    background: #f8f9fa;
                                }
                            </style>
                            <div id="plan-detail-body">
                                <table class="table table-borderless table-sm mb-0">
                                    <tbody>
                                        <tr><th style="width:180px">ID</th><td id="pd-id">-</td></tr>
                                        <tr><th>Tên kế hoạch</th><td id="pd-name">-</td></tr>
                                        <tr><th>Số lượng mục tiêu</th><td id="pd-qty">-</td></tr>
                                        <tr><th>Ngày kết thúc</th><td id="pd-end">-</td></tr>
                                        <tr><th>Ngày bắt đầu</th><td id="pd-start">-</td></tr>
                                        <tr><th>Dây chuyền (lines)</th><td id="pd-machine">-</td></tr>
                                        <tr><th>Số ca đề xuất</th><td id="pd-shifts">-</td></tr>
                                        <tr><th>Trạng thái</th><td id="pd-status">-</td></tr>
                                        <tr><th>Ghi chú</th><td id="pd-note">-</td></tr>
                                        <tr><th>Nguyên vật liệu </th><td id="pd-materials"><div class="pd-materials-content">-</div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                function fmtDate(d) {
                    if (!d) return '-';
                    try {
                        var dt = new Date(d);
                        if (isNaN(dt.getTime())) return d;
                        var dd = String(dt.getDate()).padStart(2,'0');
                        var mm = String(dt.getMonth()+1).padStart(2,'0');
                        var yy = dt.getFullYear();
                        return dd + '/' + mm + '/' + yy;
                    } catch (e) { return d; }
                }

                document.querySelectorAll('.view-plan').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var raw = btn.getAttribute('data-plan');
                        if (!raw) return;
                        var plan = null;
                        try { plan = JSON.parse(raw); } catch (e) { console && console.warn && console.warn('Invalid plan JSON', e); return; }

                        document.getElementById('pd-id').textContent = plan.id_plan || plan.id || '-';
                        document.getElementById('pd-name').textContent = plan.plan_name || plan.project_name || '-';
                        document.getElementById('pd-qty').textContent = (plan.qty_target !== undefined && plan.qty_target !== null) ? Number(plan.qty_target).toLocaleString() : '-';
                        document.getElementById('pd-end').textContent = fmtDate(plan.end_date || plan.delivery_date || '');
                        document.getElementById('pd-start').textContent = fmtDate(plan.start_date || '');

                        var linesVal = '-';
                        try {
                            if (Array.isArray(plan.lines)) {
                                linesVal = plan.lines.join(', ');
                            } else if (typeof plan.lines === 'string') {
                                try {
                                    var decoded = JSON.parse(plan.lines);
                                    if (Array.isArray(decoded)) linesVal = decoded.join(', ');
                                    else linesVal = plan.lines;
                                } catch(e) { linesVal = plan.lines; }
                            } else if (plan.lines) {
                                linesVal = String(plan.lines);
                            }
                        } catch(e) { linesVal = '-'; }
                        document.getElementById('pd-machine').textContent = linesVal || '-';
                        document.getElementById('pd-shifts').textContent = (plan.suggested_shifts !== undefined && plan.suggested_shifts !== null) ? plan.suggested_shifts : '-';
                        var status = '-';
                        if (plan.pl_status !== undefined && plan.pl_status !== null) {
                            status = (plan.pl_status == 1) ? 'Đã duyệt' : 'Chờ duyệt';
                        }
                        document.getElementById('pd-status').textContent = status;
                        document.getElementById('pd-note').textContent = plan.note ? plan.note : '-';

                        var mats = '-';
                        if (plan.materials) {
                            try {
                                if (Array.isArray(plan.materials)) {
                                    mats = plan.materials.map(function(m){
                                        if (typeof m === 'string') return m;
                                        return m.material_name || m.name || (m.id_material || m.id) || JSON.stringify(m);
                                    }).slice(0,10).join('\n');
                                } else if (typeof plan.materials === 'string') {
                                    mats = plan.materials;
                                } else {
                                    mats = JSON.stringify(plan.materials);
                                }
                            } catch (e) { mats = String(plan.materials); }
                        }
                        var pmContainer = document.querySelector('#pd-materials .pd-materials-content');
                        if (pmContainer) pmContainer.textContent = mats;
                        else document.getElementById('pd-materials').textContent = mats;

                        var modalEl = document.getElementById('planDetailModal');
                        if (window.bootstrap && window.bootstrap.Modal) {
                            var m = new bootstrap.Modal(modalEl);
                            m.show();
                        } else {
                            modalEl.classList.add('show');
                            modalEl.style.display = 'block';
                        }
                    });
                });
            });
            </script>
        </div>
    </div>
</div>
