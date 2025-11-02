<div class="container-fluid">
    <h2 class="mb-4">
        <i class="fas fa-tachometer-alt"></i> Warehouse Dashboard
    </h2>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <h3><i class="fas fa-cubes"></i> <?= $total_materials ?></h3>
                <p>Total Materials</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3><i class="fas fa-exclamation-triangle"></i> <?= $low_stock_materials ?></h3>
                <p>Low Stock Items</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3><i class="fas fa-user"></i> <?= $this->session->userdata('username') ?></h3>
                <p>Current User</p>
            </div>
        </div>
    </div>

    <!-- Recent Materials -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Danh sách Nguyên vật liệu
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Material Name</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_materials)): ?>
                            <?php foreach ($recent_materials as $material): ?>
                                <tr>
                                    <td><?= $material->id_material ?></td>
                                    <td><?= $material->material_name ?></td>
                                    <td>
                                        <strong><?= number_format($material->stock) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($material->stock < 100): ?>
                                            <span class="badge badge-danger">Low Stock</span>
                                        <?php elseif ($material->stock < 500): ?>
                                            <span class="badge badge-warning">Medium</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Good</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('warehouse/stock_in') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-arrow-down"></i> Nhập
                                        </a>
                                        <a href="<?= base_url('warehouse/stock_out') ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-arrow-up"></i> Xuất
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-arrow-down"></i> Nhập kho NVL
                </div>
                <div class="card-body">
                    <p>Ghi nhận nguyên vật liệu nhập kho từ nhà cung cấp</p>
                    <a href="<?= base_url('warehouse/stock_in') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo phiếu nhập
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-arrow-up"></i> Xuất kho NVL
                </div>
                <div class="card-body">
                    <p>Xuất nguyên vật liệu cho dây chuyền sản xuất</p>
                    <a href="<?= base_url('warehouse/stock_out') ?>" class="btn btn-warning">
                        <i class="fas fa-minus"></i> Tạo phiếu xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
