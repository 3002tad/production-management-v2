<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_sorting'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('label_production_report'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
    <div class="row pr-2">
        <div class="col-12">
            <div class="card my-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
                    <div class="col-6 d-flex align-items-center">
                    <i class="material-icons pr-3">schedule</i>
                      <h6 class="mb-0">Production Report</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="table-data" class="table align-items-center justify-content-center mb-0">
                    <thead>
                        <tr>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_production'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_plan'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_shiftment'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_target_shift'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_finished'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_waste'); ?></th>
                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_action'); ?></th>
                        </tr>
                    </thead>
                    <tbody class="pl-3">
                    <?php if (!empty($sorting)) : $i = 1; foreach ($sorting as $value) : ?>
                        <tr>
                        <td>
                        <div class="d-flex pl-3">
                            <div class="my-auto">
                                <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                            </div>
                        </div>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->staff_name; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->plan_name; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->start_date; ?></span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->qty_target; ?> /Kg</span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->finished; ?> Kg</span>
                        </td>
                        <td class="pl-4">
                            <span class="text-sm font-weight-bold"><?= $value->waste; ?> Kg</span>
                        </td>
                        <td>
                        <a href="<?= site_url('leader/detail_sorting/'.$value->id_planshift.'/print'); ?>" rel="tooltip" title="<?= lang('tooltip_detail'); ?>"  class="badge bg-gradient-secondary"><?= lang('btn_print_out'); ?></a>
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