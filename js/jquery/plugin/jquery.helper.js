var jqgird_search_string_operators = ['bw','bn','eq','ne','lt','le','gt','ge','in','ni','ew','en','cn','nc'];
var jqgird_search_date_operators = ['eq','ne','lt','le','gt','ge','in','ni'];
var jqgird_search_datetime_operators = ['ge','gt','lt','le','eq','ne','in','ni'];
var jqgird_search_number_operators = ['ge','gt','lt','le','eq','ne','in','ni'];
var jqgrid_fixed_height = new Array;

function jqgrid_window_fixed_width(id)
{
	return jQuery('#gbox_' + id + '>parent').width();
}

function jqgrid_window_fixed_height(jqgrid, add_width)
{
	if (add_width == null) add_width = -141;
	var mixed = jqgrid_window_fixed_get_height(add_width);
	jqgrid_fixed_height.push([jqgrid, add_width]);
	
	return mixed;
}

function jqgrid_window_fixed_get_height(add_width)
{
	var mixed = jQuery(window).height() + add_width;
	
	if (mixed < 250)
		mixed = 250;
		
	return mixed;
}

function jqgrid_boolean_formatter(cellvalue, options, rowObject)
{
	return boolean_selectors[cellvalue];
}

function jqgrid_language_formatter(cellvalue, options, rowObject)
{
	return language_selectors[cellvalue];
}

function jqgrid_period_formatter(cellvalue, options, rowObject)
{
	return period_selectors[cellvalue];
}

function jqgrid_form_before_submit(postdata, formid)
{
	jquery_blockui();
	
	var _message = "Sending data...";
    return[true, _message];
}

function jqgrid_form_after_submit(response, postdata)
{
	jquery_unblockui();
	
	var _is_success = true;
	var _message;
	var _data = jQuery.parseJSON(response.responseText);
	
	if (_data.response == false)
	{
		_is_success = false;
		_message = _data.value;
	}
	else
		_message = "Data sent";
	
	return [_is_success, _message, postdata.id];
}

function jqgrid_form_error_text(response)
{
	jquery_unblockui();
	return jquery_ajax_error_status_handler(response);
}

function jqgrid_column_date(table_id, options)
{
	options = options || {};
	
	var defaults = { 
		width : 110,
		formatter : 'date',
		align : 'center'
	};
	options = $.extend(defaults, options);
	
	options.formatoptions = options.formatoptions || {};
	options.formatoptions = $.extend({
		srcformat:server_date_format,
		newformat:client_jqgrid_date_format
	}, options.formatoptions);
	
	options.searchoptions = options.searchoptions || {};
	options.searchoptions = $.extend({
		sopt:jqgird_search_date_operators, 
		clearSearch:false, 
		dataInit:function(element){
			jQuery(element)
				.datepicker()
				.change(function(){
					jQuery('#' + table_id)[0].triggerToolbar();
				});
		}
	}, options.searchoptions);
	
	return options;
}

function jqgrid_column_datetime(table_id, options)
{
	options = options || {};
	
	var defaults = { 
		width : 130,
		formatter : 'date',
		align : 'center'
	};
	options = $.extend(defaults, options);
	
	options.formatoptions = options.formatoptions || {};
	options.formatoptions = $.extend({
		srcformat:server_datetime_format,
		newformat:client_jqgrid_datetime_format
	}, options.formatoptions);
	
	options.searchoptions = options.searchoptions || {};
	options.searchoptions = $.extend({
		sopt:jqgird_search_datetime_operators, 
		clearSearch:false, 
		dataInit:function(element){
			jQuery(element)
				.datetimepicker()
				.change(function(){
					jQuery('#' + table_id)[0].triggerToolbar();
				});
		}
	}, options.searchoptions);
	
	return options;
}

jQuery(window).resize(function()
{
	for (var _jqgrid_counter = 0; _jqgrid_counter < jqgrid_fixed_height.length; _jqgrid_counter++)
	{
		var height = jqgrid_window_fixed_get_height(jqgrid_fixed_height[_jqgrid_counter][1]);
		jqgrid_fixed_height[_jqgrid_counter][0].setGridHeight(height);
	}
});

