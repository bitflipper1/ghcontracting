<?php

/**
 * Warning!!!
 * Don't change any function from here
 *
 */

global $stabs, $package, $wpdm_package;



/**
 * @param $tablink
 * @param $newtab
 * @param $func
 * @deprecated Deprecated from v4.2, use filter hook 'add_wpdm_settings_tab'
 * @usage Deprecated: From v4.2, use filter hook 'add_wpdm_settings_tab'
 */
function add_wdm_settings_tab($tablink, $newtab, $func)
{
    global $stabs;
    $stabs["{$tablink}"] = array('id' => $tablink,'icon'=>'fa fa-cog', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=' . $tablink, 'title' => $newtab, 'callback' => $func);
}

function wpdm_create_settings_tab($tabid, $tabtitle, $callback, $icon = 'fa fa-cog')
{
    return \WPDM\admin\menus\Settings::createMenu($tabid, $tabtitle, $callback, $icon);
}


/**
 * @usage Check user's download limit
 * @param $id
 * @return bool
 */
function wpdm_is_download_limit_exceed($id)
{
    return \WPDM\Package::userDownloadLimitExceeded($id);
}


/**
 * @param (int|array) $package Package ID (INT) or Complete Package Data (Array)
 * @param string $ext
 * @return string|void
 */
function wpdm_download_url($package, $ext = '')
{
    if(!is_array($package)) $package = intval($package);
    $id = is_int($package)?$package:$package['ID'];
    return \WPDM\Package::getDownloadURL($id, $ext);
}


/**
 * @usage Check if a download manager category has child
 * @param $parent
 * @return bool
 */

function wpdm_cat_has_child($parent)
{
    $termchildren = get_term_children( $parent, 'wpdmcategory' );
    if(count($termchildren)>0) return true;
    return false;
}

/**
 * @usage Get category checkbox list
 * @param int $parent
 * @param int $level
 * @param array $sel
 */
function wpdm_cblist_categories($parent = 0, $level = 0, $sel = array())
{
    $cats = get_terms('wpdmcategory', array('hide_empty' => false, 'parent' => $parent));
    if (!$cats) $cats = array();
    if ($parent != '') echo "<ul>";
    foreach ($cats as $cat) {
        $id = $cat->slug;
        $pres = $level * 5;

            if (in_array($id, $sel))
                $checked = 'checked=checked';
            else
                $checked = '';
            echo "<li style='margin-left:{$pres}px;padding-left:0'><label><input id='c$id' type='checkbox' name='file[category][]' value='$id' $checked /> ".$cat->name."</label></li>\n";
            wpdm_cblist_categories($cat->term_id, $level + 1, $sel);

    }
    if ($parent != '') echo "</ul>";
}

/**
 * @usage Get category dropdown list
 * @param string $name
 * @param string $selected
 * @param string $id
 * @param int $echo
 * @return string
 */
function wpdm_dropdown_categories($name = '', $selected = '', $id = '', $echo = 1)
{
    return wp_dropdown_categories(array('show_option_none'=>__('Select category', 'wpdmpro'),'show_count'=>0,'orderby'=>'name','echo'=>$echo, 'class' => 'form-control selectpicker', 'taxonomy' => 'wpdmcategory','hide_empty' => 0, 'name' => $name, 'id' => $id ,'selected' => $selected));

}


/**
 * @usage Post with cURL
 * @param $url
 * @param $data
 * @return bool|mixed|string
 */
function remote_post($url, $data)
{
    $fields_string = "";
    foreach ($data as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');
    //open connection
    if(!function_exists('curl_init')) return WPDM_Messages::Error('<b>cURL</b> is not active or installed or not functioning properly in your server',-1);
    $ch = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}

/**
 * @usage Get with cURL
 * @param $url
 * @return bool|mixed|string
 */
function remote_get($url)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_USERAGENT => "spider", // who am i
        CURLOPT_AUTOREFERER => true, // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
        CURLOPT_TIMEOUT => 120, // timeout on response
        CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
    );

    if(!function_exists('curl_init')) return WPDM_Messages::Error('<b>cURL</b> is not active or installed or not functioning properly in your server',-1);

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);
    return $content;
}



