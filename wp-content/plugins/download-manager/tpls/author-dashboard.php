<div class="w3eden">
    <?php if (is_user_logged_in()) { ?>

    <ul id="tabs" class="nav nav-pills nav-justified wpdm-frontend-tabs">
        <li><a class="<?php if ($task == '' || $task == 'edit-package') { ?>active<?php } ?>" href="<?php echo $burl; ?>"><?php _e('All Items','wpdmpro'); ?></a></li>
        <li><a class="<?php if ($task == 'add-new') { ?>active<?php } ?>" href="<?php echo $burl; ?>add-new/"><?php _e('Add New','wpdmpro'); ?></a></li>
        <?php foreach ($tabs as $tid => $tab): ?>
            <li><a class="<?php if ($task == $tid) { ?>active<?php } ?>" href="<?php echo $burl.$tid; ?>/"><?php echo $tab['label']; ?></a></li>
        <?php endforeach; ?>
        <li><a class="<?php if ($task == 'edit-profile') { ?>active<?php } ?>" href="<?php echo $burl; ?>edit-profile/"><?php _e('Edit Profile','wpdmpro'); ?></a></li>
        <li><a class="" href="<?php echo $burl; ?>logout/"><?php _e('Logout','wpdmpro'); ?></a></li>
    </ul>

    <div class="tab-content" style="border: 0;padding: 0">
<?php }
if (is_user_logged_in()) {

    if ($task == 'add-new' || $task == 'edit-package')
        include(wpdm_tpl_path('wpdm-add-new-file-front.php'));
    else if ($task == 'edit-profile')
        include(wpdm_tpl_path('wpdm-edit-user-profile.php'));
    else if ($task != '' && isset($tabs[$task]['callback']) && $tabs[$task]['callback'] != '')
        call_user_func($tabs[$task]['callback']);
    else if ($task != '' && isset($tabs[$task]['shortcode']) && $tabs[$task]['shortcode'] != '')
        echo do_shortcode($tabs[$task]['shortcode']);
    else
        include(wpdm_tpl_path('wpdm-list-files-front.php'));
} else {

    include(wpdm_tpl_path('wpdm-be-member.php'));
}
?>
    </div>
    <script>jQuery(function($){ $("#tabs > li > a").click(function(){ location.href=this.href; });  });</script>

<?php if (is_user_logged_in()) echo "</div>";