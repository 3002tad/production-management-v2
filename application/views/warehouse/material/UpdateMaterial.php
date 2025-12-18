<div class="card">
    <div class="card-header">Cập nhật nguyên vật liệu</div>
    <div class="card-body">
        <?php $d = $detail ?? (isset($material)?[
            'id_material'=>$material->id,
            'material_name'=>$material->name,
            'material_type'=>'',
            'stock'=>$material->stock,
            'min_stock'=>0,
            'uom'=>$material->unit
        ]:[]); ?>
        <form method="post" action="<?= isset($detail)?site_url('warehouse/updateMaterial'):site_url('warehouse/update_material/'.urlencode($material->id)) ?>">
            <?php if (isset($detail)): ?>
            <input type="hidden" name="old_id_material" value="<?= htmlspecialchars($detail['id_material']) ?>" />
            <?php endif; ?>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Mã NVL</label>
                    <input type="text" name="id_material" class="form-control" value="<?= htmlspecialchars($d['id_material'] ?? '') ?>" />
                </div>
                <div class="form-group col-md-4">
                    <label>Tên NVL *</label>
                    <input type="text" name="material_name" class="form-control" value="<?= htmlspecialchars($d['material_name'] ?? '') ?>" required />
                </div>
                <div class="form-group col-md-4">
                    <label>Loại NVL</label>
                    <input type="text" name="material_type" class="form-control" value="<?= htmlspecialchars($d['material_type'] ?? '') ?>" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tồn</label>
                    <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($d['stock'] ?? 0) ?>" />
                </div>
                <div class="form-group col-md-3">
                    <label>Tồn tối thiểu</label>
                    <input type="number" name="min_stock" class="form-control" value="<?= htmlspecialchars($d['min_stock'] ?? 0) ?>" />
                </div>
                <div class="form-group col-md-3">
                    <label>Đơn vị tính *</label>
                    <select name="uom" class="form-control" required>
                        <option value="">-- Chọn đơn vị --</option>
                        <option value="g" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'g') ? 'selected' : '' ?>>Gram (g)</option>
                        <option value="kg" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'kg') ? 'selected' : '' ?>>Kilogram (kg)</option>
                        <option value="pcs" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'pcs') ? 'selected' : '' ?>>Pieces (pcs)</option>
                        <option value="m" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'm') ? 'selected' : '' ?>>Meter (m)</option>
                        <option value="cm" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'cm') ? 'selected' : '' ?>>Centimeter (cm)</option>
                        <option value="mm" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'mm') ? 'selected' : '' ?>>Millimeter (mm)</option>
                        <option value="ml" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'ml') ? 'selected' : '' ?>>Milliliter (ml)</option>
                        <option value="l" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'l') ? 'selected' : '' ?>>Liter (l)</option>
                        <option value="box" <?= (isset($d['uom']) && trim(strtolower($d['uom'])) === 'box') ? 'selected' : '' ?>>Box</option>
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="<?= site_url('warehouse/material') ?>" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
