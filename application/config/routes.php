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

// QC Module Routes
$route['qc/sessions/(:num)'] = 'qc/sessions/$1';  // View session detail
$route['qc/sessions'] = 'qc/session_list';         // List all sessions
$route['qc/reports'] = 'qc/reports';               // QC Reports

// Warehouse Module Routes
$route['warehouse'] = 'warehouse/index';
$route['warehouse/material'] = 'warehouse/material';
$route['warehouse/material/addmaterial'] = 'warehouse/material';
$route['warehouse/material/addnewmaterial'] = 'warehouse/material';
$route['warehouse/addNewMaterialForm'] = 'warehouse/addNewMaterialForm';
$route['warehouse/addNewMaterial'] = 'warehouse/addNewMaterial';
$route['warehouse/add_material'] = 'warehouse/add_material';
$route['warehouse/create_material'] = 'warehouse/create_material';
$route['warehouse/editMaterial/(:any)'] = 'warehouse/editMaterial/$1';
$route['warehouse/updateMaterial'] = 'warehouse/updateMaterial';
$route['warehouse/deleteMaterialMaster/(:any)'] = 'warehouse/deleteMaterialMaster/$1';
$route['warehouse/edit_material/(:num)'] = 'warehouse/edit_material/$1';
$route['warehouse/update_material/(:num)'] = 'warehouse/update_material/$1';
$route['warehouse/delete_material/(:num)'] = 'warehouse/delete_material/$1';
$route['warehouse/stock_in'] = 'warehouse/stock_in';
$route['warehouse/stock_out'] = 'warehouse/stock_out';
$route['warehouse/save_stock_in'] = 'warehouse/save_stock_in';
$route['warehouse/save_stock_out'] = 'warehouse/save_stock_out';
$route['warehouse/material_entry_history/(:num)'] = 'warehouse/material_entry_history/$1';
$route['warehouse/material_out_history/(:num)'] = 'warehouse/material_out_history/$1';
$route['warehouse/report'] = 'warehouse/report';
$route['warehouse/export_dashboard'] = 'warehouse/export_dashboard';
$route['warehouse/project'] = 'warehouse/project';
$route['warehouse/finished'] = 'warehouse/finished';
// Warehouse Finished Goods sub-routes
$route['warehouse/finished/dashboard'] = 'warehouse/finished';
$route['warehouse/finished/receipt'] = 'warehouse/finished_receipts';
$route['warehouse/finished/receipt_form'] = 'warehouse/finished_receipt_form';
$route['warehouse/finished/receipt_save'] = 'warehouse/finished_receipt_save';
$route['warehouse/finished/receipt_view/(:num)'] = 'warehouse/finished_receipt_view/$1';
$route['warehouse/finished/receipt_cancel/(:num)'] = 'warehouse/finished_receipt_cancel/$1';
$route['warehouse/finished/receipts'] = 'warehouse/finished_receipts';
$route['warehouse/finished/receipts/new'] = 'warehouse/finished_receipt_form';
$route['warehouse/finished/receipts/view/(:num)'] = 'warehouse/finished_receipt_view/$1';
$route['warehouse/finished/deliveries'] = 'warehouse/finished_deliveries';
$route['warehouse/finished/deliveries/new'] = 'warehouse/finished_delivery_form';
$route['warehouse/finished/delivery_save'] = 'warehouse/finished_delivery_save';
$route['warehouse/finished/delivery_cancel/(:num)'] = 'warehouse/finished_delivery_cancel/$1';
$route['warehouse/finished/delivery_view/(:num)'] = 'warehouse/finished_delivery_view/$1';
$route['warehouse/finished/deliveries/view/(:num)'] = 'warehouse/finished_delivery_view/$1';
