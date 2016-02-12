<?php
global $wpdb;
echo "<img height='30px' src='" . plugins_url('/download-manager/assets/images/wpdm-logo.png') . "' /><br/>";
?>
<link href="<?php echo plugins_url('/download-manager/assets/bootstrap/css/bootstrap.css'); ?>" rel='stylesheet' type='text/css'>
<script language="JavaScript"
        src="<?php echo plugins_url('/download-manager/assets/bootstrap/js/bootstrap.min.js'); ?>"></script>

<style type="text/css">
    .nav-tabs {
        margin-bottom: 0px !important;
    }

    .tab-content {
        padding: 10px;
        background: #fff;
        border: 1px solid #ddd;
        border-top: 0px;
        -webkit-border-bottom-right-radius: 5px;
        -webkit-border-bottom-left-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;
    }
    .tab-content * {
        font-family: 'Open Sans';
        font-size: 10pt;
        font-weight: 400;
    }

    .nav-tabs a {
        font-size: 9pt;
        font-weight: 700;
    }

    .tab-content * {
        font-size: 10pt;
        font-weight: 400;
    }

    .nav-tabs{ margin: 0 !important; }

    .tab-content{
        border: 1px solid #dddddd;
        border-top: 0;
        padding: 10px;
    }
    .tab-content{
        background: #ffffff;
    }
    .w3eden td{
        font-size: 9pt;
    }
    .w3eden .nav.nav-tabs > li > a {
        background: rgba(0, 0, 0, 0.05) none repeat scroll 0 0;
        color: #333333 !important;
        border: 1px solid #dddddd;
        border-bottom: 0 none;
        margin-right: 3px;
        border-radius: 2px 2px 0 0;
        font-size: 8pt;
        padding: 7px 15px;
        text-transform: uppercase;
    }
    .w3eden .nav.nav-tabs > li.active > a{
        background: #ffffff;
    }

</style>


<div class="w3eden">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#home"><?php _e('Summary','wpdmpro'); ?></a></li>
        <li><a href="#social"><?php _e('Social','wpdmpro'); ?></a></li>
        <li><a href="#messages"><?php _e('Messages','wpdmpro'); ?></a></li>
        <li><a href="#settings"><?php _e('News Updates','wpdmpro'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="home">
            <table class="table table-bordered table-striped" style="margin-bottom: 0px;width:100%">
                <tr>
                    <td><?php _e('Total Packages','wpdmpro'); ?></td>
                    <th><?php $packs = wp_count_posts('wpdmpro'); echo $packs->publish; ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total Downloads','wpdmpro'); ?></td>
                    <th><?php echo $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'"); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total Categories','wpdmpro'); ?></td>
                    <th><?php echo wp_count_terms('wpdmcategory'); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total Subscribers','wpdmpro'); ?></td>
                    <th><?php echo count($wpdb->get_results("select count(email) from {$wpdb->prefix}ahm_emails group by email")); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Subscribed Today','wpdmpro'); ?></td>
                    <th><?php $s = strtotime(date("Y-m-d 0:0:0"));
                        $e = time();
                        echo count($wpdb->get_results("select count(email) from {$wpdb->prefix}ahm_emails where date > $s and date < $e group by email")); ?></th>
                </tr>
            </table>
        </div>
        <div class="tab-pane" id="social">
            <table class="table table-bordered table-striped" style="margin-bottom: 0px;width:100%">
                <tr>
                    <td><?php _e('Total FB Likes','wpdmpro'); ?></td>
                    <th><?php echo get_option('wpdm_fb_likes', 0); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total Tweets','wpdmpro'); ?></td>
                    <th><?php echo get_option('wpdm_tweets', 0); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total Google +1','wpdmpro'); ?></td>
                    <th><?php echo get_option('wpdm_gplus1s', 0); ?></th>
                </tr>
                <tr>
                    <td><?php _e('Total LinkedIn Shares','wpdmpro'); ?></td>
                    <th><?php echo get_option('wpdm_linkedins', 0); ?></th>
                </tr>
            </table>
        </div>
        <div class="tab-pane" id="messages">...</div>
        <div class="tab-pane" id="settings">
            <iframe src="//cdn.wpdownloadmanager.com/notice.php?wpdmvarsion=<?php echo WPDM_Version; ?>"
                    style="height: 300px;width:100%;border:0px"></iframe>
        </div>
    </div>

    <script>
        jQuery(function () {

            jQuery('#myTab a').click(function (e) {
                e.preventDefault();
                jQuery(this).tab('show');
                jQuery(this).css('outline', 'none');
            });
        })
    </script>


</div>