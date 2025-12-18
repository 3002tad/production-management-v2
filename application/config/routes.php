<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;

$route['search'] = 'petugas/cari_member';

// =====================================================
// Production Simulator Routes - MUST BE FIRST
// =====================================================
$route['simulator/records/(:num)'] = 'Simulator/get_shift_records/$1';
$route['simulator/status'] = 'Simulator/status';
$route['simulator/run'] = 'Simulator/run';
$route['simulator/toggle'] = 'Simulator/toggle';
$route['simulator/update'] = 'Simulator/update_settings';
$route['simulator/(:any)'] = 'Simulator/$1';
$route['simulator'] = 'Simulator/settings';

// =====================================================
// User Management Routes (UC6 - Core & Security)
// =====================================================
$route['admin/user'] = 'admin/UserController';
$route['admin/user/add'] = 'admin/UserController/add';
$route['admin/user/create'] = 'admin/UserController/create';
$route['admin/user/edit/(:num)'] = 'admin/UserController/edit/$1';
$route['admin/user/update/(:num)'] = 'admin/UserController/update/$1';
$route['admin/user/delete/(:num)'] = 'admin/UserController/delete/$1';
$route['admin/user/toggle_status/(:num)'] = 'admin/UserController/toggle_status/$1';

// =====================================================
// BOD Module Routes (UC1, UC2, UC7, UC8)
// =====================================================
// Customer & Product Management (UC1, UC2, UC7)
// Sử dụng _remap() trong BOD.php để forward requests

// UC08 - Production Planning Routes
$route['BOD/planning'] = 'UC08/UC8_planning/planning';
$route['BOD/planning/(:any)'] = 'UC08/UC8_planning/$1';
$route['BOD/createPlan'] = 'UC08/UC8_planning/createPlan';
$route['BOD/storePlan'] = 'UC08/UC8_planning/storePlan';
$route['BOD/updatePlan'] = 'UC08/UC8_planning/updatePlan';
$route['BOD/deletePlan'] = 'UC08/UC8_planning/deletePlan';
$route['BOD/approvePlan'] = 'UC08/UC8_planning/approvePlan';
$route['BOD/approvePlan/(:num)'] = 'UC08/UC8_planning/approvePlan/$1';
$route['BOD/plans'] = 'UC08/UC8_planning/plans';
$route['BOD/report'] = 'UC08/UC8_planning/report';
$route['BOD/getProductBom'] = 'UC08/UC8_planning/getProductBom';

// =====================================================
// Leader Module Routes (UC9)
// =====================================================
// UC9 - Leader Planning Management
$route['leader/planning'] = 'UC09/Planning/planning';
$route['leader/planning/(:any)'] = 'UC09/Planning/$1';
$route['leader/ChangePlanning'] = 'UC09/Planning/ChangePlanning';
$route['leader/ChangePlanning/(:any)'] = 'UC09/Planning/ChangePlanning/$1';
$route['Leader/updatePlan'] = 'UC09/Planning/updatePlan';
$route['leader/updatePlan'] = 'UC09/Planning/updatePlan';

// Shift Closure Routes (must be BEFORE shift routes)
$route['leader/start_shift/(:num)'] = 'leader/Leader/start_shift/$1';
$route['leader/end_shift/(:num)'] = 'leader/Leader/end_shift/$1';
$route['leader/save_closure'] = 'leader/Leader/save_closure';
$route['leader/closure_detail/(:num)'] = 'leader/Leader/closure_detail/$1';

// Shift Management Routes (specific routes before catch-all)
$route['leader/shift/detail/(:num)'] = 'leader/shift/detail/$1';
$route['leader/shift/(:any)'] = 'leader/shift/$1';
$route['leader/shift'] = 'leader/shift/index';

// Machine Management Routes
$route['leader/machine/(:any)'] = 'leader/Machine/$1';
$route['leader/machine'] = 'leader/Machine/index';

// Leader dashboard and functions - This is catch-all, must be LAST
$route['leader/(:any)'] = 'leader/Leader/$1';
$route['leader'] = 'leader/Leader/index';

