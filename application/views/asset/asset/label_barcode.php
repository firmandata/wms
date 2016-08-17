<html>
	<head>
		<title>Label Barcode</title>
		<style>
			@page {
				margin: 0px;
				padding: 0px;
			}
			
			body, table {
				font-size: 11px;
			}
			
			.cell_padding_left {
				padding-left: 3px;
			}
			
			.cell_padding_top {
				padding-top: 3px;
			}
			
			.cell_padding_right {
				padding-right: 3px;
			}
			
			.cell_padding_bottom {
				padding-bottom: 3px;
			}
			
			.line_top {
				border-top: 1px solid #000;
			}
			
			.line_left {
				border-left: 1px solid #000;
			}
			
			.line_right {
				border-right: 1px solid #000;
			}
			
			.line_bottom {
				border-bottom: 1px solid #000;
			}
		</style>
	</head>
	<body>
<?php
foreach ($records as $record_idx=>$record)
{?>
		<div <?php echo $record_idx > 0 ? 'style="page-break-before: always;"' : '';?>>
			<div style="height:132px; width:245px; padding:5px;">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="3" align="left" valign="top" class="line_left line_top line_right cell_padding_left cell_padding_bottom cell_padding_top cell_padding_right">
							<img src="<?php echo base_url('img/logo_24x24.png');?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="left" valign="top" class="line_left line_top line_right cell_padding_left cell_padding_right">
							Asset No :
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center" valign="top" class="line_left line_bottom line_right cell_padding_left cell_padding_right">
							<img src="<?php echo site_url('system/share/barcode');?>?value=<?php echo $record->code;?>&scale=1"/>
							<br/><?php echo $record->code;?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="line_left cell_padding_left" style="width:70px;">Region</td>
						<td valign="top" style="width:7px;">:</td>
						<td class="line_right cell_padding_right"><?php echo (!empty($record->c_region_code) ? $record->c_region_code : '-');?></td>
					</tr>
					<tr>
						<td valign="top" class="line_left cell_padding_left">Divisi/Group</td>
						<td valign="top">:</td>
						<td valign="top" class="line_right cell_padding_right">
							<?php echo (!empty($record->c_department_code) ? $record->c_department_code : '-');?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="line_left line_bottom cell_padding_left cell_padding_bottom">Description</td>
						<td valign="top" class="line_bottom cell_padding_bottom">:</td>
						<td valign="top" class="line_right line_bottom cell_padding_right cell_padding_bottom"><?php echo (!empty($record->name) ? $record->name : '-');?></td>
					</tr>
				</table>
			</div>
		</div>
<?php
}?>
	</body>
</html>