function is_valid_license_key()
{
    $key = isset($_POST['_wpdm_license_key']) ? $_POST['_wpdm_license_key'] : get_option('_wpdm_license_key');
    update_option("__wpdm_nlc", strtotime('+7 days'));
    $domain = strtolower(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (file_exists(dirname(__FILE__) . "/cache/wpdm_{$domain}")) {
        $data = unserialize(base64_decode(file_get_contents(dirname(__FILE__) . "/cache/wpdm_{$domain}")));
        if ($data[0] == md5($domain . $key) && $data[1] > time())
            return true;
        else
            @unlink(dirname(__FILE__) . "/cache/wpdm_{$domain}");
    }
    $res = remote_post('http://www.wpdownloadmanager.com/', array('action' => 'wpdm_pp_ajax_call', 'execute' => 'verifylicense', 'domain' => $domain, 'key' => $key, 'product' => 'wpdmpro'));

    if ($res === 'valid') {
        file_put_contents(dirname(__FILE__) . "/cache/wpdm_{$domain}", base64_encode(serialize(array(md5($domain . $key), strtotime("+30 days")))));
        return true;
    }
    if (get_option('settings_ok') == '')
        update_option('settings_ok', strtotime('+30 days'));
    else {
        $page =  isset($_GET['page'])?$_GET['page']:"";
        $time = (int)get_option('settings_ok');
        if ($time < time() && $page == 'settings' && (!isset($_GET['tab']) || $_GET['tab'] != 'license')) {
            die("<script>location.href='edit.php?post_type=wpdmpro&page=settings&tab=license';</script>");
        }
    }
    return false;
}


function check_license()
{
    if((int)get_option('__wpdm_nlc') > time()) return true;
    if ($_SERVER['HTTP_HOST'] == 'localhost') return true;
    //if (!isAjax()) {
        if (!is_valid_license_key()) {
            $time = (int)get_option('settings_ok');
            if ($time > time())
                wp_die( "
        <div id=\"warning\" class=\"error fade\"><p>
        Please enter a valid <a href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for <b>Download Manager</b> 
        </div>
        " );
            else
                wp_die( "
        <div id=\"warning\" class=\"error fade\"><p>
        Trial period for <b>Download Manager</b> is expired.<br/>
        Please enter a valid <a href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for <b>Download Manager</b> to reactivate it.<br/>
        <a href='http://www.wpdownloadmanager.com/'>Buy your copy now only at 45.00 usd</a>
        </div>
        " );
        }
    //}
}

function wpdm_license_notice()
{
    if((int)get_option('__wpdm_nlc') > time()) return '';
    if ($_SERVER['HTTP_HOST'] == 'localhost') return '';
    //if (!isAjax()) {
    if (!is_valid_license_key()) {
        $time = (int)get_option('settings_ok');
        if ($time > time())
            return "
        <div class='w3eden'><div id=\"warning\" class=\"alert alert-danger\"><p>
        Please enter a valid <a href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for <b>Download Manager</b>
        </div></div>
        " ;
        else
            return ( "
        <div class='w3eden'><div id=\"warning\" class=\"alert alert-danger\"><p>
        Trial period for <b>Download Manager</b> is expired.<br/>
        Please enter a valid <a style='font-weight: 900;text-decoration: underline' href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for Download Manager to reactivate it.<br/>
        <a href='http://www.wpdownloadmanager.com/'>Buy your copy now only at 45.00 usd</a>
        </div></div>
        " );
    }
    //}
    return '';
}

function wpdm_admin_license_notice(){
    if(basename($_SERVER['REQUEST_URI']) !='plugins.php' && basename($_SERVER['REQUEST_URI']) !='index.php' && get_post_type()!= 'wpdmpro') return '';
    if((int)get_option('__wpdm_nlc') > time()) return '';
    if ($_SERVER['HTTP_HOST'] == 'localhost') return '';
    //if (!isAjax()) {
    if (!is_valid_license_key()) {
        $time = (int)get_option('settings_ok');
        if ($time > time())
            echo "
        <div id=\"error\" class=\"error\" style='border-left: 0 !important;border-top: 3px solid #dd3d36 !important;'><p>
        Please enter a valid <a href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for <b>Download Manager</b></p>
        </div>
        " ;
        else
            echo ( "
        <div id=\"error\" class=\"error\" style='border-left: 0 !important;border-top: 3px solid #dd3d36 !important;'><p>
        Trial period for <b>Download Manager</b> is expired.<br/>
        Please enter a valid <a style='font-weight: 900;text-decoration: underline' href='edit.php?post_type=wpdmpro&page=settings&tab=license'>license key</a> for Download Manager to reactivate it.<br/>
        <a href='http://www.wpdownloadmanager.com/'>Buy your copy now only at 45.00 usd</a></p>
        </div>
        " );
    }
    //}
}


function wpdm_ajax_call_exec()
{
    if (isset($_POST['action']) && $_POST['action'] == 'wpdm_ajax_call') {
        if ($_POST['execute']=='wpdm_getlink')
            wpdm_getlink();
        die();
    }
}


function wpdm_plugin_data($dir){
    $plugins = get_plugins();
    foreach($plugins as $plugin => $data){
        $data['plugin_index_file'] = $plugin;
        $plugin = explode("/", $plugin);
        if($plugin[0]==$dir) return $data;
    }
    return false;
}

function wpdm_check_update()
{

    if(!current_user_can(WPDM_ADMIN_CAP)) return;



    $latest = '';//get_option('wpdm_latest');
    $latest_check = get_option('wpdm_latest_check');
    $time = time() - intval($latest_check);
    $plugins = get_plugins();

    $latest_v_url = 'http://www.wpdownloadmanager.com/versions.php';

    if ($latest == '' || $time > 86400) {
        $latest = remote_get($latest_v_url);
        update_option('wpdm_latest', $latest);
        update_option('wpdm_latest_check', time());

    }
    $latest = maybe_unserialize($latest);

    $page = isset($_REQUEST['page'])?esc_attr($_REQUEST['page']):'';
    $plugin_info_url = isset($_REQUEST['plugin_url'])?$_REQUEST['plugin_url']:'http://www.wpdownloadmanager.com/purchases/';
    if(is_array($latest)){
    foreach($latest as $plugin_dir => $latestv){
        $plugin_data = wpdm_plugin_data($plugin_dir);

    if (version_compare($plugin_data['Version'], $latestv, '<') == true ) {
        $plugin_name = $plugin_data['Name'];
        $plugin_info_url = $plugin_data['PluginURI'];
        $trid = sanitize_title($plugin_name);
        $plugin_update_url =  admin_url('/edit.php?post_type=wpdmpro&page=settings&tab=plugin-update&plugin='.$plugin_dir); //'http://www.wpdownloadmanager.com/purchases/?'; //
        if($trid!=''){
        if ($page == 'plugins') {
            echo <<<NOTICE
     <script type="text/javascript">
      jQuery(function(){
        jQuery('tr#{$trid}').addClass('update').after('<tr class="plugin-update-tr"><td colspan=3 class="plugin-update colspanchange"><div class="update-message">There is a new version of <strong>{$plugin_name}</strong> available. <b><a href="{$plugin_update_url}&v={$latestv}" style="color: #D54E21;margin-left:10px" target=_blank>[ Update v{$latestv} ]</a></b></div></td></tr>');
      });
      </script>
NOTICE;
        } else {
            echo <<<NOTICE
     <script type="text/javascript">
      jQuery(function(){
        jQuery('.wrap > h2').after('<div class="updated error" style="margin:10px 0px;padding:10px;border:2px solid #dd3d36;border-radius:4px;background: #ffffff"><div style="float:left;"><b style="color:#dd3d36;">Important!</b><br/>There is a new version of <u>{$plugin_name}</u> available.</div> <a style="border-radius:2px; float:right;;color:#ffffff; background: #D54E21;padding:10px 15px" href="{$plugin_update_url}&v={$latestv}"  target=_blank>Update v{$latestv}  <i class="fa fa-long-arrow-right"></i></a><div style="clear:both"></div></div>');
         });
         </script>
NOTICE;
        }}
    }
    }}
    if(wpdm_is_ajax()) die();
}

function wpdm_newversion_check(){

    if(!current_user_can(WPDM_ADMIN_CAP)) return;

    $tmpvar = explode("?", basename($_SERVER['REQUEST_URI']));
    $page = array_shift($tmpvar);
    $page = explode(".", $page);
    $page = array_shift($page);

    if (get_option('wpdm_update_notice') == 'disabled' || !($page == 'plugins' || get_post_type()=='wpdmpro') ) return;

    $page = $page == 'plugins'?$page:get_post_type();

    ?>
    <script type="text/javascript">
        jQuery(function(){

            jQuery.post(ajaxurl, {
                action:         'wpdm_check_update',
                page:           '<?php echo $page; ?>'
            }, function(res){
                jQuery('#wpfooter').after(res);
            });


        });
        </script>
    <?php
}

add_action('admin_notices', 'wpdm_admin_license_notice');

/**
 * Fontend style at tinymce
 */
if (!function_exists('wpdm_frontend_css')) {
    function wpdm_frontend_css($wp)
    {
        $wp .= ',' . get_bloginfo('stylesheet_url');
        return $wp;
    }
}


if (!isset($_REQUEST['P3_NOCACHE'])) {

include(dirname(__FILE__) . "/wpdm-hooks.php");

$files = scandir(dirname(__FILE__) . '/modules/');
    foreach ($files as $file) {
        $tmpdata = explode(".", $file);
        if ($file != '.' && $file != '..' && !@is_dir($file) && end($tmpdata) == 'php')
            include(dirname(__FILE__) . '/modules/' . $file);
    }
}


