<?php
/**
 * Not Completed Yet!
 */

namespace WPDM;

class Package {

    public $ID;
    public $PackageData = array();

    function __construct($ID = null){
        global $post;
        if(!$ID && $post->post_type == 'wpdmpro') $ID = $post->ID;
        $this->ID = $ID;
        return $this;
    }

    function Prepare($ID = null)
    {

        //if (isset($this->PackageData['formatted'])) return $this;

        if($ID == null) $ID = $this->ID;

        $vars = get_post($ID, ARRAY_A);

        $vars['title'] = stripcslashes($vars['post_title']);
        $vars['description'] = stripcslashes($vars['post_content']);
        $vars['description'] = wpautop(stripslashes($vars['description']));
        $vars['description'] = do_shortcode(stripslashes($vars['description']));
        $vars['excerpt'] = stripcslashes(strip_tags($vars['post_excerpt']));

        //Featured Image
        $src = wp_get_attachment_image_src(get_post_thumbnail_id($vars['ID']), 'full', false, '');
        $vars['preview'] = $src['0'];

        $vars['create_date'] = date_i18n(get_option('date_format'), strtotime(get_the_date(get_option('date_format'),$ID)));

        $vars['update_date'] = get_post_modified_time(get_option('date_format'), false, $ID, true);


        $vars['categories'] = get_the_term_list( $vars['ID'], 'wpdmcategory', '', ', ', '' );

        $data = self::metaData($vars['ID']);

        $vars = array_merge($vars, $data);

        $vars['files'] = get_post_meta($vars['ID'], '__wpdm_files', true);
        $vars['file_count'] = count($vars['files']);
        $vars['file_list'] = \WPDM\libs\FileList::Table($vars);
        $vars['play_list'] = $vars['file_list'];
        $vars['play_button'] = self::audioPlayer($vars);
        $vars['file_list_extended'] = \WPDM\libs\FileList::Box($vars);
        $vars['link_label'] = isset($vars['link_label']) ? $vars['link_label'] : __('Download', 'wpdmpro');
        $vars['page_link'] = "<a href='" . get_permalink($vars['ID']) . "'>{$vars['title']}</a>";
        $vars['page_url'] = get_permalink($vars['ID']);

        if(!isset($vars['btnclass']))
            $vars['btnclass'] = '[btnclass]';

        $tags = get_the_tags($vars['ID']);
        $taghtml = "";
        if(is_array($tags)){
            foreach ($tags as $tag)
            {
                $taghtml .= "<a class='btn btn-default btn-xs' style='margin:0 5px 5px 0' href=\""
                    . get_tag_link($tag->term_id)
                    . "\"><i class='fa fa-tag'></i> &nbsp; ".$tag->name."</a> &nbsp;";
            }}
        $vars['tags'] = $taghtml;

        if (count($vars['files']) > 1) $vars['file_ext'] = 'zip';
        if (is_array($vars['files']) && count($vars['files']) == 1) { $tmpdata = explode(".", $vars['files'][0]); $vars['file_ext'] = end($tmpdata); }
        $vars['file_size'] = self::Size($vars['ID']);


        $vars['audio_player'] = $vars['file_list'];
        $vars['audio_player_single'] = self::audioPlayer($vars, true);


        if (!isset($vars['icon']) || $vars['icon'] == '') {
            if(is_array($vars['files'])){
                $ifn = @end($vars['files']);
                $ifn = @explode('.', $ifn);
                $ifn = @end($ifn);
            }
            else
                $ifn = '_blank';

            $vars['icon'] = '<img class="wpdm_icon" src="' . plugins_url('download-manager/assets/file-type-icons/') . (@count($vars['files']) <= 1 ? $ifn : 'zip') . '.png" onError=\'this.src="' . plugins_url('download-manager/assets/file-type-icons/_blank.png') . '";\' />';
        }
        else if (!strpos($vars['icon'], '://'))
            $vars['icon'] = '<img class="wpdm_icon"   src="' . plugins_url(str_replace('download-manager/file-type-icons/','download-manager/assets/file-type-icons/',$vars['icon'])) . '" />';
        else if (!strpos($vars['icon'], ">"))
            $vars['icon'] = '<img class="wpdm_icon"   src="' . str_replace('download-manager/file-type-icons/','download-manager/assets/file-type-icons/',$vars['icon']) . '" />';

        if (isset($vars['preview']) && $vars['preview'] != '') {
            $vars['thumb'] = "<img title='' src='" . wpdm_dynamic_thumb($vars['preview'], array(400, 300)) . "'/>";
        } else
            $vars['thumb'] = $vars['thumb_page'] = $vars['thumb_gallery'] = $vars['thumb_widget'] = "";

        $k = 1;
        $vars['additional_previews'] = isset($vars['more_previews']) ? $vars['more_previews'] : array();
        $img = "<img id='more_previews_{$k}' title='' class='more_previews' src='" . wpdm_dynamic_thumb($vars['preview'], array(575, 170)) . "'/>\n";
        $tmb = "<a href='#more_previews_{$k}' class='spt'><img title='' src='" . wpdm_dynamic_thumb($vars['preview'], array(100, 45)) . "'/></a>\n";


        global $blog_id;
        if (defined('MULTISITE')) {
            $vars['thumb'] = str_replace(home_url('/files'), ABSPATH . 'wp-content/blogs.dir/' . $blog_id . '/files', $vars['thumb']);
        }

        $vars['link_label'] = apply_filters('wpdm_button_image', $vars['link_label'], $vars);

        $vars['link_label'] = $vars['link_label']?$vars['link_label']:__('Download','wpdmpro');

        $vars['download_url'] = self::getDownloadURL($vars['ID'], '');
        $vars['download_link_popup'] = $vars['download_link_extended'] = $vars['download_link'] = "<a class='wpdm-download-link wpdm-download-locked {$vars['btnclass']}' rel='nofollow' href='#' onclick=\"location.href='{$vars['download_url']}';return false;\">{$vars['link_label']}</a>";


        if (self::userDownloadLimitExceeded($vars['ID'])) {
            $vars['download_url'] = '#';
            $vars['link_label'] = __('Download Limit Exceeded','wpdmpro');
            $vars['download_link_popup'] = $vars['download_link_extended'] = $vars['download_link'] = "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$vars['link_label']}</div>";
        }

        else if (isset($vars['expire_date']) && $vars['expire_date'] != "" && strtotime($vars['expire_date']) < time()) {
            $vars['download_url'] = '#';
            $vars['link_label'] = __('Download was expired on', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($vars['expire_date']));
            $vars['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] = "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$vars['link_label']}</div>";
        }

        else if (isset($vars['publish_date']) && $vars['publish_date'] !='' && strtotime($vars['publish_date']) > time()) {
            $vars['download_url'] = '#';
            $vars['link_label'] = __('Download will be available from ', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($vars['publish_date']));
            $vars['download_link'] = $vars['download_link_extended'] = "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$vars['link_label']}</div>";
        }

        else if(is_user_logged_in() && !self::userCanAccess($vars['ID'])){
            $vars['download_url'] = '#';
            $vars['link_label'] = stripslashes(get_option('wpdm_permission_msg'));
            $vars['download_link'] = $vars['download_link_extended'] = $vars['download_link_popup'] =  "<div class='alert alert-danger'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$vars['link_label']}</div>";
        }

        else if(!is_user_logged_in() && count(self::AllowedRoles($vars['ID'])) > 0 && !self::userCanAccess($vars['ID'])){
            $loginform = wpdm_login_form(array('redirect'=>get_permalink($vars['ID'])));
            if (get_option('_wpdm_hide_all', 0) == 1) return 'loginform';
            $vars['download_url'] = home_url('/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
            $vars['download_link'] = $vars['download_link_extended'] = stripcslashes(str_replace(array("[loginform]","[this_url]"), array($loginform,get_permalink($vars['ID'])), get_option('wpdm_login_msg')));
            $vars['download_link'] =  $vars['download_link_extended'] = $vars['download_link_popup'] =  get_option('__wpdm_login_form', 0) == 1 ? $loginform : $vars['download_link'];
        }

        else if(self::isLocked($vars)){
            $vars['download_url'] = '#';
            $vars['download_link'] = self::activeLocks($vars);
            $vars['download_link_extended'] = self::activeLocks($vars, array('embed' => 1));
            $vars['download_link_popup'] = self::activeLocks($vars, array('popstyle' => 'popup'));
        }




//        if (!isset($vars['download_link_called'])) {
//            $tmpvar = self::downloadLink($vars['ID'], 0, array('btnclass' => '[btnclass]')); //DownloadLink($vars, 0, array('btnclass' => '[btnclass]'));
//            $tmpvar1 = self::downloadLink($vars['ID'], 1); //DownloadLink($vars, 1);
//            $vars['download_link'] = $tmpvar;
//            $vars['download_link_extended'] = $tmpvar1;
//            $vars['download_link_called'] = 1;
//        }


        if (!isset($vars['formatted'])) $vars['formatted'] = 0;
        ++$vars['formatted'];

        $vars = apply_filters('wpdm_after_prepare_package_data', $vars);

        $this->PackageData =  $vars;

        foreach($vars as $key => $val){
            $this->$key = $val;
        }
        return $this;
    }

    /**
     * @usage Get all or any specific package info
     * @param $ID
     * @param null $meta
     * @return mixed
     */
    public static function Get($ID, $meta = null){
        $ID = (int)$ID;
        if($ID == 0) return null;
        if($meta != null)
            return get_post_meta($ID, "__wpdm_".$meta, true);
        $p = new Package();
        $package = $p->Prepare($ID);
        return $package->PackageData;
    }

    /**
     * @usage Verify single file download option
     * @param null $ID
     * @return mixed|void
     */
    public static function isSingleFileDownloadAllowed($ID = null){
        global $post;
        if(!$ID && $post->post_type == 'wpdmpro') $ID = $post->ID;
        $global = get_option('__wpdm_individual_file_download', 1);
        $package = get_post_meta($ID,'__wpdm_individual_file_download', true);
        $effective = $package == -1 || $package == '' ? $global:$package;
        return $effective;
    }

    /**
     * @param $id
     * @usage Returns the user roles who has access to specified package
     * @return array|mixed
     */
    public static function AllowedRoles($id){
        $roles = get_post_meta($id, '__wpdm_access', true);
        $roles = maybe_unserialize($roles);
        $cats = get_the_terms( $id, 'wpdmcategory' );
        if(!is_array($roles)) $roles = array();
        if(is_array($cats)){
            foreach($cats as $cat){
                $croles = \WPDM\libs\CategoryHandler::GetAllowedRoles($cat->term_id);
                $roles = array_merge($roles, $croles);
            }}

        $roles = array_unique($roles);

        $roles = apply_filters("wpdm_allowed_roles", $roles, $id);

        return $roles;
    }

    /**
     * @usage Check if a package is locked or public
     * @param $id
     * @return bool
     */
    public static function isLocked($package){
        if(!is_array($package) && (int)$package > 0) {
            $id = $package;
            $package = array();
            $package['ID'] = $id;

            $package['password_lock'] = get_post_meta($id, '__wpdm_password_lock', true) || (get_post_meta($id, '__wpdm_password', true) != '');

            $package['captcha_lock'] = get_post_meta($id, '__wpdm_captcha_lock', true);

        } else
            $id = $package['ID'];
        $lock = '';

        if (isset($package['password']) && $package['password'] != '') $lock = 'locked';

        if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) $lock = 'locked';

        if ($lock !== 'locked')
            $lock = apply_filters('wpdm_check_lock', $lock, $id);

        return ($lock=='locked');


    }

    /**
     * @usage Check if current user has access to package or category
     * @param $id
     * @param string $type
     *
     * @return bool
     */
    public static function userCanAccess($ID, $type = 'package'){
        global $current_user;

        if($type=='package')
            $roles = self::AllowedRoles($ID);
        else $roles = \WPDM\libs\CategoryHandler::GetAllowedRoles($ID);

        $matched = is_user_logged_in()?array_intersect($current_user->roles, $roles):array();

        if(in_array('guest', $roles)) return true;
        if(count($matched) > 0) return true;

        return false;

    }

    /**
     * @usage Check user's download limit
     * @param $ID
     * @return bool
     */
    public static function userDownloadLimitExceeded($ID){
        global $current_user;

        if (is_user_logged_in())
            $index = $current_user->ID;
        else
            $index = $_SERVER['REMOTE_ADDR'];

        $udl = maybe_unserialize(get_post_meta($ID, "__wpdmx_user_download_count", true));
        $td = isset($udl[$index])?$udl[$index]:0;
        $mx = get_post_meta($ID, '__wpdm_download_limit_per_user', true);
        if ($mx > 0 && $td >= $mx) return true;
        return false;
    }

    /**
     * @usage Check if user is can download this package
     * @param $ID
     * @return bool
     */
    public static function UserCanDownload($ID){
        return self::UserCanAccess($ID) && self::userDownloadLimitExceeded($ID);
    }

    /**
     * @usage Count files in a package
     * @param $id
     * @return int
     */
    public static function fileCount($ID){

        $count = count(self::getFiles($ID));

        return $count;

    }

    /**
     * @usage Get list of attached files & all files inside attached dir with a package
     * @param $ID
     * @return array|mixed
     */
    public static function getFiles($ID){
        $files = get_post_meta($ID, '__wpdm_files', true);
        $package_dir = self::Get($ID, 'package_dir');
        if($package_dir != '') {
            $files += \WPDM\FileSystem::scanDir($package_dir);
        }
        return $files;
    }

    /**
     * @usage Create zip from attached files
     * @param $ID
     * @return mixed|string|\WP_Error
     */
    public static function Zip($ID){
        $files = self::getFiles($ID);
        $zipped = get_post_meta( $ID , "__wpdm_zipped_file", true);
        if(count($files) > 0) {
            if ($zipped == '' || !file_exists($zipped)) {
                $zipped = UPLOAD_DIR . sanitize_file_name(get_the_title($ID)) . '-' . $ID . '.zip';
                $zipped = \WPDM\FileSystem::zipFiles($files, $zipped);
                return $zipped;
            }
        }
        return new \WP_Error(404, __('No File Attached!', 'wpdmpro'));
    }

    /**
     * @usage Calculate package size
     * @param $ID
     * @param bool|false $recalculate
     * @return bool|float|int|mixed|string
     */
    public static function Size($ID, $recalculate = false){

        if(get_post_type($ID) !='wpdmpro') return false;

        $size = get_post_meta($ID, '__wpdm_package_size', true);

        if($size!="" && !$recalculate) return $size;

        $files = self::getFiles($ID);

        $size = 0;
        if (is_array($files)) {
            foreach ($files as $f) {
                $f = trim($f);
                if (file_exists($f))
                    $size += @filesize($f);
                else
                    $size += @filesize(UPLOAD_DIR . $f);
            }
        }


        update_post_meta($ID, '__wpdm_package_size_b', $size);
        $size = $size / 1024;
        if ($size > 1024) $size = number_format($size / 1024, 2) . ' MB';
        else $size = number_format($size, 2) . ' KB';
        update_post_meta($ID, '__wpdm_package_size', $size);
        return $size;
    }

    /**
     * @usage Generate play button for link template
     * @param $package
     * @param bool $return
     * @param $style
     * @return mixed|string|void
     */
    public static function audioPlayer($package, $return  = true, $style = 'primary' )
    {

        $audiohtml = "";

        if (!is_array($package['files']) || count($package['files']) == 0) return;
        $audios = array();
        $nonce = wp_create_nonce($_SERVER['REQUEST_URI']);

        foreach($package['files'] as $index => $file){
            $realpath = file_exists($file)?$file:UPLOAD_DIR.$file;
            $filetype = wp_check_filetype( $realpath );
            $tmpvar = explode('/',$filetype['type']);
            if($tmpvar[0]=='audio')
                $audios[$index] =  $file;
        }

        if(count($audios)>0){
            $audio = array_shift($audios);
            $song = home_url("/?wpdmdl={$package['ID']}&ind=".\WPDM_Crypt::Encrypt($audio)."&play=".basename($audio));
            $audiohtml = "<button data-player='wpdm-audio-player' data-song='{$song}' class='btn btn-lg btn-{$style} wpdm-btn-play wpdm-btn-play-lg'><i class='fa fa-play'></i></button>";
        }

        if($return)
            return $audiohtml;

        echo  $audiohtml;

    }

    /**
     * @usage Get All Custom Data of a Package
     * @param $pid
     * @return array
     */
    public static function metaData($ID)
    {
        $cdata = get_post_custom($ID);

        $data = array();
        if(is_array($cdata)){
            foreach ($cdata as $k => $v) {
                $k = str_replace("__wpdm_", "", $k);
                $data[$k] = maybe_unserialize($v[0]);
            }}

        if(!isset($data['access']) || !is_array($data['access'])) $data['access'] = array();
        $data['download_count'] = isset($data['download_count'])? intval($data['download_count']):0;
        $data['view_count'] = isset($data['view_count'])? intval($data['view_count']):0;
        $data['version'] = isset($data['version'])? $data['version']:'1.0.0';
        $data['quota'] = isset($data['quota']) && $data['quota'] > 0? $data['quota']:'&#8734;';
        $data =  apply_filters('wpdm_custom_data',$data, $ID);
        return $data;
    }

    /**
     * @usage Generate download link of a package
     * @param $package
     * @param int $embed
     * @param array $extras
     * @return string
     */
    function prepareDownloadLink(&$package, $embed = 0, $extras = array())
    {
        global $wpdb, $current_user, $wpdm_download_icon, $wpdm_download_lock_icon, $btnclass;
        if(is_array($extras))
            extract($extras);
        $data = '';
        get_currentuserinfo();

        $package['link_url'] = home_url('/?download=1&');
        $package['link_label'] = !isset($package['link_label']) || $package['link_label'] == '' ? __("Download", "wpdmpro") : $package['link_label'];

        //Change link label using a button image
        $package['link_label'] = apply_filters('wpdm_button_image', $package['link_label'], $package);


        $package['download_url'] = wpdm_download_url($package);
        if (wpdm_is_download_limit_exceed($package['ID'])) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download Limit Exceeded','wpdmpro');
        }
        if (isset($package['expire_date']) && $package['expire_date'] != "" && strtotime($package['expire_date']) < time()) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download was expired on', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['expire_date']));
            $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
            return "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$package['link_label']}</div>";
        }

