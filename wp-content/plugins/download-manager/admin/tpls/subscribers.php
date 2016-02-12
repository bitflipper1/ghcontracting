<?php
global $wpdb, $current_user;
$limit = 20;
get_currentuserinfo(); 
$_GET['paged'] = isset($_GET['paged'])?(int)$_GET['paged']:1;
$start = isset($_GET['paged'])?(($_GET['paged']-1)*$limit):0;
$field = isset($_GET['sfield'])?$_GET['sfield']:'id';
$ord = isset($_GET['sorder'])?$_GET['sorder']:'desc';
$pid = isset($_GET['pid'])?(int)$_GET['pid']:0;
if($pid > 0) $cond = " and e.pid=$pid";
if(isset($_GET['uniq'])) $group = " group by e.email";
$res = $wpdb->get_results("select * from {$wpdb->prefix}ahm_emails order by {$field} {$ord} limit $start, $limit",ARRAY_A);
$total = $wpdb->get_var("select count(*) as t from {$wpdb->prefix}ahm_emails");
 
?>

<div class="wrap w3eden">
    <div class="panel panel-primary" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-users"></i> &nbsp; <?php echo __("Subscribers", "wpdmpro"); ?></b>
            <a style="margin-left: 10px" id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=export" class="btn btn-sm btn-default pull-right"><?php echo __('Export All','wpdmpro'); ?></a>
            <a id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=export&uniq=1" class="btn btn-sm btn-default pull-right"><?php echo __('Export Unique Emails','wpdmpro'); ?></a>&nbsp;
        </div>

    <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
    <li class="active"><a id="basic" href="edit.php?post_type=wpdmpro&page=emails"><?php echo __('Emails','wpdmpro'); ?></a></li>
    <li><a id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=template"><?php echo __('Email Template','wpdmpro'); ?></a></li>        
    </ul>

<form method="post" action="edit.php?post_type=wpdmpro&page=emails&task=delete" id="posts-filter" class="panel-body">


<div class="clear"></div>

    <div class="panel panel-default">
<table id="subtbl" cellspacing="0" class="table table-striped" style="margin:0 !important;border-bottom:0;">
    <thead>
    <tr>
    <th style="width: 50px" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
    <th style="width:50px" style="" class="manage-column column-id"  scope="col"><?php echo __('ID','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="email" scope="col"><?php echo __('Email','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="email" scope="col"><?php echo __('Name','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="filename" scope="col"><?php echo __('Package Name','wpdmpro'); ?></th>
    <th style="" class="manage-column column-password" id="author" scope="col"><?php echo __('Date','wpdmpro'); ?></th>    
    <th style="" class="manage-column column-password" id="author" scope="col"><?php echo __('Action','wpdmpro'); ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>         
    <th style="width:50px" style="" class="manage-column column-id"  scope="col"><?php echo __('ID','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="email" scope="col"><?php echo __('Email','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="email" scope="col"><?php echo __('Name','wpdmpro'); ?></th>
    <th style="" class="manage-column column-media" id="filename" scope="col"><?php echo __('Package Name','wpdmpro'); ?></th>
    <th style="" class="manage-column column-password" id="author" scope="col"><?php echo __('Date','wpdmpro'); ?></th>
    <th style="" class="manage-column column-password" id="author" scope="col"><?php echo __('Action','wpdmpro'); ?></th>
    </tr>
    </tfoot>

    <tbody class="list:post" id="the-list">
    <?php foreach($res as $row) { 
                   
        ?>
    <tr valign="top" class="author-self status-inherit" id="post-<?php echo $row[id]; ?>">

                <th class="check-column text-center" style="padding: 5px 0px !important;" scope="row"><input type="checkbox" value="<?php echo $row['id']; ?>" name="id[]"></th>
                <td scope="row">
                <?php echo $row['id']; ?>
                </td>
                <td scope="row"><?php echo $row['email']; ?></td>
                <td scope="row"><?php $cd = unserialize($row['custom_data']); if($cd) { echo "<ul>"; foreach($cd as $k=>$v): echo "<li>".$k." : ".(is_array($v)?implode(", ",$v):$v)."</li>"; endforeach; echo "</ul>"; } ?></td>
                
                <td class="media column-media">
                <a href='edit.php?post_type=wpdmpro&page=emails&pid=<?php echo $row['pid']; ?>'><?php $p =  get_post($row['pid']); if(is_object($p) && $p->post_type =='wpdmpro') echo $p->post_title; else echo "Not Found or Deleted"; ?></a>
                </td>
                <td class="author column-author"><?php echo date("Y-m-d H:i",$row['date']); ?></td>                
                <td class="author column-author"><?php echo $row['request_status']==2?"<a href='#'>Send Download Link</a>":"Link Sent"; ?></td>

     </tr>
     <?php } ?>
    </tbody>
</table>
        <?php
        $cp = $_GET['paged']?(int)$_GET['paged']:1;
        $page_links = paginate_links( array(
            'base' => add_query_arg( 'paged', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total/$limit),
            'current' => $cp
        ));


        ?>
        <div class="panel-footer">
            <nobr>
                <input type="submit" class="button-secondary action submitdelete" id="doaction" value="<?php echo __('Delete Selected','wpdmpro'); ?>">
                <?php if(isset($_REQUEST['q'])) { ?>
                    <input type="button" class="button-secondary action" onclick="location.href='admin.php?page=file-manager'" value="<?php echo __('Reset Search','wpdmpro'); ?>">
                <?php } ?>
            </nobr>
            <div class="pull-right">

                <?php  if ( $page_links ) { ?>
                    <div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                            number_format_i18n( ( $_GET['paged'] - 1 ) * $limit + 1 ),
                            number_format_i18n( min( $_GET['paged'] * $limit, $total ) ),
                            number_format_i18n( $total ),
                            $page_links
                        ); echo $page_links_text; ?></div>
                <?php }  ?>

            </div><div style="clear: both"></div>
        </div>
    </div>





</form>
</div>
</div>
