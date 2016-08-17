<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
	<button id="material_inventory_product_btn" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
		<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
		<span class="ui-button-text">Product Summary</span>
	</button>
</div>

<div id="material_inventory_tabs">
	<ul>
		<li><a href="<?php echo site_url('material/inventorypakan/summary');?>">Summary</a></li>
		<li><a href="<?php echo site_url('material/inventorypakan/detail');?>">Detail</a></li>
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
		
		window.open("<?php echo site_url('material/inventorypakan/get_product_summary_list');?>");
	});
});
</script>