<div class="content-right-header-toolbar ui-state-default ui-corner-bottom ui-helper-clearfix">
</div>

<div id="material_inventory_tabs">
	<ul>
		<li><a href="<?php echo site_url('material/inventoryudang/summary');?>">Summary</a></li>
		<li><a href="<?php echo site_url('material/inventoryudang/detail');?>">Detail</a></li>
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
});
</script>