        if (isset($package['publish_date']) && $package['publish_date'] !='' && strtotime($package['publish_date']) > time()) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download will be available from ', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['publish_date']));
            $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
            return "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$package['link_label']}</div>";
        }

        $link_label = isset($package['link_label']) ? $package['link_label'] : __('Download', 'wpdmpro');

        $package['access'] = wpdm_allowed_roles($package['ID']);

        if ($package['download_url'] != '#')
            $package['download_link'] = "<a class='wpdm-download-link wpdm-download-locked {$btnclass}' rel='nofollow' href='#' onclick=\"location.href='{$package['download_url']}';return false;\"><i class='$wpdm_download_icon'></i>{$link_label}</a>";
        else
            $package['download_link'] = "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$link_label}</div>";
        $caps = array_keys($current_user->caps);
        $role = array_shift($caps);

        $matched = (is_array(@maybe_unserialize($package['access'])) && is_user_logged_in())?array_intersect($current_user->roles, @maybe_unserialize($package['access'])):array();

        $skiplink = 0;

        if (is_user_logged_in() && count($matched) <= 0 && !@in_array('guest', @maybe_unserialize($package['access']))) {
            $package['download_url'] = "#";
            $package['download_link'] = $package['download_link_extended'] = stripslashes(get_option('wpdm_permission_msg'));
            $package = apply_filters('download_link', $package);
            if (get_option('_wpdm_hide_all', 0) == 1) { $package['download_link'] = $package['download_link_extended'] = 'blocked'; }
            return $package['download_link'];
        }
        if (!@in_array('guest', @maybe_unserialize($package['access'])) && !is_user_logged_in()) {

            $loginform = wpdm_login_form(array('redirect'=>get_permalink($package['ID'])));
            if (get_option('_wpdm_hide_all', 0) == 1) return 'loginform';
            $package['download_url'] = home_url('/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
            $package['download_link'] = stripcslashes(str_replace(array("[loginform]","[this_url]"), array($loginform,get_permalink($package['ID'])), get_option('wpdm_login_msg')));
            return get_option('__wpdm_login_form', 0) == 1 ? $loginform : $package['download_link'];

        }

        $package = apply_filters('download_link', $package);

        $unqid = uniqid();
        if (!isset($package['quota']) || (isset($package['quota']) && $package['quota'] > 0 && $package['quota'] > $package['download_count']) || $package['quota'] == 0) {
            $lock = 0;

            //isset($package['password_lock']) && (int)$package['password_lock'] == 1 &&
            if ($package['password'] != '') {
                $lock = 'locked';
                $data = \WPDM\PackageLocks::AskPassword($package);
            }


            $sociallock = "";

            if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) {
                $lock = 'locked';
                $sociallock .=  \WPDM\PackageLocks::reCaptchaLock($package , true);

            }

            $extralocks = '';
            $extralocks = apply_filters("wpdm_download_lock", $extralocks, $package);

            if (is_array($extralocks) && $extralocks['lock'] === 'locked') {

                if(isset($extralocks['type']) && $extralocks['type'] == 'social')
                    $sociallock .= $extralocks['html'];
                else
                    $data .= $extralocks['html'];

                $lock = 'locked';
            }

            if($sociallock!=""){
                $data .= "<div class='panel panel-default'><div class='panel-heading'>".__("Download","wpdmpro")."</div><div class='panel-body wpdm-social-locks text-center'>{$sociallock}</div></div>";
            }

            if ($lock === 'locked') {
                $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
                if ($embed == 1)
                    $adata = "</strong><table class='table all-locks-table' style='border:0px'><tr><td style='padding:5px 0px;border:0px;'>" . $data . "</td></tr></table>";
                else {
                    $dataattrs = $popstyle == 'pop-over'? 'data-title="<button type=button id=\'close\' class=\'btn btn-link btn-xs pull-right po-close\' style=\'margin-top:-4px;margin-right:-10px\'><i class=\'fa fa-times text-danger\'></i></button> '.__('Download','wpdmpro').' ' . $package['title'] . '"' : 'data-toggle="modal" data-target="#pkg_' . $package['ID'] . "_" . $unqid . '"';
                    $adata = '<a href="#pkg_' . $package['ID'] . "_" . $unqid . '" '.$dataattrs.' class="wpdm-download-link wpdm-download-locked ' . $popstyle . ' ' . $btnclass . '"><i class=\'' . $wpdm_download_lock_icon . '\'></i>' . $package['link_label'] . '</a>';
                    if ($popstyle == 'pop-over')
                        $adata .= '<div class="modal fade"><div class="row all-locks"  id="pkg_' . $package['ID'] . "_" . $unqid . '">' . $data . '</div></div>';
                    else
                        $adata .= '<div class="modal fade" role="modal" id="pkg_' . $package['ID'] . "_" . $unqid . '"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><strong style="margin:0px;font-size:12pt">' . __('Download') . '</strong></div><div class="modal-body">' . $data . '</div><div class="modal-footer text-right"><button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button></div></div></div></div>';
                }

                $data = $adata;
            }
            if ($lock !== 'locked') {

                $data = $package['download_link'];


            }
        }
        else {
            $data = __("Download limit exceeded!",'wpdmpro');
        }


        //return str_replace(array("\r","\n"),"",$data);
        return $data;

    }

    private function activeLocks($package, $params = array('embed'=>0, 'popstyle' => 'pop-over')){

        $embed = isset($params['embed'])?$params['embed']:0;
        $popstyle = isset($params['popstyle'])?$params['popstyle']:'pop-over';

        $package = apply_filters('wpdm_before_apply_locks', $package);
        $lock = $data = "";
        $unqid = uniqid();
//isset($package['password_lock']) && (int)$package['password_lock'] == 1 &&
        if ($package['password'] != '') {
            $lock = 'locked';
            $data = \WPDM\PackageLocks::AskPassword($package);
        }


        if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) {
            $lock = 'locked';
            $captcha =  \WPDM\PackageLocks::reCaptchaLock($package , true);
            $data .= "<div class='panel panel-default'><div class='panel-heading'>".__("Verify CAPTCHA to Downlaod","wpdmpro")."</div><div class='panel-body wpdm-social-locks text-center'>{$captcha}</div></div>";
        }

        if ($lock === 'locked') {
            $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
            if ($embed == 1)
                $adata = "</strong><table class='table all-locks-table' style='border:0px'><tr><td style='padding:5px 0px;border:0px;'>" . $data . "</td></tr></table>";
            else {
                $dataattrs = $popstyle == 'pop-over'? 'data-title="<button type=button id=\'close\' class=\'btn btn-link btn-xs pull-right po-close\' style=\'margin-top:-4px;margin-right:-10px\'><i class=\'fa fa-times text-danger\'></i></button> '.__('Download','wpdmpro').' ' . $package['title'] . '"' : 'data-toggle="modal" data-target="#pkg_' . $package['ID'] . "_" . $unqid . '"';
                $adata = '<a href="#pkg_' . $package['ID'] . "_" . $unqid . '" '.$dataattrs.' class="wpdm-download-link wpdm-download-locked ' . $popstyle . ' ' . $package['btnclass'] . '">' . $package['link_label'] . '</a>';
                if ($popstyle == 'pop-over')
                    $adata .= '<div class="modal fade"><div class="row all-locks"  id="pkg_' . $package['ID'] . "_" . $unqid . '">' . $data . '</div></div>';
                else
                    $adata .= '<div class="modal fade" role="modal" id="pkg_' . $package['ID'] . "_" . $unqid . '"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><strong style="margin:0px;font-size:12pt">' . __('Download') . '</strong></div><div class="modal-body">' . $data . '</div><div class="modal-footer text-right"><button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button></div></div></div></div>';
            }

            $data = $adata;
        }
        return $data;
    }


    /**
     * @usage Generate download link of a package
     * @param $package
     * @param int $embed
     * @param array $extras
     * @return string
     */
    public static function downloadLink($ID, $embed = 0, $extras = array())
    {
        global $wpdb, $current_user, $wpdm_download_icon, $wpdm_download_lock_icon, $btnclass;
        if(is_array($extras))
            extract($extras);
        $data = '';

        $package = self::Get($ID);

        $package['link_url'] = home_url('/?download=1&');
        $package['link_label'] = !isset($package['link_label']) || $package['link_label'] == '' ? __("Download", "wpdmpro") : $package['link_label'];

        //Change link label using a button image
        $package['link_label'] = apply_filters('wpdm_button_image', $package['link_label'], $package);


        $package['download_url'] = wpdm_download_url($package);
        if (\WPDM\Package::userDownloadLimitExceeded($package['ID'])) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download Limit Exceeded','wpdmpro');
        }
        if (isset($package['expire_date']) && $package['expire_date'] != "" && strtotime($package['expire_date']) < time()) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download was expired on', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['expire_date']));
            $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
            $package = apply_filters('wpdm_after_prepare_package_data', $package);
            return "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$package['link_label']}</div>";
        }

        if (isset($package['publish_date']) && $package['publish_date'] !='' && strtotime($package['publish_date']) > time()) {
            $package['download_url'] = '#';
            $package['link_label'] = __('Download will be available from ', 'wpdmpro') . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['publish_date']));
            $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
            $package = apply_filters('wpdm_after_prepare_package_data', $package);
            return "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$package['link_label']}</div>";
        }

        $link_label = isset($package['link_label']) ? $package['link_label'] : __('Download', 'wpdmpro');

        $package['access'] = wpdm_allowed_roles($package['ID']);

        if ($package['download_url'] != '#')
            $package['download_link'] = "<a class='wpdm-download-link wpdm-download-locked {$btnclass}' rel='nofollow' href='#' onclick=\"location.href='{$package['download_url']}';return false;\"><i class='$wpdm_download_icon'></i>{$link_label}</a>";
        else
            $package['download_link'] = "<div class='alert alert-warning'><b>" . __('Download:', 'wpdmpro') . "</b><br/>{$link_label}</div>";
        $caps = array_keys($current_user->caps);
        $role = array_shift($caps);

        $matched = (is_array(@maybe_unserialize($package['access'])) && is_user_logged_in())?array_intersect($current_user->roles, @maybe_unserialize($package['access'])):array();

        $skiplink = 0;

        if (is_user_logged_in() && count($matched) <= 0 && !@in_array('guest', @maybe_unserialize($package['access']))) {
            $package['download_url'] = "#";
            $package['download_link'] = $package['download_link_extended'] = stripslashes(get_option('wpdm_permission_msg'));
            $package = apply_filters('wpdm_after_prepare_package_data', $package);
            if (get_option('_wpdm_hide_all', 0) == 1) { $package['download_link'] = $package['download_link_extended'] = 'blocked'; }
            return $package['download_link'];
        }
        if (!@in_array('guest', @maybe_unserialize($package['access'])) && !is_user_logged_in()) {

            $loginform = wpdm_login_form(array('redirect'=>get_permalink($package['ID'])));
            if (get_option('_wpdm_hide_all', 0) == 1) return 'loginform';
            $package['download_url'] = home_url('/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
            $package['download_link'] = stripcslashes(str_replace(array("[loginform]","[this_url]"), array($loginform,get_permalink($package['ID'])), get_option('wpdm_login_msg')));
            return get_option('__wpdm_login_form', 0) == 1 ? $loginform : $package['download_link'];

        }

        $package = apply_filters('wpdm_before_apply_locks', $package);
        $package = apply_filters('wpdm_after_prepare_package_data', $package);

        $unqid = uniqid();
        if (!isset($package['quota']) || (isset($package['quota']) && $package['quota'] > 0 && $package['quota'] > $package['download_count']) || $package['quota'] == 0) {
            $lock = 0;

            if ($package['password'] != '') {
                $lock = 'locked';
                $data = \WPDM\PackageLocks::AskPassword($package);
            }


            $sociallock = "";


            $extralocks = '';
            $extralocks = apply_filters("wpdm_download_lock", $extralocks, $package);

            if (is_array($extralocks) && $extralocks['lock'] === 'locked') {

                if(isset($extralocks['type']) && $extralocks['type'] == 'social')
                    $sociallock .= $extralocks['html'];
                else
                    $data .= $extralocks['html'];

                $lock = 'locked';
            }

            if($sociallock!=""){
                $data .= "<div class='panel panel-default'><div class='panel-heading'>".__("Like or Share to Download","wpdmpro")."</div><div class='panel-body wpdm-social-locks text-center'>{$sociallock}</div></div>";
            }

            if ($lock === 'locked') {
                $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
                if ($embed == 1)
                    $adata = "</strong><table class='table all-locks-table' style='border:0px'><tr><td style='padding:5px 0px;border:0px;'>" . $data . "</td></tr></table>";
                else {
                    $dataattrs = $popstyle == 'pop-over'? 'data-title="<button type=button id=\'close\' class=\'btn btn-link btn-xs pull-right po-close\' style=\'margin-top:-4px;margin-right:-10px\'><i class=\'fa fa-times text-danger\'></i></button> '.__('Download','wpdmpro').' ' . $package['title'] . '"' : 'data-toggle="modal" data-target="#pkg_' . $package['ID'] . "_" . $unqid . '"';
                    $adata = '<a href="#pkg_' . $package['ID'] . "_" . $unqid . '" '.$dataattrs.' class="wpdm-download-link wpdm-download-locked ' . $popstyle . ' ' . $btnclass . '"><i class=\'' . $wpdm_download_lock_icon . '\'></i>' . $package['link_label'] . '</a>';
                    if ($popstyle == 'pop-over')
                        $adata .= '<div class="modal fade"><div class="row all-locks"  id="pkg_' . $package['ID'] . "_" . $unqid . '">' . $data . '</div></div>';
                    else
                        $adata .= '<div class="modal fade" role="modal" id="pkg_' . $package['ID'] . "_" . $unqid . '"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><strong style="margin:0px;font-size:12pt">' . __('Download') . '</strong></div><div class="modal-body">' . $data . '</div><div class="modal-footer text-right"><button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button></div></div></div></div>';
                }

                $data = $adata;
            }
            if ($lock !== 'locked') {

                $data = $package['download_link'];


            }
        }
        else {
            $data = __("Download limit exceeded!",'wpdmpro');
        }

        return $data;

    }

    /**
     * @usage Generate download url for public/open downloads, the url will not work for the packages with lock option
     * @param $ID
     * @param $ext
     * @return string
     */
    public static function getDownloadURL($ID, $ext = ''){
        if(self::isLocked($ID) && !isset($_SESSION['_wpdm_unlocked_'.$ID])) return '#locked';
        if ($ext) $ext = '&' . $ext;
        $permalink = get_permalink($ID);
        $sap = strpos($permalink, '?')?'&':'?';
        return $permalink.$sap."wpdmdl={$ID}{$ext}";
    }

    public static function getMasterDownloadURL($ID){
        $packageURL = get_permalink($ID);
        $packageURL .= (get_option('permalink_structure', false)?'?':'&').'wpdmdl='.$ID.'&masterkey='.get_post_meta($ID, '__wpdm_masterkey', true);
        return $packageURL;
    }





    /**
     * @usage Fetch link/page template and return generated html
     * @param $template
     * @param $vars
     * @param string $type
     * @return mixed|string|void
     */
    public static function fetchTemplate($template, $vars, $type = 'link')
    {

        if(!is_array($vars) && is_int($vars) && $vars > 0) $vars = array('ID' => $vars);
        if (!isset($vars['ID']) || intval($vars['ID']) <1 ) return '';

        $default['link'] =  'link-template-default.php';
        $default['page'] =  'page-template-default.php';

        if (!isset($vars['formatted'])) {
            $pack = new \WPDM\Package($vars['ID']);
            $pack->Prepare();
            $vars = $pack->PackageData;
        }

        if ($template == '') {
            $template = $type == 'page' ? $vars['page_template'] : $vars['template'];
        }

        if ($template == '')
            $template = $default[$type];

        $templates = maybe_unserialize(get_option("_fm_".$type."_templates", true));
        if(isset($templates[$template]) && isset($templates[$template]['content'])) $template = $templates[$template]['content'];
        else
            if(!strpos(strip_tags($template), "]")){

                $ltpldir = get_stylesheet_directory().'/donwload-manager/'.$type.'-templates/';
                if(!file_exists($ltpldir) || !file_exists($ltpldir.$template))
                    $ltpldir = WPDM_BASE_DIR.'/tpls/'.$type.'-templates/';

                if (file_exists(TEMPLATEPATH . '/' . $template)) $template = file_get_contents(TEMPLATEPATH . '/' . $template);
                else if (file_exists($ltpldir . $template)) $template = file_get_contents($ltpldir . $template);
                else if (file_exists($ltpldir . $template . '.php')) $template = file_get_contents($ltpldir . $template . '.php');
                else if (file_exists($ltpldir. $type . "-template-" . $template . '.php')) $template = file_get_contents($ltpldir. $type . "-template-" . $template . '.php');
            }

        preg_match_all("/\[cf ([^\]]+)\]/", $template, $cfmatches);
        preg_match_all("/\[thumb_([0-9]+)x([0-9]+)\]/", $template, $matches);
        preg_match_all("/\[thumb_url_([0-9]+)x([0-9]+)\]/", $template, $umatches);
        preg_match_all("/\[thumb_gallery_([0-9]+)x([0-9]+)\]/", $template, $gmatches);
        preg_match_all("/\[excerpt_([0-9]+)\]/", $template, $xmatches);
        preg_match_all("/\[pdf_thumb_([0-9]+)x([0-9]+)\]/", $template, $pmatches);

        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($vars['ID']), 'full');
        $vars['preview'] = $thumb['0'];

        $pdf = isset($vars['files'][0])?$vars['files'][0]:'';
        $ext = explode(".", $pdf);
        $ext = end($ext);

        // Parse [pdf_thumb] tag in link/page template
        if(strpos($template, 'pdf_thumb')) {
            if ($ext == 'pdf')
                $vars['pdf_thumb'] = "<img alt='{$vars['title']}' src='" . wpdm_pdf_thumbnail($pdf, $vars['ID']) . "' />";
            else $vars['pdf_thumb'] = $vars['preview'] != '' ? "<img alt='{$vars['title']}' src='{$vars['preview']}' />" : "";
        }

        // Parse [pdf_thumb_WxH] tag in link/page template
        foreach ($pmatches[0] as $nd => $scode) {
            $keys[] = $scode;
            $imsrc  = wpdm_dynamic_thumb(wpdm_pdf_thumbnail($pdf, $vars['ID']), array($pmatches[1][$nd], $pmatches[2][$nd]));
            $values[] = $imsrc != '' ? "<img src='" . $imsrc . "' alt='{$vars['title']}' />" : '';
        }

        // Parse [file_type] tag in link/page template
        if(strpos($template, 'file_type')) {
            $vars['file_types'] = self::fileTypes($vars['ID'], false);
            $vars['file_type_icons'] = self::fileTypes($vars['ID']);
        }


        foreach ($matches[0] as $nd => $scode) {
            $keys[] = $scode;
            $imsrc  = wpdm_dynamic_thumb($vars['preview'], array($matches[1][$nd], $matches[2][$nd]));
            $values[] = $vars['preview'] != '' ? "<img src='" . $imsrc . "' alt='{$vars['title']}' />" : '';
        }

        foreach ($umatches[0] as $nd => $scode) {
            $keys[] = $scode;
            $values[] = $vars['preview'] != '' ? wpdm_dynamic_thumb($vars['preview'], array($umatches[1][$nd], $umatches[2][$nd])) : '';
        }

        foreach ($gmatches[0] as $nd => $scode) {
            $keys[] = $scode;
            $values[] = wpdm_get_additional_preview_images($vars, $gmatches[1][$nd], $gmatches[2][$nd]);
        }


        foreach ($xmatches[0] as $nd => $scode) {
            $keys[] = $scode;
            $ss = substr(strip_tags($vars['description']), 0, intval($xmatches[1][$nd]));
            $tmp = explode(" ", substr(strip_tags($vars['description']), intval($xmatches[1][$nd])));
            $bw = array_shift($tmp);
            $ss .= $bw;
            $values[] = $ss . '...';
        }

        if ($type == 'page' && (strpos($template, '[similar_downloads]') || strpos($vars['description'], '[similar_downloads]')))
            $vars['similar_downloads'] = wpdm_similar_packages($vars, 5);

        if(strpos($template, 'doc_preview'))
            $vars['doc_preview'] = self::docPreview($vars);

        // If need to re-process any data before fetch template
        $vars = apply_filters("wdm_before_fetch_template", $vars);

        foreach ($vars as $key => $value) {
            if(!is_array($value)) {
                $keys[] = "[$key]";
                $values[] = $value;
            }
        }

        $loginform = wpdm_login_form(array('redirect'=>get_permalink($vars['ID'])));
        $hide_all_message = get_option('__wpdm_login_form', 0) == 1 ? $loginform : stripcslashes(str_replace(array("[loginform]","[this_url]"), array($loginform,get_permalink($vars['ID'])), get_option('wpdm_login_msg')));

        if ($vars['download_link'] == 'blocked' && $type == 'link') return "";
        if ($vars['download_link'] == 'blocked' && $type == 'page') return get_option('wpdm_permission_msg');
        if ($vars['download_link'] == 'loginform' && $type == 'link') return "";
        if ($vars['download_link'] == 'loginform' && $type == 'page') return $hide_all_message;

        return @str_replace($keys, $values, @stripcslashes($template));
    }

    /**
     * @usage Find attached files types with a package
     * @param $ID
     * @param bool|true $img
     * @return array|string
     */
    public static function fileTypes($ID, $img = true){
        $files = maybe_unserialize(get_post_meta($ID, '__wpdm_files', true));
        $ext = array();
        if (is_array($files)) {
            foreach ($files as $f) {
                $f = trim($f);
                $f = explode(".", $f);
                $ext[] = end($f);
            }
        }

        $ext = array_unique($ext);
        $exico = '';
        foreach($ext as $exi){
            if(file_exists(dirname(__FILE__).'/assets/file-type-icons/'.$exi.'.png'))
                $exico .= "<img alt='{$exi}' title='{$exi}' class='ttip' style='width:16px;height:16px;' src='".plugins_url('download-manager/assets/file-type-icons/'.$exi.'.png')."' /> ";
        }
        if($img) return $exico;
        return $ext;
    }


    /**
     * @param $package
     * @return string
     * @usage Generate Google Doc Preview
     */
    public static function docPreview($package){


        $files = $package['files'];

        if(!is_array($files)) return "";
        $ind = -1;
        foreach($files as $i=>$sfile){
            $ifile = $sfile;
            $sfile = explode(".", $sfile);
            if(in_array(end($sfile),array('pdf','doc','docx','xls','xlsx','ppt','pptx'))) { $ind = \WPDM_Crypt::Encrypt($ifile); break; }
        }
        if($ind==-1) return "";
        $url = wpdm_download_url($package, 'ind='.$ind);
        if(strpos($ifile, "://")) $url = $ifile;
        return \WPDM\FileSystem::docPreview($url);
    }


    /**
     * @usage Create New Package
     * @param $data
     * @return mixed
     */
    public static function Create($package_data){

        if(isset($package_data['post_type']))
            unset($package_data['post_type']);

        $package_data_core = array(
            'post_title'           => '',
            'post_content'           => '',
            'post_excerpt'          => '',
            'post_status'           => 'publish',
            'post_type'             => 'wpdmpro',
            'post_author'           => get_current_user_id(),
            'ping_status'           => get_option('default_ping_status'),
            'post_parent'           => 0,
            'menu_order'            => 0,
            'to_ping'               =>  '',
            'pinged'                => '',
            'post_password'         => '',
            'guid'                  => '',
            'post_content_filtered' => '',
            'import_id'             => 0
        );

        $package_data_meta = array(
            'files'           => array(),
            'fileinfo'           => array(),
            'package_dir'           => '',
            'link_label'          => __('Download','wpdmpro'),
            'download_count'           => 0,
            'view_count'             => 0,
            'version'           => '1.0.0',
            'stock'           => 0,
            'package_size'           => 0,
            'package_size_b'           => 0,
            'access'            => 0,
            'individual_file_download'               =>  -1,
            'cache_zip'               =>  -1,
            'template'                => 'link-template-calltoaction3.php',
            'page_template'         => 'page-template-1col-flat.php',
            'password_lock'                  => '0',
            'facebook_lock'                  => '0',
            'gplusone_lock'                  => '0',
            'linkedin_lock'                  => '0',
            'tweet_lock'                  => '0',
            'email_lock'                  => '0',
            'icon' => '',
            'import_id'             => 0
        );

        foreach($package_data_core as $key => &$value){
            $value = isset($package_data[$key])?$package_data[$key]:$package_data_core[$key];
        }

        if(!isset($package_data['ID']))
            $post_id = wp_insert_post($package_data_core);
        else {
            $post_id = $package_data['ID'];
            $package_data_core['ID'] = $post_id;
            wp_update_post($package_data_core);
        }

        foreach($package_data_meta as $key => $value){
            $value = isset($package_data[$key])?$package_data[$key]:$package_data_meta[$key];
            update_post_meta($post_id, '__wpdm_'.$key, $value);
        }

        if(isset($package_data['cats']))
            wp_set_post_terms( $post_id, $package_data['cats'], 'wpdmcategory' );

        return $post_id;
    }




} 