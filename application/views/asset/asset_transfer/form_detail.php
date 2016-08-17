<?php
echo form_open('asset/asset_transfer/insert_detail',
	array(
		'name'	=> 'asset_asset_transfer_form_detail',
		'id'	=> 'asset_asset_transfer_form_detail'
	)
);?>
<table>
	<tr>
		<td>From
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="asset_asset_transfer_form_detail_code_caption">Asset Code</label></th>
						<td><input type="hidden" name="code" id="asset_asset_transfer_form_detail_code" class="required"/><br/>
							<label id="asset_asset_transfer_form_detail_asset_name">-</label>
						</td>
					</tr>
					<tr><th><label for="asset_asset_transfer_form_detail_m_product_code_caption">Product</label></th>
						<td><label id="asset_asset_transfer_form_detail_m_product_code">-</label>
							/
							<label id="asset_asset_transfer_form_detail_m_product_name">-</label>
						</td>
					</tr>
					<tr><th><label for="asset_asset_transfer_form_detail_c_departmentfrom_code_caption">Department</label></th>
						<td><label id="asset_asset_transfer_form_detail_c_departmentfrom_code">-</label>
							/
							<label id="asset_asset_transfer_form_detail_c_departmentfrom_name">-</label>
						</td>
					</tr>
					<tr><th><label for="asset_asset_transfer_form_detail_c_businesspartner_userfrom_code_caption">User</label></th>
						<td><label id="asset_asset_transfer_form_detail_c_businesspartner_userfrom_code">-</label>
							/
							<label id="asset_asset_transfer_form_detail_c_businesspartner_userfrom_name">-</label>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td id="asset_asset_transfer_form_detail_counter_label" style="padding-left:50px; font-size:100px;">
			0
		</td>
	</tr>
	<tr>
		<td valign="top">
			To
			<table class="form-table">
				<tbody>
					<tr><th width="100"><label for="asset_asset_transfer_form_detail_c_businesspartner_userto_code_caption">User</label></th>
						<td><input type="hidden" name="c_businesspartner_userto_code" id="asset_asset_transfer_form_detail_c_businesspartner_userto_code" class="required"/><br/>
							<label id="asset_asset_transfer_form_detail_c_businesspartner_userto_name">-</label>
						</td>
					</tr>
					<tr><th><label for="asset_asset_transfer_form_detail_c_departmentto_code_caption">Department</label></th>
						<td><input type="hidden" name="c_departmentto_code" id="asset_asset_transfer_form_detail_c_departmentto_code" class="required"/><br/>
							<label id="asset_asset_transfer_form_detail_c_departmentto_name">-</label>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			Notes<br/>
			<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'asset_asset_transfer_form_detail_notes',
		'cols'	=> 40, 'rows'	=> 4
	)
);?>
		</td>
	</tr>
	<tr>
		<td align="center">
<?php 
if (is_authorized('asset/asset_transfer', 'insert')){?>
			<button id="asset_asset_transfer_detail_process_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
				<span class="ui-button-icon-primary ui-icon ui-icon-check"></span>
				<span class="ui-button-text">Process</span>
			</button>
<?php 
}?>
		</td>
	</tr>
</table>
<?php 
echo form_close();?>

<table id="asset_asset_transfer_detail_list_table"></table>
<div id="asset_asset_transfer_detail_list_table_nav"></div>

