<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- DELETE PROJECT - Xác nhận xóa đơn hàng                              -->
<!-- ═══════════════════════════════════════════════════════════════════ -->

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">delete</i>
                        Xóa đơn hàng
                    </h6>
                </div>
            </div>

            <div class="card-body px-4 pb-4">
                
                <!-- Warning message -->
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-3x me-3"></i>
                        <div>
                            <strong>⚠️ CẢNH BÁO!</strong><br>
                            Bạn có chắc chắn muốn xóa đơn hàng này không?<br>
                            <small class="text-muted">Thao tác này không thể hoàn tác!</small>
                        </div>
                    </div>
                </div>

                <!-- Order details -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Thông tin đơn hàng sẽ bị xóa:</h6>
                        
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="text-sm font-weight-bold" style="width: 200px;">Mã đơn hàng:</td>
                                    <td class="text-sm"><?= $detail->project_name; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-sm font-weight-bold">Khách hàng:</td>
                                    <td class="text-sm"><?= $detail->cust_name; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-sm font-weight-bold">Sản phẩm:</td>
                                    <td class="text-sm"><?= $detail->product_name; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-sm font-weight-bold">Số lượng:</td>
                                    <td class="text-sm"><?= number_format($detail->qty_request); ?> chiếc</td>
                                </tr>
                                <tr>
                                    <td class="text-sm font-weight-bold">Hạn giao:</td>
                                    <td class="text-sm"><?= date('d/m/Y', strtotime($detail->entry_date)); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-sm font-weight-bold">Trạng thái:</td>
                                    <td class="text-sm">
                                        <?php if ($detail->pr_status == 1): ?>
                                            <span class="badge bg-gradient-success">Đã duyệt</span>
                                        <?php else: ?>
                                            <span class="badge bg-gradient-warning">Chờ duyệt</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12 text-end">
                        <a href="<?= site_url('BOD/project'); ?>" 
                           class="btn btn-outline-secondary mb-0">
                            <i class="material-icons opacity-10">arrow_back</i>
                            Hủy
                        </a>
                        <a href="<?= site_url('BOD/deleteProject/' . $detail->id_project); ?>" 
                           class="btn btn-danger mb-0"
                           onclick="return confirm('⚠️ BẠN CÓ CHẮC CHẮN MUỐN XÓA?\n\nĐơn hàng: <?= $detail->project_name; ?>\nKhách hàng: <?= $detail->cust_name; ?>\n\nThao tác này KHÔNG THỂ HOÀN TÁC!');">
                            <i class="material-icons opacity-10">delete_forever</i>
                            Xác nhận XÓA
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
