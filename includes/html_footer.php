<?php
$ht .= '

		    	
		    	
<!-- /////////Footer Content///////// -->

<footer>
  <div id="footer-content">
    <div id="footer-elemets">
      <div id="footer-icon"> <img src="images/icon-fox-logo.jpg" alt="icon-fox-logo" width="36" height="25"> </div>
      <!-- end footer icon -->
      
      <div id="footer-info">
        <p><sup>TM</sup> and &copy; Twentieth Century Fox Home Entertainment LLC. and Its related entitles. All Rights Reserved. Use of this website constitutes your acceptance of these Terms &<br/>
          Conditions and Pricacy Policy. The materials on this website are not to be sold, traded, or given away. Any copying, manipulation, publishing, or other transfer of these<br/>
          materials, except as specially provided in the Terms and Conditions of Use, is strictly prohibited.</p>
      </div>
      <!-- end footer info --> 
      
    </div>
    <!-- end footer-elements --> 
    
  </div>
  <!-- end footer-content --> 
  
</footer>
<!-- end footer -->

</div>
<div id="errorlay"></div>
<div id="wait"><img id="waiter" class="spinner" src="'.$cfg['home_url'].'images/spinner.png"/></div>

';

if	(	$cfg['ga']
	):
	//adding additional tracking 
	$extagaids = '';
	$k = '';
	if	(	count
			(	$cfg['ga']
			)	>=	2
		):
		foreach
			(	array_slice
				(	$cfg['ga']
				,	1
				)	as	$tr	=>	$ga
			):
			$extagaids .= ',[\''
			.	$tr
			.	'._setAccount\',\''
			.	$ga
			.	'\']'
			;
		endforeach;
	else:
		$extagaids = '';
	endif;
	
	$pagetrack = ',[\'_trackPageview\']';
	foreach
		(	array_slice
			(	$cfg['ga']
			,	1
			)	as	$tr	=>	$ga
		):
		$pagetrack .= ',[\''
		.	$tr
		.	'._trackPageview\']
