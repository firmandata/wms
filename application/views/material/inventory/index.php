<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_inventory_product_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Product Summary</span>
	</button>
<?php 
if (is_authorized('material/inventory', 'update')){?>
	<button id="material_inventory_grid_reload_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span>
		<span class="ui-button-text">Reload Warehouse Usage</span>
	</button>
<?php 
}?>
</div>

<div id="material_inventory_tabs">
	<ul>
		<li><a href="<?php echo site_url('material/inventory/summary');?>">Summary</a></li>
		<li><a href="<?php echo site_url('material/inventory/detail');?>">Detail</a></li>
	</ul>
</div>

<script type="text/javascript">
jQuery(function(){
	$("#material_inventory_tabs").tabs({
		beforeLoad: function( event, ui ) {
			if (ui.panel.text())
				return false;
		}
	});
});

jQuery(document).ready(function(){
	jQuery("#material_inventory_product_btn").click(function(e){
		e.preventDefault();
		
		window.open("<?php echo site_url('material/inventory/get_product_summary_list');?>");
	});
	
	jQuery("#material_inventory_grid_reload_btn").click(function(){
		jquery_show_confirm("This process take a several minutes. Are your sure ?", function(){
			jQuery.ajax({
				url: "<?php echo site_url('material/inventory/grid_usage_reload');?>",
				type: "POST",
				dataType: "json",
				async : false,
				error: jquery_ajax_error_handler,
				beforeSend: function(jqXHR, settings){
					jquery_blockui();
				},
				success: function(data, textStatus, jqXHR){
					jquery_show_message("Reload warehouse storage complete.");
				},
				complete: function(jqXHR, textStatus){
					jquery_unblockui();
				}
			});
		});
	});
});
</script>