jQuery(document).ready(function()
{
	jquery_ready_load();
});

function jquery_ready_load()
{
	jQuery("#dialog_alert_container").dialog({
		modal: true,
		autoOpen: false,
		close: function(event, ui)
		{
			jQuery("#dialog_alert_icon").removeClass();
		},
		buttons: {
			OK: function() {
				jQuery(this).dialog("close");
			}
		}
	});
	
	jQuery("#dialog_confirm_container").dialog({
		modal: true,
		autoOpen: false,
		close: function(event, ui)
		{
			jQuery("#dialog_confirm_icon").removeClass();
		},
		buttons: {
			OK: function() {
				jQuery(this).dialog("close");
			},
			Cancel: function() {
				jQuery(this).dialog("close");
			}
		}
	});
	
	jQuery(".date").datepicker();
	jQuery(".datetime").datetimepicker({
		showSecond: true,
		timeFormat: client_picker_time_format
	});
	jQuery(".datetime-nosecond").datetimepicker({
		timeFormat: client_picker_time_nosecond_format
	});
}

function jquery_search_date(element)
{
	jQuery(element).datepicker();
}

function jquery_search_datetime(element)
{
	jQuery(element).datetimepicker({
		showSecond: true,
		timeFormat: client_picker_time_format
	});
}

function jquery_search_datetime_nosecond(element)
{
	jQuery(element).datetimepicker({
		timeFormat: client_picker_time_nosecond_format
	});
}

function jquery_show_message(message, title, icon_class, ok_func)
{
	if (title == null) title = "Information";
	jQuery("#dialog_alert_container").dialog("option", "title", title);
	
	if (icon_class == null)
		icon_class = "ui-icon-info";
	
	jQuery("#dialog_alert_icon").addClass("ui-icon");
	jQuery("#dialog_alert_icon").addClass(icon_class);
	
	jQuery("#dialog_alert_message").html(message);
	
	if (ok_func != null)
	{
		jQuery("#dialog_alert_container").dialog("option", 
			"buttons", 
			[	{text: "OK", 
				 click: function()
				 {
					jQuery(this).dialog("close");
					if (ok_func != null)
						ok_func();
				 }
				}
			] 
		);
	}
	
	jQuery("#dialog_alert_container").dialog("open");
}

function jquery_show_confirm(message, ok_func, cancel_func, title, icon_class)
{
	if (title == null) title = "Confirmation";
	jQuery("#dialog_confirm_container").dialog("option", "title", title);
	
	if (icon_class == null)
		icon_class = "ui-icon-help";
	
	jQuery("#dialog_confirm_icon").addClass("ui-icon");
	jQuery("#dialog_confirm_icon").addClass(icon_class);
	
	jQuery("#dialog_confirm_message").html(message);
	
	jQuery("#dialog_confirm_container").dialog("option", 
		"buttons", 
		[	{text: "OK", 
			 click: function()
			 {
				jQuery(this).dialog("close");
				if (ok_func != null)
					ok_func();
			 }
			},
			{text: "Cancel", 
			 click: function()
			 {
				jQuery(this).dialog("close");
				if (cancel_func != null)
					cancel_func();
			 }
			}
		] 
	);
	
	jQuery("#dialog_confirm_container").dialog("open");
}

function jquery_form_set(form_id, data)
{
	for (var _field_name in data)
	{
		var _value = data[_field_name];
		var _selected_el = '#' + form_id + ' [name="' + _field_name + '"]';
		
		jquery_field_set(_selected_el, _value);
	}
}

