<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  Order Detail View - Chi tiết đơn hàng                                       ║
║  Material Design 3.0 với thông tin đầy đủ                                    ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
<!-- Order Info Card -->
    <div class="col-md-6">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-center mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 24px; vertical-align: middle;">shopping_cart</i>
                        Thông tin Đơn hàng
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <!-- Order Icon -->
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xxl bg-gradient-success">
                            <span class="text-white" style="font-size: 36px; font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 48px;">receipt_long</i>
                        </span>
                    </div>
<h5 class="mt-3 mb-0" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($order->project_name); ?>
                    </h5>
                    <p class="text-muted text-sm mb-2" style="font-family: 'Poppins', sans-serif;">
                        Mã: <?= $order->id_project; ?>
                    </p>
                    
                    <!-- Status Badge -->
                    <?php 
                    $status_colors = [
                        'Chờ duyệt' => 'secondary',
                        'Đã duyệt' => 'info',
                        'Đang sản xuất' => 'warning',
                        'Hoàn thành' => 'success',
                        'Hủy' => 'danger'
                    ];
                    $status_color = $status_colors[$order->status_text] ?? 'secondary';
                    ?>
                    <span class="badge badge-sm bg-gradient-<?= $status_color; ?>" style="font-family: 'Poppins', sans-serif;">
                        <?= $order->status_text; ?>
                    </span>
                </div>

                <hr class="horizontal dark my-3">

                <!-- Order Details -->
                <h6 style="font-family: 'Poppins', sans-serif;">
                    <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">info</i>
                    Chi tiết
                </h6>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                            <div class="d-flex align-items-center">
                            <i class="material-icons-round text-primary me-2">person</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Khách hàng</small>
                                <p class="text-sm mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= htmlspecialchars($order->cust_name); ?>
                                </p>
</div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-success me-2">inventory_2</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Sản phẩm</small>
                                <p class="text-sm mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= htmlspecialchars($order->product_name); ?>
                                    <span class="badge badge-sm bg-gradient-secondary ms-1"><?= $order->diameter_display; ?></span>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-warning me-2">production_quantity_limits</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Số lượng</small>
                                <p class="text-sm mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= number_format($order->qty_request); ?> cái
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-danger me-2">event</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Deadline</small>
                                <p class="text-sm mb-0 font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                    <?= date('d/m/Y', strtotime($order->entry_date)); ?>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <?php if (!empty($order->customer_request)): ?>
                        <li class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <i class="material-icons-round text-info me-2">comment</i>
                                <div>
                                    <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Yêu cầu</small>
                                    <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                        <?= nl2br(htmlspecialchars($order->customer_request)); ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                            <?php endif; ?>
