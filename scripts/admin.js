// FUNCTIONS


// checkbox.js

function any_box_checked
	(	field_id
	) {
	ch = 0;	
	b1 = document.getElementById(field_id);
	b = b1.form.elements[b1.name];
	if	(	b.length	
		) {
		for	(	var c = 0; c < b.length; c++	
			) {
			if	(	b[c].checked	
				) {
				ch = 1;
				break;
			}
		}
	}
	else {
		if	(	b1.checked	
			) {	
			ch = 1;
		}
	}
	return ch;
}

function checked_box_count
	(	field_id
	) {
	ch = 0;	
	b1 = document.getElementById(field_id);
	b = b1.form.elements[b1.name];
	if	(	b.length	
		) {
		for	(	var c = 0; c < b.length; c++	
			) {
			if	(	b[c].checked	
				) {
				ch++;
			}
		}
	}
	else {
		if	(	b1.checked	
			) {	
			ch = 1;
		}
	}
	return ch;
}


function checkbox_limit_init
	(	field_id
	,	selectable_minimum
	,	selectable_limit
	) {
	boxes_must_be_checked = selectable_minimum;
	boxes_can_be_checked = selectable_limit;
	boxes_checked = checked_box_count(field_id);
/*	
	if	(	boxes_checked < boxes_must_be_checked
		) {
		for	(	var sm = 0
			;	sm < selectable_minimum - boxes_checked
			;	sm++
			) {
			boxes[sm].checked = 1;
			document.getElementById(checkbox_field_name+'-label').className = 'rec_fld_tbl_cell_selected';
		}
	}
*/
}

function checkbox_limit
	(	checkbox_field
	,	checkbox_title
	) {
	boxes_checked = checked_box_count(checkbox_field.id);
	if	(	boxes_can_be_checked
		&&	boxes_checked > boxes_can_be_checked
		) {
		checkbox_field.checked = 0;
		alert_text = 
		(	boxes_must_be_checked == boxes_can_be_checked
		)
		? 'Please select only '+boxes_must_be_checked+' '+checkbox_title+'.'
		: 'You may only select a maximum of '+boxes_can_be_checked+' '+checkbox_title+'.'
		;
		alert(alert_text);
	}
/*	
	if	(	!boxes_checked
		) {
		
//	if	(	boxes_checked < boxes_must_be_checked
//		) {
//		checkbox_field.checked = 1;

		alert_text = 
		(	boxes_must_be_checked == boxes_can_be_checked
		)
		? 'Please select '+boxes_must_be_checked+' '+checkbox_title+'.'
		: 'Please select at least '+boxes_must_be_checked+' '+checkbox_title+'.'
		;
		alert(alert_text);
	}
*/
}


// dropdown_limit.js

function dropdown_limit_init(dropdown_field, selectable_minimum, selectable_limit) {
	options_must_be_selected = selectable_minimum;
	options_can_be_selected = selectable_limit;
	options_selected = 0;
	this_init_array = dropdown_field.name.replace(/-/,'_');
	this_init_array = this_init_array.replace(/\[\]/,'');
	eval(this_init_array+' = new Array;');
	for (var ai = 0; ai < dropdown_field.options.length; ai++) {
		eval(this_init_array+'['+ai+'] = '+dropdown_field.options[ai].selected+';');
		if (dropdown_field.options[ai].value > 0 && dropdown_field.options[ai].selected) options_selected++;
	}
	if (!options_must_be_selected) {
		if (!options_selected && dropdown_field.options[0].value < 1) dropdown_field.options[0].selected = 1;
	} 
/*
	  else {
		for (var sm = 0; sm < options_must_be_selected - options_selected; sm++) {
			dropdown_field.options[sm].selected = 1;
		}
	}
*/
}

