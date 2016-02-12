<?php

namespace WPDM;

global $gp1c, $tbc;


class PackageLocks
{

    public function __construct(){
        global $post;
        //if(has_shortcode($post->post_content, "[wpdm_package]"))
        add_action('wp_enqueue_scripts', array($this, 'Enqueue'));
    }

    function Enqueue(){
       // wp_enqueue_script('wpdm-fb', 'http://connect.facebook.net/en_US/all.js?ver=3.1.3#xfbml=1');
    }

    public static function LinkedInShare($package)
    {

        $tmpid = uniqid();
        $var = md5('li_visitor.' . $_SERVER['REMOTE_ADDR'] . '.' . $tmpid . '.' . md5(get_permalink($package['ID'])));
        $req = home_url('/?pid=' . $package['ID'] . '&var=' . $var);
        $home = home_url('/');
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        $href = $package['linkedin_url'];
        $href = $href ? $href : get_permalink($package['ID']);
        $msg = isset($package['linkedin_message']) && $package['linkedin_message'] !=''? $package['linkedin_message']:$package['title'];
        $msg .= " ".$href;
        ob_start();
        ?>
        <div class="wpdm-social-lock-box  wpdmslb-linkedinshare" id="wpdmslb-linkedinshare-<?php echo $package['ID']; ?>">
            <div class="placehold wpdmlinkedin"><i class="fa fa-linkedin"></i></div>
        <script type="in/Login"></script>
        <div id="lin_<?php echo $tmpid; ?>"></div>
        <div id="wpdm_dlbtn_<?php echo $tmpid; ?>" style="margin-top: -21px !important;"></div>
        <script type="text/javascript" src="//platform.linkedin.com/in.js">
            <?php echo "api_key: ".get_option('_wpdm_linkedin_client_id')."\r\n"."authorize: true"."\r\n"."onLoad: onLinkedInLoad_".$tmpid."\r\n"; ?>
        </script>

        <script type="text/javascript">

            // Setup an event listener to make an API call once auth is complete
            function onLinkedInLoad_<?php echo $tmpid; ?>() {
                IN.Event.on(IN, "auth", checkLogin_<?php echo $tmpid; ?>);
            }

            // Handle the successful return from the API call
            function onSuccess_<?php echo $tmpid; ?>(data) {
                console.log(data);
                var ctz = new Date().getMilliseconds();
                jQuery.post("<?php echo $home; ?>?nocache="+ctz,{id:<?php echo $package['ID']; ?>,dataType:'json',execute:'wpdm_getlink',force:'<?php echo $force; ?>',social:'l',action:'wpdm_ajax_call'},function(res){
                    if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                        jQuery('#wpdmslb-linkedinshare-<?php echo $package['ID']; ?>').addClass('wpdm-social-lock-unlocked').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-inverse btn-block">Download</a>');
                        window.open(res.downloadurl);
                    } else {
                        jQuery("#lin_<?php echo $tmpid; ?>").html(""+res.error);
                    }
                }, "json");
            }

            // Handle an error response from the API call
            function onError_<?php echo $tmpid; ?>(error) {
                console.log(error);
            }

            // Use the API call wrapper to share content on LinkedIn
            function shareContent_<?php echo $tmpid; ?>() {

                // Build the JSON payload containing the content to be shared
                var payload_<?php echo $tmpid; ?> = {
                    "comment": "<?php echo $msg ;?>",
                    "visibility": {
                        "code": "anyone"
                    }
                };

                IN.API.Raw("/people/~/shares?format=json")
                    .method("POST")
                    .body(JSON.stringify(payload_<?php echo $tmpid; ?>))
                    .result(onSuccess_<?php echo $tmpid; ?>)
                    .error(onError_<?php echo $tmpid; ?>);

                return false;
            }

            function checkLogin_<?php echo $tmpid; ?>(){
                IN.API.Raw("/people/~").result(showShareButton_<?php echo $tmpid; ?>).error(onError_<?php echo $tmpid; ?>);
            }

