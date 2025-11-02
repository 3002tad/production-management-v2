<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_material'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('breadcrumb_material'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
            </div>
        </div>
    </nav>
</br>
<div class="container-fluid pt-0 ">
        <div class="card-header p-0 w-75 position-fixed mt-n4 mx-2 z-index-2">
            <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                <div class="col-8 d-flex align-items-center">
                <i class="material-icons pr-3">view_in_ar</i>
                    <h6 class="mb-0 pr-4 "><?= lang('label_material_used'); ?></h6>
                </div>           
                <div class="col-4 text-end">
                    <a href="<?= site_url('admin/material/addnewmaterial'); ?>" class="btn badge-sm bg-gradient-secondary mb-0"><?= lang('btn_add_material'); ?></a>
                </div>
            </div>
        </div>
</div>
<div class="container py-4 pt-5">
    <div class="row">
        <div class="col-md-7 mt-4 pl-4">
          <div class="card">
            <div class="card-header pb-0 px-3">
              <h6 class="mb-0"><?= lang('label_material_history'); ?></h6>
              <span class="text-sm mb-0"><?= lang('label_material_history_desc'); ?></span>

            </div>
            <div class="card-body p-3">
                <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_out'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_production'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_date'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($material)) : $i = 1; foreach ($material as $value) : ?>
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
                                                        <?php 
                                                            $uh = isset($value->uom) ? $value->uom : 'g';
                                                            switch ($uh) {
                                                                case 'kg': $uh_label = lang('unit_kilogram'); break;
                                                                case 'g': $uh_label = lang('unit_gram'); break;
                                                                case 'pcs': $uh_label = lang('unit_pieces'); break;
                                                                case 'm': $uh_label = lang('unit_meter'); break;
                                                                case 'cm': $uh_label = lang('unit_centimeter'); break;
                                                                case 'box': $uh_label = lang('unit_box'); break;
                                                                default: $uh_label = $uh; break;
                                                            }
                                                        ?>
                                                        <span class="text-sm font-weight-bold"><?= $value->used_stock?> <?= $uh_label; ?></span> </br>
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
                    <h6 class="mb-0"><?= lang('label_material_status'); ?></h6>
                        <span class="text-sm mb-0"><?= lang('label_material_for_production'); ?></span>
                    </div>
                </div>
                    </div>
            <div class="card-body pt-4">
                <div class="table-responsive p-0">
                <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 pl-0"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_code'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_material'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_stock'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('label_min_stock'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_action'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($materials)) : $i = 1; foreach ($materials as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->id_material ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->material_name?></span>
                        </td>
                        <td class="pl-4">
                                                        <?php 
                                                            $u = isset($value->uom) ? $value->uom : 'g';
                                                            switch ($u) {
                                                                case 'kg': $u_label = lang('unit_kilogram'); break;
                                                                case 'g': $u_label = lang('unit_gram'); break;
                                                                case 'pcs': $u_label = lang('unit_pieces'); break;
                                                                case 'm': $u_label = lang('unit_meter'); break;
                                                                case 'cm': $u_label = lang('unit_centimeter'); break;
                                                                case 'box': $u_label = lang('unit_box'); break;
                                                                default: $u_label = $u; break;
                                                            }
                                                        ?>
                                                        <span class="text-sm font-weight-bold"><?= $value->stock?> <?= $u_label; ?></span>
                        </td>
                        <td class="pl-4">
                            <?php 
                              $u2 = isset($value->uom) ? $value->uom : 'g';
                              switch ($u2) {
                                case 'kg': $u2_label = lang('unit_kilogram'); break;
                                case 'g': $u2_label = lang('unit_gram'); break;
                                case 'pcs': $u2_label = lang('unit_pieces'); break;
                                case 'm': $u2_label = lang('unit_meter'); break;
                                case 'cm': $u2_label = lang('unit_centimeter'); break;
                                case 'box': $u2_label = lang('unit_box'); break;
                                default: $u2_label = $u2; break;
                              }
                            ?>
                            <span class="text-sm font-weight-bold"><?= isset($value->min_stock) ? $value->min_stock : 0 ?> <?= $u2_label; ?></span>
                        </td>
                        <td class="pl-4">
                            <a href="<?= site_url('admin/editMaterial/'.$value->id_material); ?>" class="btn btn-info btn-link btn-sm">
                                <i class="material-icons">edit</i>
                            </a>
                            <a href="<?= site_url('admin/deleteMaterialMaster/'.$value->id_material); ?>" onclick="return confirm('<?= lang('msg_confirm_delete_data');?>');" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                            </a>
                        </td>
                        </tr>
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
    </div>
</div>