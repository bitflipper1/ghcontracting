
<div class="wrap w3eden">

    <div class="panel panel-primary" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-bar-chart-o"></i> &nbsp; <?php echo __('Download Statistics','wpdmpro'); ?></b>

        </div>
        <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
            <li <?php if((!isset($_GET['type']))&&!isset($_GET['task'])){ ?>class="active"<?php } ?>><a href='edit.php?post_type=wpdmpro&page=wpdm-stats'><?php echo __('Monthly Stats','wpdmpro'); ?></a></li>
            <li <?php if(isset($_GET['type'])&&$_GET['type']=='pvdpu'){ ?>class="active"<?php } ?>><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvdpu'><?php echo __('Package vs Date','wpdmpro'); ?></a></li>
            <li <?php if(isset($_GET['type'])&&$_GET['type']=='pvupd'){ ?>class="active"<?php } ?>><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvupd'><?php echo __('Package vs User','wpdmpro'); ?></a></li>
        </ul>
        <div class="tab-content" style="padding: 15px;">
<?php 

$type = isset($_GET['type'])?WPDM_BASE_DIR."admin/tpls/stats/{$_GET['type']}.php":WPDM_BASE_DIR."admin/tpls/stats/current-month.php";

include($type);

?>
</div>
</div>