function jquery_field_set(selector_str, value)
{
	var _input_type = jQuery(selector_str).attr('type');
	switch (_input_type)
	{
		case "checkbox":
			if (value === true || value == "1" || value == "yes" || value == "y" || value == "true" || value == "t" || value == 1)
				jQuery(selector_str).prop('checked', true);
			else
				jQuery(selector_str).prop('checked', false);
		break;
		case "radio":
			if (jQuery(selector_str + '[value="' + value + '"]').length > 0)
				jQuery(selector_str + '[value="' + value + '"]').prop('checked', true);
			else
				jQuery(selector_str + '[value="' + value + '"]').prop('checked', false);
		break;
		default:
			if (jQuery(selector_str).hasClass('date'))
			{
				if (isDate(value, server_client_parse_validate_date_format))
				{
					var _date = new Date(getDateFromFormat(value, server_client_parse_validate_date_format));
					if (jQuery(selector_str).is(':data(datepicker)'))
						jQuery(selector_str).datepicker('setDate', _date);
					else
						jQuery(selector_str).val(formatDate(_date, client_validate_date_format));
				}
				else
				{
					if (jQuery(selector_str).is(':data(datepicker)') && value == null)
						jQuery(selector_str).datepicker('setDate', null);
					else
						jQuery(selector_str).val(value);
				}
			}
			else if (jQuery(selector_str).hasClass('time'))
			{
				if (isDate(value, server_client_parse_validate_time_format))
				{
					var _time = new Date(getDateFromFormat(value, server_client_parse_validate_time_format));
					if (jQuery(selector_str).is(':data(timepicker)'))
						jQuery(selector_str).datepicker('setTime', _time);
					else
						jQuery(selector_str).val(formatDate(_date, client_validate_time_format));
				}
				else
				{
					if (jQuery(selector_str).is(':data(timepicker)') && value == null)
						jQuery(selector_str).datepicker('setTime', null);
					else
						jQuery(selector_str).val(value);
				}
			}	
			else if (jQuery(selector_str).hasClass('datetime'))
			{
				if (isDate(value, server_client_parse_validate_datetime_format))
				{
					var _date_time = new Date(getDateFromFormat(value, server_client_parse_validate_datetime_format));
					if (jQuery(selector_str).is(':data(datetimepicker)'))
						jQuery(selector_str).datepicker('setDate', _date_time);
					else
						jQuery(selector_str).val(formatDate(_date_time, client_validate_datetime_format));
				}
				else
				{
					if (jQuery(selector_str).is(':data(datetimepicker)') && value == null)
						jQuery(selector_str).datepicker('setDate', null);
					else
						jQuery(selector_str).val(value);
				}
			}
			else if (jQuery(selector_str).hasClass('datetime-nosecond'))
			{
				if (isDate(value, server_client_parse_validate_datetime_nosecond_format))
				{
					var _date_time = new Date(getDateFromFormat(value, server_client_parse_validate_datetime_nosecond_format));
					if (jQuery(selector_str).is(':data(datetimepicker)'))
						jQuery(selector_str).datepicker('setDate', _date_time);
					else
						jQuery(selector_str).val(formatDate(_date_time, client_validate_datetime_nosecond_format));
				}
				else
				{
					if (jQuery(selector_str).is(':data(datetimepicker)') && value == null)
						jQuery(selector_str).datepicker('setDate', null);
					else
						jQuery(selector_str).val(value);
				}
			}
			else if (jQuery(selector_str).hasClass('ckeditor'))
			{
				var cke_id = jQuery(selector_str).attr('id');
				var cke = CKEDITOR.instances[cke_id];
				jQuery(selector_str).val(value);
				cke.setData(value);
			}
			else
				jQuery(selector_str).val(value);
		break;
	}
}

function jquery_blockui(jq_element)
{
	if (jq_element && jq_element.length > 0)
	{
		jq_element.block({
			message: '<span class="blocker-ui"></span>',
			css: {border: 'none', width: '50px', height: '50px', left: '47%', backgroundColor: 'transparent'}
		});
	}
	else
	{
		jQuery.blockUI({
			message: '<div class="content-loading"><div class="content-loading-title">Loading...</div></div>',
			css: {border: 'none', top: 0},
			overlayCSS:  {
				backgroundColor: '#ffffff',
				opacity: 0
			},
			fadeIn: 0,
			fadeOut: 0
		});
	}
}

