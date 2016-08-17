<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_water_form',
		'id'	=> 'material_inventory_water_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120"><label for="material_inventory_water_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_water_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_water_form_water_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'water_date',
		'id' 	=> 'material_inventory_water_form_water_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->water_date) ? date($this->config->item('server_display_date_format'), strtotime($record->water_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_water_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_water_form_notes',
		'value'	=> (!empty($record) ? $record->notes : ''),
		'cols'	=> 30, 'rows'	=> 2
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		<td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120"><label for="material_inventory_water_form_supervisor">Supervisor</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'supervisor',
		'id' 	=> 'material_inventory_water_form_supervisor',
		'value'	=> (!empty($record) ? $record->supervisor : '')
	)
);?>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<br/>
<table id="material_inventory_water_inventory_waterdetail_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Location</th>
			<th class="ui-state-default">DOC</th>
			<th class="ui-state-default">Suhu(<sup>o</sup>C)</th>
			<th class="ui-state-default">DO (Disolved Oksigen)</th>
			<th class="ui-state-default">pH</th>
			<th class="ui-state-default">Salinitas</th>
			<th class="ui-state-default">Kecerahan</th>
			<th class="ui-state-default">Total Ammonia</th>
			<th class="ui-state-default">Total Nitrite</th>
			<th class="ui-state-default">Total Nitrate</th>
			<th class="ui-state-default">Keterangan</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="12" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="material_inventory_water_inventory_waterdetail_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_water_on_sucess;
var material_inventory_water_inventory_waterdetail_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_water_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_water_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_water_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery('#material_inventory_water_inventory_waterdetail_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		material_inventory_water_form_inventory_waterdetail_add(
			null,
			null, null,
			0, 0, 0, 0, 0, 
			0, 0, 0, 0,
			null
		);
		
		jQuery('#material_inventory_water_form_detail_m_product_id_' + material_inventory_water_inventory_waterdetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($m_inventory_waterdetails) ? json_encode($m_inventory_waterdetails) : '[]';?>, function(inventory_waterdetail_idx, inventory_waterdetail){
		material_inventory_water_form_inventory_waterdetail_add(
			inventory_waterdetail.id,
			inventory_waterdetail.m_grid_id, inventory_waterdetail.m_grid_code,
			inventory_waterdetail.doc, inventory_waterdetail.suhu, inventory_waterdetail.disolved_oksigen, inventory_waterdetail.ph, inventory_waterdetail.salinitas, 
			inventory_waterdetail.kecerahan, inventory_waterdetail.total_ammonia, inventory_waterdetail.total_nitrite, inventory_waterdetail.total_nitrate,
			inventory_waterdetail.notes
		);
	});
	
<?php
if (empty($m_inventory_waterdetails))
{?>
	material_inventory_water_form_inventory_waterdetail_add(
		null,
		null, null,
		0, 0, 0, 0, 0, 
		0, 0, 0, 0,
		null
	);
<?php
}?>
});

function material_inventory_water_form_inventory_waterdetail_add(
	id, 
	m_grid_id, m_grid_code,
	doc, suhu, disolved_oksigen, ph, salinitas, 
	kecerahan, total_ammonia, total_nitrite, total_nitrate,
	notes
){
	material_inventory_water_inventory_waterdetail_row_id++;
	
	$('#material_inventory_water_inventory_waterdetail_list_table tbody').append(
		$("<tr>").append(
			$("<td>", {'class':'ui-state-default', 'align':'center'}).append(
				$("<button>", {'class' : 'table-data-button'}).text("Remove").button({text: false,icons: {primary: "ui-icon-trash"}}).click(function(e){
					e.preventDefault();
					var to_remove_row = $(this).parent().parent();
					jquery_show_confirm("Are your sure ?", function(){
						to_remove_row.remove();
					});
				})
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'hidden', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][id]', id:'material_inventory_water_form_detail_inventory_waterdetail_id_' + material_inventory_water_inventory_waterdetail_row_id.toString()})
					.val(id)
			).append(
				$("<input>", {type:'hidden', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][m_grid_id]', id:'material_inventory_water_form_detail_m_grid_id_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required'})
					.val(m_grid_id).attr('data-text', m_grid_code)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][doc]', id:'material_inventory_water_form_detail_doc_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(40).val(doc).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][suhu]', id:'material_inventory_water_form_detail_adg_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(suhu).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][disolved_oksigen]', id:'material_inventory_water_form_detail_biomass_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(disolved_oksigen).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][ph]', id:'material_inventory_water_form_detail_sr_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(40).val(ph).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][salinitas]', id:'material_inventory_water_form_detail_fcr_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(salinitas).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][kecerahan]', id:'material_inventory_water_form_detail_abw_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(kecerahan).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][total_ammonia]', id:'material_inventory_water_form_detail_fd_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(total_ammonia).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][total_nitrite]', id:'material_inventory_water_form_detail_population_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(60).val(total_nitrite).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][total_nitrate]', id:'material_inventory_water_form_detail_fr_' + material_inventory_water_inventory_waterdetail_row_id.toString(), 'class':'required number'})
					.width(40).val(total_nitrate).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_waterdetails[' + material_inventory_water_inventory_waterdetail_row_id.toString() + '][notes]', id:'material_inventory_water_form_detail_notes_' + material_inventory_water_inventory_waterdetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	material_inventory_water_form_select(material_inventory_water_inventory_waterdetail_row_id);
}

function material_inventory_water_form_select(idx){
	jquery_autocomplete_build('#material_inventory_water_form_detail_m_grid_id_' + idx.toString(), "<?php echo site_url('material/inventory_water/get_grid_autocomplete_list_json');?>", {
		width : 100
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#material_inventory_water_form_detail_m_grid_id_' + idx.toString()).val('');
				$('#material_inventory_water_form_detail_doc_' + idx.toString()).val(0);
			}
			else
			{
				$('#material_inventory_water_form_detail_m_grid_id_' + idx.toString()).val(ui.item.id);
				$('#material_inventory_water_form_detail_doc_' + idx.toString()).val(ui.item.inventory_age);
			}
		}
	});
}

function material_inventory_water_form_submit(on_success){
	material_inventory_water_on_sucess = on_success;
	jQuery('#material_inventory_water_form').submit();
}
</script>