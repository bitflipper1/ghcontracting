<?php
/*
Plugin Name: WPDM - Extended Short-codes
Description: WordPress Download Manager Pro Extended Short-Codes
Plugin URI: http://www.wpdownloadmanager.com/
Author: Shaon
Version: 2.5.0
Author URI: http://www.wpdownloadmanager.com/
*/


if (defined('WPDM_Version')) {


    function wpdm_tree($params = array())
    {
        $treejs = plugins_url() . '/wpdm-extended-shortcodes/js/jqueryFileTree.js';
        $treecss = plugins_url() . '/wpdm-extended-shortcodes/css/jqueryFileTree.css';
        $siteurl = site_url();
        @extract($params);
        $category = isset($category) ? get_term_by("slug", $category, 'wpdmcategory') : null;
        $category = $category ? $category->term_id : '/';
        $download_link = isset($download_link) ? (int)$download_link : 0;
        $tid = uniqid();
        $data = <<<TREE
    <script language="JavaScript" src="{$treejs}"></script>     
    <link rel="stylesheet" href="{$treecss}" />          
    <div id="tree{$tid}"></div>
    <script language="JavaScript">

      jQuery(document).ready( function() {
            jQuery('#tree{$tid}').fileTree({
                script: '{$siteurl}/?task=wpdm_tree&ddl={$download_link}',
                expandSpeed: 1000,
                collapseSpeed: 1000,
                root: '{$category}',
                multiFolder: false
            }, function(file) {

               location.href=    file;
            });
            
            
      });

    </script>    
TREE;

        return str_replace(array("\n","\r"),"", $data);
    }

    function wpdm_slider($params = array())
    {
        $ids = array();
        $n = 0;
        extract($params);
        $ids = explode(",", $ids);
        ob_start();
        ?>
        <div class="w3eden">
            <div id="myCarousel" class="carousel slide">
                <!--<ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>-->
                <!-- Carousel items -->
                <div class="carousel-inner">
                    <?php
                    foreach ($ids as $id) {
                        $file = wpdm_get_package($id);
                        ?>
                        <div class="<?php if ($n++ == 0) echo 'active'; ?> item">
                            <img alt="" src='<?php echo wpdm_dynamic_thumb($file['preview'], array(1000, 400)); ?>'/>

                            <div class="carousel-caption">
                                <div class="media">
                                    <div class="media-body">
                                        <h4><?php echo $file['title']; ?></h4>

                                        <p><?php echo substr(strip_tags($file['post_content']), 0, 100); ?>...</p>
                                        <a class="btn btn-bordered btn-gpls" style="color: #fff"
                                           href="<?php echo $file['page_url']; ?>">More Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <a class="carousel-control left" href="#myCarousel" data-slide="prev"><span
                        class="fa fa-chevron-left"></span></a>
                <a class="carousel-control right" href="#myCarousel" data-slide="next"><span
                        class="fa fa-chevron-right"></span></a>
            </div>
        </div>
        <style>
            .carousel-inner {
                border-radius: 6px !important;
            }

            #myCarousel .carousel-control {
                border-radius: 0 !important;
                border: 0 !important;
                background: transparent !important;
                padding: 10px 5px 18px 5px;
            }

            #myCarousel .carousel-control .fa {
                top: 40%;
                position: absolute;
            }

            .carousel-caption {
                background: rgba(0, 0, 0, 0.6);
                border-radius: 0 !important;
                bottom: 0 !important;
                color: #FFFFFF;
                padding-top: 20px;
                position: absolute;
                text-align: center;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
                width: 100%;
                z-index: 10;
                left: 0 !important;
            }

            .btn-bordered {
                background: transparent !important;
                color: #ffffff !important;
                border: 1px solid rgba(255, 255, 255, 0.7) !important;
                border-radius: 5px !important;
            }
        </style>
        <script>
            jQuery(function ($) {
                $('.carousel').carousel();
            });
        </script>
        <?php
        $data = ob_get_contents();
        ob_clean();
        return $data;
    }

    function wpdm_carousel($params = array())
    {

        if (is_array($params))
            extract($params);
        if (isset($category)) {
            if(intval($category) == 0 && $category != '')
                $cat = get_term_by('slug', $category, 'wpdmcategory');
            else
                $cat = get_term_by('term_id', $category, 'wpdmcategory');
        }
        ob_start();

        ?>

        <div class="w3eden">
        <div class="well">
            <div class="row">
                <div class="col-md-12">
                    <h3 style="line-height: normal;margin: 0px;float:left;">

                    <?php echo isset($category) ? $cat->name : 'New Downloads'; ?>

                    </h3>

                    <div class="pull-right" style="margin-top: -5px;">
                        <a class="btn btn-xs btn-inverse btn-transparent" href="#myCarousel1" data-slide="prev"><i
                                class="fa fa-white fa-chevron-left"></i></a>
                        <a class="btn btn-xs btn-inverse btn-transparent" href="#myCarousel1" data-slide="next"><i
                                class="fa fa-white fa-chevron-right"></i></a>
                        <?php if (isset($category)): ?><a class="btn btn-xs btn-inverse btn-transparent"
                                                          title='View All'  href="<?php echo get_term_link($cat); ?>"
                                                          data-slide="next"><i class="fa fa-white fa-th"></i>
                            </a><?php endif; ?>
                    </div>
                    <hr style="clear: both;margin-top: 25px"/>

                </div>

            </div>
            <div id="myCarousel1" class="carousel slide">
                <!--<ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>-->
                <!-- Carousel items -->
                <div class="carousel-inner">
                    <div class="active item">
                        <div class="row">
                            <?php
                            $params = array(
                                'post_type' => 'wpdmpro',
                                'posts_per_page' => 4
                            );
                            if (isset($category))
                                $params['tax_query'] = array(array(
                                    'taxonomy' => 'wpdmcategory',
                                    'field' => 'slug',
                                    'terms' => array($cat->slug)
                                ));
                            $packs = get_posts($params);
                            foreach ($packs as $file) {


                                ?>
                                <div class="col-md-3">
                                    <figure class="rift">
                                        <?php wpdm_thumb($file->ID, array(300, 200)); ?>


                                        <figcaption class="caption"><a
                                                href='<?php echo get_permalink($file->ID); ?>'><?php echo $file->post_title; ?></a>
                                        </figcaption>


                                    </figure>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="item">
                        <div class="row">
                            <?php
                            $params = array(
                                'post_type' => 'wpdmpro',
                                'offset' => 4,
                                'posts_per_page' => 4
                            );
                            if (isset($category))
                                $params['tax_query'] = array(array(
                                    'taxonomy' => 'wpdmcategory',
                                    'field' => 'slug',
                                    'terms' => array($category)
                                ));
                            $packs = get_posts($params);
                            foreach ($packs as $file) {

                                ?>
                                <div class="col-md-3">
                                    <figure class="rift">
                                        <?php wpdm_thumb($file->ID, array(300, 200)); ?>


                                        <figcaption class="caption"><a
                                                href='<?php echo get_permalink($file->ID); ?>'><?php echo $file->post_title; ?></a>
                                        </figcaption>


                                    </figure>


                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <style>
            .carousel-control {
                border-radius: 0 !important;
                border: 0 !important;
                background: rgba(30, 96, 158, 0.8);
                padding: 10px 5px 18px 5px;
            }

            /* Base plugin styles */

            .rift {

                position: relative;
                overflow: hidden;
                backface-visibility: hidden;
                border-radius: 3px !important;
            }

            .rift img {
                width: 100%;
                height: auto;
                opacity: 0;
            }

            .rift .caption {
                position: absolute;
                top: 50%;
                width: 100%;
                height: 60px; /* Define caption height */
                line-height: 60px; /* Define matched line-height */
                margin: -30px 0 0 0; /* Half caption height */
                text-align: center;
                z-index: 0;
            }

            .rift span[class*="span"] {
                display: block;
                width: 100%;
                height: 50%;
                overflow: hidden;
                position: absolute;
                left: 0;
                z-index: 1;
                transform: translate3d(0, 0, 0); /* Acceleration FTW */
                transition: transform .25s; /* Define anim. speed */
            }

            .rift span.top-span {
                top: 0;
            }

            .rift span.btm-span {
                bottom: 0;
            }

            .rift:hover span.top-span {
                transform: translate(0, -30px); /* Half caption height */
            }

            .rift:hover > span.btm-span {
                transform: translate(0, 30px); /* Half caption height */
            }

            /* Non-plugin styles */

            .rift {
                display: inline-block;
                cursor: pointer;
            }

            .rift .caption {
                color: #ffffff;
                background: rgba(0, 0, 0, 0.57);
            }

            .rift .caption a {
                color: #ffffff;
                font-weight: bold;
            }

        </style>
        <script>
            jQuery(function ($) {
                $('#myCarousel1').carousel();
            });

            /**
             * Rift v1.0.0
             * An itsy bitsy image-splitting jQuery plugin
             *
             * Licensed under the MIT license.
             * Copyright 2013 Kyle Foster @hkfoster
             */
            ;
            (function ($, window, document, undefined) {

                $.fn.rift = function () {

                    return this.each(function () {

                        // Vurribles
                        var element = $(this),
                            elemImg = element.find('img'),
                            imgSrc = elemImg.attr('src');

                        // We be chainin'
                        element
                            .prepend('<span class="top-span"></span>')
                            .append('<span class="btm-span"></span>')
                            .find('span.top-span')
                            .css('background', 'url(' + imgSrc + ') no-repeat center top')
                            .css('background-size', '100%')
                            .parent()
                            .find('span.btm-span')
                            .css('background', 'url(' + imgSrc + ') no-repeat center bottom')
                            .css('background-size', '100%');
                    });
                };
            })(jQuery, window, document);

            jQuery('.rift').rift();
        </script>
        <?php
        $data = ob_get_clean();

        return $data;
    }


    function wpdm_embed_tree()
    {
        if (wpdm_query_var('task', 'txt') != 'wpdm_tree') return;
        global $wpdb;
        $cats = get_terms('wpdmcategory', array('hide_empty' => false));

        //print_r($cats);

        echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
        // All Cats
        $scat = wpdm_query_var('dir') == '/' ? '' : wpdm_query_var('dir', 'txt');

        foreach ($cats as $id => $cat) {
            if ($cat->parent == intval($scat))
                echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . $cat->term_id . "\">" . $cat->name . "</a></li>";

        }

        // All files
        $params = array(
            'post_type' => 'wpdmpro',
            'posts_per_page' => 9999
        );

        if($scat==''){
            $params['tax_query'] = array(
                array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => get_terms( 'wpdmcategory', array( 'fields' => 'ids'  ) ),
                    'operator' => 'NOT IN',
                )
            );
        } else {
            $params['tax_query'] = array(
                array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => $scat,
                    'include_children' => false
                )
            );
        }


        $packs = new WP_Query($params);


        while ($packs->have_posts()) {
            $packs->the_post();

            $files = maybe_unserialize(get_post_meta(get_the_ID(), '__wpdm_files', true));
            if (count($files) == 1) {
                $tfiles = $files;
                $file = array_shift($tfiles);
                $ext = explode(".", $file);
                $ext = end($ext);
            }
            if (count($files) > 1) {
                $ext = 'zip';
            }
            if (!is_array($files) || count($files) == 0) {
                $ext = '_blank';
            }

            if(wpdm_user_has_access(get_the_ID())) {
                if (wpdm_query_var('ddl', 'int') == 0 || wpdm_is_locked(get_the_ID()))
                    echo "<li  class=\"file ext_$ext\"><a href='" . get_permalink(get_the_ID()) . "' rel='" . get_permalink(get_the_ID()) . "'>" . get_the_title() . "</a></li>";
                else
                    echo "<li  class=\"file ext_$ext\"><a href='" . wpdm_download_url(get_the_ID()) . "' rel='" . wpdm_download_url(get_the_ID()) . "'>" . get_the_title() . "</a></li>";
            }


        }
        echo "</ul>";
        die();


    }

    function wpdm_popup_link_tag($vars)
    {
        $vars['popup_link'] = "<a class='wpdm-popup-link' data-title='".$vars['title']."'  data-toggle='modal' data-target='#wpdm-popup-link' href='" . get_permalink($vars['ID']) . "'>" . $vars['title'] . "</a>";
        return $vars;
    }

    function wpdm_popup_link()
    {
        //return;
        ?>
        <div class="w3eden">
            <div id="wpdm-popup-link" class="modal fade">
                <div class="modal-dialog" style="width: 750px">
                    <div class="modal-content">
                        <div class="modal-header">
                              <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body" id='wpdm-modal-body'>
                            <p><a href="http://www.wpdownloadmanager.com/">WordPress Download Manager - Best Download Management Plugin</a></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->


        </div>
        <script language="JavaScript">
            <!--
            jQuery(function () {
                //jQuery('#wpdm-popup-link').modal('hide');
                jQuery('.wpdm-popup-link').click(function (e) {
                    e.preventDefault();
                    jQuery('#wpdm-popup-link .modal-title').html(jQuery(this).data('title'));
                    jQuery('#wpdm-modal-body').html('<i class="icon"><img align="left" style="margin-top: -1px" src="<?php echo plugins_url('/download-manager/images/loading-new.gif'); ?>" /></i>&nbsp;Please Wait...');
                    jQuery('#wpdm-popup-link').modal('show');
                    jQuery('#wpdm-modal-body').load(this.href,{mode:'popup'});
                    return false;
                });
            });
            //-->
        </script>
        <style type="text/css">
            #wpdm-modal-body img {
                max-width: 100% !important;
            }
        </style>
    <?php
    }

    function wpdm_popup_data(){
        if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'popup'){
            global $post;
            $template = get_post_meta($post->ID,'__wpdm_page_template', true);
            echo FetchTemplate($template, $post->ID, 'page');
            die();
        }
    }

    function wpdm_extsc_generate(){
        ?>
     <div class="panel panel-default">
         <div class="panel-heading">Tree View</div>
         <div class="panel-body">
             <?php wpdm_dropdown_categories('c',0, 'scc'); ?>

             <label><input type="checkbox" id="tvddl"> Direct Download Link</label>

             <button class="btn btn-primary btn-sm" id="tvw">Insert to Post</button>
             <script>
                 jQuery('#tvw').click(function(){

                     var cats = jQuery('#scc').val()!='-1'?' category="' + jQuery('#scc').val() + '" ':'';
                     var tvddl = jQuery('#tvddl').prop('checked')?' download_link=1':'';
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_tree' + cats + tvddl + ']');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>

         </div>
         <div class="panel-heading">Carousel</div>
         <div class="panel-body">
             <?php wpdm_dropdown_categories('c',0, 'scc1'); ?>  <button class="btn btn-primary btn-sm" id="crs">Insert to Post</button>
             <script>
                 jQuery('#crs').click(function(){
                     if(jQuery('#pids').val()=='-1'){
                         alert("Select Category!");
                         return false;
                     }
                     cats = jQuery('#scc1').val()!='-1'?' category="' + jQuery('#scc1').val() + '" ':'';
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_carousel' + cats + ']');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>
         </div>
         <div class="panel-heading">Slider</div>
         <div class="panel-body">
             <input type="text" id="pids" placeholder="Package IDs separated by comma" style="width: 250px;display: inline" class="form-control input-sm" value="" />
             <button class="btn btn-primary btn-sm" id="sld">Insert to Post</button>
             <script>
                 jQuery('#sld').click(function(){
                     if(jQuery('#pids').val()==''){
                         alert("Enter package ids separate by comma!");
                         return false;
                     }
                     var win = window.dialogArguments || opener || parent || top;
                     win.send_to_editor('[wpdm_slider ids="'+jQuery('#pids').val()+'"]');
                     tinyMCEPopup.close();
                     return false;
                 });
             </script>
         </div>
     </div>
    <?php
    }



    add_action('init', 'wpdm_embed_tree');
    add_action('wpdm_ext_shortcode', 'wpdm_extsc_generate');

    add_shortcode('wpdm_tree', 'wpdm_tree');
    add_shortcode('wpdm_slider', 'wpdm_slider');
    add_shortcode('wpdm_carousel', 'wpdm_carousel');
    add_filter('wdm_before_fetch_template', 'wpdm_popup_link_tag');

    add_filter('wp_footer', 'wpdm_popup_link');
    add_action("wp", "wpdm_popup_data");

//add_wdm_settings_tab('extende-sc', 'Short-codes', 'wpdm_extended_shortcode_settings');
}