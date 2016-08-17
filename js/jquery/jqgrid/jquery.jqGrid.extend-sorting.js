function jqgrid_extend_sorting_get_name_by_sidx(self, sidx){
	var sname = null;
	
	var cm = jQuery(self).jqGrid('getGridParam', 'colModel'),
		i,
		l = cm.length;
	for (i = 0; i < l; i++)
	{
		if (cm[i].index === sidx)
		{
			sname = cm[i].name;
			break;
		}
	}
	
	return sname;
}

jQuery.extend(jQuery.jgrid.defaults, { 
	serializeGridData: function (postData){
		
		if (postData.sidx)
		{
			var self = this,
				sidx = postData.sidx,
				sidx_news = new Array();
			
			var sidx_comma = sidx.split(",");
			jQuery.each(sidx_comma, function(idx, val){
				var value = val.trim();
				
				var sname = null;
				var sidx_space = value.split(" ");
				if (sidx_space.length > 0)
					sname = jqgrid_extend_sorting_get_name_by_sidx(self, sidx_space[0].trim());
				
				var sdir = "";
				if (sidx_space.length > 1)
					sdir = sidx_space[1].trim();
				
				if (sname)
					sidx_news.push(sname + (sdir ? " " + sdir : ""));
			});
			
			if (sidx_news.length > 0)
				postData.sidx = sidx_news.join(",");
		}
		
		return postData;
	}
});