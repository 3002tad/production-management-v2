<?php
// Báo cáo tổng hợp cho Ban Giám Đốc — hiển thị chi tiết khi chọn đơn hàng
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12"><h3>Báo cáo Đơn hàng — Chi tiết</h3></div>
    </div>

    <div class="row mb-3">
        <div class="col-md-8">
            <form id="project-select-form" method="get" class="form-inline">
                <div class="input-group w-100">
                    <select name="id_project" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Chọn đơn hàng --</option>
                        <?php foreach ($projects ?? [] as $pr):
                            $sel = (!empty($selected_project_id) && $selected_project_id == $pr->id_project) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($pr->id_project, ENT_QUOTES) ?>" <?= $sel ?>><?= htmlspecialchars(trim(($pr->project_name ?? '') . ' — ' . ($pr->cust_name ?? '') . ' — ' . ($pr->product_name ?? '')), ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-group-append">
                        <button type="button" id="print-btn" class="btn btn-primary">In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($project)): ?>
        <div class="mt-3">
            <div class="card mb-3 p-3">
                <h5>Tổng quan</h5>
                <div class="row">
                    <div class="col-md-4">
                        <h6>Thông tin Đơn hàng</h6>
                        <p>Mã: <strong><?= htmlspecialchars($project->id_project ?? '', ENT_QUOTES) ?></strong></p>
                        <p>Tên: <strong><?= htmlspecialchars($project->project_name ?? '-', ENT_QUOTES) ?></strong></p>
                        <p>Khách: <strong><?= htmlspecialchars($project->cust_name ?? '-', ENT_QUOTES) ?></strong></p>
                        <p>Sản phẩm: <strong><?= htmlspecialchars($project->product_name ?? ($project->product->product_name ?? '-'), ENT_QUOTES) ?></strong></p>
                        <p>Số lượng yêu cầu: <strong><?= number_format($project->qty_request ?? 0) ?></strong></p>
                        <p>Hạn giao: <strong><?= htmlspecialchars($project->entry_date ?? ($project->delivery_date ?? '-'), ENT_QUOTES) ?></strong></p>
                    </div>
                    <div class="col-md-8">
                        <h6>Tổng thành phẩm</h6>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="h5"><?= number_format($total_finished ?? 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3 p-3">
                <h5>Kế hoạch</h5>
                <?php if (!empty($plan)): ?>
                    <table class="table table-sm mb-0">
                        <tr><th style="width:220px">Tên</th><td><?= htmlspecialchars($plan->plan_name ?? ('Plan#' . ($plan->id_plan ?? '')), ENT_QUOTES) ?></td></tr>
                        <tr><th>Mục tiêu</th><td><?= number_format($plan->qty_target ?? 0) ?></td></tr>
                        <tr><th>Ngày bắt đầu</th><td><?= htmlspecialchars($plan->start_date ?? '-', ENT_QUOTES) ?></td></tr>
                        <tr><th>Ngày kết thúc</th><td><?= htmlspecialchars($plan->finish_date ?? ($plan->end_date ?? '-'), ENT_QUOTES) ?></td></tr>
                        <tr><th>Ghi chú</th><td><?= htmlspecialchars($plan->note ?? '-', ENT_QUOTES) ?></td></tr>
                    </table>
                    <div class="mt-2">
                        <strong>Số ca gợi ý:</strong> <?= number_format((isset($suggested_shifts) && $suggested_shifts !== null) ? $suggested_shifts : (count($plan_shifts ?? []))) ?>
                    </div>
                <?php else: ?>
                    <p>Không có kế hoạch liên kết cho đơn hàng này.</p>
                <?php endif; ?>
            </div>

            <div class="card mb-3 p-3">
                <h5>Kho</h5>
                <p>Tổng nhập kho: <strong><?= number_format($total_received ?? 0) ?></strong></p>
                <p>Tổng xuất kho: <strong><?= number_format($total_issued ?? 0) ?></strong></p>

                <?php if (!empty($receipts)): ?>
                    <h6 class="mt-3">Chi tiết nhập kho</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Ngày</th><th>Mã phiếu</th><th>Số lượng</th><th>Ghi chú</th></tr></thead>
                        <tbody>
                            <?php foreach ($receipts as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r->date ?? $r->received_date ?? '-', ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($r->code ?? $r->receipt_code ?? '-', ENT_QUOTES) ?></td>
                                    <td><?= number_format($r->qty ?? ($r->received_qty ?? 0)) ?></td>
                                    <td><?= htmlspecialchars($r->note ?? '-', ENT_QUOTES) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($issues)): ?>
                    <h6 class="mt-3">Chi tiết xuất kho</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Ngày</th><th>Mã phiếu</th><th>Số lượng</th><th>Ghi chú</th></tr></thead>
                        <tbody>
                            <?php foreach ($issues as $it): ?>
                                <tr>
                                    <td><?= htmlspecialchars($it->date ?? $it->issue_date ?? '-', ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($it->code ?? $it->issue_code ?? '-', ENT_QUOTES) ?></td>
                                    <td><?= number_format($it->qty ?? ($it->issued_qty ?? 0)) ?></td>
                                    <td><?= htmlspecialchars($it->note ?? '-', ENT_QUOTES) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="card mb-3 p-3">
                <h5>Ca & Chốt</h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ca làm việc </h6>
                        <?php if (!empty($plan_shifts)): ?>
                            <table class="table table-sm">
                                <thead><tr><th>Ca</th><th>Ngày</th><th>Giờ Bắt Đầu</th><th>Giờ Kết Thúc</th></tr></thead>
                                <tbody>
                                    <?php foreach ($plan_shifts as $ps): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ps->shift_name ?? ($ps->shift_code ?? ($ps->shift_id ?? ($ps->id_shift ?? '-'))), ENT_QUOTES) ?></td>
                                        <td><?php
                                            $__ps_date = $ps->production_shift->shift_date ?? $ps->shift_date ?? null;
                                            if (empty($__ps_date) && !empty($ps->start_date) && is_string($ps->start_date)) {
                                                $__ps_date = (strpos($ps->start_date, ' ') !== false) ? substr($ps->start_date, 0, 10) : $ps->start_date;
                                            }
                                            echo htmlspecialchars($__ps_date ?? '', ENT_QUOTES);
                                        ?></td>
                                        <td><?php
                                            $__ps_start = $ps->production_shift->start_time ?? $ps->start_time ?? null;
                                            if (empty($__ps_start) && !empty($ps->start_date) && is_string($ps->start_date) && strpos($ps->start_date, ' ') !== false) {
                                                $__ps_start = substr($ps->start_date, 11);
                                            }
                                            echo htmlspecialchars($__ps_start ?? '-', ENT_QUOTES);
                                        ?></td>
                                        <td><?php
                                            $__ps_end = $ps->production_shift->end_time ?? $ps->end_time ?? null;
                                            if (empty($__ps_end) && !empty($ps->end_date) && is_string($ps->end_date) && strpos($ps->end_date, ' ') !== false) {
                                                $__ps_end = substr($ps->end_date, 11);
                                            }
                                            echo htmlspecialchars($__ps_end ?? '-', ENT_QUOTES);
                                        ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Không có ca gợi ý cho kế hoạch này.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Chốt ca liên quan đã được tách ra thành card riêng bên dưới -->
                </div>
            </div>

            <div class="card mb-3 p-3">
                <h5>Chốt ca liên quan</h5>
                <?php if (!empty($shift_closures)): ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Ngày</th>
                                <th class="text-right">Tổng sản xuất</th>
                                <th class="text-right">Đạt chuẩn</th>
                                <th class="text-right">Lỗi</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shift_closures as $sc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sc->closure_code ?? ('#' . ($sc->closure_id ?? '-')), ENT_QUOTES) ?></td>
                                    <td><?= htmlspecialchars($sc->closure_date ?? '-', ENT_QUOTES) ?></td>
                                    <td class="text-right"><?= number_format($sc->total_produced ?? 0) ?></td>
                                    <td class="text-right"><?= number_format($sc->total_good ?? 0) ?></td>
                                    <td class="text-right"><?= number_format($sc->total_defect ?? 0) ?></td>
                                    <td><?= htmlspecialchars(is_string($sc->notes) ? $sc->notes : json_encode($sc->notes), ENT_QUOTES) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Không tìm thấy chốt ca liên quan.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Vui lòng chọn một đơn hàng để xem chi tiết liên quan (kế hoạch, kho, ca).</div>
    <?php endif; ?>

</div>

<script>
    (function(){
        var btn = document.getElementById('print-btn');
        if (!btn) return;
        btn.addEventListener('click', function(e){
            // optional: hide controls before printing
            window.print();
        });
    })();
</script>
