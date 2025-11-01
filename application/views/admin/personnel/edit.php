<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa thông tin nhân sự</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('personnel/edit/'.$personnel->id) ?>" method="post">
                <div class="form-group">
                    <label for="name">Tên nhân sự</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $personnel->name ?>" required>
                </div>
                <div class="form-group">
                    <label for="position">Chức vụ</label>
                    <input type="text" class="form-control" id="position" name="position" value="<?= $personnel->position ?>" required>
                </div>
                <div class="form-group">
                    <label for="department">Phòng ban</label>
                    <input type="text" class="form-control" id="department" name="department" value="<?= $personnel->department ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= $personnel->phone ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $personnel->email ?>">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= $personnel->address ?></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active" <?= $personnel->status == 'active' ? 'selected' : '' ?>>Đang làm việc</option>
                        <option value="inactive" <?= $personnel->status == 'inactive' ? 'selected' : '' ?>>Nghỉ việc</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="<?= base_url('personnel') ?>" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>