            function showShareButton_<?php echo $tmpid; ?>(data){
                console.log(data);
                jQuery('#wpdm_dlbtn_<?php echo $tmpid; ?>').html('<a class="btn btn-linkedin btn-xs" href="#" onclick="return shareContent_<?php echo $tmpid; ?>();">Share in LinkedIn</a>');
            }

            jQuery(function(){
                jQuery(".IN-widget span[id='*-title-text']").html("");
            });

        </script>
        </div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    public static function GooglePlusShare($package){

        $tmpid = "gps_".uniqid();
        $var = md5('li_visitor.' . $_SERVER['REMOTE_ADDR'] . '.' . $tmpid . '.' . md5(get_permalink($package['ID'])));
        $req = home_url('/?pid=' . $package['ID'] . '&var=' . $var);
        $home = home_url('/');
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        $href = $package['google_plus_share'];
        $href = $href ? $href : get_permalink($package['ID']);
        $msg = ""; //isset($package['linkedin_message']) && $package['linkedin_message'] !=''? $package['linkedin_message']:$package['post_title'];
        $msg .= " ".$href;
        ob_start();

        ?>

        <!-- Place this tag in your head or just before your close body tag. -->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <div id="lin_<?php echo $tmpid; ?>"></div>
        <div id="wpdm_dlbtn_<?php echo $tmpid; ?>"></div>
        <!-- Place this tag where you want the share button to render. -->
        <div class="g-plus" data-href="<?php echo $href; ?>" data-action="share" data-onendinteraction="download_file_<?php echo $tmpid; ?>"></div>

        <script>
            function download_file_<?php echo $tmpid; ?>(data) {
                if(data.type != 'confirm') return;
                console.log(data);
                var ctz = new Date().getMilliseconds();
                jQuery.post("<?php echo $home; ?>?nocache="+ctz,{id:<?php echo $package['ID']; ?>,dataType:'json',execute:'wpdm_getlink',force:'<?php echo $force; ?>',social:'l',action:'wpdm_ajax_call'},function(res){
                    if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                        jQuery('#wpdmslb-googleshare-<?php echo $package['ID']; ?>').addClass('wpdm-social-lock-unlocked').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-inverse btn-block">Download</a>');
                        window.open(res.downloadurl);
                    } else {
                        jQuery("#lin_<?php echo $tmpid; ?>").html(""+res.error);
                    }
                }, "json");
            }
        </script>

        <?php
        $data = ob_get_clean();
        return $data;
    }

    public static function GooglePlusOne($package, $buttononly = false)
    {
        global $gp1c;

        $gp1c++;
        $var = md5('visitor.' . $_SERVER['REMOTE_ADDR'] . '.' . $gp1c . '.' . md5(get_permalink($package['ID'])));

        $href = $package['google_plus_1'];

        $href = $href ? $href : get_permalink($package['ID']);
        $dlabel =  __('Download', 'wpdmpro');

        //update_post_meta(get_the_ID(),$var,$package['download_url']);
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        if (isset($_COOKIE[$var]) && $_COOKIE[$var] == 1)
            return $package['download_url'];
        else
            $data = '<div class="g-plusone" data-size="medium" data-href="' . $href . '" data-callback="wpdm_plus1st_unlock_' . $gp1c . '"></div>';
        $req = home_url('/?pid=' . $package['ID'] . '&var=' . $var);
        $home = home_url('/');
        $btitle = isset($package['gplus_heading']) ? $package['gplus_heading'] : __('Google +1 to download', 'wpdmpro');
        $intro = isset($package['gplus_intro']) ? "<p>" . $package['gplus_intro'] . "<p>" : '';
        $html = <<<DATA

               <div class="panel panel-default">
            <div class="panel-heading">
    {$btitle}
  </div>
  <div class="panel-body">
                <div id="plus_$gp1c" style="max-width:100%;overflow:hidden">
                {$intro}<br/>
                $data
                </div>

               <!-- Place this tag where you want the +1 button to render. -->
<div class="g-plusone" data-size="small" data-callback="wpdm_plus1st_unlock_$gp1c" data-href="{$href}"></div>


                <script type="text/javascript">
                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/platform.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();

                  function wpdm_plus1st_unlock_$gp1c(plusone){
                  console.log(plusone);
                        if(plusone.state!='on') { jQuery.cookie('unlocked_{$package['ID']}',null); return; }
                        jQuery.cookie('unlocked_{$package['ID']}',1);
                        var ctz = new Date().getMilliseconds();
                        jQuery.post("{$home}?nocache="+ctz,{id:{$package['ID']},dataType:'json',execute:'wpdm_getlink',force:'$force',social:'g',action:'wpdm_ajax_call'},function(res){
                            if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                            location.href=res.downloadurl;
                            jQuery('#pkg_{$package['ID']}').html('<a style="color:#000" href="'+res.downloadurl+'">{$dlabel}</a>');
                            } else {
                                jQuery("#msg_{$package['ID']}").html(""+res.error);
                            }
                    }, "json");


                  }

                </script></div></div>



