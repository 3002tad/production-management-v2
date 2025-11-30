<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  DataTables Component - Reusable Table with Material Design 3.0             ║
║  Usage: Include trong các list views (Customer, Product, Order)             ║
╚══════════════════════════════════════════════════════════════════════════════╝

PARAMETERS:
- $table_id: ID của table (VD: customerTable, productTable)
- $columns: Array columns config [
    ['title' => 'STT', 'data' => null, 'width' => '50px'],
    ['title' => 'Tên KH', 'data' => 'cust_name']
  ]
- $ajax_url: URL để load data (VD: site_url('BOD/customer/ajax_list'))
- $language_url: URL file Vietnamese JSON
- $page_length: Số rows mỗi trang (default: 25)
- $order: Cột sort mặc định (default: [[1, 'desc']])
- $buttons: Array buttons config (VD: ['copy', 'excel', 'pdf'])
-->

<div class="table-responsive">
    <table id="<?= $table_id; ?>" class="table align-items-center mb-0" style="width:100%">
        <thead>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 
                               <?= isset($column['class']) ? $column['class'] : ''; ?>"
                        style="font-family: 'Poppins', sans-serif; <?= isset($column['width']) ? 'width: ' . $column['width'] . ';' : ''; ?>">
                        <?= $column['title']; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <!-- DataTables will populate this -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    const table = $('#<?= $table_id; ?>').DataTable({
        // Language
        "language": {
            "url": "<?= isset($language_url) ? $language_url : base_url('asset/Backend/json/Vietnamese.json'); ?>"
        },
        
        // Display
        "pageLength": <?= isset($page_length) ? $page_length : 25; ?>,
        "order": <?= isset($order) ? json_encode($order) : '[[1, "desc"]]'; ?>,
        "dom": 'lrtip', // Remove default search (custom search provided)
        
        // Performance optimization
        "deferRender": true,
        "processing": false,
        "serverSide": false, // Change to true for large datasets
        
        <?php if (isset($ajax_url)): ?>
        // AJAX data source
        "ajax": {
            "url": "<?= $ajax_url; ?>",
            "type": "POST",
            "dataSrc": "data",
            "error": function(xhr, error, code) {
                console.error('DataTables error:', error, code);
                alert('Lỗi tải dữ liệu. Vui lòng thử lại!');
            }
        },
        <?php endif; ?>
        
        // Column definitions
        "columns": <?= json_encode($columns); ?>,
        
        <?php if (isset($column_defs)): ?>
        "columnDefs": <?= json_encode($column_defs); ?>,
        <?php endif; ?>
        
        <?php if (isset($buttons) && !empty($buttons)): ?>
        // Buttons for export
        "buttons": <?= json_encode($buttons); ?>,
        <?php endif; ?>
        
        // Responsive
        "responsive": true,
        
        // Callback after draw
        "drawCallback": function(settings) {
            // Re-initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Re-bind custom events
            if (typeof window.bindTableEvents === 'function') {
                window.bindTableEvents();
            }
        }
    });
    
    <?php if (isset($custom_search_input)): ?>
    // Custom search input binding
    $('#<?= $custom_search_input; ?>').on('keyup', function() {
        table.search(this.value).draw();
    });
    <?php endif; ?>
    
    // Event delegation for tooltips (performance optimization)
    $('body').tooltip({
        selector: '[data-bs-toggle="tooltip"]',
        trigger: 'hover'
    });
});
</script>
