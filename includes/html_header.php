<?php
require_once
(	'includes/initialize_site.php'
);

$ht .= '<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=IE7"/>
        <title>'.$loc['site_title'].'</title>
		<meta name="author" content="Addison Interactive"/>
		<meta name="description" content="'.$loc['site_description'].'">
		<meta name="keywords" content="'.$loc['site_keywords'].'"/>
		
		 <!-- facebook open graph information -->
		<meta property="og:title" content="'.$loc['site_title'].'"/>
		<meta property="og:type" content="website"/>
		<meta property="og:image" content="'.$cfg['home_url'].$_COOKIE[$cfg['cookie']['co']['name']].'/images/share_img_justice.jpg"/>
		<meta property="og:url" content="'.$cfg['home_url'].'">
		<meta property="og:site_name" content="'.$loc['site_title'].'"/>
		<meta property="fb:admins" content="100003041354030"/>
		<meta property="fb:app_id" content="'.$cfg['fb']['appId'].'" />
		<meta property="og:description" content="'.$loc['site_description'].'"/>
	
		<link rel="shortcut icon" href="'.$cfg['home_url'].'favicon.ico"/>
		<link rel="icon" type="image/gif" href="'.$cfg['home_url'].'favicon.gif" >
        <link rel="stylesheet" href="'.$cfg['home_url'].'styles/custom.css"/>
        <link rel="stylesheet" href="'.$cfg['home_url'].'styles/normalize_header.css"/>
        <link rel="stylesheet" href="'.$cfg['home_url'].'styles/main.css?f='.$cfg['stamp'].'"/>
        <link rel="stylesheet" href="'.$cfg['home_url'].'styles/jquery-ui-1.10.2.custom.css?f='.$cfg['stamp'].'"/>';
$loc['main_css'] = $_COOKIE[$cfg['cookie']['co']['name']]
.	'/main_'
.	$_COOKIE[$cfg['cookie']['co']['name']]
.	'.css'
;
if	(	file_exists
		(	$loc['main_css']
		)
	&&	filesize
		(	$loc['main_css']
		)
	):
	$ht .= '<link rel="stylesheet" href="'.$cfg['home_url']
	.	$loc['main_css']
	.	'?f='.$cfg['stamp'].'"/>'
	;
endif;//adds fonts.com project fonts
$ht .= '<link rel="stylesheet" href="'.$cfg['home_url'].'styles/normalize_footer.css"/>
		
        <script src="'.$cfg['home_url'].'scripts/vendor/modernizr-2.6.2.min.js"></script>

    </head>
    <body class="'
.	$bodyClass
;
foreach
	(	$client	as	$ua	=>	$wh
	):
	if	(	$wh
		&&	$ua	!=	'string'
		):
		$ht .= ' ';
		$ht .= 
		(	$ua	==	'msie'
		)
		?	'ie'
			.	$wh
		:	$ua
		;
	endif;	
endforeach;
$ht .= '">';
if	(	$cfg['cf']
	):
	$ht .= '<!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->';
endif;
$ht .= '<div id="wrapper">
    	<!-- /////////Header Content///////// -->

<div id="header">
  <div id="btn-grp">
    <div class="btn form_closer" id="goto_gallery" onclick="showGall()"> <span>View all Submissions</span> </div>
    <!-- end btn -->
    
    <div class="btn" id="goto_form_ov" onclick="showOV1()"> <span>Submit your story</span> </div>
    <!-- end btn --> 
    
  </div>
  <!-- end btn-grp -->
  
  <div id="icon-box">
    <div id="social">
      <ul>
        <li id="fb" onclick="share_site_fb()"></li>
        <li id="tw" onclick="share_site_tw()"></li>
        <li id="t" onclick="share_site_tum()"></li>
        <li id="pin" onclick="share_site_pin()"></li>
      </ul>
    </div>
    <!-- social -->
    
    <div id="box-item"><a href="javascript:void();">
      <div id="box">
        <div id="box-img"> <img src="images/icon-dvd.png" alt="icon-dvd" width="111" height="139" border="0"> </div>
        <!-- box-img -->
        
        <div id="dvd-info">
          <h2>Own It On<br>
            Blu-Ray & DVD<br/>
            <span>October 15th</span></h2>
        </div>
        <!-- dvd-info --> 
        
      </div>
      <!-- box --> 
     </a> 
    </div>
    <!-- box-item --> 
    
  </div>
  <!-- end icon-box --> 
  
</div>
<!-- end header --> 

';



