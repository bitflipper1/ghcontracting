<?php
namespace WPDM;

class AuthorDashboard
{
    function __construct(){

        add_shortcode("wpdm_frontend", array($this, 'Dashboard'));
        add_action('wp_ajax_delete_package_frontend', array($this, 'deletePackage'));
        add_action('wp_ajax_wpdm_frontend_file_upload', array($this, 'uploadFile'));
    }

    /**
     * @usage Short-code function for front-end UI
     * @return string
     */
    function Dashboard()
    {

        global $current_user;
        wp_reset_query();
        $currentAccess = maybe_unserialize(get_option('__wpdm_front_end_access', array()));

        $task = get_query_var('adb_page');

        $task = explode("/", $task);

        if($task[0] == 'edit-package') $pid = $task[1];
        if($task[0] == 'page') { $task[0] = ''; set_query_var('paged', $task[1]); }
        $task = $task[0];

        if (!array_intersect($currentAccess, $current_user->roles) && is_user_logged_in())
            return "<div class='w3eden'><div class='alert alert-danger'>" . wpautop(stripslashes(get_option('__wpdm_front_end_access_blocked', __('Sorry, Your Are Not Allowed!','wpdmpro')))) . "</div></div>";

        $id = wpdm_query_var('ID');


        $tabs = array( //'sales' => array('label'=>'Sales','callback'=>'wpdm_sales_report')
        );
        $tabs = apply_filters('wpdm_frontend', $tabs);
        $burl = get_permalink();
        $sap = strpos($burl, '?') ? '&' : '?';
        ob_start();
        include \WPDM\Template::Locate('author-dashboard.php');
        $data = ob_get_clean();

        return $data;
    }

    /**
     * @usage Delete package from front-end
     */
    function deletePackage()
    {
        global $wpdb, $current_user;
        if (isset($_GET['ID']) && intval($_GET['ID'])>0) {
            $id = (int)$_GET['ID'];
            $uid = $current_user->ID;
            if ($uid == '') die('Error! You are not logged in.');
            $post = get_post($id);
            if($post->post_author==$uid)
                wp_delete_post($id, true);
            echo "deleted";
            die();
        }
    }

    /**
     * @usage Upload files
     */
    function uploadFile(){

        global $current_user;

        $currentAccess = maybe_unserialize(get_option( '__wpdm_front_end_access', array()));
        // Check if user is authorized to upload file from front-end
        if(!is_user_logged_in() || !array_intersect($currentAccess, $current_user->roles) ) die('Error!');

        check_ajax_referer('frontend-file-upload');
        if(file_exists(UPLOAD_DIR.$_FILES['async-upload']['name']) && get_option('__wpdm_overwrite_file_frontend',0)==1){
            @unlink(UPLOAD_DIR.$_FILES['async-upload']['name']);
        }
        if(file_exists(UPLOAD_DIR.$_FILES['async-upload']['name']))
            $filename = time().'wpdm_'.$_FILES['async-upload']['name'];
        else
            $filename = $_FILES['async-upload']['name'];
        move_uploaded_file($_FILES['async-upload']['tmp_name'],UPLOAD_DIR.$filename);
        //@unlink($status['file']);
        echo $filename;
        exit;
    }



}