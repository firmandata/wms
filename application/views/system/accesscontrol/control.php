<table id="system_accesscontrol_control_list_table"></table>
<div id="system_accesscontrol_control_list_table_nav"></div>
<script type="text/javascript">
jQuery(function(){
	system_accesscontrol_form_control_list_load_table('system_accesscontrol_control_list_table');
});

function system_accesscontrol_form_control_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		pginput: false,
		rowNum: 1000, 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/accesscontrol/get_control_list_json');?>", 
		editurl: "<?php echo site_url('system/control/jqgrid_cud');?>",
		hidegrid: false,
		height: 300,
		colNames: [
			'Id', 
			'Control'
		], 
		colModel: [
			{name:'id', index:'id', key:true, hidden:true, frozen:true},  
			{name:'name', index:'name', width:250, editable:true, editrules:{required:true}},
		],
		pager: '#' + table_id + '_nav', 
		sortname: 'name', 
		sortorder: "asc"
	});
	
	jQuery("#" + table_id).jqGrid('navGrid', '#' + table_id + '_nav', 
	{
		/* -- Button Configuration -- */
<?php 
if (is_authorized('system/control', 'update'))
{?>
		edit: true,
<?php 
}
else
{?>
		edit: false,
<?php
}
if (is_authorized('system/control', 'insert'))
{?>
		add: true,
<?php 
}
else
{?>
		add: false,
<?php
}
if (is_authorized('system/control', 'delete'))
{?>
		del: true
<?php 
}
else
{?>
		del: false
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