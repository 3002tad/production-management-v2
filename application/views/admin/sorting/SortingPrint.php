    <!-- End Navbar -->
<div class="container-fluid">
    <div class="p-4">
        <div class="row">
            <div class="col-10 d-flex align-items-center">
                <h4 class="mb-0 p-2 "><?= lang('label_production_report'); ?>.</h4>
                <button onclick="window.print()"><?= lang('btn_print_out'); ?></button>
            </div>
        </div>
    </div>
<div class="col-12 mb-lg-0 mb-4">
    <div class="row">
    <div class="card">
    <div class="row">
        <div class="col-lg-6">
                <div class="col align-items-center p-3 pb-3">
                    <span class="text-lg"><?= lang('label_shiftment_head'); ?> :  <b><?= $detail['staff_name']?></b><br/></span>
                    <span class="text-sm mb-0"><?= lang('label_detail_on_production'); ?></span>
                </div>
            <div class="row">
                <div class="col-6 p-3">
                        <div class="col align-items-center">
                            <span class="text-md"><?= lang('label_finished_goods'); ?> :  </span>
                            <span class="text-xl"><b><?= $detail['finished']?></b> <?= lang('unit_pieces'); ?></br></span>
                            <span class="text-sm mb-0"><?= lang('label_detail_on_production'); ?></span>
                        </div>
                </div>
                <div class="col-6 p-3">
                        <div class="col align-items-center">
                            <span class="text-md"><?= lang('label_production_waste'); ?> :  </span>
                            <span class="text-xl"><b><?= $detail['waste']?></b> <?= lang('unit_pieces'); ?></br> </span>
                            <span class="text-sm mb-0"><?= lang('label_detail_on_production'); ?></span>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
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
                            <span class="mb-3 text-sm"><?= lang('table_shiftment'); ?> : </br><b><?= $detail['shift_name']?></b></span>
                        </div>
                        <div class="pl-5 d-flex flex-column">
                            <span class="mb-3 text-sm"><?= lang('label_start_date'); ?> : </br><b><?= $detail['start_date']?></b></span>
                            <span class="mb-3 text-sm"><?= lang('label_target'); ?> : </br><b><?= $detail['qty_target']?> <?= lang('unit_pieces'); ?>/<?= lang('table_shiftment'); ?></b></span>
                        </div>
                        </li>
                    </ul>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    </div>
      <div class="row">
        <div class="col-lg-6 p-0">
          <div class="row">
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-8 pl-4 align-items-center">
                            <span class="text-lg mb-0"><b><?= lang('label_materials_used'); ?></b></span></br>
                            <span class="text-sm mb-0"><?= lang('label_material_for_production'); ?></span>
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
        <div class="col-lg-6 pt-4 pl-4 pr-0">
          <div class="card pb-4">
            <div class="card-header pb-0 p-3">
            <div class="row">
                <div class="col-8 pl-4 align-items-center">
                    <span class="text-lg mb-0"><b><?= lang('label_machines_used'); ?></b></span></br>
                    <span class="text-sm mb-0"><?= lang('label_machine_used_history'); ?></span>
                </div>
            </div>
            <div class="card-body p-3 pb-0">
            <div class="table-responsive">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-2"><?= lang('table_machine'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-2"><?= lang('label_capacity'); ?></th>
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
                            <span class="text-sm font-weight-bold"><?= $value->machine_name?></span> </br>
                        </td>
                        <td class="">
                            <span class="text-sm font-weight-bold"><?= $value->capacity?> <?= lang('unit_pieces_per_hour'); ?></span> </br>
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