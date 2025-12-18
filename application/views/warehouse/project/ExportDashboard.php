<div class="card">
    <div class="card-header card-header-rose py-2 px-3">
        <div>
            <h6 class="mb-0">Tiến độ xuất theo kế hoạch</h6>
            <span class="text-sm">Tổng hợp xuất kho theo từng kế hoạch</span>
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
                <span class="badge bg-gradient-info text-white"><?= $pct ?>%</span>
            </div>
            <div class="progress" style="height:6px;">
                <div class="progress-bar bg-gradient-info" role="progressbar" style="width: <?= $pct ?>%"></div>
            </div>
            <div class="d-flex flex-wrap gap-3 mt-2 text-xs">
                <span class="badge bg-gradient-secondary">Tổng KH: <strong class="ms-1"><?= $tp ?></strong></span>
                <span class="badge bg-gradient-primary">Đã xuất: <strong class="ms-1"><?= $te ?></strong></span>
                <span class="badge bg-gradient-warning">Còn lại: <strong class="ms-1"><?= $tr ?></strong></span>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-sm table-center align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>Mã NVL</th>
                            <th>NVL</th>
                            <th>Kế hoạch</th>
                            <th>Đã xuất</th>
                            <th>Còn lại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($pl['items'] ?? []) as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['id_material'] ?? ($it['id'] ?? '-')) ?></td>
                            <td><?= htmlspecialchars($it['name'] ?? '-') ?></td>
                            <td><?= (int)($it['planned'] ?? 0) ?></td>
                            <td><?= (int)($it['exported'] ?? 0) ?></td>
                            <td><?= (int)($it['remaining'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">Tổng</th>
                            <th><?= $tp ?></th>
                            <th><?= $te ?></th>
                            <th><?= $tr ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