DATA;

        if($buttononly==true)
            $html = <<<DATA
                <div class="placehold wpdmgoogle"><i class="fa fa-google-plus"></i></div>
                <div id="plus_$gp1c" class="labell pull-right">

                $data



                <script type="text/javascript">
                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();

                  function wpdm_plus1st_unlock_$gp1c(plusone){
                        if(plusone.state!='on') { jQuery.cookie('unlocked_{$package['ID']}',null); return; }
                        jQuery.cookie('unlocked_{$package['ID']}',1);
                        var ctz = new Date().getMilliseconds();
                        jQuery.post("{$home}?nocache="+ctz,{id:{$package['ID']},dataType:'json',execute:'wpdm_getlink',force:'$force',social:'g',action:'wpdm_ajax_call'},function(res){
                            if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                            location.href=res.downloadurl;
                            jQuery('#wpdmslb-googleplus-{$package['ID']}').addClass('wpdm-social-lock-unlocked').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-inverse btn-block">{$dlabel}</a>');

                            } else {
                                jQuery("#msg_{$package['ID']}").html(""+res.error);
                            }
                    }, "json");


                  }

                </script></div>



DATA;

        return $html;
    }

    public static function TwitterFollow($package){

        ob_start();
        $tmpid = "tf_".uniqid();
        $home = home_url('/');
        $twitter_handle = $package['twitter_handle'];
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        ?>
        <div class="wpdm-social-lock-box wpdmslb-twitterfollow" id="wpdmslb-twitterfollow-<?php echo $package['ID']; ?>">
            <div class="placehold wpdmtwitter"><i class="fa fa-twitter"></i></div>
        <div id="lin_<?php echo $tmpid; ?>"></div>
        <div id="wpdm_dlbtn_<?php echo $tmpid; ?>"></div>
        <a href="https://twitter.com/<?php echo $twitter_handle; ?>" class="twitter-follow-button" id="follow-me-<?php echo $package['ID']; ?>" data-pid="<?php echo $package['ID']; ?>" data-show-count="false">Follow @<?php echo $twitter_handle; ?></a>
        <script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
        <script type="text/javascript">
        if(typeof twttr != 'undefined') {
            twttr.events.bind('follow', function (event) {
                console.log(event);
                var followed_user_id = event.data.user_id;
                var followed_screen_name = event.data.screen_name;
                var pid = localStorage.getItem('tfid');
                var ctz = new Date().getMilliseconds();
                jQuery.post("<?php echo $home; ?>?nocache=" + ctz, {
                    id: pid,
                    dataType: 'json',
                    execute: 'wpdm_getlink',
                    force: '<?php echo $force; ?>',
                    social: 'l',
                    action: 'wpdm_ajax_call'
                }, function (res) {
                    if (res.downloadurl != "" && res.downloadurl != undefined) {
                        jQuery('#wpdmslb-twitterfollow-' + pid).addClass('wpdm-social-lock-unlocked').html('<a href="' + res.downloadurl + '" class="wpdm-download-button btn btn-inverse btn-block">Download</a>');
                        window.open(res.downloadurl);
                    } else {
                        jQuery("#lin_<?php echo $tmpid; ?>").html("" + res.error);
                    }
                }, "json");

            });

            twttr.events.bind(
                'click',
                function (ev) {
                    var pid = ev.target.id.replace('follow-me-', '');
                    localStorage.setItem('tfid', pid);

                }
            );
        }

        </script>
        </div>
        <?php

        $data = ob_get_clean();
        return $data;
    }

    public static function AskPassword($package){
        ob_start();
        $unqid = uniqid();
        ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php _e('Enter Correct Password to Download','wpdmpro'); ?>
            </div>
            <div class="panel-body" id="wpdmdlp_<?php echo  $unqid . '_' . $package['ID']; ?>">
                <div id="msg_<?php echo $package['ID']; ?>" style="display:none;"><?php _e('Processing...','wpdmpro'); ?></div>
                <form id="wpdmdlf_<?php echo $unqid . '_' . $package['ID']; ?>" method=post action="<?php echo home_url('/'); ?>" style="margin-bottom:0px;">
                    <input type=hidden name="id" value="<?php echo $package['ID']; ?>" />
                    <input type=hidden name="dataType" value="json" />
                    <input type=hidden name="execute" value="wpdm_getlink" />
                    <input type=hidden name="action" value="wpdm_ajax_call" />
                    <div class="input-group">
                        <input type="password"  class="form-control" placeholder="<?php _e('Enter Password','wpdmpro'); ?>" size="10" id="password_<?php echo $unqid . '_' . $package['ID']; ?>" name="password" />
                        <span class="input-group-btn"><input id="wpdm_submit_<?php echo $unqid . '_' . $package['ID']; ?>" class="wpdm_submit btn btn-info" type="submit" value="<?php _e('Submit', 'wpdmpro'); ?>" /></span>
                    </div>

                </form>

                <script type="text/javascript">
                    jQuery("#wpdmdlf_<?php echo $unqid . '_' . $package['ID']; ?>").submit(function(){
                        var ctz = new Date().getMilliseconds();
                        jQuery("#msg_<?php echo  $package['ID']; ?>").html('<?php _e('Processing...','wpdmpro'); ?>').show();
                        jQuery("#wpdmdlf_<?php echo  $unqid . '_' . $package['ID']; ?>").hide();
                        jQuery(this).removeClass("wpdm_submit").addClass("wpdm_submit_wait");
                        jQuery(this).ajaxSubmit({
                            url: "<?php echo home_url('/?nocache='); ?>" + ctz,
                            success: function(res){

                                jQuery("#wpdmdlf_<?php echo  $unqid . '_' . $package['ID']; ?>").hide();
                                jQuery("#msg_<?php echo  $package['ID']; ?>").html("verifying...").css("cursor","pointer").show().click(function(){ jQuery(this).hide();jQuery("#wpdmdlf_<?php echo  $unqid . '_' . $package['ID']; ?>").show(); });
                                if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                                    location.href=res.downloadurl;
                                    jQuery("#wpdmdlf_<?php echo  $unqid . '_' . $package['ID']; ?>").html("<a style='color:#ffffff !important' class='btn btn-success' href='"+res.downloadurl+"'><?php _e('Download','wpdmpro'); ?></a>");
                                    jQuery("#msg_<?php echo  $package['ID']; ?>").hide();
                                    jQuery("#wpdmdlf_<?php echo  $unqid . '_' . $package['ID']; ?>").show();
                                } else {
                                    jQuery("#msg_<?php echo $package['ID']; ?>").html(""+res.error+"");
                                }
                            }
                        });
                        return false;
                    });
                </script>
            </div>
        </div>

        <?php
        $data = ob_get_clean();
        return $data;
    }

    public static  function AskEmail($package)
    {

        $data = '<div class="alert alert-danger">Email Lock Is Not Enabled for This Download!</div>';
        if (isset($package['email_lock']) && $package['email_lock'] == '1') {

            $lock = 'locked';
            $unqid = uniqid();
            $btitle = isset($package['email_heading']) ? $package['email_heading'] : __('Subscribe to download', 'wpdmpro');
            $blabel = isset($package['button_label']) ? $package['button_label'] : __('Download', 'wpdmpro');
            $dlabel =  __('Download', 'wpdmpro');
            $eeml =  __('Enter Email', 'wpdmpro');
            $intro = isset($package['email_intro']) ? "<p>" . $package['email_intro'] . "</p>" : '';
            $data = '
                <div id="emsg_' . $unqid . $package['ID'] . '" class="emsg_' . $unqid . $package['ID'] . '" style="display:none;">'.__('Processing...','wpdmpro').'</div>
               <form id="wpdmdlf_' . $unqid . '_' . $package['ID'] . '" class="wpdmdlf_' . $unqid . '_' . $package['ID'] . '" method=post action="' . home_url('/') . '" style="font-weight:normal;font-size:12px;padding:0px;margin:0px">
                 <div class="panel panel-default">
            <div class="panel-heading">
    ' . $btitle . '
  </div>
  <div class="panel-body">
        ' . $intro . '
        <input type=hidden name="id" value="' . $package['ID'] . '" />
        <input type=hidden name="dataType" value="json" />
        <input type=hidden name="execute" value="wpdm_getlink" />
        <input type=hidden name="verify" value="email" />
        <input type=hidden name="action" value="wpdm_ajax_call" />
        ';
            $html = "";
            $html = apply_filters('wpdm_render_custom_form_fields', $html, $package['ID']);
            $data .= $html;
            $data .= '

        <input type="email" required="required" class="form-control group-item email-lock-mail" placeholder="'.$eeml.'" size="20" id="email_' . $unqid . '_' . $package['ID'] . '" name="email" style="margin:5px 0" />



</div><div class="panel-footer text-right"><button id="wpdm_submit_' . $unqid . '_' . $package['ID'] . '" class="wpdm_submit btn btn-success  group-item"  type=submit>'.$blabel.'</button></div></div>
        </form>

        <script type="text/javascript">
        jQuery(function($){
            $(".email-lock-mail").val(localStorage.getItem("email_lock_mail"));
            $(".email-lock-name").val(localStorage.getItem("email_lock_name"));
        });
        jQuery(".wpdmdlf_' . $unqid . '_' . $package['ID'] . '").submit(function(){
            var paramObj = {};
            localStorage.setItem("email_lock_mail", jQuery("#email_' . $unqid . '_' . $package['ID'] . '").val());
            localStorage.setItem("email_lock_name", jQuery("#wpdmdlf_' . $unqid . '_' . $package['ID'] . ' input.email-lock-name").val());
            jQuery(".emsg_' . $unqid . $package['ID'] . '").removeAttr("style").html("<div class=\'alert alert-warning\'><i class=\'pull-right fa fa-spinner fa-spin\'></i>'.__('Processing...','wpdmpro').'</div>").show();
            jQuery(".wpdmdlf_' . $unqid . '_' . $package['ID'] . '").hide();
            jQuery.each(jQuery(this).serializeArray(), function(_, kv) {
              paramObj[kv.name] = kv.value;
            });
            var ctz = new Date().getMilliseconds();
            jQuery(this).removeClass("wpdm_submit").addClass("wpdm_submit_wait");
            jQuery(this).ajaxSubmit({
            url: "'.home_url('/?nocache=').'" + ctz,
            success:function(res){
                jQuery(".wpdmdlf_' . $unqid . '_' . $package['ID'] . '").hide();
                jQuery(".emsg_' . $package['ID'] . '").html("verifying...").css("cursor","pointer").show().click(function(){ jQuery(this).hide();jQuery(".wpdmdlf_' . $unqid . '_' . $package['ID'] . '").show(); });
                if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                location.href=res.downloadurl;
                jQuery(".emsg_' . $unqid . $package['ID'] . '").html(res.msg);
                jQuery(".pkg_' . $package['ID'] . ' .modal-body").html("<div style=\'padding:10px;text-align:center\'><a style=\'color:#fff !important\' class=\'btn btn-success\' href=\'"+res.downloadurl+"\'>'.$dlabel.'</a></div>").fadeIn();
                } else {
                    jQuery(".emsg_' . $unqid . $package['ID'] . '").html(""+res.error);
                }

            }});

        return false;
         });
        </script>

        ';
        }
        return $data;
    }

    public static function Tweet($package, $buttononly = false)
    {
        global $tbc;

        $tbc++;
        $var = md5('tl_visitor.' . $_SERVER['REMOTE_ADDR'] . '.' . $tbc . '.' . md5(get_permalink($package['ID'])));

        $tweet_message = $package['tweet_message'];
        $dlabel =  __('Download', 'wpdmpro');
        //$href = $href?$href:get_permalink(get_the_ID());
        $tmpid = uniqid();
        //update_post_meta(get_the_ID(),$var,$package['download_url']);
        $force = rtrim(base64_encode("unlocked|" . date("Ymdh")), '=');
        if (isset($_COOKIE[$var]) && $_COOKIE[$var] == 1)
            return $package['download_url'];
        else
            $data = '<div id="tweet_content_' . $package['ID'] . '" class="locked_ct"><a href="https://twitter.com/share?text=' . $tweet_message . '" class="twitter-share-button" data-via="w3eden">Tweet</a></div><div style="clear:both"></div>';
        $req = home_url('/?pid=' . $package['ID'] . '&var=' . $var);
        $home = home_url('/');
        $btitle = isset($package['tweet_heading']) ? $package['tweet_heading'] : __('Tweet to download', 'wpdmpro');
        $intro = isset($package['tweet_intro']) ? "<p>" . $package['tweet_intro'] . "</p>" : '';
        $html = <<<DATA

                <div class="panel panel-default">
            <div class="panel-heading">
    {$btitle}
  </div>
  <div class="panel-body" id="in_{$tmpid}">

                <div id="tl_$tbc" style="max-width:100%;overflow:hidden">
                {$intro}<Br/>
                $data
                </div>


                <script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>
                <script type="text/javascript">

                if(typeof twttr !== 'undefined'){
                alert(1);
                twttr.ready(function (twttr) {

                    twttr.events.bind('tweet', function(event) {
                        document.log(event);
                        var data = {unlock_key : '<?php echo base64_encode(session_id());?>'};
                        var ctz = new Date().getMilliseconds();

                        jQuery.cookie('unlocked_{$package['ID']}',1);
                        jQuery.post("{$home}?nocache="+ctz,{id:{$package['ID']},dataType:'json',execute:'wpdm_getlink',force:'$force',social:'t',action:'wpdm_ajax_call'},function(res){
                            if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                            location.href=res.downloadurl;
                            jQuery('#in_{$tmpid}').html('<div style="padding:10px;text-align:center;"><a style="color:#fff" class="btn btn-success" href="'+res.downloadurl+'">{$dlabel}</a></div>');
                            } else {
                                jQuery("#msg_{$package['ID']}").html(""+res.error);
                            }
                    }, "json").error(function(xhr, ajaxOptions, thrownError) {

                        });
                    });

                });}

                </script>

           </div></div>

