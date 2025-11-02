<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content">
    <div class="container-fluid">
        <div class="row pb-3">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">inventory_2</i>
                        </div>
                        <p class="card-category"><?php echo $this->lang->line('total_materials'); ?></p>
                        <h3 class="card-title counter"><?= $total_materials ?></h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons">inventory_2</i> <?php echo $this->lang->line('total_materials'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-header-rose pb-0">
                        <h4 class="card-title"><?php echo $this->lang->line('pending_orders'); ?></h4>
                        <p class="card-category">Danh sách đơn chờ nhập của kho</p>
                    </div>
                    <div class="card-body table-responsive pt-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo $this->lang->line('material_name'); ?></th>
                                    <th><?php echo $this->lang->line('quantity'); ?></th>
                                    <th><?php echo $this->lang->line('date_entry'); ?></th>
                                    <th><?php echo $this->lang->line('supplier'); ?></th>
                                    <th><?php echo $this->lang->line('attachment'); ?></th>
                                    <th><?php echo $this->lang->line('order_status'); ?></th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pending_orders)) : $i = 1; foreach ($pending_orders as $order) : ?>
                                    <tr>
                                        <td class="pl-4"><?= isset($order->id) ? $order->id : $i++; ?></td>
                                        <td class="pl-4"><?= isset($order->material_name) ? $order->material_name : '' ?></td>
                                        <td class="pl-4"><?= isset($order->quantity) ? $order->quantity : '' ?></td>
                                        <td class="pl-4"><?= isset($order->date_entry) ? $order->date_entry : '' ?></td>
                                        <td class="pl-4"><?= isset($order->supplier) ? $order->supplier : '' ?></td>
                                        <td class="pl-4"><?php if (!empty($order->attachment)) : ?><a href="<?= site_url($order->attachment) ?>" target="_blank"><?php echo $this->lang->line('attachment'); ?></a><?php endif; ?></td>
                                        <td class="pl-4"><?php echo (!empty($order->status) && $order->status === 'received') ? $this->lang->line('received') : $this->lang->line('pending'); ?></td>
                                        <td>
                                            <a href="<?= site_url('warehouse/material_entry/'.$order->id.'/view') ?>" class="btn btn-info btn-sm">Xem</a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Không có đơn chờ nhập</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-12">
                <!-- TO DO List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ion ion-stats-bars mr-1"></i>
                            <?php echo $this->lang->line('current_stock'); ?>
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo $this->lang->line('material_name'); ?></th>
                                    <th><?php echo $this->lang->line('material_type'); ?></th>
                                    <th><?php echo $this->lang->line('unit'); ?></th>
                                    <th><?php echo $this->lang->line('current_stock'); ?></th>
                                    <th><?php echo $this->lang->line('supplier'); ?></th>
                                    <th><?php echo $this->lang->line('date_entry'); ?></th>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $material) : 
                                    // Calculate status (localized)
                                    $status = $this->lang->line('status_normal');
                                    $status_class = 'success';
                                    $stockVal = isset($material->stock) ? (int)$material->stock : 0;
                                    if ($stockVal < 100) {
                                        $status = $this->lang->line('status_low_stock');
                                        $status_class = 'danger';
                                    } elseif ($stockVal > 1000) {
                                        $status = $this->lang->line('status_overstock');
                                        $status_class = 'warning';
                                    }
                                ?>
                                    <tr>
                                        <td><?= isset($material->id_material) ? $material->id_material : (isset($material->id) ? $material->id : '') ?></td>
                                        <td class="cell-material" title="<?= isset($material->material_name) ? htmlspecialchars($material->material_name, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->material_name) ? $material->material_name : '' ?></td>
                                        <td class="cell-type" title="<?= isset($material->material_type) ? htmlspecialchars($material->material_type, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->material_type) ? $material->material_type : '' ?></td>
                                        <td class="cell-unit" title="<?= isset($material->unit) ? htmlspecialchars($material->unit, ENT_QUOTES, 'UTF-8') : (isset($material->unit_name) ? htmlspecialchars($material->unit_name, ENT_QUOTES, 'UTF-8') : '') ?>"><?= isset($material->unit) ? $material->unit : (isset($material->unit_name) ? $material->unit_name : '') ?></td>
                                        <td class="cell-smallnum"><small title="<?= isset($material->stock) ? number_format((int)$material->stock) : '0' ?>"><?= isset($material->stock) ? number_format((int)$material->stock) : '0' ?></small></td>
                                        <td class="cell-supplier" title="<?= isset($material->supplier) ? htmlspecialchars($material->supplier, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->supplier) ? $material->supplier : '' ?></td>
                                        <td class="cell-date" title="<?= isset($material->date_entry) ? htmlspecialchars($material->date_entry, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->date_entry) ? $material->date_entry : '' ?></td>
                                        <td><span class="badge badge-<?= $status_class ?>"><?= $status ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </section>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    </div>
</div>