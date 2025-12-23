<div class="card">
    <div class="card-header">Thêm nguyên vật liệu mới</div>
    <div class="card-body">
        <form method="post" action="<?= site_url('warehouse/addNewMaterial') ?>">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Mã NVL (tùy chọn)</label>
                    <input type="text" name="id_material" class="form-control" />
                </div>
                <div class="form-group col-md-4">
                    <label>Tên NVL *</label>
                    <input type="text" name="material_name" class="form-control" required />
                </div>
                <div class="form-group col-md-4">
                    <label>Loại NVL *</label>
                    <input type="text" name="material_type" class="form-control" required />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tồn ban đầu</label>
                    <input type="number" name="stock" class="form-control" value="0" required />
                </div>
                <div class="form-group col-md-3">
                    <label>Tồn tối thiểu</label>
                    <input type="number" name="min_stock" class="form-control" value="0" required />
                </div>
                <div class="form-group col-md-3">
                    <label>Đơn vị tính *</label>
                    <select name="uom" class="form-control" required>
                        <option value="g">Gram (g)</option>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="m">Meter (m)</option>
                        <option value="cm">Centimeter (cm)</option>
                        <option value="mm">Millimeter (mm)</option>
                        <option value="ml">Milliliter (ml)</option>
                        <option value="l">Liter (l)</option>
                        <option value="box">Box</option>
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
