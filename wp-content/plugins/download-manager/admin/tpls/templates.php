

<div class="wrap w3eden">

<div class="panel panel-primary" id="wpdm-wrapper-panel">
<div class="panel-heading">
<b><i class="fa fa-magic"></i> &nbsp; <?php echo __("Templates", "wpdmpro"); ?></b>
    <div class="pull-right">
<a href="edit.php?post_type=wpdmpro&page=templates&_type=page&task=NewTemplate" class="btn btn-sm btn-default"><i class="fa fa-plus"></i> <?php echo __("Create Page Template", "wpdmpro"); ?></a> <a href="edit.php?post_type=wpdmpro&page=templates&_type=link&task=NewTemplate" class="btn btn-sm btn-default"><i class="fa fa-plus"></i> <?php echo __("Create Link Template", "wpdmpro"); ?></a>
    </div>
    <div style="clear: both"></div>
</div>
    <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
    <li <?php if(!isset($_GET['_type'])||$_GET['_type']=='link'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=link" id="basic">Link Templates</a></li>
    <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='page'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=page" id="basic">Page Templates</a></li>
    </ul>
<div class="tab-content panel-body">
<blockquote  class="alert alert-info" style="margin-bottom: 10px">
<?php echo __("Pre-designed templates can't be deleted or edited from this section. But you can clone any of them and edit as your own. If you seriously want to edit any pre-designed template you have to edit those directly edting php files at /download-manager/templates/ dir","wpdmpro"); ?>
</blockquote>

<div class="panel panel-default">
<table cellspacing="0" class="table">
    <thead>
    <tr>
    <th style="width: 50%" class="manage-column column-media" id="media" scope="col"><?php echo __("Template Name", "wpdmpro"); ?></th>
    <th style="width: 300px" class="manage-column column-media" id="tid" scope="col"><?php echo __("Template ID", "wpdmpro"); ?></th>
    <th style="width: 300px" class="manage-column column-media" id="tid" scope="col"><?php echo __("Actions", "wpdmpro"); ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
    <th style="" class="manage-column column-media" id="media" scope="col">Template Name</th>    
    <th style="" class="manage-column column-media" id="tid" scope="col">Template ID</th>
    <th class="manage-column column-media" id="tid" scope="col">Actions</th>
    </tr>
    </tfoot>
    <tbody class="list:post" id="the-list">

    <?php 
    $ttype = isset($_GET['_type'])?$_GET['_type']:'link';

    $ctpls = WPDM\admin\menus\Templates::Dropdown(array('data_type' => 'ARRAY', 'type' => $ttype));
     
    foreach($ctpls as $ctpl => $title){

    ?>
     
    <tr valign="top" class="author-self status-inherit" id="post-8">
                <td class="column-icon media-icon" style="text-align: left;">                                     
                    <?php echo $title; ?>
                 
                    </td>
                <td>
                <input class="form-control input-sm" type="text" readonly="readonly" onclick="this.select()" value="<?php echo str_replace(".php","",$ctpl); ?>" style="width: 200px;text-align: center;font-weight: bold; background: #fff;cursor: alias"/>
                </td>
        <td>
            <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=NewTemplate&clone=<?php echo $ctpl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-copy"></i> Clone</a>
            <a data-toggle="modal" href="#" data-href="admin-ajax.php?action=template_preview&_type=<?php echo $ttype; ?>&template=<?php echo $ctpl; ?>" data-target="#preview-modal" rel="<?php echo $ctpl; ?>" class="template_preview btn btn-sm btn-success"><i class="fa fa-desktop"></i> Preview</a>

        </td>
                
     
     </tr>
    <?php    

    }
    if($templates = maybe_unserialize(get_option("_fm_{$ttype}_templates",true))){    
    if(is_array($templates)){
    foreach($templates as $id=>$template) {  ?>
    <tr valign="top" class="author-self status-inherit" id="post-8">
                <td class="column-icon media-icon" style="text-align: left;">                
                    <a title="Edit" href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=EditTemplate&tplid=<?php echo $id; ?>">
                    <?php echo $template['title']?>
                    </a>
                </td>
                <td>
                <input class="form-control input-sm" type="text" readonly="readonly" onclick="this.select()" value="<?php echo $id; ?>" style="width: 200px;text-align: center;font-weight: bold; background: #fff;cursor: alias"/>
                </td>
        <td>
            <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=EditTemplate&tplid=<?php echo $id; ?>" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i> <?php echo __("Edit", "wpdmpro"); ?></a>
            <a data-toggle="modal" href="#" data-href="admin-ajax.php?action=template_preview&template=<?php echo $id; ?>" data-target="#preview-modal" rel="<?php echo $id; ?>" class="template_preview btn btn-sm btn-success"><i class="fa fa-desktop"></i> <?php echo __("Preview", "wpdmpro"); ?></a>
            <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo $ttype; ?>&task=DeleteTemplate&tplid=<?php echo $id?>" onclick="return showNotice.warn();" class="submitdelete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> <?php echo __("Delete", "wpdmpro"); ?></a>
        </td>
                
     
     </tr>
     <?php }}} ?>
    </tbody>
</table>
</div>

    </div>
    </div>


    <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Template Preview</h4>
                </div>
                <div class="modal-body" id="preview-area">

                </div>
                <div class="modal-footer text-left" style="text-align: left">
                    <div class='alert alert-info'><?php _e('This is a preview, original template color scheme may look little different, but structure will be same','wpdmpro'); ?></div>
                </div>
            </div>
        </div>
    </div>



<script>



    jQuery(function($){
        $('.template_preview').click(function(){
            $('#preview-area').html("<i class='fa fa-spin fa-spinner'></i> Loading Preview...").load($(this).attr('data-href'));
        });
    });

</script>
</div>


 
