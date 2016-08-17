<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['jqgrid_limit_per_page'] = '100';
$config['jqgrid_limit_pages'] = '100, 200, 500, 1000';

$config['boolean'] = array(
	1 => "Yes",
	0 => "No"
);

$config['languages'] = array(
	'english'	=> "English",
	'bahasa'	=> "Indonesia"
);

$config['months'] = array(
	1 => "Januari",
	2 => "February",
	3 => "March",
	4 => "April",
	5 => "May",
	6 => "June",
	7 => "July",
	8 => "August",
	9 => "September",
	10 => "October",
	11 => "November",
	12 => "December"
);

$config['filter_year_from'] = 2015;

$config['inventory_default_warehouse'] = 'WHSYS';
$config['inventory_default_grid'] = 'WHSYS000000';

$config['business_partner_types'] = array(
	'SUPPLIER'	=> 'Supplier',
	'CUSTOMER'	=> 'Customer'
);

$config['business_partner_models'] = array(
	'COMPANY'	=> 'Company',
	'PERSONAL'	=> 'Personal'
);

$config['product_uoms'] = array(
	'PCS'	=> 'PCS',
	'GR'	=> 'GR',
	'KG'	=> 'KG',
	'LBR'	=> 'LBR',
	'EA'	=> 'EA',
	'UNIT'	=> 'UNIT'
);

$config['product_origins'] = array(
	'LOCAL'		=> 'Local',
	'IMPORT'	=> 'Import'
);

$config['product_conditions'] = array(
	'OK'		=> 'OK',
	'DEFECT'	=> 'DEFECT'
);

$config['product_types'] = array(
	'AYAM'				=> 'AYAM',
	'BACK RIBS'			=> 'BACK RIBS',
	'BEBEK'				=> 'BEBEK',
	'BLADE'				=> 'BLADE',
	'BRISKET'			=> 'BRISKET',
	'BRISKET BONE'		=> 'BRISKET BONE',
	'CHUCK'				=> 'CHUCK',
	'CONRO'				=> 'CONRO',
	'CUBEROLL'			=> 'CUBEROLL',
	'FAT'				=> 'FAT',
	'FEET / KAKI'		=> 'FEET / KAKI',
	'FLANK'				=> 'FLANK',
	'HEAD MEAT'			=> 'HEAD MEAT',
	'HEART / JANTUNG'	=> 'HEART / JANTUNG',
	'INSIDE'			=> 'INSIDE',
	'KAMBING'			=> 'KAMBING',
	'KNUCKLE'			=> 'KNUCKLE',
	'LIPS'				=> 'LIPS',
	'LIVER / HATI'		=> 'LIVER / HATI',
	'LOKAL'				=> 'LOKAL',
	'LUNGS / PARU'		=> 'LUNGS / PARU',
	'MDM'				=> 'MDM',
	'NECK BONE '		=> 'NECK BONE',
	'OUTSIDE'			=> 'OUTSIDE',
	'OXTAIL'			=> 'OXTAIL',
	'ROUND'				=> 'ROUND'
);

$config['grid_types'] = array(
	'RACK'	=> 'Rack',
	'STAGE'	=> 'Stage'
);

$config['grid_statuses'] = array(
	'OK'		=> 'Ok',
	'DAMAGE'	=> 'Damage'
);

$config['project_categories'] = array(
	'INTERNAL'	=> 'Internal',
	'RENT'		=> 'Rent'
);

$config['orderin_origins'] = array(
	'LOCAL'		=> 'Local',
	'IMPORT'	=> 'Import'
);

$config['transport_modes'] = array(
	'CD4'	=> 'CD4',
	'CD6'	=> 'CD6',
	'CNT'	=> 'CNT',
	'BU'	=> 'BU'
);

$config['inventory_log_types'] = array(
	'INBOUND'	=> 'INBOUND',
	'PUTAWAY'	=> 'PUTAWAY',
	'MOVE'		=> 'MOVE',
	'ADJUST'	=> 'ADJUST',
	'HOLD'		=> 'HOLD',
	'ASSEMBLY'	=> 'ASSEMBLY',
	'PICKLIST'	=> 'PICKLIST',
	'PICKING'	=> 'PICKING',
	'SHIPMENT'	=> 'SHIPMENT'
);

$config['inventory_adjust_types'] = array(
	'HANDLING'	=> 'HANDLING',
	'CUSTOM'	=> 'CUSTOM',
	'PO'		=> 'PO',
	'DEFECT'	=> 'DEFECT',
	'STOCKTAKE'	=> 'STOCKTAKE',
	'OTHER'		=> 'OTHER'
);

$config['orderout_origins'] = array(
	'LOCAL'		=> 'Local',
	'IMPORT'	=> 'Import'
);

$config['shipment_types'] = array(
	'LOCO'		=> 'LOCO',
	'FRANCO'	=> 'FRANCO'
);

