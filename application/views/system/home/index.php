<!-- jChartFX -->
<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jchartfx/jchartfx.css');?>"/>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.system.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.coreBasic.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.coreVector.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.advanced.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.data.js');?>"></script>

<div id="system_home_tabs">
	<ul>
		<li><a href="<?php echo site_url('system/home/inventory_warehouse');?>">Warehouse</a></li>
		<li><a href="<?php echo site_url('system/home/inventory_stock_accuration');?>">Stock Accuration</a></li>
		<li><a href="<?php echo site_url('system/home/inventory_ontime_delivery');?>">On Time Delivery</a></li>
		<li><a href="<?php echo site_url('system/home/inventory_top10_product');?>">Top 10 Product</a></li>
		<li><a href="<?php echo site_url('system/home/inventory_product_position');?>">Product Position</a></li>
		<li><a href="<?php echo site_url('system/home/inventory_warehouse_throughput');?>">Warehouse Throughput</a></li>
	</ul>
</div>

<script type="text/javascript">
jQuery(function(){
	$("#system_home_tabs").tabs({
		beforeLoad: function( event, ui ) {
			if (ui.panel.text())
				return false;
		}
	});
	
	jQuery("#system_home_tabs").removeClass("ui-corner-all");
	jQuery("#system_home_tabs").addClass("ui-corner-bottom");
});
</script>