<script type="text/javascript">
jQuery(document).ready(function(){
	asset_asset_transfer_detail_list_load_table('asset_asset_transfer_detail_list_table');
	
	jQuery("#asset_asset_transfer_form_detail").validate({
		submitHandler: function(form){
			jQuery("#asset_asset_transfer_form_detail").ajaxSubmit({
				dataType: "json",
				async : false,
				data : {
					a_asset_transfer_id	: <?php echo $id;?>
				},
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					if (data.response == false)
					{
						jquery_show_message(data.value, null, "ui-icon-close", function(){
							asset_asset_transfer_detail_clear_field();
						});
					}
					else
					{
						jQuery("#asset_asset_transfer_detail_list_table").trigger('reloadGrid', [{current:true}]);
						asset_asset_transfer_detail_clear_field();
					}
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery("#asset_asset_transfer_detail_process_btn").click(function(e){
		e.preventDefault();
		jQuery('#asset_asset_transfer_form_detail').submit();
	});
	
	jquery_autocomplete_build("#asset_asset_transfer_form_detail_code", "<?php echo site_url('asset/asset_transfer/get_asset_autocomplete_list_json');?>", {
		width : 200,
		must_select : false
	},
	{
		autoFocus : true,
		search: function(event, ui){
			jQuery("#asset_asset_transfer_form_detail_asset_name").text('-');
			
			jQuery("#asset_asset_transfer_form_detail_m_product_code").text('-');
			jQuery("#asset_asset_transfer_form_detail_m_product_name").text('-');

			jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_code").text('-');
			jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_name").text('-');

			jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_code").text('-');
			jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_name").text('-');
		},
		select: function(event, ui){
			if (ui.item != null)
			{
				jQuery("#asset_asset_transfer_form_detail_asset_name").text(ui.item.name);
				
				jQuery("#asset_asset_transfer_form_detail_m_product_code").text(ui.item.m_product_code);
				jQuery("#asset_asset_transfer_form_detail_m_product_name").text(ui.item.m_product_name);

				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_code").text(ui.item.c_department_code);
				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_name").text(ui.item.c_department_name);

				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_code").text(ui.item.c_businesspartner_user_code);
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_name").text(ui.item.c_businesspartner_user_name);
			}
		},
		change: function(event, ui){
			if (ui.item != null)
			{
				jQuery("#asset_asset_transfer_form_detail_code").val(ui.item.id);
				jQuery("#asset_asset_transfer_form_detail_asset_name").text(ui.item.name);
				
				jQuery("#asset_asset_transfer_form_detail_m_product_code").text(ui.item.m_product_code);
				jQuery("#asset_asset_transfer_form_detail_m_product_name").text(ui.item.m_product_name);

				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_code").text(ui.item.c_department_code);
				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_name").text(ui.item.c_department_name);

				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_code").text(ui.item.c_businesspartner_user_code);
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_name").text(ui.item.c_businesspartner_user_name);
			}
			else
			{
				jQuery("#asset_asset_transfer_form_detail_code").val(jQuery(this).val());
				jQuery("#asset_asset_transfer_form_detail_asset_name").text('-');
				
				jQuery("#asset_asset_transfer_form_detail_m_product_code").text('-');
				jQuery("#asset_asset_transfer_form_detail_m_product_name").text('-');

				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_code").text('-');
				jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_name").text('-');

				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_code").text('-');
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_name").text('-');
			}
		}
	});
	
	jquery_autocomplete_build("#asset_asset_transfer_form_detail_c_businesspartner_userto_code", "<?php echo site_url('asset/asset_transfer/get_businesspartner_autocomplete_list_json');?>", {
		width : 200,
		must_select : false
	},
	{
		autoFocus : true,
		search: function(event, ui){
			jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_name").text('-');
		},
		select: function(event, ui){
			if (ui.item != null)
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_name").text(ui.item.name);
		},
		change: function(event, ui){
			if (ui.item != null)
			{
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_code").val(ui.item.id);
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_name").text(ui.item.name);
			}
			else
			{
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_code").val(jQuery(this).val());
				jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_name").text('-');
			}
		}
	});
	
	jquery_autocomplete_build("#asset_asset_transfer_form_detail_c_departmentto_code", "<?php echo site_url('asset/asset_transfer/get_department_autocomplete_list_json');?>", {
		width : 200,
		must_select : false
	},
	{
		autoFocus : true,
		search: function(event, ui){
			jQuery("#asset_asset_transfer_form_detail_c_departmentto_name").text('-');
		},
		select: function(event, ui){
			if (ui.item != null)
				jQuery("#asset_asset_transfer_form_detail_c_departmentto_name").text(ui.item.name);
		},
		change: function(event, ui){
			if (ui.item != null)
			{
				jQuery("#asset_asset_transfer_form_detail_c_departmentto_code").val(ui.item.id);
				jQuery("#asset_asset_transfer_form_detail_c_departmentto_name").text(ui.item.name);
			}
			else
			{
				jQuery("#asset_asset_transfer_form_detail_c_departmentto_code").val(jQuery(this).val());
				jQuery("#asset_asset_transfer_form_detail_c_departmentto_name").text('-');
			}
		}
	});
});

function asset_asset_transfer_detail_list_load_table(table_id){
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
			repeatitems: false,
			id: '0'
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('asset/asset_transfer/get_list_detail_full_json');?>", 
		postData : {
			id : <?php echo $id;?>
		},
		colNames: [
			'Asset Move Detail Id', 
			'Asset Move Id', 
			'Asset Id', 
			'Code', 
			'Name', 
			'Product Id',
			'Product Name',
			'User From Id',
			'User From',
			'User From Desc',
			'User To Id',
			'User To',
			'User To Desc',
			'Dept From Id',
			'Dept From',
			'Dept From Desc',
			'Dept To Id',
			'Dept To',
			'Dept To Desc',
			'Notes',
			'Scan Date',
			''
		], 
		colModel: [
			{name:'id', index:'atd.id', frozen:true, hidden:true},
			{name:'a_asset_transfer_id', index:'atd.a_asset_transfer_id', frozen:true, hidden:true},
			{name:'a_asset_id', index:'atd.a_asset_id', frozen:true, hidden:true},
			{name:'a_asset_code', index:'ast.code', width:80, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'a_asset_name', index:'ast.name', width:130, frozen:true, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'m_product_id', index:'ast.m_product_id', hidden:true},
			{name:'m_product_name', index:'pro.name', width:200, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userfrom_id', index:'atd.c_businesspartner_userfrom_id', hidden:true},
			{name:'c_businesspartner_userfrom_code', index:'bpf.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userfrom_name', index:'bpf.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userto_id', index:'atd.c_businesspartner_userto_id', hidden:true},
			{name:'c_businesspartner_userto_code', index:'bpt.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_businesspartner_userto_name', index:'bpt.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentfrom_id', index:'atd.c_departmentfrom_id', hidden:true},
			{name:'c_departmentfrom_code', index:'depf.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentfrom_name', index:'depf.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentto_id', index:'atd.c_departmentto_id', hidden:true},
			{name:'c_departmentto_code', index:'dept.code', width:80, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'c_departmentto_name', index:'dept.name', width:120, searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			{name:'notes', index:'atd.notes', width:180, classes:'jqgrid-nowrap-cell', searchoptions:{sopt:jqgird_search_string_operators, clearSearch:false}},
			jqgrid_column_datetime(table_id, {name:'created', index:'created', search:false}),
			{name:'cmd_action', index:'cmd_action', width:26, search:false, sortable:false}
		],
		afterInsertRow : function(rowid, rowdata, rowelem){
			jQuery("#" + table_id).jqGrid('setRowData', rowid, {
				cmd_action: 
					  '<button onclick="asset_asset_transfer_detail_list_remove(\'' + table_id + '\', ' + rowid.toString() + ')" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" role="button" style="height:20px;" title="Remove">'
					+ '	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>'
					+ '	<span class="ui-button-text">Remove</span>'
					+ '</button>'
			});
		},
		pager: '#' + table_id + '_nav', 
		sortname: 'id', 
		sortorder: "desc",
		loadComplete: function(data){
			var row_total = jQuery("#" + table_id).getGridParam('records');
			jQuery('#asset_asset_transfer_form_detail_counter_label').text(row_total);
		}
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
	jQuery("#" + table_id).setGridHeight(250);
}

function asset_asset_transfer_detail_list_remove(table_id, id){
	jquery_show_confirm("Are your sure ?", function(){
		var row_data = jQuery("#asset_asset_transfer_detail_list_table").getRowData(id);
		
		jQuery.ajax({
			url: "<?php echo site_url('asset/asset_transfer/delete_detail');?>",
			type: "POST",
			dataType: "json",
			data : {
				a_asset_transfer_id				: row_data.a_asset_transfer_id,
				id								: row_data.id,
				a_asset_id						: row_data.a_asset_id,
				c_businesspartner_userfrom_id	: row_data.c_businesspartner_userfrom_id,
				c_businesspartner_userto_id		: row_data.c_businesspartner_userto_id,
				c_departmentfrom_id				: row_data.c_departmentfrom_id,
				c_departmentto_id				: row_data.c_departmentto_id
			},
			async : false,
			error: jquery_ajax_error_handler,
			beforeSend: function(jqXHR, settings){
				jquery_blockui();
			},
			success: function(data, textStatus, jqXHR){
				if (data.response == false)
					jquery_show_message(data.value, null, "ui-icon-close");
				else
					jQuery("#" + table_id).trigger('reloadGrid', [{current:true}]);
			},
			complete: function(jqXHR, textStatus){
				jquery_unblockui();
			}
		});
	});
}

function asset_asset_transfer_detail_clear_field(){
	jQuery('#asset_asset_transfer_form_detail_code').val('');
	jQuery('#asset_asset_transfer_form_detail_code_caption').val('').focus();
	jQuery("#asset_asset_transfer_form_detail_asset_name").text('-');
	
	jQuery("#asset_asset_transfer_form_detail_m_product_code").text('-');
	jQuery("#asset_asset_transfer_form_detail_m_product_name").text('-');

	jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_code").text('-');
	jQuery("#asset_asset_transfer_form_detail_c_departmentfrom_name").text('-');

	jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_code").text('-');
	jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userfrom_name").text('-');
	
	jQuery('#asset_asset_transfer_form_detail_c_businesspartner_userto_code').val('');
	jQuery('#asset_asset_transfer_form_detail_c_businesspartner_userto_code_caption').val('');
	jQuery("#asset_asset_transfer_form_detail_c_businesspartner_userto_name").text('-');
	
	jQuery('#asset_asset_transfer_form_detail_c_departmentto_code').val('');
	jQuery('#asset_asset_transfer_form_detail_c_departmentto_code_caption').val('');
	jQuery("#asset_asset_transfer_form_detail_c_departmentto_name").text('-');
	
	jQuery("#asset_asset_transfer_form_detail_notes").val('');
}
</script>