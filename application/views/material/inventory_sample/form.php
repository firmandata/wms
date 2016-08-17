<?php 
echo form_open($form_action.(!empty($record) ? '/'.$record->id : ''),
	array(
		'name'	=> 'material_inventory_sample_form',
		'id'	=> 'material_inventory_sample_form'
	)
);?>
<table>
	<tr><td valign="top">
			<table class="form-table">
				<tbody>
					<tr>
						<th width="120"><label for="material_inventory_sample_form_code">No</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'code',
		'id' 	=> 'material_inventory_sample_form_code',
		'class'	=> (!empty($record) ? 'required' : ''),
		'style'	=> 'width:120px;',
		'value'	=> (!empty($record) ? $record->code : ''),
		'placeholder'	=> '## Auto ##'
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_sample_form_sampling_date">Date</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'sampling_date',
		'id' 	=> 'material_inventory_sample_form_sampling_date',
		'class'	=> 'required date',
		'style'	=> 'width:75px;',
		'value'	=> (!empty($record->sampling_date) ? date($this->config->item('server_display_date_format'), strtotime($record->sampling_date)) : '')
	)
);?>
						</td>
					</tr>
					<tr>
						<th><label for="material_inventory_sample_form_notes">Notes</label></th>
						<td>
<?php 
echo form_textarea(
	array(
		'name' 	=> 'notes',
		'id' 	=> 'material_inventory_sample_form_notes',
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
						<th width="120"><label for="material_inventory_sample_form_supervisor">Supervisor</label></th>
						<td>
<?php 
echo form_input(
	array(
		'name' 	=> 'supervisor',
		'id' 	=> 'material_inventory_sample_form_supervisor',
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
<table id="material_inventory_sample_inventory_sampledetail_list_table" class="table-data ui-widget ui-widget-content ui-corner-all" width="100%" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th class="ui-state-default ui-corner-tl" width="22px">&nbsp;</th>
			<th class="ui-state-default">Location</th>
			<th class="ui-state-default">DOC</th>
			<th class="ui-state-default">ADG(gr)</th>
			<th class="ui-state-default">BIOMASS(kg)</th>
			<th class="ui-state-default">SR(%)</th>
			<th class="ui-state-default">FCR</th>
			<th class="ui-state-default">ABW(gr)</th>
			<th class="ui-state-default">F/D(kg)</th>
			<th class="ui-state-default">Population</th>
			<th class="ui-state-default">FR(%)</th>
			<th class="ui-state-default">Notes</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td colspan="12" class="ui-state-default ui-corner-bl ui-corner-br">
				<button id="material_inventory_sample_inventory_sampledetail_list_table_add" class="table-data-button">Add</button>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo form_close();?>

<script type="text/javascript">
var material_inventory_sample_on_sucess;
var material_inventory_sample_inventory_sampledetail_row_id = 0;

jQuery(function(){
	jquery_ready_load();
	
	jQuery("#material_inventory_sample_form").validate({
		submitHandler: function(form){
			jQuery("#material_inventory_sample_form").ajaxSubmit({
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					material_inventory_sample_on_sucess(data, textStatus, jqXHR);
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		}
	});
	
	jQuery('#material_inventory_sample_inventory_sampledetail_list_table_add').button({
		text: false,
		icons: {
			primary: "ui-icon-plus"
		}
	})
	.click(function(e){
		e.preventDefault();
		material_inventory_sample_form_inventory_sampledetail_add(
			null,
			null, null,
			0, 0, 0, 0, 0, 
			0, 0, 0, 0,
			null
		);
		
		jQuery('#material_inventory_sample_form_detail_m_product_id_' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '_caption').focus();
	});
	
	jQuery.each(<?php echo !empty($m_inventory_sampledetails) ? json_encode($m_inventory_sampledetails) : '[]';?>, function(inventory_sampledetail_idx, inventory_sampledetail){
		material_inventory_sample_form_inventory_sampledetail_add(
			inventory_sampledetail.id,
			inventory_sampledetail.m_grid_id, inventory_sampledetail.m_grid_code,
			inventory_sampledetail.doc, inventory_sampledetail.adg, inventory_sampledetail.biomass, inventory_sampledetail.sr, inventory_sampledetail.fcr, 
			inventory_sampledetail.abw, inventory_sampledetail.fd, inventory_sampledetail.population, inventory_sampledetail.fr,
			inventory_sampledetail.notes
		);
	});
	
<?php
if (empty($m_inventory_sampledetails))
{?>
	material_inventory_sample_form_inventory_sampledetail_add(
		null,
		null, null,
		0, 0, 0, 0, 0, 
		0, 0, 0, 0,
		null
	);
<?php
}?>
});

function material_inventory_sample_form_inventory_sampledetail_add(
	id, 
	m_grid_id, m_grid_code,
	doc, adg, biomass, sr, fcr, 
	abw, fd, population, fr,
	notes
){
	material_inventory_sample_inventory_sampledetail_row_id++;
	
	$('#material_inventory_sample_inventory_sampledetail_list_table tbody').append(
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
				$("<input>", {type:'hidden', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][id]', id:'material_inventory_sample_form_detail_inventory_sampledetail_id_' + material_inventory_sample_inventory_sampledetail_row_id.toString()})
					.val(id)
			).append(
				$("<input>", {type:'hidden', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][m_grid_id]', id:'material_inventory_sample_form_detail_m_grid_id_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required'})
					.val(m_grid_id).attr('data-text', m_grid_code)
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][doc]', id:'material_inventory_sample_form_detail_doc_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(40).val(doc).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][adg]', id:'material_inventory_sample_form_detail_adg_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(adg).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][biomass]', id:'material_inventory_sample_form_detail_biomass_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(biomass).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][sr]', id:'material_inventory_sample_form_detail_sr_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(40).val(sr).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][fcr]', id:'material_inventory_sample_form_detail_fcr_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(fcr).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][abw]', id:'material_inventory_sample_form_detail_abw_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(abw).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][fd]', id:'material_inventory_sample_form_detail_fd_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(fd).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][population]', id:'material_inventory_sample_form_detail_population_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(60).val(population).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][fr]', id:'material_inventory_sample_form_detail_fr_' + material_inventory_sample_inventory_sampledetail_row_id.toString(), 'class':'required number'})
					.width(40).val(fr).css('text-align', 'right')
			)
		).append(
			$("<td>", {'class':'ui-widget-content', 'align':'center'}).append(
				$("<input>", {type:'text', name:'m_inventory_sampledetails[' + material_inventory_sample_inventory_sampledetail_row_id.toString() + '][notes]', id:'material_inventory_sample_form_detail_notes_' + material_inventory_sample_inventory_sampledetail_row_id.toString()})
					.width(200).val(notes)
			)
		)
	);
	
	material_inventory_sample_form_select(material_inventory_sample_inventory_sampledetail_row_id);
}

function material_inventory_sample_form_select(idx){
	jquery_autocomplete_build('#material_inventory_sample_form_detail_m_grid_id_' + idx.toString(), "<?php echo site_url('material/inventory_sample/get_grid_autocomplete_list_json');?>", {
		width : 100
	}, {
		change: function(event, ui){
			if (ui.item == null)
			{
				$(this).val('');
				$('#material_inventory_sample_form_detail_m_grid_id_' + idx.toString()).val('');
				$('#material_inventory_sample_form_detail_doc_' + idx.toString()).val(0);
			}
			else
			{
				$('#material_inventory_sample_form_detail_m_grid_id_' + idx.toString()).val(ui.item.id);
				$('#material_inventory_sample_form_detail_doc_' + idx.toString()).val(ui.item.inventory_age);
			}
		}
	});
}

function material_inventory_sample_form_submit(on_success){
	material_inventory_sample_on_sucess = on_success;
	jQuery('#material_inventory_sample_form').submit();
}
</script>