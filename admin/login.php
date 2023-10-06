<?php

//$GLOBALS['page']->scripts['src'][] = 'enter_submit';
$GLOBALS['page']->scripts['ready'][] = 'if($("#redirect").val()==""){$("#redirect").val(window.location.href);}';

$GLOBALS['page']->body .= '<div style="width:100%;height:100%;"><div class="center_float_container">
	<div class="center_float">';
			
if	(	$GLOBALS['user']->did
	):
	$did_class = 'did';
else:
	if	(	$GLOBALS['user']->dud
		):
		$did_class = 'dud';
		$GLOBALS['user']->did = $GLOBALS['user']->dud;
	endif;
endif;
if	(	!empty
		(	$did_class
		)
	):
	$GLOBALS['page']->body .= '<div id="login_error" class="'
	.	$did_class
	.	'"><span>'
	.	htmlentities($GLOBALS['user']->did, ENT_QUOTES)
	.	'</span></div>'
	;
endif;

$GLOBALS['page']->body .= '
		<div id="login_box_outer"><div id="login_box_inner"><form name="user" method="post" action="'
.	$cfg['admin_prot']
.	'//'
.	$_SERVER['HTTP_HOST']
.       htmlentities(urldecode($_SERVER['REQUEST_URI']), ENT_QUOTES)
//.	$_SERVER['SCRIPT_URI']
.	'">
<input type="hidden" id="redirect" name="redirect" value="';
if	(	!empty
		(	$GLOBALS['page']->request['redirect']
		)
	):
	$GLOBALS['page']->body .= htmlentities($GLOBALS['page']->request['redirect'], ENT_QUOTES);
endif;
$GLOBALS['page']->body .= '"/>
			<div id="login_logo"></div>
			<div id="login_title">Client Login</div>
			<div id="login_title_underline"></div>
			<div id="username_input" class="login_input">Username:<br/><input id="username" name="username" type="text" onkeypress="return enter_submit(this,event)" /></div>
			<div id="password_input" class="login_input">Password:<br/><input name="password" type="password" id="password" maxlength="64" onkeypress="return enter_submit(this,event)" /></div>
			<div id="login_button" class="record_button"><a href="#" onclick="document.user.forgotten.value=0;document.user.submit();return false">Log In</a></div>
			<div id="forgot_password_link"><input type="hidden" name="forgotten" value="" /><a href="#" onclick="if(document.user.username.value.length){document.user.forgotten.value=1;document.user.submit()}else{alert(\'Please enter your username, and click this link again to have your password emailed to you.\')};return false" class="lil">Forgot Password?</a></div>';
			
//			<div id="reset_password"></div>';

/*
			
			<!-- CONTACT EMAIL -->
				<div style="margin-bottom:6px;"><a href="mailto:support@leagueoflegends.com"><img src="img/login/contact.gif" width="438" height="15" border="0" /></a></div>
			
			<!-- NEW USER CODE / PASSWORD REQUEST -->
				<div style="margin-bottom:6px;"><a href="password_request.php" class="lil">Don\'t have admin login credentials? If this is your first visit, Click here to request a password.</a></div>
			
			<!-- FORGOTTEN PASSWORD REQUEST -->
				<div style="margin-bottom:6px;"><input type="hidden" name="forgotten" value="" /><a href="#" onclick="if(document.user.username.value.length){document.user.forgotten.value=1;document.user.submit()}else{alert(\'Please enter your username, and click this link again to have your password emailed to you.\')};return false" class="lil">Click here if you already have a password and can\'t remember it.</a></div>
			
			<!-- COOKIE TEST LINK -->
				<div style="margin-bottom:6px;"><a href="#" onclick="javascript:window.open(\'cookie_test.php\',\'cookie_test\',\'width=350,height=200,left=100,top=100,screenX=100,screenY=100\');return false;" CLASS="lil">Trouble logging in? Click here to test your browser\'s cookie settings.</a></div>
				
*/

$GLOBALS['page']->body .= '</form></div></div></div></div></div>';