function dropdown_limit(dropdown_field, checkbox_title) {
	options_selected = 0;
	last_option_selected = -1;
	for (var oi = 0; oi < dropdown_field.options.length; oi++) {
		if (dropdown_field.options[oi].value > 0 && dropdown_field.options[oi].selected) {
			options_selected++;
			eval('wtf = '+this_init_array+'['+oi+'];');
			if (!wtf && last_option_selected != oi) last_option_selected = oi;
		}
	}
	if (options_selected && dropdown_field.options[0].value < 1) dropdown_field.options[0].selected = 0;
	if (options_can_be_selected && options_selected > options_can_be_selected) {
		dropdown_field.options[last_option_selected].selected = false;
		alert_text = (options_must_be_selected == options_can_be_selected) 
		? 'Please select only '+options_must_be_selected+' '+checkbox_title+'.'
		: 'You may only select a maximum of '+options_can_be_selected+' '+checkbox_title+'.';
		alert(alert_text);
	}
/*
	if (!options_selected) {
//	if (options_selected < options_must_be_selected) {
		alert_text = (options_must_be_selected == options_can_be_selected) 
		? 'Please select '+options_must_be_selected+' '+checkbox_title+'.'
		: 'Please select at least '+options_must_be_selected+' '+checkbox_title+'.';
		alert(alert_text);
	}
*/
	dropdown_limit_init(dropdown_field, options_must_be_selected, options_can_be_selected);
}


// enter_submit.js

function enter_submit
(	field
,	e_vent
)
{	if	(	field.value.length
		)
	{	var keycode;
		if	(	window.event
			)
		{	keycode = window.event.keyCode;
		}
		else if (	e_vent
			)
		{	keycode = e_vent.which;
		}
		else
		{	return true;
		}
	
		if	(	keycode == 13
			)
		{	if	(	field.form.most_recent_filter
				)
			{	field.form.most_recent_filter.value = field.name;
			}
			field.form.submit();
			return false;
		}
		else
		{	return true;
		}
	}
	else
	{	return true;
	}
}


// forgot_password.js

function forgotPass(formName,loginWith,loginWithSplay) {
	var logForm = document.forms[formName];
	var logField = logForm[loginWith];
	if (logField.value.length > 0) {
		logForm.ja.value = "forgot";
		logForm.submit();
	} else {
		alert("Please enter the "+loginWithSplay+" you used to create your account.");
	}
}


// framebuster.js

/* FRAMEBUSTER */
if (top.frames.length > 0 && top.location != self.location) top.location.href = self.location;


// last_day_of_month.js

var days_in_months = new Array;
days_in_months[1] = 31;
days_in_months[2] = 28;
days_in_months[3] = 31;
days_in_months[4] = 30;
days_in_months[5] = 31;
days_in_months[6] = 30;
days_in_months[7] = 31;
days_in_months[8] = 31;
days_in_months[9] = 30;
days_in_months[10] = 31;
days_in_months[11] = 30;
days_in_months[12] = 31;

function last_day_of_month(filter_field) {
	var cc_month_select = document.getElementById(filter_field+"-month");
	var cc_day_select = document.getElementById(filter_field+"-day");
	var cc_year_select = document.getElementById(filter_field+"-year");
	var selected_month = cc_month_select[cc_month_select.selectedIndex].value;
	var selected_year = cc_year_select[cc_year_select.selectedIndex].value;
	var days_in_month = days_in_months[selected_month];
	if (is_leap_year(selected_year) && selected_month == 2) days_in_month++;
	cc_day_select.value = days_in_month;
}
function is_leap_year(year) {
	var leap = 0;
	if (year%100 == 0) {
		if (year%400 == 0) leap = 1;
	} else {
		if (year%4 == 0) leap = 1;
	}
	return leap;
}


// paginator_init.js

function to_page(form_to_submit,first_item) {
	var page_form = document.getElementById(form_to_submit);
	page_form[form_to_submit+'-first_item'].value = first_item;
	page_form.submit();
}


// radio_limit.js