DATA;

        if($buttononly==true)
            $html = <<<DATA

<div class="placehold wpdmtwitter"><i class="fa fa-twitter"></i></div>
  <div class="labell" id="in_{$tmpid}">


                $data


                <script type="text/javascript" src="https://platform.twitter.com/widgets.js"></script>
                <script type="text/javascript">
                var ctz = new Date().getMilliseconds();

                if(typeof twttr !== 'undefined'){
                twttr.ready(function (twttr) {

                    twttr.events.bind('tweet', function(event) {

                        var data = {unlock_key : '<?php echo base64_encode(session_id());?>'};
                        var ctz = new Date().getMilliseconds();
                        jQuery.cookie('unlocked_{$package['ID']}',1);
                        jQuery.post("{$home}?nocache="+ctz,{id:{$package['ID']},dataType:'json',execute:'wpdm_getlink',force:'$force',social:'t',action:'wpdm_ajax_call'},function(res){
                            if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                            location.href=res.downloadurl;
                            jQuery('#wpdmslb-tweet-{$package['ID']}').addClass('wpdm-social-lock-unlocked').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-inverse btn-block">{$dlabel}</a>');
                            } else {
                                jQuery("#msg_{$package['ID']}").html(""+res.error);
                            }
                    }, "json").error(function(xhr, ajaxOptions, thrownError) {

                        });
                    });

                });}

                </script>

           </div>

