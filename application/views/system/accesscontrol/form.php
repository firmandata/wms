<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'system_accesscontrol_form',
		'id'	=> 'system_accesscontrol_form'
	)
);?>
	<table width="100%">
		<tr>
			<td valign="top">
				<table class="form-table">
					<thead>
						<tr>
							<td colspan="2" class="form-table-title">Information</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="120"><label for="system_accesscontrol_form_name">Name</label></th>
							<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'name',
		'id' 	=> 'system_accesscontrol_form_name',
		'class'	=> 'required',
		'value'	=> (!empty($record) ? $record->name : '')
	)
);?>
							</td>
						</tr>
						
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div style="width:730px;">
					<table id="system_accesscontrol_control_action_list_table"></table>
				</div>
			</td>
		</tr>
	</table>
<?php echo form_close();?>

<script type="text/javascript">
var system_accesscontrol_on_sucess;

jQuery(function(){
	jQuery("#system_accesscontrol_form").validate({
		submitHandler: function(form){
			jQuery("#system_accesscontrol_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					system_accesscontrol_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	/* -- Load User Group List -- */
	system_accesscontrol_form_control_action_list_load_table('system_accesscontrol_control_action_list_table', <?php echo json_encode($sys_actions);?>, <?php echo !empty($record) ? $record->id : '0';?>);
});

function system_accesscontrol_form_submit(on_success){
	system_accesscontrol_on_sucess = on_success;
	jQuery('#system_accesscontrol_form').submit();
}

function system_accesscontrol_form_control_action_list_load_table(table_id, sys_actions, sys_usergroup_id){
	var col_names = new Array;
	var col_models = new Array;
	
	col_names.push('System Control Id'); 
	col_models.push({name:'sys_control_id', index:'sys_control_id', key:true, hidden:true, frozen:true});
	
	col_names.push('Control');
	col_models.push({name:'sys_control_name', index:'sys_control_name', width:150, sortable:false, frozen:true});
	
	var col_groups = new Array;
	jQuery.each(sys_actions, function(idx, sys_action){
		col_names.push('Allow');
		col_models.push({name:'sys_action_allow_' + sys_action.id.toString(), index:'sys_action_allow_' + sys_action.id.toString(), width:45, sortable:false, align:'center',
			formatter: function(cellvalue, options, rowObject){
				return '<input type="radio" name="accesscontrols[' + rowObject.sys_control_id.toString() + '][' + sys_action.id.toString() + ']" value="allow" ' + (cellvalue ? 'checked' : '') + '/>';
			}
		});
		col_names.push('Denied');
		col_models.push({name:'sys_action_denied_' + sys_action.id.toString(), index:'sys_action_denied_' + sys_action.id.toString(), width:45, sortable:false, align:'center',
			formatter: function(cellvalue, options, rowObject){
				return '<input type="radio" name="accesscontrols[' + rowObject.sys_control_id.toString() + '][' + sys_action.id.toString() + ']" value="denied" ' + (cellvalue ? 'checked' : '') + '/>';
			}
		});
		col_groups.push({startColumnName: 'sys_action_allow_' + sys_action.id.toString(), numberOfColumns: 2, titleText: sys_action.name});
	});
	
	jQuery('#' + table_id).jqGrid({
		loadError: jquery_ajax_error_handler,
		datatype: "json", 
		viewrecords: true, 
		rownumbers: true,
		shrinkToFit: false,
		pginput: false,
		pgbuttons: false,
		rowNum: 1000, 
		postData : {
			sys_usergroup_id : sys_usergroup_id
		},
		jsonReader : {
			root: "data",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: false
		},
		width: jqgrid_window_fixed_width(table_id),
		url: "<?php echo site_url('system/accesscontrol/get_control_action_list_json');?>", 
		caption: "Access Control",
		hidegrid: false,
		height: 250,
		colNames: col_names, 
		colModel: col_models
	});
	
	jQuery("#" + table_id).jqGrid('setGroupHeaders', {
		useColSpanStyle: true,
		groupHeaders: col_groups
	});
	
	jQuery("#" + table_id).jqGrid('setFrozenColumns');
}
</script>