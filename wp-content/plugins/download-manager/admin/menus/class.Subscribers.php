<?php

namespace WPDM\admin\menus;


class Subscribers
{

    function __construct()
    {
        add_action( "admin_init", array($this, 'ExportEmails') );
        add_action( "admin_init", array($this, 'deleteEmails') );
        add_action( "admin_init", array($this, 'saveMailTemplate') );
        add_action( "admin_menu", array($this, 'Menu') );
    }

    function Menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __('Subscribers &lsaquo; Download Manager', "wpdmpro"), __('Subscribers', "wpdmpro"), WPDM_MENU_ACCESS_CAP, 'emails', array($this, 'UI'));
    }


    function UI()
    {
        if (isset($_GET['task']) && $_GET['task'] == 'template')
            \WPDM\admin\menus\Subscribers::mailTemplates();
        else
            \WPDM\admin\menus\Subscribers::Emails();
    }

    public static function Emails(){
        include(WPDM_BASE_DIR . "admin/tpls/subscribers.php");
    }

    public static function mailTemplates(){
        include(WPDM_BASE_DIR . "admin/tpls/emails-template.php");
    }


    function exportEmails()
    {
        global $wpdb;
        if(!current_user_can(WPDM_ADMIN_CAP)) return;
        $task = isset($_GET['task']) ? $_GET['task'] : '';
        if ($task == 'export' && isset($_GET['page']) && $_GET['page'] == 'emails') {
            $custom_fields = array();
            $csv = '';
            $custom_fields = apply_filters('wpdm_export_custom_form_fields', $custom_fields);
            $res = $wpdb->get_results("select e.* from {$wpdb->prefix}ahm_emails e order by id desc", ARRAY_A);
            if (isset($_GET['uniq']) && $_GET['uniq'] == 1)
                $res = $wpdb->get_results("select email,custom_data from {$wpdb->prefix}ahm_emails group by email", ARRAY_A);
            $csv .= "\"package\", \"email\", \"" . implode("\", \"", $custom_fields) . "\", \"date\"\r\n";
            foreach ($res as $row) {
                $data = array();
                $data['package'] = get_the_title($row['pid']);
                $data['email'] = $row['email'];
                $cf_data = unserialize($row['custom_data']);
                foreach ($custom_fields as $c) {
                    $data[$c] = isset($cf_data[$c])?$cf_data[$c]:"";
                    if(is_array($data[$c])) $data[$c] = implode(", ", $data[$c]);
                }
                $data['date'] = isset($row['date'])?date("Y-m-d H:i", $row['date']):"";
                $csv .= '"' . @implode('","', $data) . '"' . "\r\n";
            }
            header("Content-Description: File Transfer");
            header("Content-Type: text/csv; charset=UTF-8");
            header("Content-Disposition: attachment; filename=\"emails.csv\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . strlen($csv));
            echo $csv;
            die();
        }
    }


    function deleteEmails()
    {
        global $wpdb;
        if(!current_user_can(WPDM_ADMIN_CAP)) return;
        $task = isset($_GET['task']) ? $_GET['task'] : '';
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if ($task == 'delete' && $page == 'emails') {
            $ids = implode(",", $_POST['id']);
            $wpdb->query("delete from {$wpdb->prefix}ahm_emails where id in ($ids)");
            header("location: edit.php?post_type=wpdmpro&page=emails");
            die();
        }
    }


    function saveMailTemplate()
    {
        if(!current_user_can(WPDM_ADMIN_CAP)) return;
        if (isset($_POST['task']) && $_POST['task'] == 'save-etpl') {
            update_option('_wpdm_etpl', $_POST['et']);
            header("location: edit.php?post_type=wpdmpro&page=emails&task=template");
            die();
        }

    }

}