function radio_limit(radio_field_name, force_checked) {
	this_button = document.getElementById(radio_field_name);
	if	(force_checked) {
		this_button.checked = 1;
	}
	buttons = this_button.form.elements[this_button.name];
	for (var button = 0; button < buttons.length; button++) {
		this_button = buttons[button];
		this_button_label = document.getElementById(this_button.name+'-'+button+'-label');
		if	(	this_button.checked	
			) {
			if	(	this_button_label.className.indexOf('rec_fld_tbl_cell_selected')	< 0
				) {
				this_button_label.className = this_button_label.className.replace(/rec_fld_tbl_cell/,'rec_fld_tbl_cell_selected');
			}
		} else {
			this_button_label.className = this_button_label.className.replace(/rec_fld_tbl_cell_selected/,'rec_fld_tbl_cell');
		}
	}
}


// sort_table_columns.js

function sort_sort(which_form,no_touch,ini_num) {
	var this_form = document.getElementById(which_form);
	if (ini_num > 0) {
		for (var i in sort_field_ray) {
			if (sort_field_ray[i].length > 0 && no_touch != sort_field_ray[i]) {
				var this_elem = this_form[sort_field_ray[i]];
				if (this_elem) {
					var this_index = this_elem.selectedIndex;
					if (this_index >= ini_num) this_elem.selectedIndex = (this_index+1 < this_elem.options.length) ? this_index + 1 : 0;
				}
			}
		}
	}
	var s = 1;
	var t = 0;
	var sort_ray = new Array();
	var sorted_ray = new Array();
	while (s < sort_field_ray.length) {
		sort_ray[s] = 0;
		while (!sort_ray[s] && s + t < sort_field_ray.length) {
			for (var i in sort_field_ray) {
				if (sorted_ray[i] == null) sorted_ray[i] = 0;
//				alert("s = "+s+"\ni = "+i+"\nt = "+t+"\nsorted_ray["+i+"] = "+sorted_ray[i]);
				if (sort_field_ray[i].length > 0 && !sorted_ray[i]) {
					var this_elem = this_form[sort_field_ray[i]];
					if (this_elem) {
						var this_index = this_elem.selectedIndex;
						if (this_index) {
							if (this_index == s + t) {
								sort_ray[s] = i;
								sorted_ray[i] = 1;
								t = 0;
								break;
							}
						} else {
							continue;
						}
					}
				}
			}
			if (!sort_ray[s]) t++;
		}
		s++;
		t = 0;
	}
	for (var b in sort_ray) {
		if (sort_ray[b]) {
			this_elem = this_form[sort_field_ray[sort_ray[b]]];
//			alert("sort_field_ray[sort_ray["+b+"]] = "+sort_field_ray[sort_ray[b]]+"\nsort_ray["+b+"] = "+sort_ray[b]);
			this_elem.selectedIndex = b;
		}
	}
	this_form.submit();
}

function sort_dir(which_form,which_sort) {
	var this_form = document.getElementById(which_form);
	var this_sort = document.getElementById(which_form+'-'+which_sort+'-sort');
	if (this_sort.selectedIndex > 0) {
		this_form.submit();
	} else {
		var this_dir = document.getElementById(which_form+'-'+which_sort+'-dir');
		this_dir.selectedIndex = 0;
	}
}

function sort_single(sort_name,dir_name,this_form,e) {
	var tget = e.target || e.srcElement;
	if	(	tget.className.indexOf('filter_select')	==	-1
		)
	{	if	(	$('#'+sort_name).val()
			)
		{	if	(	$('#'+dir_name).val() == 'ASC'
				)
			{	$('#'+dir_name).val('DESC');
			}
			else
			{	$('#'+dir_name).val('ASC');
			}
		}
	
		$('#'+sort_name).val('1');
		
		sort_sort(this_form,sort_name,1);
	}
}


// sort_table_records.js

/*
// Original:  Roelof Bos (roelof667@hotmail.com)
// Web Site:  http://www.refuse.nl
// This script and many more are available free online at
// The JavaScript Source!! http://javascript.internet.com
*/

