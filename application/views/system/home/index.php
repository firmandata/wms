<!-- jChartFX -->
<link type="text/css" rel="stylesheet" media="screen" href="<?php echo base_url('css/jchartfx/jchartfx.css');?>"/>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.system.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.coreBasic.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.coreVector.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.advanced.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jchartfx/jchartfx.data.js');?>"></script>

<div id="system_home_tabs">
	<ul>
<?php 
if (is_authorized('material/inventory', 'index'))
{?>
		<li><a href="#system_home_tab_wms">Warehouse Management System</a></li>
<?php 
}
if (is_authorized('asset/asset', 'index'))
{?>
		<li><a href="#system_home_tab_fa">Fixed Asset</a></li>
<?php 
}?>
	</ul>
<?php 
if (is_authorized('material/inventory', 'index'))
{?>
	<div id="system_home_tab_wms">
		<div id="system_home_inventory_tabs">
			<ul>
				<li><a href="<?php echo site_url('system/home/inventory_warehouse');?>">Warehouse</a></li>
				<li><a href="<?php echo site_url('system/home/inventory_stock_accuration');?>">Stock Accuration</a></li>
				<li><a href="<?php echo site_url('system/home/inventory_ontime_delivery');?>">On Time Delivery</a></li>
				<li><a href="<?php echo site_url('system/home/inventory_top10_product');?>">Top 10 Product</a></li>
				<li><a href="<?php echo site_url('system/home/inventory_product_position');?>">Product Position</a></li>
				<li><a href="<?php echo site_url('system/home/inventory_warehouse_throughput');?>">Warehouse Throughput</a></li>
			</ul>
		</div>
	</div>
<?php 
}
if (is_authorized('asset/asset', 'index'))
{?>
	<div id="system_home_tab_fa">
		<div id="system_home_fa_tabs">
			NEXT TO DO
			<br/>FIXED ASSET DASHBOARD
		</div>
	</div>
<?php 
}?>
</div>

<script type="text/javascript">
jQuery(function(){
	jQuery("#system_home_tabs").tabs();
	
	$("#system_home_inventory_tabs").tabs({
		beforeLoad: function( event, ui ) {
			if (ui.panel.text())
				return false;
		}
	});
	
	jQuery("#system_home_tabs")
		.removeClass("ui-corner-all")
		.addClass("ui-corner-bottom");
});
</script>