function jquery_unblockui(jq_element)
{
	if (jq_element && jq_element.length > 0)
		jq_element.unblock();
	else
		jQuery.unblockUI();
}

function jquery_add_date(date, day, month, year)
{
	if (day)
		return new Date(date.getTime() + day * 24 * 60 * 60 * 1000);
	else if (month)
	{
		var new_month = date.getMonth() + month;
		if (new_month < 0) 
			new_month = 11;
		return new Date(date.getFullYear(), new_month, date.getDate());
	}
	else if (year)
		return new Date(date.getFullYear() + year, date.getMonth(), date.getDate());
	
	return date;
}

function jquery_ajax_error_handler(xhr, exception)
{
	var _status_error = jquery_ajax_error_status_handler(xhr, exception);
	var _server_error = jquery_ajax_error_server_handler(xhr, exception);	
	var _error_message = _status_error + (_server_error ? "<br/>" + _server_error : '');
	
	jquery_show_message(_error_message, "Error", "ui-icon-circle-close");
}

function jquery_ajax_error_server_handler(xhr, exception)
{
	var server_message = "";
	try
	{
		var jsonResponse = jQuery.parseJSON(xhr.responseText);
		
		var server_messages = new Array;
		if (jsonResponse.title != undefined)
			server_messages.push('<strong>' + jsonResponse.title + '</strong>');
		if (jsonResponse.heading != undefined)
			server_messages.push('<i>' + jsonResponse.heading + '</i>');
		if (jsonResponse.type != undefined)
			server_messages.push(jsonResponse.type);
		if (jsonResponse.message != undefined)
			server_messages.push(jsonResponse.message);
		if (jsonResponse.value != undefined)
			server_messages.push(jsonResponse.value);
		if (jsonResponse.severity != undefined)
			server_messages.push('severity : ' + jsonResponse.severity);
		if (jsonResponse.filepath != undefined)
			server_messages.push('filepath : ' + jsonResponse.filepath);
		if (jsonResponse.filename != undefined)
			server_messages.push('filename : ' + jsonResponse.filename);
		if (jsonResponse.line != undefined)
			server_messages.push('line : ' + jsonResponse.line);
		if (jsonResponse.backtraces != undefined)
		{
			var backtraces = new Array;
			jQuery.each(jsonResponse.backtraces, function(i, v){
				var backtrace = new Array;
				if (v.file != undefined)
					backtrace.push('file : ' + v.file);
				if (v.line != undefined)
					backtrace.push('line : ' + v.line);
				if (v.function != undefined)
					backtrace.push('function : ' + v.function);
				backtraces.push(backtrace.join('<br/>'));
			});
			server_messages.push('backtrace : ' + backtraces.join('<br/>'));
		}
		
		server_message = server_messages.join('<br/>');
	}
	catch (e)
	{
		server_message = xhr.responseText;
	}
	
	return server_message;
}

function jquery_ajax_error_status_handler(xhr, exception)
{
	var error_message = "";
	if (xhr.status === 0) {
		error_message += 'Not connect.<br/>Verify network.';
	} else if (xhr.status == 400) {
		error_message += 'Server understood the request but request content was invalid.';
	} else if (xhr.status == 401) {
		error_message += 'Unauthorised access.';
	} else if (xhr.status == 403) {
		error_message += 'Forbidden resouce can\'t be accessed.';
	} else if (xhr.status == 404) {
		error_message += 'Requested not found.';
	} else if (xhr.status == 500) {
		error_message += 'Internal server error.';
	} else if (xhr.status == 503) {
		error_message += 'Service unavailable.';
	} else if (exception != null) {
		if (exception === 'parsererror') {
			error_message += 'Requested JSON parse failed.';
		} else if (exception === 'timeout') {
			error_message += 'Time out error.';
		} else if (exception === 'abort') {
			error_message += 'Request aborted.';
		}
	} else {
		error_message += 'Uncaught Error.';
	}
	
	return error_message;
}