</ul>

                <!-- Metadata -->
                <hr class="horizontal dark my-3">
                <small class="text-muted d-block" style="font-family: 'Poppins', sans-serif;">
                    <i class="material-icons-round" style="font-size: 12px;">schedule</i>
                    Tạo: <?= date('d/m/Y H:i', strtotime($order->created_at)); ?>
                </small>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="<?= site_url('BOD/project/updateproject/' . $order->id_project); ?>" 
                       class="btn btn-sm bg-gradient-warning w-100 mb-2"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">edit</i>
                        Chỉnh sửa
                    </a>
                    <button type="button" 
                            class="btn btn-sm bg-gradient-info w-100 mb-2" 
                            id="refresh_analysis_btn"
                            style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">refresh</i>
                        Cập nhật phân tích
                    </button>
                    <a href="<?= site_url('BOD/project'); ?>" 
                       class="btn btn-sm btn-outline-secondary w-100"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">arrow_back</i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Analysis Card -->
    <div class="col-md-6">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <?php 
                    // Standardized warning name mapping
                    $warning_name_mapping = [
                        'product_not_found' => 'Sản phẩm không tồn tại',
                        'bom_missing' => 'BOM thiếu NVL',
                        'new_material_shortage' => 'NVL mới cần nhập',
                        'material_shortage' => 'NVL không đủ',
                        'deadline_overdue' => 'Quá hạn',
                        'deadline_too_close' => 'Gần deadline',
                        'low_stock_warning' => 'Cần nhập thêm hàng',
                        'stock_available' => 'Có sẵn kho',
                        'capacity_overload' => 'Vượt công suất',
                        'level_2_required' => 'Level 2',
                        'level_2_feasible' => 'Level 2 khả thi',
                        'ok' => 'Bình thường',
                        'normal' => 'Bình thường',
                        'capacity_exceeded' => 'Vượt công suất'
                    ];

                    // Xác định badge cho header card
                    $warnings = isset($order->warning_details) ? json_decode($order->warning_details, true) : [];
                    $warning_type = $order->warning_type ?? 'normal';
                    $has_missing_material = isset($warnings['missing_materials_warning']);
                    $has_material_warning = isset($warnings['material_warning']);
                    $stock_status = $warnings['stock_status'] ?? '';
                    $has_deadline_warning = isset($warnings['deadline_warning']);
                    $has_deadline_passed = isset($warnings['deadline_passed']);
                    $has_capacity_warning = isset($warnings['capacity_warning']);
                    $capacity_level = $order->capacity_level_used ?? 1;

                    // Use standardized warning name from mapping
                    $badge_text = $warning_name_mapping[$warning_type] ?? 'Bình thường';

                    // Determine badge color and icon based on warning type
                    $badge_color = 'success';
                    $badge_icon = '✓';
                    $header_gradient = 'success';

                    if (in_array($warning_type, ['product_not_found', 'bom_missing', 'capacity_overload', 'capacity_exceeded'])) {
                        $badge_color = 'danger';
                        $badge_icon = '⚠️';
                        $header_gradient = 'danger';
                    } elseif (in_array($warning_type, ['material_shortage', 'new_material_shortage', 'deadline_too_close', 'level_2_required', 'level_2_feasible'])) {
                        $badge_color = 'warning';
                        $badge_icon = '⚠️';
                        $header_gradient = 'warning';
                    } elseif ($warning_type === 'deadline_overdue') {
                        $badge_color = 'dark';
                        $badge_icon = '⏰';
                        $header_gradient = 'dark';
                    } elseif ($warning_type === 'stock_available') {
                        $badge_color = 'info';
                        $badge_icon = '🏪';
                        $header_gradient = 'info';
                    }
                    // 'ok', 'normal', 'low_stock_warning' use default success styling
                ?>
                <div class="bg-gradient-<?= $header_gradient; ?> shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-center mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 24px; vertical-align: middle;">analytics</i>
                        Phân tích Sản xuất
                    </h6>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Status Badge -->
                <div class="text-center mb-4">
                    <span class="badge badge-lg bg-gradient-<?= $badge_color; ?> px-4 py-2" style="font-size: 16px; font-family: 'Poppins', sans-serif;">
                        <?= $badge_icon; ?> <?= $badge_text; ?>
                    </span>
                </div>

                <?php if (!empty($warnings)): ?>
                    <hr class="horizontal dark my-3">
                    
                    <!-- Tồn kho thành phẩm -->
                    <?php if (isset($warnings['finished_stock_info'])): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-info" style="font-size: 18px; vertical-align: middle;">warehouse</i>
                                Tồn kho thành phẩm
                            </h6>
                            <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                <?= $warnings['finished_stock_info']; ?>
                            </p>
                            <?php
                            // Hiển thị thông tin chi tiết về tồn kho thành phẩm
                            $stock_allocation = json_decode($order->stock_allocation, true);
                            if ($stock_allocation) {
                                $finished_stock_available = $order->finished_stock_available ?? 0;
                                $qty_request = $order->qty_request;
                                $from_stock = $stock_allocation['from_stock'] ?? 0;
                                $for_production = $stock_allocation['for_production'] ?? 0;

                                echo '<div class="mt-2 p-2 bg-light rounded">';
                                echo '<small class="text-muted">';
                                echo '<strong>Tồn thành phẩm:</strong> ' . number_format($finished_stock_available) . ' sản phẩm<br>';

                                if ($from_stock >= $qty_request) {
                                    echo '<span class="text-success">✓ Đủ giao ngay toàn bộ đơn hàng</span>';
                                } elseif ($from_stock > 0) {
                                    echo '<span class="text-warning">⚠️ Đủ giao ' . number_format($from_stock) . ' sản phẩm, cần sản xuất thêm ' . number_format($for_production) . ' sản phẩm</span>';
                                } else {
                                    echo '<span class="text-info">📦 Cần sản xuất ' . number_format($for_production) . ' sản phẩm</span>';
                                }
                                echo '</small></div>';
                            }
                            ?>
