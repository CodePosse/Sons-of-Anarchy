<?php

if	(	$GLOBALS['page']->request['z']	!=	'login'
	):
	
	$GLOBALS['page']->body .= '<header><div id="header"><nav>
	<div id="top_link_home" onclick="top.location=\''
	.	$_SERVER['SCRIPT_NAME']
	.	'\'"></div>
	<div class="top_link" onclick="top.location=\''
	.	$_SERVER['SCRIPT_NAME']
	.	'\'">Story Tellers</div>
	<div class="top_link" onclick="top.location=\''
	.	$_SERVER['SCRIPT_NAME']
	.	'?z=story_photo\'">Photos</div>
	<div class="top_link" onclick="top.location=\''
	.	$_SERVER['SCRIPT_NAME']
	.	'?z=story_video\'">Videos</div>'
	;
			
	$GLOBALS['page']->body .= '<div id="top_link_logout" onclick="top.location=\''
	.	$_SERVER['SCRIPT_NAME']
	.	'?z=out\'">log out</div></nav></div></header>'
	;

	$GLOBALS['page']->body .= '<div id="page_content">';
	
endif;