function jquery_dialog_form_open(element_id, url, data, on_submit, dialog_options)
{
	var container_elem = jQuery('#' + element_id);
	if (container_elem && container_elem.length > 0)
		container_elem.remove();
	
	data = data || {};
	
	var buttons = new Array;
	if (on_submit != null)
	{
		buttons = [ 
			{text: "Save", 
			 icons: {
				primary: "ui-icon-disk"
			 },
			 click: function(){
				on_submit(jQuery(this));
			 }
			},
			{text: "Cancel", 
			 icons: {
				primary: "ui-icon-cancel"
			 },
			 click: function(){
				jQuery(this).dialog("close");
			 }
			} 
		];
	}
	
	dialog_options = dialog_options || {};
	var dialog_defaults = {
		autoOpen: true,
		modal: false,
		buttons: buttons,
		close: function(event, ui){
			jQuery(this).remove();
		}
	};
	dialog_options = $.extend(dialog_defaults, dialog_options);
	
	jQuery.ajax({
		url: url,
		type: "GET",
		dataType: "html",
		async : false,
		data : data,
		error: jquery_ajax_error_handler,
		success: function(data, textStatus, jqXHR){
			jQuery("<div>", {
				id		: element_id,
				'class'	: "ui-widget ui-widget-content ui-corner-all"
			})
			.dialog(dialog_options)
			.html(data)
			.dialog("option", "position", {my: "center", at: "center", of: window})
			.find('form').keypress(function(e){
				var charCode = e.charCode || e.keyCode || e.which;
				if (charCode  == 13)
				{
					e.preventDefault();
					return false;
				}
			});
		}
	});
}

function jquery_select2_build(src_element, url, options)
{
	options = options || {};
	var defaults = { 
		minimumInputLength	: 0,
		allowClear			: true,
		ajax 				: {
			url: url,
			quietMillis: 800,
			dataType: 'json',
			data: function(term, page){
				return {
					q: term,
					page: page,
					limit: 20
				};
			},
			results: function(data, page){
				return {
					more: data.more,
					results: data.results,
					context: data
				};
			}
		},
		initSelection 		: function (element, callback){
			var id = element.val();
			if (id)
			{
				if (element.is("[data-text]") && element.attr('data-text') != null)
				{
					callback({
						id 		: id,
						text 	: element.attr('data-text')
					});
				}
				else
				{
					jQuery.ajax({
						url: url,
						type: "GET",
						dataType: "json",
						data : {
							id : id
						},
						error: jquery_ajax_error_handler,
						success: function(data, textStatus, jqXHR){
							if (data.results.length > 0)
							{
								var selected_data = data.results[0];
								callback(selected_data);
							}
						}
					});
				}
			}
		}
	};
	options = $.extend(defaults, options);
	jQuery(src_element).select2(options);
}

function jquery_autocomplete_build(src_element, url, display_options, options)
{
	display_options = display_options || {};
	var default_display_options = {
		must_select : true,
		width : 200
	}
	display_options = $.extend(default_display_options, display_options);
	
	src_id = jQuery(src_element).attr('id') + '_caption';
	src_class = jQuery(src_element).attr('class');
	
	target_element = src_element + '_caption';
	
	options = options || {};
	var defaults = { 
		source: url,
		autoFocus: true,
		minLength: 0,
		change: function(event, ui){
			if (display_options.must_select == true)
			{
				if (ui.item == null)
				{
					jQuery(this).val('');
					jQuery(src_element).val('');
				}
			}
			if (ui.item != null)
			{
				jQuery(src_element).val(ui.item.id);
			}
		}
	};
	options = $.extend(defaults, options);
	
	var text_default = '';
	if (jQuery(src_element).is("[data-text]") && jQuery(src_element).attr('data-text') != null)
		text_default = jQuery(src_element).attr('data-text');
	
	jQuery("<input>", {type : 'text', id : src_id, name:src_id, 'class' : src_class})
		.width(display_options.width)
		.val(text_default)
		.insertAfter(jQuery(src_element))
		.autocomplete(options);
}