function move(index,to) {
	var old_order = document.order_form.old_order;
	var total = old_order.options.length-1;
	if (index == -1) return false;
	if (to == +1 && index == total) return false;
	if (to == -1 && index == 0) return false;
	var items = new Array;
	var values = new Array;
	for (var i = total; i >= 0; i--) {
		items[i] = old_order.options[i].text;
		values[i] = old_order.options[i].value;
	}
	for (var i = total; i >= 0; i--) {
		if (index == i) {
			old_order.options[i + to] = new Option(items[i],values[i + to], 0, 1);
			old_order.options[i] = new Option(items[i + to], values[i]);
			i--;
		} else {
			old_order.options[i] = new Option(items[i], values[i]);
		}
	}
	old_order.focus();
}

var bitchit = '';

function submit_form() {
	var old_order = document.order_form.old_order;
	var the_list = "";
	for (var i = 0; i <= old_order.options.length-1; i++) { 
		bitchit += old_order.options[i].text+"\n";
		for (var t = 0; t <= old_order.options.length-1; t++) {
			bitchit += ' = '+ref_list[old_order.options[t].value]+"\n";
			if (old_order.options[i].text == ref_list[old_order.options[t].value]) {
				the_list += old_order.options[t].value;
				if (i != old_order.options.length-1) the_list += ",";
				bitchit += "\n";
				break;
			}
		}
	}
	document.order_form.new_order.value = the_list;
	alert(bitchit);
	document.order_form.submit();
}

var ref_list = new Array;


// textarea_limit.js

function textarea_limit(text_field, char_limit) {
	if (text_field.value.length > char_limit) /* if too long...trim it! */
		text_field.value = text_field.value.substring(0,char_limit);
}


function getFileName() {
	//this gets the full url
	var url = document.location.href;
	//this removes the anchor at the end, if there is one
	url = url.substring(0, (url.indexOf('#') == -1) ? url.length : url.indexOf('#'));
	//this removes the query after the file name, if there is one
	url = url.substring(0, (url.indexOf('?') == -1) ? url.length : url.indexOf('?'));
	//this removes everything before the last slash in the path
	url = url.substring(url.lastIndexOf('/') + 1, url.length);
	//return
	return url;
}

var $file_name = getFileName();

function inline_submit
(	$field_names
,	$e_vent
,	$re_turn	= false
)
{	if	(	$('#inl_edr').is(':visible')
		)
	{	$table				=	$('#z').val();
		$id					=	$('#inl_edr').attr('data-id');
		$fields				=	new Object();
		$field_names = $field_names.split(',');
		for	($a=0;$a<$field_names.length;$a++)
		{	$fields[$field_names[$a]]	=	$('#'+$field_names[$a]).val();
		}
		if	(	!$re_turn
			)
		{	var $keycode;
			if	(	window.event
				)
			{	$keycode = window.event.keyCode;
			}
			else if (	$e_vent
				)
			{	$keycode = $e_vent.which;
			}
			else
			{	return true;
			}
		
			if	(	$keycode == 13
				)
			{	return inline_update
				(	$table
				,	$id
				,	$fields
				);
			}
			else
			{	return true;
			}
		}
		else
		{	return inline_update
			(	$table
			,	$id
			,	$fields
			);
		}
	}
}

