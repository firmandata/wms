<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix content-commandbar">
<?php 
if (is_authorized('core/region', 'insert')){?>
	<button id="core_region_new_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
		<span class="ui-button-text">New</span>
	</button>
<?php 
}
if (is_authorized('core/region', 'update')){?>
	<button id="core_region_edit_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
		<span class="ui-button-text">Edit</span>
	</button>
<?php 
}
if (is_authorized('core/region', 'delete')){?>
	<button id="core_region_delete_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
		<span class="ui-button-text">Delete</span>
	</button>
<?php 
}?>
</div>

<table id="core_region_list_table"></table>
<div id="core_region_list_table_nav"></div>

<script type="text/javascript">
jQuery(function(){
	core_region_list_load_table('core_region_list_table');
	
	jQuery("#core_region_new_btn").click(function(){
		jquery_dialog_form_open('core_region_form_container', "<?php echo site_url('core/region/form');?>", {
			form_action : "core/region/insert"
		}, 
		function(form_dialog){
			core_region_form_submit(function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
				{
					form_dialog.dialog("close");
					jQuery("#core_region_list_table").trigger("reloadGrid", [{current:true}]);
				}
			});
		},
		{
			title : "Create Region", 
			width : 600
		});
	});
	
	jQuery("#core_region_edit_btn").click(function(){
		var id = jQuery("#core_region_list_table").getGridParam("selrow");
		if (id)
		{
			jquery_dialog_form_open('core_region_form_container', "<?php echo site_url('core/region/form');?>", {
				form_action : "core/region/update",
				id : id
			}, 
			function(form_dialog){
				core_region_form_submit(function(data, textStatus, jqXHR){
					if (data.response == false)
						jquery_show_message(data.value, null, "ui-icon-close");
					else
					{
						form_dialog.dialog("close");
						jQuery("#core_region_list_table").trigger("reloadGrid", [{current:true}]);
					}
				});
			},
			{
				title : "Edit Region", 
				width : 600
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
	
	jQuery("#core_region_delete_btn").click(function(){
		var id = jQuery("#core_region_list_table").getGridParam("selrow");
		if (id)
		{
			var _delete_url = "<?php echo site_url('core/region/delete/@id');?>";
			_delete_url = _delete_url.replace(/@id/g, id);
			
			jquery_show_confirm("Are your sure ?", function(){
				jQuery.ajax({
					url: _delete_url,
					type: "GET",
					dataType: "json",
					async : false,
					error: jquery_ajax_error_handler,
					beforeSend: function(jqXHR, settings){
						jquery_blockui();
					},
					success: function(data, textStatus, jqXHR){
						if (data.response == false)
							jquery_show_message(data.value, null, "ui-icon-close");
						else
							jQuery("#core_region_list_table").trigger('reloadGrid', [{current:true}]);
					},
					complete: function(jqXHR, textStatus){
						jquery_unblockui();
					}
				});
			});
		}
		else
			jquery_show_message("Please select the row data !", null, "ui-icon-alert");
	});
});

function core_region_list_load_table(table_id){
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		rowNum: <?php echo $this->config->item('jqgrid_limit_per_page');?>, 
		rowList: [<?php echo $this->config->item('jqgrid_limit_pages');?>], 
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('core/region/get_list_json');?>", 
		editurl: "<?php echo site_url('core/region/jqgrid_cud');?>",
		hidegrid: false,
		colNames: [
			'Id', 
			'Code',
			'Name',
			'Address',
			'City',
			'Phone No',
			'Fax No'
		], 
		colModel: [
			{name:'id', index:'rgn.id', key:true, hidden:true, frozen:true},
			{name:'code', index:'rgn.code', width:80, editable:true, editrules:{required:true}, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'name', index:'rgn.name', width:180, editable:true, editrules:{required:true}, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'address', index:'rgn.address', width:200, classes:'jqgrid-nowrap-cell', editable:true, edittype:'textarea', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'address_city', index:'rgn.address_city', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'phone_no', index:'rgn.phone_no', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'fax_no', index:'rgn.fax_no', width:120, editable:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}}
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
		edit: false,
		add: false,
		del: false
	},
	{},
	{},
	{}, 
	{
		/* -- Searching Configuration -- */
		multipleSearch: true, 
		multipleGroup: true, 
		showQuery: true
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
	jQuery("#" + table_id).setGridHeight(jqgrid_window_fixed_height(jQuery("#" + table_id), -164));
}
</script>