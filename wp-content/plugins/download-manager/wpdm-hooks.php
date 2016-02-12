<?php


if (is_admin()) {


    add_action('wp_ajax_wpdm-activate-shop', 'wpdm_activate_shop');
    add_action('wp_ajax_wpdm-install-addon', 'wpdm_install_addon');

    add_action('wp_ajax_wpdm_generate_password', 'wpdm_generate_password');

    add_filter("wpdm_export_custom_form_fields", 'wpdm_export_custom_form_fields');
    add_action("wpdm_custom_form_field", 'wpdm_ask_for_custom_data');
    add_action('wp_ajax_wpdm_check_update', 'wpdm_check_update');
    add_action('admin_footer', 'wpdm_newversion_check');






} else {

    /** Short-Codes */

    add_shortcode("wpdm_file", "wpdm_package_link_legacy");
    add_shortcode("wpdm_category", "wpdm_category");

    add_shortcode('wpdm-email-2download', 'wpdm_email_2download');
    add_shortcode('wpdm-plus1-2download', 'wpdm_plus1_2download');
    add_shortcode('wpdm-like-2download', 'wpdm_like_2download');
    add_shortcode('wpdm-tweet-2download', 'wpdm_tweet_2download');
    add_shortcode('wpdm-lishare-2download', 'wpdm_lishare_2download');

    /** Actions */

    add_action("init", 'wpdm_view_countplus');
    add_action("wp_footer", 'wpdm_searchable_filelist',999999);

    add_action("wp", "wpdm_ajax_call_exec");
    add_action('wp', 'wpdm_print_file_list');
    add_action('wpdm_user_logged_in', 'wpdm_user_logged_in');

    /** Filters */

    add_filter('the_content', 'wpdm_downloadable');
    add_filter('the_excerpt', 'wpdm_archive_page_template');
    add_filter('the_content', 'wpdm_archive_page_template');

    add_filter("wpdm_render_custom_form_fields", 'wpdm_render_custom_data', 10, 2);


    add_action('init', 'wpdm_check_invpass');
    add_filter('wp_footer', 'wpdm_footer_codes');

    // Tags



}


add_filter('run_ngg_resource_manager', '__return_false');



