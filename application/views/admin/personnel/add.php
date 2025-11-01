<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm nhân sự mới</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('personnel/add') ?>" method="post">
                <div class="form-group">
                    <label for="name">Tên nhân sự</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="position">Chức vụ</label>
                    <input type="text" class="form-control" id="position" name="position" required>
                </div>
                <div class="form-group">
                    <label for="department">Phòng ban</label>
                    <input type="text" class="form-control" id="department" name="department" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active">Đang làm việc</option>
                        <option value="inactive">Nghỉ việc</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="<?= base_url('personnel') ?>" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>