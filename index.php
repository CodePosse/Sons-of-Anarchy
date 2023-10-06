<?php

function parse_json($url) {
	$data = json_decode(file_get_contents($url), true);
	
	$output = '';
	foreach($data['response']['stories'] as $story) {
		$output .= '<li class="gallery_chrome"><a href="?type=video&id=' . htmlentities($story['i']) . '" ><img src="' . htmlentities($story['th']) . '" title="" onclick="deeplink_call(' . htmlentities($story['i']) . ');"><span class="caption fade-caption"><h3>' . htmlentities($story['t']) . '</h3>
		</span></a></li>.';
	}
	
	return $output;
}

$deeplink_class = 'not-deeplink';
if(isset($_GET['type']) && $_GET['type'] == 'video' && isset($_GET['id']) && $_GET['id']) {
  $deeplink_class = 'deeplink';
}
require_once
(	'includes/html_header.php'
);

/* index page html goes here */
$ht .= '


<div style="background-color:red;display:none">Follows flow: 
<span onclick="showOV1()">showOV1</span> &gt; 
<span onclick="showOV2()">showOV2</span> &gt; 
<span onclick="editVideo()">editVideo</span> &gt; or &lt; 
<span onclick="showOV4()">showOV4</span><br />
To be attached to close buttons: <span onclick="closeOV1()">closeOV1</span> | 
<span onclick="closeOV2()">closeOV2</span> |  
<span onclick="closeOV4()">closeOV4</span>
</div> 
  <!-- /////////Page Content///////// -->
<div id="page">
  <div id="intro" class="' . $deeplink_class . '">
    
    <!-- end icon-x -->
    
    <div id="intro-info"><div class="close_grandma"> </div>
      <p>Everyone&#39;s got one of THOSE stories. That story<br/>
        you don&#39;t tell your mother. The &quot;clubhouse&quot; story.<br/>
        <span>Upload your <i>Sons Of Anarchy</i> - inspired<br/>
        clubhouse story.</span> We won&#39;t tell your mother</p>
    </div>
    <!-- end intro info -->
    <div class="btn" id="goto_form" onclick="showOV1()"> <span>Submit your story</span> </div>
  </div>
  <!-- end intro -->
  

  <div id="form_wrapper">
    <div id="form_ov_1">
      <div id="icon-x" class="icon-x2" onClick="closeOV1()"> </div>
      <span class="title">'.$loc['submit_ychs'].'</span>
      <form id="entry_form">
        <div class="column" id="column1">
          <input id="form_title" name="title"  value="'.$loc['form_title'].'" data-val="'.$loc['form_title'].'" required="required" maxlength="30" class="form_field">
          <input id="form_name"  name="name"  value="'.$loc['form_name'].'" data-val="'.$loc['form_name'].'" required="required" maxlength="16" class="form_field">
          <input id="form_youtube_link" " value="'.$loc['form_youtube'].'" data-val="'.$loc['form_youtube'].'" class="form_field">
          <span id="or">'.$loc['or'].'</span>
          <div class="btn" id="upload_btn"> <span>'.$loc['upload'].'</span> </div>
          <!-- end btn --> 
        </div>
        <div class="column" id="column2">
          <textarea id="form_story" required="required" data-val="'.$loc['form_story'].'" class="form_field">'.$loc['form_story'].'</textarea>
        </div>
        <div class="btn" id="submit_btn" type="submit"> <span>'.$loc['submit'].'</span> </div>
        <!-- end btn -->
      </form>
    </div>
    <!-- end form_ov_1 --> 
    <!--form_ov_2 is for the confirmation of the submission-->
    <div id="form_ov_2">
      <div id="icon-x" class="icon-x3" onClick="closeOV2()"></div>
      <span class="title"></span>
      <div class="column" id="column3">
        <div id="confirm_image"></div>
        <div class="d1">&nbsp; '.$loc['info_correct'].'</div>
        <div>
          <div id="gobackto_form" class="btn" onclick="editVideo()">'.$loc['edit'].'</div>
          <div id="goto_success" class="btn">'.$loc['submit'].'</div>
        </div>
      </div>
      <div class="column" id="column4">
        <div class="story"></div>
		<div class="viewUser user_name"></div>
      </div>
    </div>
    <!--form_ov_3 is undefined-->    
    <div id="form_ov_3">
    </div>
    <!--form_ov_4 is the dialogue to check back to see the video-->
    <div id="form_ov_4">
      <div id="icon-x" class="icon-x5" onClick="closeOV4()"></div>
      <span class="title">YOUR SUBMISSION HAS BEEN RECEIVED</span>
      <div class="d1">Check back to see your Clubhouse Story in the gallery!</div>
    </div>
    <!--form_ov_5 is the view story box-->
    <div id="form_ov_5">
        <div id="wrap">
          <div class="viewTitle title">TITLE</div>
          <div class="close_grandma" onclick="showGall();">&nbsp;</div>
          <div class="boxxy">
            <div class="viewArea vid_code">pix here</div>
            <div class="descWrap description">DESC</div>
            <div class="viewUser user_name">USER NAME</div>
            <div class="viewSocial">
              <ul>
                <li id="fb" onclick="share_site_fb()"></li>
                <li id="tw" onclick="share_site_tw()"></li>
                <li id="t" onclick="share_site_tum()"></li>
                <li id="pin" onclick="share_site_pin()"></li>
              </ul>
            </div><!-- /.viewSocial -->
          </div><!-- /.boxxy -->
        </div><!-- /#wrap -->
    </div><!-- /#form_ov_5 -->
    </div>
  </div>
</div>
<!-- end form_wrapper -->
  <div id="gallery_wrapper">
    <div id="gallery">
      <ul class="gridx effect-2" id="grid">
        ' . parse_json($cfg['home_url'] . 'interface.php?z=stories_get') . '
      </ul>
    </div>
    <!-- end gallery --> 
  </div>
</div>
<!-- end content -->

</div>
		






<script>



</script>

    	<!-- end page -->
';



require_once
(	'includes/html_footer.php'
);
