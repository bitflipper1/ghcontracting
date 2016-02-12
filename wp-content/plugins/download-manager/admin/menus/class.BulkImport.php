<?php

namespace WPDM\admin\menus;


class BulkImport
{

    function __construct()
    {
        add_action("admin_init", array($this, 'Import'));
        add_action("admin_menu", array($this, 'Menu'));
        add_action("wp_ajax_wpdm_dimport", array($this, 'importDirFile'));

    }

    function Menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __('Bulk Import &lsaquo; Download Manager', "wpdmpro"), __('Bulk Import', "wpdmpro"), WPDM_MENU_ACCESS_CAP, 'importable-files', array($this, 'UI'));
    }

    public static function UI(){

        if (isset($_POST['wpdm_importdir'])) update_option('wpdm_importdir', $_POST['wpdm_importdir']);
        $scan = @scandir(get_option('wpdm_importdir', false));
        $k = 0;
        if ($scan) {
            foreach ($scan as $v) {
                if ($v == '.' or $v == '..' or @is_dir(get_option('wpdm_importdir') . $v)) continue;

                $fileinfo[$k]['file'] = get_option('wpdm_importdir') . $v;
                $fileinfo[$k]['name'] = $v;
                $k++;
            }
        }

        include(WPDM_BASE_DIR . 'admin/tpls/import.php');


    }



    /**
     * @usage Import CSV File
     */
    function Import()
    {
        global $wpdb;

        if (!isset($_GET['task']) || $_GET['task'] != 'wpdm-import-csv' || !current_user_can(WPDM_ADMIN_CAP)) return;

        if (! ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }
        check_license();
        $max_line_length = 10000;
        $source_file = $_FILES['csv']['tmp_name'];
        $alldata = file_get_contents($source_file);
        $alldata = str_replace("\r","\n",$alldata);
        $alldata = str_replace("\n\n","\n",$alldata);
        $alldata = str_getcsv($alldata, "\n");

        if (is_array($alldata)) {

            foreach ($alldata as &$data) {
                $data = str_getcsv($data, ",");
            }

            $columns = array_shift($alldata);

            foreach ($alldata as $idx => $adata) {

                $adata[0] = trim($adata[0]);
                while (count($adata) < count($columns))
                    array_push($adata, NULL);
                $values = quote_all_array($adata);
                $drow = array_combine($columns, $values);
                if (isset($drow['url_key']))
                    unset($drow['url_key']);
                $drow['files'] = explode(',', $drow['files']);
                $drow['category'] = explode(',', $drow['category']);
                $drow['create_date'] = isset($drow['create_date']) ?  date("Y-m-d H:i:s", strtotime($drow['create_date'])) : date("Y-m-d H:i:s",time());
                $drow['update_date'] = isset($drow['update_date']) ? date("Y-m-d H:i:s", strtotime($drow['update_date'])) : date("Y-m-d H:i:s",time());
                $access = explode(",", $drow['access']);
                $drow['access'] = isset($drow['access']) && $drow['access'] != '' ? $access : array('guest');

                if (!isset($drow['ID'])) {

                    $postdata = array(
                        'post_title' => $drow['title'],
                        'post_content' => utf8_encode($drow['description']),
                        'post_date' => $drow['create_date'],
                        'post_modified' => $drow['update_date'],
                        'post_type' => 'wpdmpro',
                        'post_status' => 'publish',
                        'filter' => false
                    );
                    
                    $post_id = wp_insert_post($postdata);

                    foreach($drow['category'] as $index => $term){
                        if((int)$term > 0) $term = (int)$term;
                        if(term_exists($term, 'wpdmcategory')){
                            $eterm = term_exists($term, 'wpdmcategory');
                            $drow['category'][$index] = $eterm['term_id'];
                        }
                        else {
                            $tinf =  wp_insert_term($term, 'wpdmcategory');
                            if(is_array($tinf) && isset($tinf['term_id']))
                                $drow['category'][$index] = $tinf['term_id'];

                        }
                    }
                    $ret = wp_set_post_terms($post_id, $drow['category'], 'wpdmcategory' );
                } else {
                    $post_id = $drow['ID'];
                }
                if (isset($drow['title']))
                    unset($drow['title']);
                if (isset($drow['description']))
                    unset($drow['description']);
                if (isset($drow['create_date']))
                    unset($drow['create_date']);

                if(isset($drow['additional_previews']) && $drow['additional_previews']!='') {
                    $drow['additional_previews'] = explode(",", $drow['additional_previews']);
                }
                if(isset($drow['preview']) && $drow['preview']!='') {
                    $mime_type = '';
                    $wp_filetype = wp_check_filetype(basename($drow['preview']), null);
                    if (isset($wp_filetype['type']) && $wp_filetype['type'])
                        $mime_type = $wp_filetype['type'];
                    unset($wp_filetype);
                    $attachment = array(
                        'post_mime_type' => $mime_type,
                        'post_parent' => $post_id,
                        'post_title' => basename($drow['preview']),
                        'post_status' => 'inherit'
                    );
                    $attachment_id = wp_insert_attachment($attachment, $drow['preview'], $post_id);
                    unset($attachment);

                    if (!is_wp_error($attachment_id)) {
                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $drow['preview']);
                        wp_update_attachment_metadata($attachment_id, $attachment_data);
                        unset($attachment_data);
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                    unset($drow['preview']);
                }


                foreach ($drow as $meta_key => $value) {
                    update_post_meta($post_id, "__wpdm_".$meta_key, $value);
                }

                do_action('after_add_package', $post_id, $drow);
            }

        }
        @unlink($source_file);
        header("location: edit.php?post_type=wpdmpro");
        die();
    }

    function importDirFile()
    {
        global $wpdb;
        if(!current_user_can(WPDM_ADMIN_CAP)) die('Error!');
        //array_shift($flds);
        $fileinf = array();
        $files = array($_POST['fname']);
        $fileinf['access'] = $_POST['access'];
        if (isset($_POST['password']) && $_POST['password'] != '') {
            $fileinf['password_lock'] = 1;
            $fileinf['password'] = $_POST['password'];

        }
        $fileinf['files'] = $files;
        $post_id = wp_insert_post(array(
            'post_title' => esc_attr($_POST['title']),
            'post_content' => esc_attr($_POST['description']),
            'post_type' => 'wpdmpro',
            'post_status' => 'publish'
        ));
        wp_set_post_terms($post_id, $_POST['category'], 'wpdmcategory');
        foreach ($fileinf as $meta_key => $value) {
            update_post_meta($post_id, "__wpdm_" . $meta_key, $value);
        }

        if(file_exists(UPLOAD_DIR.$_POST['fname']) && get_option('__wpdm_overwrrite_file',0)==1){
            @unlink(UPLOAD_DIR.$_POST['fname']);
        }
        if(file_exists(UPLOAD_DIR.$_POST['fname']))
            $filename = time().'wpdm_'.$_POST['fname'];
        else
            $filename = $_POST['fname'];

        copy(get_option('wpdm_importdir') . $_POST['fname'], UPLOAD_DIR . '/' . $filename);
        do_action('after_add_package', $post_id, $fileinf);
        //@unlink(dirname(__FILE__).'/imports/'.$_POST['fname']);
        die('Done!');
    }

}

