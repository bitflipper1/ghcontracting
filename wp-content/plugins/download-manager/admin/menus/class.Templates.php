<?php

namespace WPDM\admin\menus;


class Templates
{

    function __construct()
    {
        add_action('admin_init', array($this, 'Save'));
        add_action('wp_ajax_template_preview', array($this, 'Preview'));
        add_action('admin_menu', array($this, 'Menu'));
    }

    function Menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __('Templates &lsaquo; Download Manager', "wpdmpro"), __('Templates', "wpdmpro"), WPDM_MENU_ACCESS_CAP, 'templates', array($this, 'UI'));
    }

    function UI(){
        $ttype = isset($_GET['_type']) ? $_GET['_type'] : 'link';

        if (isset($_GET['task']) && ($_GET['task'] == 'EditTemplate' || $_GET['task'] == 'NewTemplate'))
            \WPDM\admin\menus\Templates::Editor();
        else
            \WPDM\admin\menus\Templates::Show();
    }


    public static function Editor(){
        include(WPDM_BASE_DIR . "admin/tpls/template-editor.php");
    }


    public static function Show(){
        include(WPDM_BASE_DIR . "admin/tpls/templates.php");
    }


    /**
     * @usage Save Link/Page Templates
     */
    function Save()
    {
        if (!isset($_GET['page']) || $_GET['page'] != 'templates') return;
        $ttype = isset($_GET['_type']) ? $_GET['_type'] : 'link';
        if (isset($_GET['task']) && $_GET['task'] == 'DeleteTemplate') {
            $tpldata = maybe_unserialize(get_option("_fm_{$ttype}_templates"));
            if (!is_array($tpldata)) $tpldata = array();
            unset($tpldata[$_GET['tplid']]);
            update_option("_fm_{$ttype}_templates", @serialize($tpldata));

            header("location: edit.php?post_type=wpdmpro&page=templates&_type=$ttype");
            die();
        }

        if (isset($_POST['tpl'])) {
            if (is_array(get_option("_fm_{$ttype}_templates")))
                $tpldata = (get_option("_fm_{$ttype}_templates"));
            else
                $tpldata = maybe_unserialize(get_option("_fm_{$ttype}_templates"));
            if (!is_array($tpldata)) $tpldata = array();
            $tpldata[$_POST['tplid']] = $_POST['tpl'];
            update_option("_fm_{$ttype}_templates", @serialize($tpldata));

            header("location: edit.php?post_type=wpdmpro&&page=templates&_type=$ttype");
            die();
        }
    }

    /**
     * @usage Preview link/page template
     */
    function Preview()
    {
        error_reporting(0);

        $wposts = array();

        $template = wpdm_query_var("template","html");
        $type = wpdm_query_var("_type","html");


        $args=array(
            'post_type'=>'wpdmpro',
            'posts_per_page'=>1
        );

        $wposts = get_posts( $args  );

        $html = "";

        foreach( $wposts as $p ) {

            $package = (array)$p;

            $html .= FetchTemplate($template, $package, $type);

        }

        if(count($wposts)==0) $html = "<div class='col-md-12'><div class='alert alert-info'>".__('No package found! Please create at least 1 package to see template preview','wpdmpro')."</div> </div>";
        $html = "<div class='w3eden'>".$html."</div><div style='clear:both'></div>";

        echo $html;
        die();

    }

    public static function Dropdown($params)
    {
        extract($params);
        $type = isset($type) ? $type : 'link';
        $ttpldir = get_stylesheet_directory() . '/download-manager/' . $type . '-templates/';
        $ttpls = array();
        if(file_exists($ttpldir)) {
            $ttpls = scandir($ttpldir);
            array_shift($ttpls);
            array_shift($ttpls);
        }

        $ltpldir = WPDM_BASE_DIR . '/tpls/' . $type . '-templates/';
        $ctpls = scandir($ltpldir);
        array_shift($ctpls);
        array_shift($ctpls);

        foreach($ctpls as $ind => $tpl){
            $ctpls[$ind] = $ltpldir.$tpl;
        }

        foreach($ttpls as $tpl){
            if(!in_array($ltpldir.$tpl, $ctpls))
                $ctpls[] = $ttpldir.$tpl;
        }

        $custom_templates = maybe_unserialize(get_option("_fm_{$type}_templates",true));

        $name = isset($name)?$name:$type.'_template';
        $css = isset($css)?"style='$css'":'';
        $id = isset($id)?$id:uniqid();
        $default = $type == 'link'?'link-template-calltoaction3.php':'page-template-1col-flat.php';
        $html = "<select name='$name' id='$id' class='form-control template {$type}_template' {$css}><option value='$default'>Select ".ucfirst($type)." Template</option>";
        $data = array();
        foreach ($ctpls as $ctpl) {
            $tmpdata = file_get_contents($ctpl);
            $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
            if (preg_match($regx, $tmpdata, $matches)) {
                $data[basename($ctpl)] = $matches[1];
                $eselected = isset($selected) && $selected == basename($ctpl) ? 'selected=selected':'';
                $html .= "<option value='".basename($ctpl)."' {$eselected}>{$matches[1]}</option>";
            }
        }
        if(is_array($custom_templates)) {
            foreach ($custom_templates as $id => $template) {
                $data[$id] = $template['title'];
                $eselected = isset($selected) && $selected == $id ? 'selected=selected' : '';
                $html .= "<option value='{$id}' {$eselected}>{$template['title']}</option>";
            }
        }
        $html .= "</select>";
        return isset($data_type) && $data_type == 'ARRAY'? $data : $html;
    }
}