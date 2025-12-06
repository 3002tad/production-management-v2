<?php
// Danh sách Kế hoạch - Hiển thị plan_name, qty_target, end_date, pl_status
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
                        <div class="col-4 text-end">
                            <a href="<?= site_url('BOD/planning'); ?>" class="btn bg-gradient-light mb-0">
                                <i class="material-icons opacity-10">arrow_back</i>
                                Quay lại
                            </a>
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
                                <th class="text-center">Hạn giao</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $i = 1; foreach ($data as $plan): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= !empty($plan->plan_name) ? $plan->plan_name : ($plan->project_name ?? 'Không tên'); ?></p>
                                            <p class="text-xs text-secondary mb-0">ID: <?= $plan->id_plan; ?></p>
                                        </td>
                                        <td class="text-center"><span class="text-sm font-weight-bold"><?= number_format($plan->qty_target ?? 0); ?></span></td>
                                        <td class="text-center"><span class="text-xs"><?= !empty($plan->end_date) ? date('d/m/Y', strtotime($plan->end_date)) : '-'; ?></span></td>
                                        <td class="text-center text-sm">
                                            <?php if (isset($plan->pl_status) && $plan->pl_status == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success">Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4">Chưa có kế hoạch nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