$config['status_inventory_receive'] = array(
	'NO RECEIVE'			=> 'NO RECEIVE',
	'COMPLETE'				=> 'COMPLETE',
	'INCOMPLETE'			=> 'INCOMPLETE',
	'INCOMPLETE BOX'		=> 'INCOMPLETE BOX',
	'INCOMPLETE QUANTITY'	=> 'INCOMPLETE QUANTITY'
);

$config['status_inventory_inbound'] = array(
	'NO INBOUND'			=> 'NO INBOUND',
	'COMPLETE'				=> 'COMPLETE',
	'INCOMPLETE'			=> 'INCOMPLETE',
	'INCOMPLETE BOX'		=> 'INCOMPLETE BOX',
	'INCOMPLETE QUANTITY'	=> 'INCOMPLETE QUANTITY'
);

$config['status_inventory_picklist'] = array(
	'NO PICK LIST'			=> 'NO PICK LIST',
	'COMPLETE'				=> 'COMPLETE',
	'INCOMPLETE'			=> 'INCOMPLETE',
	'INCOMPLETE BOX'		=> 'INCOMPLETE BOX',
	'INCOMPLETE QUANTITY'	=> 'INCOMPLETE QUANTITY'
);

$config['status_inventory_picking'] = array(
	'NO PICKING'			=> 'NO PICKING',
	'COMPLETE'				=> 'COMPLETE',
	'INCOMPLETE'			=> 'INCOMPLETE',
	'INCOMPLETE BOX'		=> 'INCOMPLETE BOX',
	'INCOMPLETE QUANTITY'	=> 'INCOMPLETE QUANTITY'
);

$config['status_inventory_shipment'] = array(
	'NO SHIPMENT'			=> 'NO SHIPMENT',
	'COMPLETE'				=> 'COMPLETE',
	'INCOMPLETE'			=> 'INCOMPLETE',
	'INCOMPLETE BOX'		=> 'INCOMPLETE BOX',
	'INCOMPLETE QUANTITY'	=> 'INCOMPLETE QUANTITY'
);

/* -- Email Preferences -- */
$config['mail_smtp_host'] = 'ssl://smtp.googlemail.com';  // agar ssl berfungsi, tambahkan >>> extension=php_openssl.dll <<< di php.ini
$config['mail_smtp_port'] = 465;
$config['mail_smtp_user'] = 'firman.data@gmail.com';
$config['mail_smtp_pass'] = 'tebak';
$config['mail_smtp_label_name'] = "Workflow";

$config['application_title'] = "ERP";
$config['application_copy_right'] = "Enterprise Resource Planning &copy; 2014";

$config['upload_path'] = './upload';
$config['temporary_file_path'] = './temp';

/* -- Date Format -- */
$config['server_date_format'] = 'Y-m-d';
$config['server_datetime_format'] = 'Y-m-d H:i:s';
$config['server_datetime_nosecond_format'] = 'Y-m-d H:i';
$config['server_time_format'] = 'H:i:s';
$config['server_time_nosecond_format'] = 'H:i';

$config['server_display_date_format'] = 'd-m-Y';
$config['server_display_datetime_format'] = 'd-m-Y H:i:s';
$config['server_display_datetime_nosecond_format'] = 'd-m-Y H:i';
$config['server_display_time_format'] = 'H:i:s';
$config['server_display_time_nosecond_format'] = 'H:i';

$config['server_client_parse_validate_date_format'] = 'yyyy-MM-dd';
$config['server_client_parse_validate_datetime_format'] = 'yyyy-MM-dd HH:mm:ss';
$config['server_client_parse_validate_datetime_nosecond_format'] = 'yyyy-MM-dd HH:mm';
$config['server_client_parse_validate_time_format'] = 'HH:mm:ss';
$config['server_client_parse_validate_time_nosecond_format'] = 'HH:mm';

$config['client_picker_date_format'] = 'dd-mm-yy';
$config['client_picker_time_format'] = 'hh:mm:ss';
$config['client_picker_time_nosecond_format'] = 'hh:mm';

$config['client_validate_date_format'] = 'dd-MM-yyyy';
$config['client_validate_datetime_format'] = 'dd-MM-yyyy HH:mm:ss';
$config['client_validate_datetime_nosecond_format'] = 'dd-MM-yyyy HH:mm';
$config['client_validate_time_format'] = 'HH:mm:ss';
$config['client_validate_time_nosecond_format'] = 'HH:mm';

$config['client_jqgrid_date_format'] = 'd-m-Y';
$config['client_jqgrid_datetime_format'] = 'd-m-Y H:i:s';
$config['client_jqgrid_datetime_nosecond_format'] = 'd-m-Y H:i';
$config['client_jqgrid_time_format'] = 'H:i:s';
$config['client_jqgrid_time_nosecond_format'] = 'H:i';