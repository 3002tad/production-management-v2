<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_production'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('label_detail_production'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('label_details_production'); ?></h6>
            </div>
        </div>
    </nav>
</br>
    <!-- End Navbar -->
    <div class="container-fluid ">
    <div class="pb-4">
        <div class="row">
            <div class="col-10 d-flex align-items-center">
                <h4 class="mb-0"><?= lang('label_production_on_process'); ?></h4>
            </div>
            <div class="col-2 text-end d-flex">
                <div class="pl-4">
                <a href="<?= site_url('admin/detail_production/'.$detail['id_planshift'].'/addsorting'); ?>"class="btn btn-info btn-sm mb-0"><?= lang('btn_complete'); ?></a>
                </div>
            </div>
        </div>
    </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="row">
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card">
                <div class="card-header pb-0 p-3">
                  <div class="row d-flex">
                      <div class="col pb-2 pl-4 align-items-center">
                      <span class="text-lg"><?= lang('label_shiftment_head'); ?> :  <b><?= $detail['staff_name']?></b><br/></span>
                      <span class="text-sm mb-0"><?= lang('label_detail_on_production'); ?></span>
                      </div>
                  </div>
            </div>
                <div class="card-body p-3">
                  <div class="row">
                    <div class="col-md mb-md-0 mb-4">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                        <div class=" d-flex flex-column">
                            <span class="mb-3 text-sm"><?= lang('label_planning'); ?> : </br><b><?= $detail['plan_name']?></b></span>
                            <span class="mb-3 text-sm"><?= lang('label_product'); ?> : </br><b><?= $detail['product_name']?></b></span>
                        </div>
                        <div class="pl-5 d-flex flex-column">
                        <span class="mb-3 text-sm"><?= lang('label_diameter'); ?> : </br><b><?= $detail['diameter']?> mm</b></span>
                            <span class="mb-3 text-sm"><?= lang('label_shiftment'); ?> : </br><b><?= $detail['shift_name']?></b></span>
                        </div>
                        <div class="pl-5 d-flex flex-column">
                            <span class="mb-3 text-sm"><?= lang('label_start_date'); ?> : </br><b><?= $detail['start_date']?></b></span>
                            <span class="mb-3 text-sm"><?= lang('label_target'); ?> : </br><b><?= $detail['qty_target']?> Kg/Shift</b></span>
                        </div>

                        </li>
                    </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-8 pl-4 align-items-center">
                            <span class="text-lg mb-0"><b><?= lang('label_materials'); ?></b></span></br>
                            <span class="text-sm mb-0"><?= lang('label_material_for_production'); ?></span>
                        </div>
                        <div class="col-4 text-end">
                            <a href="<?= site_url('admin/material/addmaterial'); ?>" class="btn btn-outline-dark btn-sm mb-0"><?= lang('btn_add_material'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('label_used'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_action'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($p_material)) : $i = 1; foreach ($p_material as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex pl-3">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->material_name?></span> </br>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->used_stock?> <?= lang('unit_gram'); ?></span> </br>
                        </td>
                        <td class="">
                        <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="<?= site_url('admin/deleteMaterial/'.$value->id_pmaterial.'/'.$value->id_material); ?>"><i class="material-icons text-sm me-2">delete</i><?= lang('btn_delete'); ?></a>
                        </td>
                        <?php  endforeach; endif;?>
                        </tr>
                    </tbody>
                    </table>
                </div>
                </div>

              </div>
            </div>

          </div>
        </div>
        <div class="col-lg-6">
          <div class="card pb-4">
            <div class="card-header pb-0 p-3">
            <div class="row">
                <div class="col-8 pl-4 align-items-center">
                    <span class="text-lg mb-0"><b><?= lang('label_machines_on_process'); ?> </b></span></br>
                    <span class="text-sm mb-0"><?= lang('label_finish_machine_before_complete'); ?></span>
                </div>
                <div class="col-4 text-end">
                    <a href="<?= site_url('admin/machine/addmachine'); ?>" class="btn btn-outline-dark btn-sm mb-0"><?= lang('btn_add_machine'); ?></a>
                </div>
            </div>
            <div class="card-body p-3 pb-0">
            <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-2"><?= lang('table_machine'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_status'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-4"></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($p_machine)) : $i = 1; foreach ($p_machine as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex ">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="">
                            <span class="text-sm font-weight-bold"><?= $value->machine_name?>/<?= $value->capacity?> <?= lang('unit_pieces_per_hour'); ?></span> </br>
                        </td>
                        <td>
                        <?php if ( $value->mc_stats == 1) :?>
                            <span class="text-xs font-weight-bold text-success"><?= lang('status_finished'); ?></span>
                        <?php elseif ( $value->mc_stats == 2) : ?>
                            <span class="text-xs font-weight-bold text-danger"><?= lang('status_processing'); ?></span>
                        <?php endif ;?> 

                        <td class="pl-4">
                        <?php if ( $value->mc_stats == 2) :?>
                            <a class="btn btn-link text-info text-gradient px-3 mb-0" href="<?= site_url('admin/finishMachine/'.$value->id_pmachine.'/'.$value->id_machine); ?>"><i class="material-icons text-sm me-2">logout</i><?= lang('btn_finish'); ?></a>
                        <?php elseif ( $value->mc_stats == 1) : ?>
                            <span class="text-xs font-weight-bold text-success"></span>
                        </td>
                        <?php endif ;?> 
                        <?php  endforeach; endif;?>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
