<div class="card">
    <div class="card-header card-header-rose">
        <div>
            <h6 class="mb-0">Xuất NVL cho ca/kế hoạch</h6>
            <span class="text-sm">Chọn kế hoạch/ca (tuỳ chọn) và nhập số lượng</span>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="<?= site_url('warehouse/save_stock_out') ?>" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Kế hoạch (tuỳ chọn)</label>
                    <select name="id_plan" class="form-control">
                        <option value="">-- Chọn kế hoạch --</option>
                        <?php foreach (($plans ?? []) as $p): ?>
                            <option value="<?= (int)$p->id_plan ?>"><?= htmlspecialchars($p->plan_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Ca sản xuất (tuỳ chọn)</label>
                    <select name="id_planshift" class="form-control">
                        <option value="">-- Chọn ca --</option>
                        <?php foreach (($shifts ?? []) as $s): ?>
                            <option value="<?= (int)$s->id_planshift ?>"><?= htmlspecialchars($s->id_planshift.' - '.$s->ps_name ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Ngày xuất</label>
                    <input type="date" name="date_out" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <input type="text" name="note" class="form-control" />
            </div>
            <div class="form-group">
                <label>File đính kèm</label>
                <input type="file" name="attachment" class="form-control-file" />
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-sm table-center align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>NVL</th>
                            <th>Tồn</th>
                            <th>Số lượng xuất</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($materials ?? []) as $m): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($m->material_name ?? ($m->name ?? '')) ?>
                            </td>
                            <td><?= (int)($m->stock ?? 0) ?></td>
                            <td>
                                <input type="number"
                                       min="0"
                                       max="<?= (int)($m->stock ?? 0) ?>"
                                       name="items[<?= (int)($m->id_material ?? $m->id) ?>]"
                                       class="form-control form-control-sm"
                                       placeholder="0"
                                       />
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn bg-gradient-primary text-white">
                    <i class="material-icons-round align-middle" style="font-size:18px">outbox</i>
                    <span class="align-middle ml-1">Xuất kho</span>
                </button>
                <a href="<?= site_url('warehouse/material') ?>" class="btn bg-gradient-secondary text-white">
                    <i class="material-icons-round align-middle" style="font-size:18px">close</i>
                    <span class="align-middle ml-1">Hủy</span>
                </a>
            </div>
        </form>
    </div>
</div>