</div>
                            <?php endif; ?>

                            <!-- Công suất sản xuất -->
                            <?php if (isset($warnings['capacity_info']) || isset($warnings['capacity_warning'])): ?>
                                <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round <?= $capacity_level == 2 ? 'text-warning' : 'text-success'; ?>" style="font-size: 18px; vertical-align: middle;">settings</i>
                                Công suất sản xuất
                            </h6>
                            <?php if ($capacity_level == 0): ?>
                                <div class="alert alert-success mb-0">
                                    <strong>✓ Không cần sản xuất</strong><br>
                                    <small>Đủ tồn kho để giao hàng ngay</small>
                                </div>
                            <?php elseif ($capacity_level == 1): ?>
                                <div class="alert alert-info mb-0">
                                    <strong>✓ Level 1: Công suất tiêu chuẩn</strong><br>
                                    <small>8 giờ × 2 ca/ngày (16h/ngày)</small>
                                    <?php if (isset($warnings['estimated_shifts'])): ?>
                                        <br><small>→ Cần <?= $warnings['estimated_shifts']; ?> ca (~<?= $warnings['estimated_days'] ?? 'N/A'; ?> ngày)</small>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($capacity_level == 2): ?>
                                <div class="alert alert-warning mb-0">
                                    <strong>⚠️ Level 2: Công suất tối đa</strong><br>
                                    <small><?= $warnings['capacity_warning'] ?? 'Vượt Level 1, chuyển sang Level 2 (12h×2ca=24h/ngày)'; ?></small>
                                    <?php if (isset($warnings['estimated_shifts'])): ?>
                                        <br><small>→ Cần <?= $warnings['estimated_shifts']; ?> ca Level 2 (~<?= $warnings['estimated_days'] ?? 'N/A'; ?> ngày)</small>
                                    <?php endif; ?>
</div>
                            <?php endif; ?>
</div>
                            <?php endif; ?>

                            <!-- BOM thiếu NVL -->
                            <?php if ($has_missing_material): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-danger" style="font-size: 18px; vertical-align: middle;">error</i>
                                Định mức BOM
                            </h6>
                                <div class="alert alert-danger mb-0">
                                    <strong>⚠️ <?= $warnings['missing_materials_warning']; ?></strong>
                                <?php if (isset($warnings['missing_materials_list'])): ?>
                                    <?php $missing_list = explode(', ', $warnings['missing_materials_list']); ?>
                                    <ul class="mt-2 mb-0">
                                        <?php foreach ($missing_list as $missing): ?>
                                            <li><?= htmlspecialchars($missing); ?></li>
                                    <?php endforeach; ?>
                                    </ul>
<small class="d-block mt-2 text-dark">→ Cần bổ sung NVL vào kho trước khi sản xuất</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Nguyên vật liệu với thông tin chi tiết -->
                    <?php if (isset($warnings['material_details']) && !empty($warnings['material_details'])): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-primary" style="font-size: 18px; vertical-align: middle;">inventory</i>
                                Nguyên vật liệu
                            </h6>
                            <?php
                            // Tính tổng sản phẩm có thể sản xuất từ NVL
                            $total_products_possible = PHP_INT_MAX;
                            $bottleneck_material = '';
                            foreach ($warnings['material_details'] as $mat) {
                                if (isset($mat['products_possible'])) {
                                    if ($mat['products_possible'] < $total_products_possible) {
                                        $total_products_possible = $mat['products_possible'];
                                        $bottleneck_material = $mat['material_name'];
                                    }
                                }
                            }

                            // Tính số ca có thể sản xuất (dựa trên Level 1: 3200 sản phẩm/ca)
                            $shifts_possible = $total_products_possible == PHP_INT_MAX ? 0 : ceil($total_products_possible / 3200);

                            if ($total_products_possible > 0 && $total_products_possible != PHP_INT_MAX) {
                                echo '<div class="alert alert-info mb-3">';
                                echo '<strong>📊 Tồn NVL còn đủ sản xuất ' . number_format($total_products_possible) . ' sản phẩm cho đơn hàng cho ' . $shifts_possible . ' ca sản xuất</strong>';
                                if (!empty($bottleneck_material)) {
                                    echo '<br><small>NVL giới hạn: ' . htmlspecialchars($bottleneck_material) . '</small>';
                                }
                                echo '</div>';
                            }
                            ?>

                            <?php foreach ($warnings['material_details'] as $mat): ?>
                                <?php
                                    $mat_status_color = 'success';
                                    $mat_status_icon = '✓';
                                    $mat_status_text = 'Đủ NVL';

                                    if (isset($mat['is_bottleneck']) && $mat['is_bottleneck']) {
                                        $mat_status_color = 'warning';
                                        $mat_status_icon = '⚠️';
                                        $mat_status_text = 'GIỚI HẠN';
                                    } elseif (isset($mat['is_sufficient']) && !$mat['is_sufficient']) {
                                        $mat_status_color = 'danger';
                                        $mat_status_icon = '❌';
                                        $mat_status_text = 'THIẾU';
                                    }
                                ?>
                                <div class="alert alert-<?= $mat_status_color; ?> mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong><?= $mat_status_icon; ?> <?= htmlspecialchars($mat['material_name']); ?></strong>
                                        <span class="badge bg-white text-dark"><?= $mat_status_text; ?></span>
                                    </div>
                                    <small>
                                        📦 Tồn kho: <strong><?= number_format($mat['stock'], 2); ?> <?= $mat['uom']; ?></strong><br>
                                        <?php if (isset($mat['quantity_per_unit'])): ?>
                                            📏 Định mức: <?= $mat['quantity_per_unit']; ?> <?= $mat['uom']; ?>/sản phẩm<br>
                                            🎯 Cần cho ĐH: <?= number_format($mat['quantity_needed'], 2); ?> <?= $mat['uom']; ?><br>
                                            <?php if (isset($mat['quantity_shortage']) && $mat['quantity_shortage'] > 0): ?>
                                                <span class="text-danger">⚠️ THIẾU: <?= number_format($mat['quantity_shortage'], 2); ?> <?= $mat['uom']; ?></span><br>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (isset($mat['shifts_possible'])): ?>
                                            ⏱️ Đủ cho: <strong><?= $mat['shifts_possible']; ?> ca</strong>
                                        <?php endif; ?>
                                    </small>
                                </div>