';
	endforeach;			
	
	$ht .= '<script>function track_event($cat,$act,$label,$val)
	{	try
		{	if	(	_gaq
				)
			{	_gaq.push(["_trackEvent",$cat,$act,$label,$val]);
			';
	foreach
		(	array_slice
			(	$cfg['ga']
			,	1
			)	as	$tr	=>	$ga
		):
		$ht .= '_gaq.push(["'
		.	$tr
		.	'._trackEvent",$cat,$act,$label,$val]);'
		;
	endforeach;
			
	$ht .= '
			
			}
		}
		catch(err)
		{	return err;
		}
		return true;
	}
	
	function track_pageview($page)
	{	try
		{	if	(	_gaq
				)
			{	_gaq.push(["_trackPageview",$page]);
					';
	foreach
		(	array_slice
			(	$cfg['ga']
			,	1
			)	as	$tr	=>	$ga
		):
		$ht .= '_gaq.push(["'
		.	$tr
		.	'._trackPageview",$page]);'
		;
	endforeach;
			
	$ht .= '
			}
		}
		catch(err)
		{	return err;
		}
		return true;
	}
	
	function track_out($where) 
	{	try
		{	if	(	_gaq
				)
			{	_gaq.push(["_link", \'Exit\', \'$where\']);
					';
	foreach
		(	array_slice
			(	$cfg['ga']
			,	1
			)	as	$tr	=>	$ga
		):
		$ht .= '_gaq.push(["'
		.	$tr
		.	'._link", \'Exit\', \'$where\']);'
		;
	endforeach;
			
	$ht .= '
			}
		}
		catch(err)
		{	return err;
		}
		return true;
	}';
else:
	$ht .= '<script>
function track_event($cat,$act,$label,$val){return false};
function track_pageview($page){return false};
function track_out($where){return false};
';
endif;

$ht .= '	
function out_link(link, category, action)
{	
	track_event(category,action);
	setTimeout(function(){window.open(link,"_blank")}, 100);
	
}
var _gaq='
.	(	(	empty
			(	$cfg['ga']
			)
		)
		?	'false'
		:	'[[\'_setAccount\',\''
			.	array_shift
				(	$cfg['ga']
				)
			.	'\']'
			.	$extagaids
			.	$pagetrack
			.	'];
(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
g.src=(\'https:\'==location.protocol?\'//ssl\':\'//www\')+\'.google-analytics.com/ga.js\';
s.parentNode.insertBefore(g,s)}(document,\'script\'))'
    )
.	';</script>'
;

// INCLUDE JQUERY AND ALL SCRIPTS FROM $cfg['scripts']['src'] UP TO & INCLUDING plugins.js
foreach
	(	$cfg['scripts']['src']	as	$src	=>	$script
	):
	$ht .=
	(	is_numeric
		(	$src
		)
	)
	?	scriptSrc
		(	$script
		)
	:	$script
	;
	unset
	(	$cfg['scripts']['src'][$src]
	);
	if	(	$script	==	'plugins.js'
		):
		break;
	endif;
endforeach;
$ht .= '<script>
var $home_url = "'.$cfg['home_url'].'";
var $locookie = "'.$cfg['cookie']['co']['name'].'";
var $loc = "'.$_COOKIE[$cfg['cookie']['co']['name']].'";
var $loc_lang = "'.$loc['loc_lang'].'";
var $interface = "'.$cfg['interface'].'";
var $title = "'.$loc['site_title'].'";
var $share_image = "'.$cfg['home_url'].$_COOKIE[$cfg['cookie']['co']['name']].'/images/share_img.jpg";
var $ui = "'.$cfg['cookie']['ui']['name'].'";
var $waittime = "'.$cfg['wait_time'].'";

$(function(){
$.tools.validator.localize($loc_lang,{';
foreach
	(	$loc['form_errors']	as	$error_id	=>	$error_message
	):
	$ht .= '"'
	.	$error_id
	.	'":"'
	.	$error_message
	.	'",'
	;
endforeach;
$ht = substr
(	$ht
,	0
,	-1
)
.	'});
';
$ht .= '
});
var $js_errors = new Object();
';
foreach
	(	$loc['form_error_strings']	as	$error_id	=>	$error_message
	):
	$ht .= '$js_errors.'
	.	$error_id
	.	' = "'
	.	$error_message
	.	'";
'
	;
endforeach;
$ht .= '
var $db_errors = new Object();
';
foreach
	(	$loc['db_errors']	as	$error_id	=>	$error_message
	):
	$ht .= '$db_errors.'
	.	$error_id
	.	' = "'
	.	$error_message
	.	'";
'
	;
endforeach;
$ht .= '

</script>'
;


// INCLUDE global.js AND ALL INLINE JAVASCRIPT
$ht .= '
<script src="'.$cfg['home_url'].'scripts/global.js?x='.$cfg['stamp'].'"></script>
<script>
'
.	$js
.	'
</script>';
// INCLUDE ALL REMAINING SCRIPTS IN $cfg['scripts']
foreach
	(	$cfg['scripts']['src']	as	$src	=>	$script
	):
	$ht .=
	(	is_numeric
		(	$src
		)
	)
	?	scriptSrc
		(	$script
		)
	:	$script
	;
	unset
	(	$cfg['scripts']['src'][$src]
	);
endforeach;
$ht .= '
	<script>
//login
function fb_enter() {
 FB.login(function(response) {
   if (response.authResponse) {
     console.log(\'Welcome!  Fetching your information.... \');
     FB.api(\'/me\', function(response) {
       console.log(\'Good to see you, \' + response.name + \'.\');
       $(\'.login\').hide();
       $(\'.logout\').show();
     });
   } else {
     console.log(\'User cancelled login or did not fully authorize.\');
   }
 });
}

//logout
function fb_exit() {
	FB.logout(function(response) {
	  // user is now logged out
	  console.log(\'logged out\');
	   $(\'.login\').show();
       $(\'.logout\').hide();
	});
}

//cosmetic
function login_toggle(state) {
	switch (state) { 
	case \'login\':
		$(\'.login\').show();
		$(\'.logout\').hide();
	break;
	case \'logout\':
		$(\'.logout\').show();
		$(\'.login\').hide();
	break;
	case \'auth\':
		$(\'.login\').show();
		$(\'.logout\').hide();	
	break;
	}
}


	</script>
		<script src="scripts/vendor/modernizr.custom.js"></script>
		<script src="scripts/masonry.pkgd.min.js"></script>
		<script src="scripts/imagesloaded.js"></script>
		<script src="scripts/classie.js"></script>
		<script src="scripts/AnimOnScroll.js"></script>
		<script>
			new AnimOnScroll( document.getElementById( "grid" ), {
				minDuration : 0.4,
				maxDuration : 0.7,
				viewportFactor : 0.2
			} );

		</script>
 	
';
$ht .= '</body></html>';
echo $ht;
unset
(	$ht
);
$dbi->kill
(	$dbi->thread_id
);
$dbi->close();

exit;
