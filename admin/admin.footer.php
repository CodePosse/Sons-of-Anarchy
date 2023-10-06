<?php

/*
if	(	$GLOBALS['page']->request['z']	!=	'login'
	):
	$GLOBALS['page']->body .= '<div id="spacey"></div></div><footer>
<div id="footer_bar">
	<div id="footer_bar_inner">
		<div id="footer_bar_copyright">&copy;'
	.	date('Y')
	.	' '
	.	$GLOBALS['cfg']['company_title']
	.	'</div>
		<div id="footer_bar_buttons">'
	;
	if	(	in_array
			(	$GLOBALS['page']->request['z']
			,	array
				(	'project'
				,	'file_manager'
				,	'comp_viewer'
				)
			)
		&&	!empty
			(	$GLOBALS['page']->request['id']
			)
		):
		$GLOBALS['page']->body .= '<div class="record_button">';
		if	(	$GLOBALS['page']->request['z'] == 'file_manager'
			):
			$GLOBALS['page']->body .= '<a href=".?z=comp_viewer&id='
			.	$GLOBALS['page']->request['id']
			.	'" title="View files belonging to this project.">Quick View</a>'
			;
		else:
			if	(	$GLOBALS['dbi']->tables['project']->record->values['allow_downloads'] == 'Yes'
				):
				$GLOBALS['page']->body .= '<a href=".?z=file_manager&id='
				.	$GLOBALS['page']->request['id']
				.	'" title="Browse, download and upload files relating to this project.">Download Files</a>'
				;
			endif;
		endif;
		$GLOBALS['page']->body .= '</div>';
		if	(	!empty
				(	$GLOBALS['dbi']->tables['project']->record->owners[$GLOBALS['dbi']->tables['project']->owners['contact']]
				)
			):
			$project_contacts = '';
			foreach
				(	$GLOBALS['dbi']->tables['project']->record->owners[$GLOBALS['dbi']->tables['project']->owners['contact']]	as	$project_contact
				):
				if	(	!empty
						(	$project_contacts
						)
					):
					$project_contacts .= ', ';
				endif;
				$project_contacts .= $project_contact['email'];
			endforeach;
			reset
			(	$GLOBALS['dbi']->tables['project']->record->owners[$GLOBALS['dbi']->tables['project']->owners['contact']]
			);
			$GLOBALS['page']->body .= '<div class="record_button"><a href="mailto:'
			.	$project_contacts
			.	'" title="Send an email to the project contact'
			;	
			if	(	count
					(	$GLOBALS['dbi']->tables['project']->record->owners[$GLOBALS['dbi']->tables['project']->owners['contact']]
					)	>	1
				):
				$GLOBALS['page']->body .= 's';
			endif;
			$GLOBALS['page']->body .= ': '
			.	$project_contacts
			.	'">Contact</a></div>'
			;
		endif;
	endif;		
	$GLOBALS['page']->body .= '</div>
	</div>
</div></footer>';
	// http://jdsharp.us/jQuery/minute/calculate-scrollbar-width.php
    // Append our div, do our calculation and then remove it
	$GLOBALS['page']->scripts['functions'][] = '
function scrollBarWidth() {
    var div = $(\'<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>\');
    $("body").append(div);
    var w1 = $("div", div).innerWidth();
    div.css("overflow-y","scroll");
    var w2 = $("div", div).innerWidth();
    $(div).remove();
    return (w1 - w2);
}
function hasHorzScrollBar() {
	$wwidth = window.innerWidth ? window.innerWidth : $(window).width();
	$wheight = window.innerHeight ? window.innerHeight : $(window).height();
	$wwidthWithoutScrollBar = ($(document).height() > $wheight)?$wwidth - scrollBarWidth():$wwidth;
	return ($(document).width() > $wwidthWithoutScrollBar)?true:false;
}
var $footer_bar = $("#footer_bar");
$(window).resize(function(){
	$wheight = window.innerHeight ? window.innerHeight + $(window).scrollTop() : $(window).height();
	if(hasHorzScrollBar())$wheight -= scrollBarWidth();
	$footer_bar.animate({top:($wheight-$footer_bar.height()-2)+"px"},0);
});
$(window).scroll(function(){
	$wheight = window.innerHeight ? window.innerHeight : $(window).height(); 
	if(hasHorzScrollBar())$wheight -= scrollBarWidth();
	$footer_bar.animate({top:($(window).scrollTop()+$wheight-$footer_bar.height()-2)+"px"},0);
});
';
	if	(	!$GLOBALS['user']->browser['mobile']
		&&	!$GLOBALS['user']->is_admin()
		):
		$GLOBALS['page']->scripts['ready'][] = '
$("body *[title]").tooltip({
	// tweak the position
   offset: [10, 2],

   // use the "slide" effect
   effect: "slide"
});	
';
	endif;
endif;
*/