var $inline_updating = false;
function inline_update
(	$table
,	$id
,	$fields
)
{	if	(	!$inline_updating
		&&	typeof $fields			== 'object'
// ADMIN LOGGED-IN COOKIE CHECK HERE ALSO ???
		)
	{	$inline_updating = true;
		$('#inl_edr').prepend('<div id="wait_for_it"><img src="images/admin/figure_8_loader.gif"/></div>');
		if	(	$('#inl_edr').outerWidth()	<	$('#wait_for_it > img').outerWidth()
			)
		{	$('#wait_for_it > img').css('margin-left','-'+Math.floor(($('#wait_for_it > img').outerWidth()-$('#inl_edr').outerWidth())/1.5)+'px');
		}
		$('#inl_edr').addClass('waiting');
		$.ajax(
		{	type:		'POST'
		,	url:		'interface'
		,	data:
			{	z:			'record_fields_update'
			,	table:		$table
			,	id:			$id
			,	fields:		$fields
	      	}
//		,	timeout: $waittime
	  	,	dataType:	'jsonp'
		,	success: function(data)
			{	if	(	typeof data.response.errors	!= 'undefined'
					)
				{	$('#console_message').append(data.response.errors);
					console.log(data.response.errors);	
					for (var key in data.response.errors)
					{	if (data.response.errors.hasOwnProperty(key))
						{	data.response.errors[key] = $db_errors[data.response.errors[key]];
						}
					}
				}
				else
				{	if	(	typeof data.response.updated != 'undefined'
						)
					{	console.log('updated value: |'+data.response.updated+'|');
						$opts = $('#inl_edr select option');
						if	(	$opts.length
							)
						{	$opts.each(function()
							{	if	(	data.response.updated.length
									)
								{	if	(	$(this).attr('value')	==	data.response.updated
										)
									{	if	(	$('.big_tbl .cap[data-col="'+$co+'"] .flt_sel select').length
											)	
										{	if	(	$(this).attr('value') == 0
												)
											{	$nuht = '';												
											}
											else
											{	$oght = $('.big_tbl tr[data-id="'+$id+'"] .cel[data-col="'+$('#inl_edr').attr('data-col')+'"]').html();
												$nuht = ($oght.match(/<a /))?$oght.replace(/>.*?<\//,'>'+Encoder.htmlEncode($(this).text())+'</').replace(/&amp;id=[^"]+"/,'&id='+data.response.updated+'"'):Encoder.htmlEncode($(this).text());
											}
										}
										else
										{	$nuht = Encoder.htmlEncode($(this).text());												
										}
										$('.big_tbl tr[data-id="'+$id+'"] .cel[data-col="'+$('#inl_edr').attr('data-col')+'"]').html($nuht);
										return true;
									}
								} 
								else
								{	if	(	$(this).hasClass('null_option')
										)
									{	$('.big_tbl tr[data-id="'+$id+'"] .cel[data-col="'+$('#inl_edr').attr('data-col')+'"]').html('');
										return true;													
									}												
								}
							});		
						}
						else
						{	$('.big_tbl tr[data-id="'+$id+'"] .cel[data-col="'+$('#inl_edr').attr('data-col')+'"]').html(data.response.updated);
						}
						$('#inl_edr').hide();
						$('#inl_edr').removeClass('waiting');
						$inline_updating = false;
						return false;
					}
					else
					{	console.log('there was an error updating for record #'+$id+' in table "'+$table+'"');
					}
				}
			}
		,	error: function()
			{	console.log('there was an error making the ajax call "record_fields_update"');
			}
		});
	}
	return true;
}


// EXECUTE ON DOCUMENT READY
$(function(){

	$('#z').live(
	{	change:	function()
		{	if	(	$(this).val().length
				)
			{	$(location).attr('href',$file_name+'?z='+$(this).val());
			}
		}
	});	
	
	$('.big_tbl .cel').on(
	{	click: function()
		{	if	(	!$inline_updating
				)
			{	$cell = $(this);
				if	(	$cell.attr('data-col')	>	0
					&&	$cell.attr('data-inedible') == 0
					)
				{	if	(	!$('#inl_edr').length
						)
					{	$('#page_content').prepend('<div id="inl_edr"></div>');		
					}
					$ed = $('#inl_edr');
					$co = $cell.attr('data-col');
					// CAN'T DO THIS WITH DATES/TIMES/MULTIPLE SELECTS YET... LOTTA SCHTUFF GOTTA HAPPEN THERE
					if	(	$('.big_tbl .cap[data-col="'+$co+'"] .flt_sel').length
						&&	$('.big_tbl .cap[data-col="'+$co+'"] .flt_sel select').length	<=	1
						)
					{	$id = $cell.closest('tr').attr('data-id');
						$ed.attr(
						{	'data-id':	$id
						,	'data-col':	$co
						});
						$ov = $cell.text();
						if	(	$ed.html($('.big_tbl .cap[data-col="'+$co+'"] .flt_sel').html())
							)
						{	$yo_input = $ed.children('input');
							$ed.removeClass('frm');	
							if	(	$yo_input.length == 1
								)
							{	$ed.attr('style','top:'+Math.floor($cell.position().top)+'px !important;left:'+Math.floor($cell.position().left)+'px !important;width:'+$cell.outerWidth()+'px !important;height:'+$cell.outerHeight()+'px !important');
								$in = $yo_input.attr('id').replace($cell.closest('form').attr('id')+'-','');
								$ed.children('input[type!="hidden"]').remove();
								$ed.append('<textarea max_length="65535" wrap="virtual" name="'+$in+'" id="'+$in+'" class="rec_fld_content" style="width:'+($cell.outerWidth()-3)+'px !important;height:'+($cell.outerHeight()-2)+'px !important;margin:0 !important;padding:0 !important;" onkeypress="'+$yo_input.attr('onkeypress').replace('return enter_submit(this,','return inline_submit(\''+$in+'\',')+'" onchange="inline_submit(\''+$in+'\',event,true)">'+$ov+'</textarea>');
								$('#'+$in).focus(); 
								$('#inl_edr').on(
								{	focusout:	function()
									{	inline_submit($in,false,true);
									}			
								});
							}
							else
							{	$ed.prepend('<div style="height:8px;"><div>');
								$ed.addClass('frm');
								$ed.attr('style','top:'+Math.floor($cell.position().top)+'px !important;left:'+(Math.floor($cell.position().left)-1)+'px !important;width:'+($cell.outerWidth()-3)+'px !important;height:'+($cell.outerHeight()-2)+'px !important');
								$yo_select = $ed.children('select');
								if	(	$yo_select.length
									)
								{	$in = [];
									$yo_select.each(function()
									{	$new_name = $(this).attr('id').replace($cell.closest('form').attr('id')+'-','');
										$in.push($new_name);
										$(this).attr(
										{	title:		''
										,	id:			$new_name
										,	name:		$new_name
										});
										if	(	$cell.attr('data-nullable') == 0
											)
										{	$(this).children('.null_option').remove();										
										}
										else
										{	$(this).children('.null_option').text($(this).children('.null_option').text().replace('All ','No '));
										}
										$(this).removeClass('in_use');
										$opts = $(this).children('option');
										$opts.removeAttr('selected');
										$opts.each(function()
										{	if	(	$ov.length
												)
											{	if	(	$(this).text()	==	$ov
													)
												{	$(this).attr('selected','selected');
													return true;
												}
											} 
											else
											{	if	(	$(this).hasClass('null_option')
													)
												{	$(this).attr('selected','selected');
													return true;													
												}												
											}
										});
									});
									$('#'+$in[0]).focus(); 
									$in = $in.join(',');
									$yo_select.each(function() {
										$(this).attr(
										{	onchange:	'inline_submit(\''+$in+'\',event,true)'
										});
									});
									$('#inl_edr').on(
									{	focusout:	function()
										{	inline_submit($in,false,true);
										}			
									});
								}
							}
						}
						$ed.show();
					}
				}
			}
		}	
	});	
	
	$('.mass_edit_selector').on(
	{	click:	function()
		{	$(this).children('input').val($(this).closest('.cel_row').attr('data-id'));
			if	(	$(this).children('input[checked]').length
				)	
			{	$(this).children('input').removeAttr('checked')
			}
			else
			{	$(this).children('input').attr('checked','checked')
			}			
		}
	});


});