<?php endforeach; ?>
                        </div>
                    <?php elseif ($has_material_warning): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-warning" style="font-size: 18px; vertical-align: middle;">inventory</i>
                                Nguyên vật liệu
                            </h6>
                            <div class="alert alert-warning mb-0">
                                <strong>📦 <?= $warnings['material_warning']; ?></strong>
                                <?php if (isset($warnings['material_shortage_details'])): ?>
                                    <br><small>→ <?= $warnings['material_shortage_details']; ?></small>
                            <?php endif; ?>
</div>
                        </div>
                            <?php elseif (isset($warnings['material_status'])): ?>
                                <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-success" style="font-size: 18px; vertical-align: middle;">inventory</i>
                                Nguyên vật liệu
                            </h6>
                            <div class="alert alert-success mb-0">
                                <strong>✓ <?= $warnings['material_status']; ?></strong>
                            </div>
</div>
                            <?php endif; ?>

                            <!-- Deadline -->
                            <?php if ($has_deadline_passed): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-dark" style="font-size: 18px; vertical-align: middle;">event_busy</i>
                                Deadline
                            </h6>
                                <div class="alert alert-dark mb-0">
                                <strong>⏰ <?= $warnings['deadline_passed']; ?></strong>
                                <?php if (isset($warnings['deadline_details'])): ?>
                                    <br><small><?= $warnings['deadline_details']; ?></small>
                            <?php endif; ?>
</div>
                        </div>
                    <?php elseif ($has_deadline_warning): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-warning" style="font-size: 18px; vertical-align: middle;">schedule</i>
                                Deadline
                            </h6>
                            <div class="alert alert-warning mb-0">
                                <strong>⏰ <?= $warnings['deadline_warning']; ?></strong>
                                <?php if (isset($warnings['deadline_details'])): ?>
                                    <br><small><?= $warnings['deadline_details']; ?></small>
                        <?php endif; ?>
</div>
                        </div>
<?php elseif (isset($warnings['deadline_status'])): ?>
                        <div class="mb-3">
                            <h6 style="font-family: 'Poppins', sans-serif;">
                                <i class="material-icons-round text-success" style="font-size: 18px; vertical-align: middle;">schedule</i>
                                Deadline
                            </h6>
                            <div class="alert alert-info mb-0">
                                <small>✓ <?= $warnings['deadline_status']; ?></small>
                    </div>
                </div>
<?php endif; ?>

                <?php else: ?>
                    <!-- No warnings -->
                <div class="text-center py-4">
                    <i class="material-icons-round text-success" style="font-size: 64px;">check_circle</i>
                        <h6 class="mt-3 mb-2" style="font-family: 'Poppins', sans-serif;">
                            Không có cảnh báo
                        </h6>
                        <p class="text-muted text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                            Đơn hàng trong tình trạng bình thường
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
