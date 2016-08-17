<?php
$business_partner_types = array_merge(array('' => ''), $this->config->item('business_partner_types'));
$business_partner_models = array_merge(array('' => ''), $this->config->item('business_partner_models'));?>

<table id="core_businesspartner_list_table"></table>
<div id="core_businesspartner_list_table_nav"></div>
<script type="text/javascript">
jQuery(function(){
	core_businesspartner_list_load_table('core_businesspartner_list_table');
});

function core_businesspartner_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		pginput: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('core/businesspartner/get_list_json');?>", 
		editurl: "<?php echo site_url('core/businesspartner/jqgrid_cud');?>",
		hidegrid: false,
		height: 300,
		colNames: [
			'Id', 
			'Type',
			'Model',
			'Code',
			'Name',
			'Address',
			'Phone No',
			'Fax No',
			'Credit Limit',
			'PIC',
			'Notes'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},
			{name:'type', index:'type', width:80, frozen:true, editable:true, editrules:{required:true},
			 edittype: 'select', editoptions: {value:<?php echo json_encode($business_partner_types);?>},
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($business_partner_types);?>}
			},
			{name:'model', index:'model', width:80, frozen:true,editable:true, editrules:{required:true},
			 edittype: 'select', editoptions: {value:<?php echo json_encode($business_partner_models);?>},
			 stype:'select', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false, value:<?php echo json_encode($business_partner_models);?>}
			},
			{name:'code', index:'code', width:80, editable:true, editrules:{required:true}, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'name', width:180, editable:true, editrules:{required:true}, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'address', index:'address', width:200, classes:'jqgrid-nowrap-cell', editable:true, edittype:'textarea', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'phone_no', index:'phone_no', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'fax_no', index:'fax_no', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'credit_limit', index:'credit_limit', width:100, editable:true, editrules:{number:true, required:true}, editoptions:{defaultValue:'0'}, formatter:'number', formatoptions:{decimalPlaces: 4}, searchoptions:{sopt:jqgird_search_number_operators, clearSearch:false}},
			{name:'pic', index:'pic', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'notes', index:'notes', width:200, classes:'jqgrid-nowrap-cell', editable:true, edittype:'textarea', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('filterToolbar', {
		stringResult : true, 
		searchOperators : true
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', {
		/* -- Button Configuration -- */
<?php 
if (is_authorized('core/businesspartner', 'insert'))
{?>
		add: true,
<?php 
} else {?>
		add: false,
<?php 
}
if (is_authorized('core/businesspartner', 'update'))
{?>
		edit: true,
<?php 
} else {?>
		edit: false,
<?php 
}
if (is_authorized('core/businesspartner', 'delete'))
{?>
		del: true,
<?php 
} else {?>
		del: false,
<?php 
}?>
	},
	{
		/* -- Edit Configuration -- */
		afterSubmit: jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Add Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit : jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true,
		bottominfo: "*) Required"
	},
	{
		/* -- Delete Configuration -- */
		afterSubmit : jqgrid_form_after_submit, 
		beforeSubmit: jqgrid_form_before_submit,
		errorTextFormat : jqgrid_form_error_text,
		closeOnEscape: true
	}
	);
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}
</script>