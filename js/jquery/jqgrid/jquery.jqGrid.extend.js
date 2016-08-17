jQuery.extend(jQuery.jgrid.defaults, { 
	loadComplete: function (data){
		if (jQuery(this).jqGrid('getGridParam', 'reccount') == 0)
		{
			jQuery(this, ".jqgfirstrow").css("height", "1px");
		}
		else
		{
			jQuery(this, ".jqgfirstrow").css("height", "auto");
		}
		
		jQuery(this).triggerHandler("loadComplete.jqGrid", data);
	}
});