<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?php echo $this->lang->line('material_list'); ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('warehouse') ?>"><?php echo $this->lang->line('warehouse_title'); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo $this->lang->line('material_list'); ?></li>
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
                <!-- Success Message -->
                <?php if ($this->session->flashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        <?= $this->session->flashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $this->lang->line('material_list'); ?></h3>
                        <div class="card-tools">
                            <a href="<?= base_url('warehouse/material_entry/add') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> <?php echo $this->lang->line('material_entry'); ?>
                            </a>
                        </div>
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
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $material) : ?>
                                    <tr>
                                        <td><?= isset($material->id_material) ? $material->id_material : (isset($material->id) ? $material->id : '') ?></td>
                                        <td class="cell-material" title="<?= isset($material->material_name) ? htmlspecialchars($material->material_name, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->material_name) ? $material->material_name : '' ?></td>
                                        <td class="cell-type" title="<?= isset($material->material_type) ? htmlspecialchars($material->material_type, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->material_type) ? $material->material_type : '' ?></td>
                                        <td class="cell-unit" title="<?= isset($material->unit) ? htmlspecialchars($material->unit, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->unit) ? $material->unit : '' ?></td>
                                        <td class="cell-smallnum"><small title="<?= isset($material->stock) ? number_format((int)$material->stock) : '0' ?>"><?= isset($material->stock) ? number_format((int)$material->stock) : '0' ?></small></td>
                                        <td class="cell-supplier" title="<?= isset($material->supplier) ? htmlspecialchars($material->supplier, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->supplier) ? $material->supplier : '' ?></td>
                                        <td class="cell-date" title="<?= isset($material->date_entry) ? htmlspecialchars($material->date_entry, ENT_QUOTES, 'UTF-8') : '' ?>"><?= isset($material->date_entry) ? $material->date_entry : '' ?></td>
                                        <td>
                                            <a href="<?= base_url('warehouse/material_entry/'.(isset($material->id_material)?$material->id_material:$material->id).'/view') ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->