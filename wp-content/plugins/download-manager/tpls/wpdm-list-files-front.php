<?php
global $wpdb, $current_user, $wp_query;

$limit = 10;
get_currentuserinfo(); 
$cond[] = "uid='{$current_user->ID}'";
$Q = wpdm_query_var('q','txt');
$paged = $wp_query->query_vars['paged']?$wp_query->query_vars['paged']:1;


$start = $paged?(($paged-1)*$limit):0;
$field = wpdm_query_var('sfield')?wpdm_query_var('sfield'):'publish_date';
$ord = wpdm_query_var('sorder')?wpdm_query_var('sorder'):'desc';

$author = $current_user->ID;
$params = array('post_status'=>array('publish','pending','draft'), 'post_type'=>'wpdmpro', 'author'=> $author, 'offset'=>$start, 'posts_per_page' => $limit);
$params['orderby'] = $field;
$params['order'] = $ord;
if(isset($sparams['base_category'])){
    $params['tax_query'] = array(
        array(
            'taxonomy' => 'wpdmcategory',
            'field'    => 'slug',
            'terms'    => $sparams['base_category'],
            'include_children' => true
        )
    );
}
if($field=='download_count'){
    $params['orderby'] = 'meta_value_num';
    $params['meta_key'] = '__wpdm_download_count';
    $params['order'] = $ord;
}

if($Q) $params['s'] = $Q;



$res = new WP_Query($params);
if(!isset($qr)) $qr = '';
?>

<div class="wpdm-front wpdmpro">
 
<br/>
           
<form method="post" action="" id="posts-filter">
    <input type="hidden" name="do" value="search" />
<div class="panel panel-default">
    <div class="panel-heading">
 <div class="input-group">
     <span class="input-group-addon"><i class="fa fa-search"></i></span>
<input type="text" id="sfld" class="form-control input-sm" name="q" value="<?php echo $Q; ?>">

        <?php if($Q) { ?>
        <span class="input-group-btn"><a  class="btn btn-danger btn-sm" href='<?php the_permalink(); ?>' >Reset Search</a></span>
        <?php } ?>
 </div>
</div>


        <table cellspacing="0" class="table table-hover table-striped">
    <thead>
    <tr>

    <th style="" class="manage-column column-media sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="media" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=title&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Package Title</span> <?php if(wpdm_query_var('sfield')=='title') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
<!--    <th style="" class="manage-column column-media" id="media" scope="col">Embed Code</th>    -->
    <th width="120" style="" class="manage-column column-parent sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="parent" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=download_count&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Downloads</span><?php if(wpdm_query_var('sfield')=='download_count') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center"><a href='<?php echo  $burl.$sap;?>sfield=publish_date&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Publish Date</span> <?php if(wpdm_query_var('sfield')=='publish_date') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center">Status</th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center">Actions</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

    <th style="" class="manage-column column-media sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="media" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=title&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Package Title</span> <?php if(wpdm_query_var('sfield')=='title') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
<!--    <th style="" class="manage-column column-media" id="media" scope="col">Embed Code</th>    -->
    <th style="" class="manage-column column-parent sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="parent" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=download_count&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Downloads</span><?php if(wpdm_query_var('sfield')=='download_count') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center"><a href='<?php echo  $burl.$sap;?>sfield=publish_date&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&paged=<?php echo $paged;?>'><span>Publish Date</span> <?php if(wpdm_query_var('sfield')=='publish_date') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center">Status</th>
    <th style="" class="manage-column column-media" id="media" scope="col" align="center">Actions</th>
    </tr>
    </tfoot>

    <tbody class="list:post" id="the-list">
    <?php while($res->have_posts()) { $res->the_post(); global $post;
                   
        ?>
    <tr valign="top" class="alternate author-self status-inherit" id="post-<?php the_ID(); ?>">


                  
                <td class="media column-media">
                    <a title="Edit" href="<?php echo $burl; ?>edit-package/<?php the_ID(); ?>/"><?php the_title();?></a>
                </td>
<!--                <td><input class="form-control input-xs" type="text" onclick="this.select()" size="20" title="Simply Copy and Paste in post contents" value="[wpdm_package id=--><?php //the_ID();?><!--]" /></td>-->
                <td class="parent column-parent"><?php echo get_post_meta(get_the_ID(),'__wpdm_download_count', true); ?></td>
                <td class="parent column-parent <?php echo $post->post_status=='publish'?'text-success':'text-danger';?>"><?php echo $post->post_status=='publish'?get_the_date():'Not Yet';?></td>
                <td class="parent column-parent <?php echo $post->post_status=='publish'?'text-success':'text-danger';?>"><?php echo ucfirst($post->post_status);?></td>
                <td class="actions"><a class="btn btn-primary btn-xs" href="<?php echo $burl; ?>edit-package/<?php the_ID(); ?>/"><i class="fa fa-pencil"></i></a> <a class="btn btn-xs btn-success" target="_blank" href='<?php echo get_permalink($post->ID); ?>'><i class="fa fa-eye"></i></a> <a href="#" class="delp btn btn-danger btn-xs" onclick="return false;" data-toggle="popover" data-content="Are You Sure? <a style='margin:0 5px' href='#' class='canceldelete btn btn-default btn-xs pull-right'>No</a> <a href='#' class='submitdelete btn btn-danger btn-xs pull-right' rel='<?php the_ID(); ?>'>Yes</a>" title="Delete Package" ><i class="fa fa-trash-o"></i></a></td>
     
     </tr>
     <?php } ?>
    </tbody>
</table>

    <?php
    global $wp_query;
    $cp = $paged;

    $page_links = paginate_links( array(
        'base' => add_query_arg( 'paged', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($res->found_posts/$limit),
        'current' => $cp
    ));


    ?>


    <div class="panel-footer">

        <?php if ( $page_links ) { ?>
            <div class="tablenav-pages"><?php $page_links_text = sprintf( '<span style="margin-right:20px;" class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                    number_format_i18n( ( $cp - 1 ) * $limit + 1 ),
                    number_format_i18n( min( $cp * $limit, $res->found_posts ) ),
                    number_format_i18n( $res->found_posts ),
                    $page_links
                ); echo $page_links_text; ?></div>
        <?php }
        wp_reset_query();
        ?>

    </div>
</div>

</form>

</div>

<script language="JavaScript">
<!--
  jQuery(function(){
     jQuery('body').on('click', '.submitdelete' ,function(){
          var id = '#post-'+this.rel;
          jQuery('#li-'+this.rel).html("<a href='#'><i class='fa fa-time'></i> Deleting...</a>");
          jQuery.post('<?php echo admin_url().'/admin-ajax.php?action=delete_package_frontend&ID=';?>'+this.rel,function(){
              jQuery(id).fadeOut();
          }) ;
          return false;
     });
     jQuery('.delp').popover({placement:'left', html:true});

      jQuery('body').on('click', '.canceldelete',function(){
          jQuery('.delp').popover('hide');
          return false;
      });

  });
//-->
</script> 