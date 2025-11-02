<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">lan</i>
                        Quản lý Sản phẩm
                    </h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table id="table" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã SP</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên sản phẩm</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($data as $row): ?>
                            <tr>
                                <td class="align-middle text-center text-sm">
                                    <span class="text-secondary text-xs font-weight-bold"><?= $no++; ?></span>
                                </td>
                                <td>
                                    <span class="text-xs font-weight-bold"><?= $row->id_product; ?></span>
                                </td>
                                <td>
                                    <span class="text-xs font-weight-bold"><?= $row->product_name; ?></span>
                                </td>
                                <td>
                                    <span class="text-xs font-weight-bold"><?= $row->description ?? 'N/A'; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
