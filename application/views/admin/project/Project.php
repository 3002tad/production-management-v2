<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_projects'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_projects'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
<div class="container-fluid py-4 pt-0">
    <div class="card-header p-0 w-75 position-fixed mt-n4 mx-2 z-index-2">
        <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
            <div class="col-8 d-flex align-items-center">  
                <i class="material-icons pr-3">task</i>
                <h6 class="mb-0"><?= lang('label_data_projects'); ?></h6>
            </div>
            <div class="col-4 text-end">
                <a href="<?= site_url('admin/project/addproject'); ?>" class="btn bg-gradient-dark mb-0"><?= lang('btn_add_project'); ?></a>
            </div>
        </div>
    </div>
</div>
<div class="container py-4 pl-5 pr-5">
    <div class="row">
        <div class="card">
            <div class="card-body pt-4 p-3">
            <div class="table-responsive p-0">
                <table id="table-data" class="table align-items-center justify-content-center mb-0">
                <thead>
                    <tr>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('menu_project'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('menu_customer'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('menu_product'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_diameter'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('form_quantity'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('form_date'); ?></th>
                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($project)) : $i = 1; foreach ( array_reverse($project) as $value) : ?>
                        <tr>
                    <td>
                        <div class="d-flex pl-3">
                            <div class="my-auto">
                            <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->project_name; ?></span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->cust_name; ?></span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->product_name; ?></span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->diameter; ?> <?= lang('unit_mm'); ?></span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->qty_request; ?> <?= lang('unit_pieces'); ?></span>
                    </td>
                    <td>
                        <span class="text-sm font-weight-bold pl-3"><?= $value->entry_date; ?></span>
                    </td>
                    <td>
                        <a href="<?= site_url('admin/project/'.$value->id_project.'/view'); ?>" rel="tooltip" title="<?= lang('label_edit_task'); ?>" class="btn btn-outline-info text-info btn-link btn-sm">
                            <i class="material-icons text-info text-sm">edit</i> <?= lang('btn_update'); ?>
                        </a>
                    </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>