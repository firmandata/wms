<div id="asset_asset_detail_tabs">
	<ul>
		<li><a href="#asset_asset_detail_tab_general">General</a></li>
		<li><a href="#asset_asset_detail_tab_depreciation">Depreciation</a></li>
	</ul>
	<div id="asset_asset_detail_tab_general">
		<table width="100%">
			<tr>
				<td valign="top">
					<table class="form-table">
						<thead>
							<tr>
								<td colspan="2" class="form-table-title">General</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th width="80">Code</th>
								<td><?php echo $record->code;?></td>
							</tr>
							<tr>
								<th>Product</th>
								<td><?php echo $record->m_product_name;?> (<?php echo $record->m_product_code;?>)</td>
							</tr>
							<tr>
								<th>Name</th>
								<td><?php echo $record->name;?></td>
							</tr>
							<tr>
								<th>Type</th>
								<td><?php echo $record->type;?></td>
							</tr>
							<tr>
								<th>Voucher No</th>
								<td><?php echo $record->voucher_no;?></td>
							</tr>
							<tr>
								<th>Quantity</th>
								<td><?php echo number_format($record->quantity, 4);?></td>
							</tr>
							<tr>
								<th>Notes</th>
								<td><?php echo nl2br($record->notes);?></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td valign="top">
					<table class="form-table">
						<thead>
							<tr>
								<td colspan="2" class="form-table-title">Information</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Region</th>
								<td><?php echo $record->c_region_name;?> (<?php echo $record->c_region_code;?>)</td>
							</tr>
							<tr>
								<th>Department</th>
								<td><?php echo $record->c_department_name;?> (<?php echo $record->c_department_code;?>)</td>
							</tr>
							<tr>
								<th>Location</th>
								<td><?php echo $record->c_location_name;?> (<?php echo $record->c_location_code;?>)</td>
							</tr>
							<tr>
								<th>User</th>
								<td><?php echo $record->c_businesspartner_user_name;?> (<?php echo $record->c_businesspartner_user_code;?>)</td>
							</tr>
						</tbody>
					</table>
					<table class="form-table">
						<thead>
							<tr>
								<td colspan="2" class="form-table-title">Purchase</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Supplier</th>
								<td><?php echo $record->c_businesspartner_supplier_name;?> (<?php echo $record->c_businesspartner_supplier_code;?>)</td>
							</tr>
							<tr>
								<th>Date</th>
								<td><?php echo (!empty($record->purchase_date) ? date($this->config->item('server_display_date_format'), strtotime($record->purchase_date)) : '');?></td>
							</tr>
							<tr>
								<th>Price</th>
								<td><?php echo $record->currency;?> <?php echo number_format($record->purchase_price, 2);?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div id="asset_asset_detail_tab_depreciation">
		<table class="form-table" width="100%">
			<tbody>
				<tr>
					<th width="80">Parameters</th>
					<td><?php echo $record->depreciation_period_type;?> <?php echo $record->depreciation_period_time;?></td>
				</tr>
			</tbody>
		</table>
		<div id="asset_asset_detail_a_asset_amounts">
			<?php echo $a_assetamount_view;?>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#asset_asset_detail_tabs").tabs({
		activate: function(event, ui){
			switch (ui.newTab.context.hash)
			{
				case '#asset_asset_detail_tab_depreciation':
					a_assetamount_global_scalled();
					break;
				default:
			}
		}
	});
});
</script>