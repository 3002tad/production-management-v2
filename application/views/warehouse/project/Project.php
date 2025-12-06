<!-- Breadcrumb Navigation -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tiến độ & Kế hoạch</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">Tiến độ & Kế hoạch</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <h6 class="text-sm font-weight-bolder mb-0">Production System</h6>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid ps-0 pe-3">
<style>
    .toggle-arrow{display:inline-block;transition:transform .2s ease;font-size:14px;line-height:1}
    .toggle-btn{padding:0;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;line-height:1;vertical-align:middle;position:relative;top:8px}
    .toggle-btn[aria-expanded="true"] .toggle-arrow{transform:rotate(180deg)}
    .pct-cell{ text-align:center }
    .pct-cell .progress{height:8px; width:140px; margin:0 auto; background:#e9ecef; border-radius:9999px; overflow:hidden}
    .pct-cell .progress-bar{transition:width .4s ease}
    .pct-cell small{font-size:.9rem; font-weight:600; display:block; margin-top:4px}
</style>
<div class="card mt-3">
    <div class="card-header card-header-success py-2 px-3">
        <div>
            <h6 class="mb-0">Tiến độ & Kế hoạch</h6>
            <span class="text-sm">Theo dõi tiến độ xuất NVL theo từng kế hoạch</span>
        </div>
    </div>
    <div class="card-body">
        <?php foreach (($plans_data ?? []) as $pl): ?>
        <?php 
            $pct = (int)($pl['progress_pct'] ?? 0);
            $tp = 0; $te = 0; $tr = 0;
            foreach (($pl['items'] ?? []) as $it) { $tp += (int)$it['planned']; $te += (int)$it['exported']; $tr += (int)$it['remaining']; }
        ?>
        <div class="mb-4 p-3 border rounded-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0"><?= htmlspecialchars($pl['plan_name']) ?></h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-gradient-success text-white"><?= $pct ?>%</span>
                    <?php $collapse_id = 'plan_'.(int)($pl['id_plan'] ?? 0); ?>
                    <button class="btn btn-sm btn-outline-secondary toggle-btn" type="button" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#<?= $collapse_id ?>" data-target="#<?= $collapse_id ?>" aria-expanded="false" aria-controls="<?= $collapse_id ?>" title="Mở/đóng chi tiết">
                        <span class="material-icons-round toggle-arrow">expand_more</span>
                    </button>
                </div>
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-gradient-success" role="progressbar" style="width: <?= $pct ?>%"></div>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-2 text-xs">
                <span class="badge bg-gradient-secondary">Tổng KH: <strong class="ms-1"><?= $tp ?></strong></span>
                <span class="badge bg-gradient-info">Đã xuất: <strong class="ms-1"><?= $te ?></strong></span>
                <span class="badge bg-gradient-warning">Còn lại: <strong class="ms-1"><?= $tr ?></strong></span>
            </div>
            <div class="collapse" id="<?= $collapse_id ?>">
            <div class="table-responsive mt-3">
                <table class="table table-sm table-center align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>Mã NVL</th>
                            <th>NVL</th>
                            <th>Kế hoạch</th>
                            <th>Đã xuất</th>
                            <th>Còn lại</th>
                            <th>Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($pl['items'] ?? []) as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['id_material'] ?? ($it['id'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars($it['name'] ?? '-') ?></td>
                            <?php 
                                $u = htmlspecialchars($it['uom'] ?? ''); 
                                $pp=(int)($it['planned']??0); 
                                $pe=(int)($it['exported']??0); 
                                $pr=$pp>0?round(($pe/$pp)*100):0; 
                                $barClass = ($pr >= 80) ? 'bg-gradient-success' : (($pr >= 50) ? 'bg-gradient-warning' : 'bg-gradient-danger');
                                $textClass = ($pr >= 80) ? 'text-success' : (($pr >= 50) ? 'text-warning' : 'text-danger');
                            ?>
                            <td><?= $pp . ($u !== '' ? ' ' . $u : '') ?></td>
                            <td><?= $pe . ($u !== '' ? ' ' . $u : '') ?></td>
                            <td><?= (int)($it['remaining'] ?? max(0,$pp-$pe)) . ($u !== '' ? ' ' . $u : '') ?></td>
                            <td class="pct-cell" style="min-width:140px">
                                <div class="progress">
                                    <div class="progress-bar <?= $barClass ?>" role="progressbar" style="width: <?= $pr ?>%"></div>
                                </div>
                                <small class="<?= $textClass ?> fw-bold"><?= $pr ?>%</small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    
                </table>
            </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    </div>
</div>