// =====================================================
// QC Module Routes
// =====================================================
$route['qc/sessions/(:num)'] = 'qc/sessions/$1';
$route['qc/sessions'] = 'qc/session_list';
$route['qc/reports'] = 'qc/reports';

// =====================================================
// Incident Reports Routes (UC15, UC16, UC17)
// =====================================================
// UC15 - BCSC (Báo cáo sự cố)
$route['uc15_qlns/uc15_bcsc'] = 'UC15_BCSC/UC15_BCSC/index';
$route['uc15_qlns/uc15_bcsc/add'] = 'UC15_BCSC/UC15_BCSC/add';
$route['uc15_qlns/uc15_bcsc/store'] = 'UC15_BCSC/UC15_BCSC/store';
$route['uc15_qlns/uc15_bcsc/edit/(:num)'] = 'UC15_BCSC/UC15_BCSC/edit/$1';
$route['uc15_qlns/uc15_bcsc/update/(:num)'] = 'UC15_BCSC/UC15_BCSC/update/$1';
$route['uc15_qlns/uc15_bcsc/update_status/(:num)'] = 'UC15_BCSC/UC15_BCSC/update_status/$1';
$route['uc15_qlns/uc15_bcsc/detail/(:num)'] = 'UC15_BCSC/UC15_BCSC/detail/$1';
$route['uc15_qlns/uc15_bcsc/delete/(:num)'] = 'UC15_BCSC/UC15_BCSC/delete/$1';

// Old routes (backward compatibility)
$route['uc15_bcsc/uc15_bcsc'] = 'UC15_BCSC/UC15_BCSC/index';
$route['uc15_bcsc/uc15_bcsc/add'] = 'UC15_BCSC/UC15_BCSC/add';
$route['uc15_bcsc/uc15_bcsc/store'] = 'UC15_BCSC/UC15_BCSC/store';
$route['uc15_bcsc/uc15_bcsc/edit/(:num)'] = 'UC15_BCSC/UC15_BCSC/edit/$1';
$route['uc15_bcsc/uc15_bcsc/update/(:num)'] = 'UC15_BCSC/UC15_BCSC/update/$1';
$route['uc15_bcsc/uc15_bcsc/update_status/(:num)'] = 'UC15_BCSC/UC15_BCSC/update_status/$1';
$route['uc15_bcsc/uc15_bcsc/detail/(:num)'] = 'UC15_BCSC/UC15_BCSC/detail/$1';
$route['uc15_bcsc/uc15_bcsc/delete/(:num)'] = 'UC15_BCSC/UC15_BCSC/delete/$1';

// UC16 - Ghi nhận & Điều phối
$route['uc16_gn_dp'] = 'UC16_GN_DP/UC16_GN_DP/index';
$route['uc16_gn_dp/view/(:num)'] = 'UC16_GN_DP/UC16_GN_DP/view/$1';
$route['uc16_gn_dp/submit_report'] = 'UC16_GN_DP/UC16_GN_DP/submit_report';
$route['uc16_gn_dp/confirm_dispatch'] = 'UC16_GN_DP/UC16_GN_DP/confirm_dispatch';
$route['uc16_gn_dp/assign_action'] = 'UC16_GN_DP/UC16_GN_DP/assign_action';
$route['uc16_gn_dp/mark_completed'] = 'UC16_GN_DP/UC16_GN_DP/mark_completed';
$route['uc16_gn_dp/history/(:num)'] = 'UC16_GN_DP/UC16_GN_DP/history/$1';

// UC17 - Xử lý sự cố
$route['uc17_xlsc'] = 'UC17_XLSC/UC17_XLSC/index';
$route['uc17_xlsc/view/(:num)'] = 'UC17_XLSC/UC17_XLSC/view/$1';
$route['uc17_xlsc/submit_estimate'] = 'UC17_XLSC/UC17_XLSC/submit_estimate';
$route['uc17_xlsc/update_progress'] = 'UC17_XLSC/UC17_XLSC/update_progress';
$route['uc17_xlsc/mark_repair_done'] = 'UC17_XLSC/UC17_XLSC/mark_repair_done';
