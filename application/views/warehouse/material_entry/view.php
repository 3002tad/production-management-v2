<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?php echo $this->lang->line('material_list'); ?> / <?php echo $this->lang->line('material_id'); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('warehouse') ?>"><?php echo $this->lang->line('warehouse_title'); ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('warehouse/material_entry') ?>"><?php echo $this->lang->line('material_entry'); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo $this->lang->line('material_id'); ?></li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('material_list'); ?> - <?php echo $this->lang->line('material_id'); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                        <tr>
                                            <th width="30%"><?php echo $this->lang->line('material_id'); ?></th>
                                            <td><?= isset($detail->id_material) ? $detail->id_material : (isset($detail->id) ? $detail->id : '') ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('material_name'); ?></th>
                                            <td class="cell-material" title="<?= isset($detail->material_name) ? htmlspecialchars($detail->material_name, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($detail->material_name) ? $detail->material_name : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('material_type'); ?></th>
                                            <td class="cell-type" title="<?= isset($detail->material_type) ? htmlspecialchars($detail->material_type, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($detail->material_type) ? $detail->material_type : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('unit'); ?></th>
                                            <td class="cell-unit" title="<?= isset($detail->unit) ? htmlspecialchars($detail->unit, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($detail->unit) ? $detail->unit : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('current_stock'); ?></th>
                                            <td class="cell-smallnum"><small title="<?= isset($detail->stock) ? number_format((int)$detail->stock) : '0' ?>"><?= isset($detail->stock) ? number_format((int)$detail->stock) : '0' ?></small></td>
                                        </tr>
                                        <!-- max_stock removed: hidden per user request -->
                                        <tr>
                                            <th><?php echo $this->lang->line('supplier'); ?></th>
                                            <td><?= isset($detail->supplier) ? $detail->supplier : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('date_entry'); ?></th>
                                            <td><?= isset($detail->date_entry) ? $detail->date_entry : '' ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo $this->lang->line('attachment'); ?></th>
                                            <td class="cell-supplier"><?php if (!empty($detail->attachment)) : ?><a href="<?= site_url($detail->attachment) ?>" target="_blank"><?php echo $this->lang->line('attachment'); ?></a><?php else: echo '-'; endif; ?></td>
                                        </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="<?= base_url('warehouse/material_entry') ?>" class="btn btn-default"><?php echo $this->lang->line('material_list'); ?></a>
                                <button type="button" class="btn btn-primary" onclick="window.print();">
                                    <i class="fas fa-print"></i> In
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>
<!-- /.content -->