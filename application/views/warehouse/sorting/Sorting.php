<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
          <a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a>
        </li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('menu_report'); ?></li>
      </ol>
      <h6 class="font-weight-bolder mb-0"><?= lang('menu_report'); ?></h6>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <div class="ms-md-auto pe-md-3 d-flex align-items-center">
        <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
      </div>
    </div>
  </div>
</nav>

</br>

<div class="row px-2">
  <div class="col-12">
    <div class="card my-2">
      <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
        <div class="shadow-dark border-radius-lg d-flex px-5 pt-4 pb-3">
          <div class="col-6 d-flex align-items-center">
            <i class="material-icons pr-3">history</i>
            <h6 class="mb-0"><?= lang('label_production_history'); ?></h6>
          </div>
          <div class="col-6 text-end">
            <span class="text-sm mb-0 opacity-8"><?= lang('dashboard_summary_finished'); ?></span>
          </div>
        </div>
      </div>

      <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
          <table id="table-data" class="table align-items-center justify-content-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_no'); ?></th>
                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('menu_planning'); ?></th>
                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('menu_shiftment'); ?></th>
                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('dashboard_finished_products'); ?></th>
                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><?= lang('table_waste'); ?></th>
              </tr>
            </thead>
            <tbody class="pl-3">
              <?php if (!empty($sorting)) : $i = 1; foreach ($sorting as $row) : ?>
              <tr>
                <td>
                  <div class="d-flex pl-3">
                    <div class="my-auto">
                      <h6 class="mb-0 text-sm"><?= $i++; ?></h6>
                    </div>
                  </div>
                </td>
                <td class="pl-4">
                  <span class="text-sm font-weight-bold"><?= $row->plan_name; ?></span>
                </td>
                <td class="pl-4">
                  <span class="text-sm font-weight-bold"><?= $row->staff_name; ?> <?= isset($row->shift_name) ? '(' . $row->shift_name . ')' : ''; ?></span>
                </td>
                <td class="pl-4">
                  <span class="text-sm font-weight-bold"><?= $row->finished; ?> <?= lang('unit_pieces'); ?></span>
                </td>
                <td class="pl-4">
                  <span class="text-sm font-weight-bold"><?= $row->waste; ?> <?= lang('unit_pieces'); ?></span>
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
