<div class="card">
    <div class="card-header">Báo cáo tồn kho</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên</th>
                        <th>Tồn</th>
                        <th>Tối thiểu</th>
                        <th>ĐVT</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (($materials ?? []) as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m->id_material ?? ($m->id ?? '')) ?></td>
                        <td><?= htmlspecialchars($m->material_name ?? ($m->name ?? '')) ?></td>
                        <td><?= (int)($m->stock ?? ($m->qty ?? 0)) ?></td>
                        <td><?= (int)($m->min_stock ?? 0) ?></td>
                        <td><?= htmlspecialchars($m->uom ?? ($m->unit ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
