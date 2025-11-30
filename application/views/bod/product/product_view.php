<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC2: Product Management - VIEW DETAIL with BOM table                        ║
║  Material Design 3.0 với order history và statistics                        ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <!-- Product Info & BOM Card -->
    <div class="col-md-4">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-center mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 24px; vertical-align: middle;">inventory_2</i>
                        Thông tin Sản phẩm
                    </h6>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Product Icon -->
                <div class="text-center mb-4">
                    <div class="avatar avatar-xxl bg-gradient-primary">
                        <span class="text-white" style="font-size: 36px; font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 48px;">lan</i>
                        </span>
                    </div>
                    <h5 class="mt-3 mb-0" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($product->product_name); ?>
                    </h5>
                    <p class="text-muted text-sm mb-2" style="font-family: 'Poppins', sans-serif;">
                        Mã: <?= $product->id_product; ?>
                    </p>
                    
                    <!-- Status Badge -->
                    <?php if ($product->is_active == 1): ?>
                        <span class="badge badge-sm bg-gradient-success" style="font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">check_circle</i>
                            Hoạt động
                        </span>
                    <?php else: ?>
                        <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                            <i class="material-icons-round" style="font-size: 12px; vertical-align: middle;">pause_circle</i>
                            Ngừng
                        </span>
                    <?php endif; ?>
                </div>

                <hr class="horizontal dark my-3">

                <!-- Product Details -->
                <h6 style="font-family: 'Poppins', sans-serif;">
                    <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">info</i>
                    Chi tiết
                </h6>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-primary me-2">straighten</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Đường kính</small>
                                <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                    <span class="badge badge-sm bg-gradient-secondary"><?= $product->diameter; ?>mm</span>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-warning me-2">palette</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Ứng dụng</small>
                                <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($product->application) ? htmlspecialchars($product->application) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <?php if (!empty($product->summary)): ?>
                        <li class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <i class="material-icons-round text-info me-2">description</i>
                                <div>
                                    <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Tóm tắt</small>
                                    <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                        <?= htmlspecialchars($product->summary); ?>
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
                    Tạo: <?= date('d/m/Y H:i', strtotime($product->created_at)); ?>
                </small>
                <?php if (!empty($product->updated_at)): ?>
                    <small class="text-muted d-block" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 12px;">update</i>
                        Cập nhật: <?= date('d/m/Y H:i', strtotime($product->updated_at)); ?>
                    </small>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="<?= site_url('BOD/product/edit/' . $product->id_product); ?>" 
                       class="btn btn-sm bg-gradient-warning w-100 mb-2"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">edit</i>
                        Chỉnh sửa
                    </a>
                    <a href="<?= site_url('BOD/product'); ?>" 
                       class="btn btn-sm btn-outline-secondary w-100"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">arrow_back</i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics & BOM & Orders -->
    <div class="col-md-8">
        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md mb-2">
                            <i class="material-icons-round text-lg opacity-10">shopping_cart</i>
                        </div>
                        <h5 class="mb-0" style="font-family: 'Poppins', sans-serif;">
                            <?= isset($product->total_orders) ? $product->total_orders : 0; ?>
                        </h5>
                        <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">Tổng đơn hàng</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-gradient-<?= isset($product->bom_data['materials']) && !empty($product->bom_data['materials']) ? 'success' : 'secondary'; ?> shadow text-center border-radius-md mb-2">
                            <i class="material-icons-round text-lg opacity-10">precision_manufacturing</i>
                        </div>
                        <h5 class="mb-0" style="font-family: 'Poppins', sans-serif;">
                            <?= isset($product->bom_data['materials']) ? count($product->bom_data['materials']) : 0; ?>
                        </h5>
                        <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">Nguyên liệu trong BOM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOM Table -->
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                    <h6 class="text-white px-3 mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 18px; vertical-align: middle;">precision_manufacturing</i>
                        Bill of Materials (BOM)
                    </h6>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <?php if (isset($product->bom_data['materials']) && !empty($product->bom_data['materials'])): ?>
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">STT</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="font-family: 'Poppins', sans-serif;">Nguyên liệu</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Số lượng</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Đơn vị</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($product->bom_data['materials'] as $material): ?>
                                    <tr>
                                        <td>
                                            <span class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                                <?= $no++; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="material-icons-round text-success me-2">category</i>
                                                <span class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                                    <?= htmlspecialchars($material['material_name']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                                <?= $material['quantity']; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                                                <?= htmlspecialchars($material['unit']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="material-icons-round text-secondary" style="font-size: 64px;">remove_circle_outline</i>
                        <p class="text-muted mt-3" style="font-family: 'Poppins', sans-serif;">
                            Sản phẩm chưa có BOM
                        </p>
                        <a href="<?= site_url('BOD/product/edit/' . $product->id_product); ?>" 
                           class="btn btn-sm bg-gradient-success">
                            <i class="material-icons-round" style="font-size: 14px;">add</i>
                            Thêm BOM
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order History -->
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white px-3 mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 18px; vertical-align: middle;">history</i>
                        Lịch sử Đơn hàng
                    </h6>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive p-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Mã đơn</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Khách hàng</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Số lượng</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Hạn giao</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0" style="font-family: 'Poppins', sans-serif;">
                                                <?= $order->project_name; ?>
                                            </p>
                                        </td>
                                        <td>
                                            <span class="text-sm" style="font-family: 'Poppins', sans-serif;">
                                                <?= htmlspecialchars($order->cust_name); ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                                <?= number_format($order->qty_request); ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs" style="font-family: 'Poppins', sans-serif;">
                                                <?= date('d/m/Y', strtotime($order->entry_date)); ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-<?= $order->status_text === 'Hoàn thành' ? 'success' : ($order->status_text === 'Hủy' ? 'danger' : 'info'); ?>" 
                                                  style="font-family: 'Poppins', sans-serif;">
                                                <?= $order->status_text; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="material-icons-round text-secondary" style="font-size: 64px;">shopping_cart_off</i>
                        <p class="text-muted mt-3" style="font-family: 'Poppins', sans-serif;">
                            Sản phẩm chưa có đơn hàng nào
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