DATA;
        return $html;
    }

    public static function FacebookLike($package, $buttononly = false)
    {
        $url = $package['facebook_like'];
        $url = $url ? $url : get_permalink();
        $dlabel =  __('Download', 'wpdmpro');
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        //return '<div class="fb-like" data-href="'.$url.'#'.$package['ID'].'" data-send="false" data-width="300" data-show-faces="false" data-font="arial"></div>';
        $unlockurl = home_url("/?id={$package['ID']}&execute=wpdm_getlink&force={$force}&social=f");
        $btitle = isset($package['facebook_heading']) ? $package['facebook_heading'] : __('Like on FB to Download', 'wpdmpro');
        $intro = isset($package['facebook_intro']) ? "<p>" . $package['facebook_intro'] . "</p>" : '';

        if($buttononly==true){
            return '<div id="wpdmslb-facebooklike-'.$package['ID'].'" class="wpdm-social-lock-box wpdmslb-facebooklike">' .'

    <div class="placehold wpdmfacebook"><i class="fa fa-thumbs-up"></i></div>
  <div class="labell">

     <div id="fb-root"></div>
     <div style="display:none" id="' . strtolower(str_replace(array("://", "/", "."), "", $url)) . '" >' . $package['ID'] . '</div>
     <script>
     var ctz = new Date().getMilliseconds();
            var siteurl = "' . home_url('/?nocache=') . '"+ctz,force="' . $force . '", appid="' . get_option('_wpdm_facebook_app_id', 0) . '";
            window.fbAsyncInit = function() {
                 console.log(FB);
                FB.Event.subscribe(\'edge.create\', function(href) {
                    console.log("FB Like");
                    console.log(href);
                    var id = href.replace(/[^0-9a-zA-Z-]/g,"");
                    id = id.toLowerCase();
                      var pkgid = jQuery(\'#\'+id).html();
                      jQuery.cookie(\'unlocked_\'+pkgid,1);

                      jQuery.post(siteurl,{id:pkgid,dataType:\'json\',execute:\'wpdm_getlink\',force:force,social:\'f\',action:\'wpdm_ajax_call\'},function(res){
                                            if(res.downloadurl!=\'\'&&res.downloadurl!=\'undefined\'&&res!=\'undefined\') {
                                            location.href=res.downloadurl;
                                            jQuery(\'#wpdmslb-facebooklike-\'+pkgid).addClass(\'wpdm-social-lock-unlocked\').html(\'<a href="\'+res.downloadurl+\'" class="wpdm-download-button btn btn-inverse btn-block">'.$dlabel.'</a>\');
                                            } else {
                                                jQuery(\'#msg_\'+pkgid).html(\'\'+res.error);
                                            }
                                    });
                      return false;
                });
            };

            (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id; /* js.async = true; */
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . get_option('_wpdm_facebook_app_id', 0) . '";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));
     </script>
     <div class="fb-like" data-href="' . $url . '" data-send="false" data-width="100" data-show-faces="false" data-layout="button_count" data-font="arial"></div>

     <style>.fb_edge_widget_with_comment{ max-height:20px !important; overflow:hidden !important;}</style>
     </div>
     </div>

     ';
        }

        return '
            <div class="panel panel-default">
            <div class="panel-heading">
    ' . $btitle . '
  </div>
  <div class="panel-body">

' . $intro . '<br/>
     <div id="fb-root"></div>
     <div style="display:none" id="' . str_replace(array("://", "/", "."), "", $url) . '" >' . $package['ID'] . '</div>
     <script>
            var siteurl = "' . home_url('/') . '",force="' . $force . '", appid="' . get_option('_wpdm_facebook_app_id', 0) . '";
            window.fbAsyncInit = function() {
                /*FB.init({
                    appId: \'' . get_option('_wpdm_facebook_app_id', 0) . '\',
                    cookie: true
                });*/

                FB.Event.subscribe(\'edge.create\', function(href) {
                    var id = href.replace(/[^0-9a-z-]/g,"");
                      var pkgid = jQuery(\'#\'+id).html();
                      jQuery.cookie(\'unlocked_\'+pkgid,1);

                      jQuery.post(siteurl,{id:pkgid,dataType:\'json\',execute:\'wpdm_getlink\',force:force,social:\'f\',action:\'wpdm_ajax_call\'},function(res){
                                            if(res.downloadurl!=\'\'&&res.downloadurl!=\'undefined\'&&res!=\'undefined\') {
                                            location.href=res.downloadurl;
                                            jQuery(\'#pkg_\'+pkgid).html(\'<a style="color:#000" href="\'+res.downloadurl+\'">'.$dlabel.'</a>\');
                                            /*jQuery.cookie(\'liked_' . str_replace(array("://", "/", "."), "", $url) . '\',res.downloadurl,{expires:30});*/
                                            } else {
                                                jQuery(\'#msg_\'+pkgid).html(\'\'+res.error);
                                            }
                                    });
                      return false;
                });
            };

            (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id; /* js.async = true; */
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . get_option('_wpdm_facebook_app_id', 0) . '";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));
     </script>
     <div class="fb-like" data-href="' . $url . '" data-send="false" data-width="100" data-show-faces="false" data-layout="button_count" data-font="arial"></div>

     <style>.fb_edge_widget_with_comment{ max-height:20px !important; overflow:hidden !important;}</style>
     </div>

</div>
     ';

    }

    public static function reCaptchaLock($package, $buttononly = false){
        ob_start();
        //wp_enqueue_script('wpdm-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit');
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        ?>
        <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'></script>
        <div  id="reCaptchaLock_<?php echo $package['ID']; ?>"></div>
        <div id="msg_<?php echo $package['ID']; ?>"></div>
        <script type="text/javascript">
            var ctz = new Date().getMilliseconds();
            var siteurl = "<?php echo home_url('/?nocache='); ?>"+ctz,force="<?php echo $force; ?>";
            var verifyCallback_<?php echo $package['ID']; ?> = function(response) {
                jQuery.post(siteurl,{id:<?php echo $package['ID'];?>,dataType:'json',execute:'wpdm_getlink',force:force,social:'c',reCaptchaVerify:response,action:'wpdm_ajax_call'},function(res){
                    if(res.downloadurl!='' && res.downloadurl != undefined && res!= undefined ) {
                    location.href=res.downloadurl;
                    jQuery('#reCaptchaLock_<?php echo $package['ID']; ?>').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-inverse btn-lg"><?php _e('Download', 'wpdmpro'); ?></a>');
                    } else {
                        jQuery('#msg_<?php echo $package['ID']; ?>').html(''+res.error);
                    }
                });
            };
            var widgetId2;
            var onloadCallback = function() {
                grecaptcha.render('reCaptchaLock_<?php echo $package['ID']; ?>', {
                    'sitekey' : '<?php echo get_option('_wpdm_recaptcha_site_key'); ?>',
                    'callback' : verifyCallback_<?php echo $package['ID']; ?>,
                    'theme' : 'light'
                });
            };
        </script>

        <?php
        return ob_get_clean();
    }



}
