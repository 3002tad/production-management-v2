<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">schedule</i>
                        Kế hoạch sản xuất (Chỉ xem)
                    </h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="table" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã KH</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên KH</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dự án</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">SL mục tiêu</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày kết thúc</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-sm py-3">
                                        <i class="material-icons opacity-10">info</i>
                                        Chưa có kế hoạch sản xuất nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($data as $row): ?>
                                <tr>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-secondary text-xs font-weight-bold"><?= $no++; ?></span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold"><?= $row->id_plan; ?></span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold"><?= $row->plan_name; ?></span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold"><?= $row->id_project; ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-xs font-weight-bold"><?= number_format($row->qty_target); ?> chiếc</span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-weight-bold"><?= date('d/m/Y', strtotime($row->end_date)); ?></span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <?php if ($row->pl_status == 1): ?>
                                            <span class="badge badge-sm bg-gradient-success">Đang thực hiện</span>
                                        <?php else: ?>
                                            <span class="badge badge-sm bg-gradient-secondary">Chưa bắt đầu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
