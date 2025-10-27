<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_machine'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_machine'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
<div class="container-fluid pt-0 ">
        <div class="card-header p-0 w-75 position-fixed mt-n4 mx-2 z-index-2">
            <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                <div class="col-8 d-flex align-items-center">
                <i class="material-icons pr-3">build</i>
                    <h6 class="mb-0 pr-4 "><?= lang('label_machine_schedule'); ?></h6>
                </div>           
                <div class="col-4 text-end">
                    <a href="<?= site_url('admin/machine/addnewmachine'); ?>" class="btn badge-sm bg-gradient-secondary mb-0"><?= lang('btn_add_machine'); ?></a>
                </div>
            </div>
        </div>
</div>
<div class="container py-4 pt-5">
    <div class="row">
        <div class="col-md-7 mt-4 pl-4">
          <div class="card">
            <div class="card-header pb-0 px-3">
              <h6 class="mb-0"><?= lang('label_machine_history'); ?></h6>
              <span class="text-sm mb-0"><?= lang('label_machine_history_desc'); ?></span>

            </div>
            <div class="card-body p-3">
                <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_machine'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_production'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_date'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($p_machine)) : $i = 1; foreach ($p_machine as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex pl-3">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->machine_name?></span> </br>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->staff_name?></span> </br>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->start_date?></span> </br>
                        </td>
                        <?php  endforeach; endif;?>
                        </tr>
                    </tbody>
                    </table>
                </div>
                </div>
          </div>
        </div>
        <div class="col-md-5 mt-4 pr-4">
          <div class="card h-100 mb-4">
            <div class="card-header pb-0 px-3">
                <div class="row">
                    <div class="col-7 pl-4 align-items-center">
                        <h6 class="mb-0"><?= lang('label_machine_status'); ?></h6>
                        <span class="text-sm mb-0"><?= lang('label_machine_for_production'); ?></span>
                    </div>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="table-responsive p-0">
                <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_machine'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_status'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($machines)) : $i = 1; foreach ($machines as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->machine_name?> / <?= $value->capacity?> <?= lang('unit_pieces_per_hour'); ?></span> </br>
                        </td>
                        <td class="pl-4">
                        <?php if ( $value->mc_status == 1) :?>
                            <span class="text-xs font-weight-bold text-success"><?= lang('status_ready'); ?></span>
                        <?php elseif ( $value->mc_status == 2) : ?>
                            <span class="text-xs font-weight-bold text-warning"><?= lang('status_used'); ?></span>
                        <?php elseif ( $value->mc_status == 4) : ?>
                            <span class="text-xs font-weight-bold text-info"><?= lang('status_maintenance'); ?></span>
                        <?php else : ?>
                            <span class="text-xs font-weight-bold text-danger"><?= lang('status_trouble'); ?></span>
                        <?php endif ;?> 
                        </tr>
                        <?php  endforeach; endif;?>
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>