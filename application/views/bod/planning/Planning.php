<?php
// Trang danh sách đơn hàng giống Project.php nhưng có nút 'Lập kế hoạch' cho mỗi đơn
?>
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <div class="row px-3">
                        <div class="col-8 d-flex align-items-center">
                            <i class="material-icons text-white opacity-10 me-2">task</i>
                            <h6 class="text-white mb-0">Danh sách Đơn hàng (Lập kế hoạch)</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= site_url('BOD/plans'); ?>" 
                               class="btn bg-gradient-light mb-0">
                                <i class="material-icons opacity-10">view_list</i>
                                Xem danh sách kế hoạch
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-3">
                    <div class="mb-2"><small class="text-muted">Lưu ý: các đơn đã có kế hoạch sẽ không hiển thị trong danh sách này.</small></div>
                    <table id="table" class="table align-items-center justify-content-center mb-0">
                        <thead>
                                <tr>
                                <th>STT</th>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đường kính</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Hạn giao</th>
                                <th class="text-center">Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data)): ?>
                                <?php $i = 1; ?>
                                <?php foreach (array_reverse($data) as $order): ?>
                                    <?php if (!empty($order->id_plan)): // đã có planning, ẩn khỏi danh sách ?>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $order->project_name; ?></p>
                                            <p class="text-xs text-secondary mb-0">ID: <?= $order->id_project; ?></p>
                                        </td>
                                        <td><span class="text-sm font-weight-bold"><?= $order->cust_name; ?></span></td>
                                        <td><span class="text-sm"><?= $order->product_name; ?></span></td>
                                        <td class="text-center"><span class="badge badge-sm bg-gradient-secondary"><?= $order->diameter; ?> mm</span></td>
                                        <td class="text-center"><span class="text-sm font-weight-bold"><?= number_format($order->qty_request); ?></span> <small class="text-muted"> chiếc</small></td>
                                        <td class="text-center"><span class="text-xs"><?= date('d/m/Y', strtotime($order->entry_date)); ?></span></td>
                                        <td class="text-center text-sm">
                                            <?php if ($order->pr_status == 1): ?>
                                                <span class="badge badge-sm bg-gradient-success">Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge badge-sm bg-gradient-warning">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Nguy cơ column removed per request -->
                                        <td>
                                            <!-- Chỉ giữ nút Lập kế hoạch theo yêu cầu -->
                                            <a href="<?= site_url('BOD/createPlan') . '?project_id=' . $order->id_project; ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Lập kế hoạch cho đơn này">
                                                <i class="material-icons">playlist_add</i> Lập kế hoạch
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr><td colspan="9" class="text-center py-4">Chưa có đơn hàng nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
