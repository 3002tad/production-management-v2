<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?php echo $title; ?></h1>
    
    <!-- Search Box -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="<?php echo base_url('admin/staff/search'); ?>" method="get" class="form-inline">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên nhân viên</th>
                            <th>Chức vụ</th>
                            <th>Phòng ban</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($staffs as $index => $staff): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $staff->name; ?></td>
                            <td><?php echo $staff->position; ?></td>
                            <td><?php echo $staff->department; ?></td>
                            <td><?php echo $staff->phone; ?></td>
                            <td><?php echo $staff->email; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
