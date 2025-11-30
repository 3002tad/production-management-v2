<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  UC1: Customer Management - VIEW DETAIL                                      ║
║  Material Design 3.0 với order history và statistics                        ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

<div class="row">
    <!-- Customer Info Card -->
    <div class="col-md-4">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-center mb-0" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round opacity-10" style="font-size: 24px; vertical-align: middle;">person</i>
                        Thông tin Khách hàng
                    </h6>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Avatar Placeholder -->
                <div class="text-center mb-4">
                    <div class="avatar avatar-xxl bg-gradient-primary">
                        <span class="text-white" style="font-size: 48px; font-family: 'Poppins', sans-serif;">
                            <?= strtoupper(substr($customer->cust_name, 0, 1)); ?>
                        </span>
                    </div>
                    <h5 class="mt-3 mb-0" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($customer->cust_name); ?>
                    </h5>
                    <p class="text-muted text-sm mb-2" style="font-family: 'Poppins', sans-serif;">
                        Mã: <?= $customer->id_cust; ?>
                    </p>
                    
                    <!-- Status Badge -->
                    <?php if ($customer->is_active == 1): ?>
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

                <!-- Contact Information -->
                <h6 style="font-family: 'Poppins', sans-serif;">
                    <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">contact_phone</i>
                    Thông tin liên hệ
                </h6>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-primary me-2">email</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Email</small>
                                <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->email) ? htmlspecialchars($customer->email) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center">
                            <i class="material-icons-round text-success me-2">phone</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Điện thoại</small>
                                <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->telp) ? htmlspecialchars($customer->telp) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-start">
                            <i class="material-icons-round text-warning me-2">location_on</i>
                            <div>
                                <small class="text-muted" style="font-family: 'Poppins', sans-serif;">Địa chỉ</small>
                                <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">
                                    <?= !empty($customer->address) ? htmlspecialchars($customer->address) : '<em class="text-muted">Chưa có</em>'; ?>
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Notes -->
                <?php if (!empty($customer->notes)): ?>
                    <hr class="horizontal dark my-3">
                    <h6 style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 18px; vertical-align: middle;">notes</i>
                        Ghi chú
                    </h6>
                    <p class="text-sm" style="font-family: 'Poppins', sans-serif;">
                        <?= nl2br(htmlspecialchars($customer->notes)); ?>
                    </p>
                <?php endif; ?>

                <!-- Metadata -->
                <hr class="horizontal dark my-3">
                <small class="text-muted d-block" style="font-family: 'Poppins', sans-serif;">
                    <i class="material-icons-round" style="font-size: 12px;">schedule</i>
                    Tạo: <?= date('d/m/Y H:i', strtotime($customer->created_at)); ?>
                </small>
                <?php if (!empty($customer->updated_at)): ?>
                    <small class="text-muted d-block" style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 12px;">update</i>
                        Cập nhật: <?= date('d/m/Y H:i', strtotime($customer->updated_at)); ?>
                    </small>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="<?= site_url('BOD/customer/edit/' . $customer->id_cust); ?>" 
                       class="btn btn-sm bg-gradient-warning w-100 mb-2"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">edit</i>
                        Chỉnh sửa
                    </a>
                    <a href="<?= site_url('BOD/customer'); ?>" 
                       class="btn btn-sm btn-outline-secondary w-100"
                       style="font-family: 'Poppins', sans-serif;">
                        <i class="material-icons-round" style="font-size: 16px;">arrow_back</i>
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders and Statistics -->
    <div class="col-md-8">
        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md mb-2">
                            <i class="material-icons-round text-lg opacity-10">shopping_cart</i>
                        </div>
                        <h5 class="mb-0" style="font-family: 'Poppins', sans-serif;">
                            <?= isset($customer->total_orders) ? $customer->total_orders : 0; ?>
                        </h5>
                        <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">Tổng đơn hàng</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md mb-2">
                            <i class="material-icons-round text-lg opacity-10">inventory</i>
                        </div>
                        <h5 class="mb-0" style="font-family: 'Poppins', sans-serif;">
                            <?= isset($customer->total_quantity) ? number_format($customer->total_quantity) : 0; ?>
                        </h5>
                        <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">Tổng số lượng</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md mb-2">
                            <i class="material-icons-round text-lg opacity-10">calendar_today</i>
                        </div>
                        <h5 class="mb-0 text-sm" style="font-family: 'Poppins', sans-serif;">
                            <?= isset($customer->last_order_date) ? date('d/m/Y', strtotime($customer->last_order_date)) : 'Chưa có'; ?>
                        </h5>
                        <p class="text-sm mb-0" style="font-family: 'Poppins', sans-serif;">Đơn gần nhất</p>
                    </div>
                </div>
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Sản phẩm</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-family: 'Poppins', sans-serif;">Đường kính</th>
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
                                                <?= $order->product_name; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-secondary" style="font-family: 'Poppins', sans-serif;">
                                                <?= $order->diameter_display; ?>
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
                            Khách hàng chưa có đơn hàng nào
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
