<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;"><?= lang('breadcrumb_pages'); ?></a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= lang('breadcrumb_projects'); ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= lang('label_add_project'); ?></h6>
            </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <h6 class="text-sm font-weight-bolder mb-0"><?= lang('title_production_system'); ?></h6>
            </div>
        </div>
    </nav>
</br>
<div class="d-flex justify-content-center">
    <div class="col-lg-10 col-md-12">
        <div class="card">
        <div class="card-header card-header-primary">
            <div class="row">
                <div class="col-7 align-items-center pl-4">
                    <h4 class="mb-0"><?= lang('label_add_project'); ?></h4>
                    <span class="text-sm mb-0 text-end"><?= lang('form_add_new'); ?></span>
                </div>
            </div>
            <div class="d-flex pt-4" method="post">
                <div class="col-8">
                    <div class="card border-0 d-flex p-4 pt-0 mb-2 bg-gray-100">
                    
                    <!-- ============================================================= -->
                    <!-- FLASH MESSAGES - Hiển thị thông báo lỗi/cảnh báo/thành công -->
                    <!-- Basic Flow Bước 8, AF 4.1.1, AF 6.1.1, Exception 5.2.1       -->
                    <!-- ============================================================= -->
                    
                    <!-- Thông báo LỖI (AF 4.1.1 - Thiếu dữ liệu, Exception 5.2.1 - Lỗi DB) -->
                    <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                            <div>
                                <strong>Lỗi!</strong><br>
                                <?= $this->session->flashdata('error'); ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Thông báo CẢNH BÁO (AF 6.1.1 - Vượt công suất) -->
                    <?php if ($this->session->flashdata('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <strong>Cảnh báo!</strong><br>
                                <?= $this->session->flashdata('warning'); ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Thông báo THÀNH CÔNG (BF Bước 8 - Tạo đơn thành công) -->
                    <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <div>
                                <strong>Thành công!</strong><br>
                                <?= $this->session->flashdata('success'); ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- ============================================================= -->
                    <!-- FORM TIẾP NHẬN ĐƠN HÀNG                                      -->
                    <!-- Basic Flow Bước 3                                            -->
                    <!-- ============================================================= -->
                    
                    <form class="pt-4" id="order_form" action="<?= site_url('admin/addProject'); ?>" method="post">
                    
                        <div class="row d-flex">
                            <div class="col-4">
                                <span><?= lang('form_project_name'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="hidden" name="pr_status" value="1">
                                    <input type="text" name="project_name" value="PJ-" class="form-control">
                                </div>
                            </div>
                            <div class="col-1">
                            </div>
                            <div class="col-7">
                            <span><?= lang('form_date'); ?></span></br>
                            <div class="input-group input-group-dynamic mb-4">
                                <label class="form-label"></label>
                                <input type="date" name="entry_date" class="form-control">
                            </div>
                            </div>
                        </div>
                        <span><?= lang('menu_customer'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <select class="selectpicker form-control" name="id_cust" data-style="btn btn-link" data-live-search="true">
                                <option disabled selected><?= lang('form_select_customer'); ?></option>
                                <?php if (!empty($customer)) : $i = 1; foreach ($customer as $value) : ?>
                                    <option value="<?= $value->id_cust; ?>"><?= $value->cust_name; ?></option> 
                                <?php endforeach; endif; ?>
                            </select>                      
                        </div>
                        <span><?= lang('menu_product'); ?></span></br>
                        <div class="input-group input-group-dynamic mb-4">
                            <select class="selectpicker form-control" id="product_select" name="id_product" data-style="btn btn-link" data-live-search="true">
                                <option disabled selected><?= lang('form_select_product'); ?></option>
                                <?php if (!empty($product)) : $i = 1; foreach ($product as $value) : ?>
                                    <option value="<?= $value->id_product; ?>" data-diameter="<?= $value->diameter; ?>"><?= $value->product_name; ?></option> 
                                <?php endforeach; endif; ?>
                            </select>    
                        </div>
                        <div class="row d-flex">
                            <div class="col-5">
                                <span><?= lang('form_quantity'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="number" name="qty_request" class="form-control">
                                    <p class="text-end pt-2"><?= lang('unit_pieces'); ?></p>
                                </div>
                            </div>
                            <div class="col-2">
                            </div>
                            <div class="col-5 pl-3">
                                <span><?= lang('form_diameter'); ?></span></br>
                                <div class="input-group input-group-dynamic mb-4">
                                    <label class="form-label"></label>
                                    <input type="number" step="0.1" id="diameter_input" name="diameter" class="form-control" placeholder="Tự động điền từ sản phẩm">
                                    <p class="text-end pt-2"><?= lang('unit_mm'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================================= -->
                        <!-- TRƯỜNG YÊU CẦU KHÁCH HÀNG (Optional)                         -->
                        <!-- Basic Flow Bước 3 - Yêu cầu của khách hàng (nếu có)          -->
                        <!-- ============================================================= -->
                        <span>Yêu cầu của khách hàng (nếu có)</span>
                        <small class="text-muted"> - Ví dụ: màu sắc, bao bì, thời gian giao đặc biệt...</small>
                        <div class="input-group input-group-dynamic mb-4">
                            <textarea name="customer_request" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Nhập các yêu cầu đặc biệt của khách hàng (nếu có)..."></textarea>
                        </div>

                    </div>
                </div>
                <div class="col-4">
                    <div class="pr-2">
                        <span><?= lang('msg_confirm_save'); ?></span></br>
                    </div>
                    <div class="d-flex">
                        <div class="pt-2 pl-2">
                            <a class="btn btn-outline-dark btn-sm mb-0" href="<?= site_url('admin/project'); ?>"><?= lang('btn_back'); ?></a>
                        </div>
                        <div class="pt-2 pl-2">
                            <button class="btn btn-dark btn-sm mb-0" type="submit"><?= lang('btn_save'); ?></button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        </div>
    </div>
<div>

<script>
// ============================================================================
// JAVASCRIPT VALIDATION & CONFIRM DIALOG
// Alternative Flow 4.1 - Kiểm tra thiếu dữ liệu bắt buộc
// Exception 5.1 - Hủy đơn trước khi lưu
// ============================================================================

$(document).ready(function() {
    
    // ========================================================================
    // AUTO-FILL DIAMETER KHI CHỌN PRODUCT
    // Basic Flow Bước 2 - Hiển thị gợi ý hợp lệ
    // ========================================================================
    $('#product_select').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var diameter = selectedOption.data('diameter');
        
        if (diameter) {
            // Tự động điền diameter vào input
            $('#diameter_input').val(diameter);
            
            // Hiệu ứng highlight
            $('#diameter_input').addClass('is-valid');
            setTimeout(function() {
                $('#diameter_input').removeClass('is-valid');
            }, 1500);
        }
    });
    
    // Xử lý cho selectpicker khi đã load
    $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        if ($(this).attr('id') === 'product_select') {
            var selectedOption = $(this).find('option:selected');
            var diameter = selectedOption.data('diameter');
            
            if (diameter) {
                $('#diameter_input').val(diameter);
                $('#diameter_input').addClass('is-valid');
                setTimeout(function() {
                    $('#diameter_input').removeClass('is-valid');
                }, 1500);
            }
        }
    });
    
    // ========================================================================
    // CLIENT-SIDE VALIDATION
    // Alternative Flow 4.1 - Thiếu dữ liệu bắt buộc
    // ========================================================================
    $('#order_form').on('submit', function(e) {
        // Lấy giá trị từ form
        const id_cust = $('select[name="id_cust"]').val();
        const id_product = $('select[name="id_product"]').val();
        const diameter = $('input[name="diameter"]').val();
        const qty_request = parseInt($('input[name="qty_request"]').val());
        const entry_date = $('input[name="entry_date"]').val();
        
        let errorMessage = '';
        
        // Kiểm tra khách hàng
        if (!id_cust || id_cust === '') {
            errorMessage += '• Vui lòng chọn khách hàng\n';
        }
        
        // Kiểm tra sản phẩm
        if (!id_product || id_product === '') {
            errorMessage += '• Vui lòng chọn sản phẩm\n';
        }
        
        // Kiểm tra đường kính
        if (!diameter || diameter === '' || parseFloat(diameter) <= 0) {
            errorMessage += '• Vui lòng nhập đường kính hợp lệ (> 0)\n';
        }
        
        // Kiểm tra số lượng (AF 4.1 - Số lượng phải > 0)
        if (!qty_request || isNaN(qty_request) || qty_request <= 0) {
            e.preventDefault();
            alert('⚠️ LỖI: Số lượng phải lớn hơn 0\n\nVui lòng nhập lại.');
            $('input[name="qty_request"]').focus();
            return false;
        }
        
        // Kiểm tra hạn giao (AF 4.1 - Hạn giao phải >= hôm nay)
        if (!entry_date || entry_date === '') {
            errorMessage += '• Vui lòng nhập hạn giao\n';
        } else {
            const entryDateObj = new Date(entry_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (entryDateObj < today) {
                e.preventDefault();
                alert('⚠️ LỖI: Hạn giao phải từ hôm nay trở đi\n\nNgày bạn chọn: ' + entry_date + '\nNgày hôm nay: ' + today.toISOString().split('T')[0]);
                $('input[name="entry_date"]').focus();
                return false;
            }
        }
        
        // Nếu có lỗi validation
        if (errorMessage !== '') {
            e.preventDefault();
            alert('⚠️ LỖI: Thiếu dữ liệu bắt buộc\n\n' + errorMessage + '\nVui lòng nhập đầy đủ thông tin.');
            return false;
        }
        
        // ====================================================================
        // CONFIRM DIALOG - Exception 5.1: Khách hàng hủy đơn trước khi lưu
        // ====================================================================
        const confirmMessage = 
            '🎯 XÁC NHẬN TẠO ĐƠN HÀNG\n\n' +
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n' +
            '📦 Sản phẩm: ' + $('select[name="id_product"] option:selected').text() + '\n' +
            '👤 Khách hàng: ' + $('select[name="id_cust"] option:selected').text() + '\n' +
            '📊 Số lượng: ' + qty_request.toLocaleString() + ' chiếc\n' +
            '📏 Đường kính: ' + diameter + ' mm\n' +
            '📅 Hạn giao: ' + entry_date + '\n' +
            '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n' +
            '✅ Bấm OK để LƯU VÀ DUYỆT đơn hàng\n' +
            '❌ Bấm Cancel để HỦY và quay lại';
        
        if (!confirm(confirmMessage)) {
            // Exception 5.1.2 - Hiển thị thông báo xác nhận
            // Exception 5.1.3 - Ban giám đốc xác nhận hủy
            e.preventDefault();
            alert('❌ Đã hủy tạo đơn hàng.\n\nBạn có thể tiếp tục chỉnh sửa hoặc quay lại.');
            // Exception 5.1.4 - Kết thúc use case
            return false;
        }
        
        // Nếu confirm = OK → Submit form (tiếp tục Basic Flow)
        return true;
    });
});
</script>