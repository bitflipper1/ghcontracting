<?php

if(!defined('ABSPATH')) die();

if(isset($pid))
$post = get_post($pid);
else {
$post = new stdClass();
$post->ID = 0;
$post->post_title = '';
$post->post_content = '';
}

if(isset($hide)) $hide = explode(',',$hide);
else $hide = array();
?>
<div class="w3eden">
 <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/download-manager/assets/css/chosen.css'); ?>" />
<style>
    .cat-panel ul,
    .cat-panel label,
    .cat-panel li{
        padding: 0;
        margin: 0;
        font-size: 9pt;
    }
    .cat-panel ul{
        margin-left: 20px;
    }
    .cat-panel > ul{
        padding-top: 10px;
    }
</style>
<div class="wpdm-front"><br>
<form id="wpdm-pf" action="" method="post">
<div class="row">

    <div class="col-md-8">


<input type="hidden" id="act" name="act" value="<?php echo $task =='edit-package'?'_ep_wpdm':'_ap_wpdm'; ?>" />

<input type="hidden" name="id" id="id" value="<?php echo isset($pid)?$pid:0; ?>" />
<div class="form-group">
<input id="title" class="form-control input-lg"  placeholder="Enter title here" type="text" value="<?php echo isset($post->post_title)?$post->post_title:''; ?>" name="pack[post_title]" /><br/>
</div>
<div  class="form-group">
<?php $cont = isset($post)?$post->post_content:''; wp_editor(stripslashes($cont),'post_content',array('textarea_name'=>'pack[post_content]')); ?>
</div>

        <div class="panel panel-default" id="attached-files-section">
            <div class="panel-heading"><b><?php _e('Attached Files','wpdmpro'); ?></b></div>
            <div class="panel-body-ff">
                <?php
                require_once wpdm_tpl_path('metaboxes/attached-files-front.php');
                ?>
            </div>
        </div>

            <div class="panel panel-default" <?php if(in_array('package-settings', $hide)) { ?>style="display: none"<?php }?> id="package-settings-section">
                <div class="panel-heading"><b><?php _e('Package Settings','wpdmpro'); ?></b></div>
                <div class="panel-body">
                    <?php
                    require_once wpdm_tpl_path("metaboxes/package-settings-front.php");
                    ?>
                </div>
            </div>

        <?php

            do_action("wpdm-package-form-left");
        ?>


</div>
<div class="col-md-4">

    <div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e('Attach Files','wpdmpro'); ?></b></div>
        <div class="panel-body">
            <?php
            require_once wpdm_tpl_path("metaboxes/attach-file-front.php");
            ?>
        </div>
    </div>

    <div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e('Live Demo/Preview','wpdmpro'); ?></b></div>
        <div class="panel-body" style="padding: 20px !important;">

            <input type="text" placeholder="<?php _e('Live Preview URL','wpdmpro'); ?>" class="form-control" name="file[demo_url]">
        </div>
    </div>

    <div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e('Categories','wpdmpro'); ?></b></div>
        <div class="panel-body cat-panel">
            <?php
            $term_list = wp_get_post_terms($post->ID, 'wpdmcategory', array("fields" => "all"));

            function wpdm_categories_checkboxed_tree($parent = 0, $selected = array()){
                $categories = get_terms( 'wpdmcategory' , array('hide_empty'=>0,'parent'=>$parent));
                $checked = "";
                foreach($categories as $category){
                    if($selected){
                        foreach($selected as $ptype){
                            if($ptype->term_id==$category->term_id){$checked="checked='checked'";break;}else $checked="";
                        }
                    }
                    echo '<li><label><input type="checkbox" '.$checked.' name="cats[]" value="'.$category->term_id.'"> '.$category->name.' </label>';
                    echo "<ul>";
                    wpdm_categories_checkboxed_tree($category->term_id, $selected);
                    echo "</ul>";
                    echo "</li>";
                }
            }

            echo "<ul class='ptypes'>";
            $cparent = isset($params['base_category'])?$params['base_category']:0;
            if($cparent!==0){
                $cparent = get_term_by('slug', $cparent, 'wpdmcategory');
                $cparent = $cparent->term_id;
                echo "<input type='hidden' value='{$cparent}' name='cats[]' />";
            }
            wpdm_categories_checkboxed_tree($cparent, $term_list);
            echo "</ul>";
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><b><?php _e('Main Preview Image','wpdmpro'); ?></b></div>
        <div class="inside">
            <div id="img"><?php if(has_post_thumbnail((isset($pid)?$pid:0))):
                    $thumbnail_id = get_post_thumbnail_id((isset($pid)?$pid:0));
                    $thumbnail_url = wp_get_attachment_image_src($thumbnail_id,'full', true);
                    ?>
                    <p><img src="<?php  echo $thumbnail_url[0]; ?>" width="240" alt="preview" /><input type="hidden" name="file[preview]" value="<?php  echo $thumbnail_url[0]; ?>" id="fpvw" /></p>
                    <a href="#"  id="rmvp" class="text-danger">Remove Featured Image</a>
                <?php else: ?>
                    <a href="#" id="wpdm-featured-image">&nbsp;</a>
                    <input type="hidden" name="file[preview]" value="" id="fpvw" />
                <?php endif; ?>
            </div>
            <!-- <input type="file" name="preview" /> -->
            <div class="clear"></div>
        </div>
    </div>

<div class="panel panel-default">
<div class="panel-heading"><b><?php _e('Additional Preview Images','wpdmpro'); ?></b></div>
<div class="inside">

    <?php
    wpdm_additional_preview_images($post); ?>


 <div class="clear"></div>
</div>
</div>


 











<div class="panel panel-primary " id="form-action">
    <div class="panel-heading">
        <b>Actions</b>
    </div>
<div class="panel-body">

<label><input type="checkbox" <?php if(isset($post->post_status)) checked($post->post_status,'draft'); ?> value="draft" name="status"> <?php _e('Save as Draft','wpdmpro'); ?></label><br/><br/>


<button type="submit" accesskey="p" tabindex="5" id="publish" class="btn btn-success btn-block btn-lg" name="publish"><i class="fa fa-save" id="psp"></i> &nbsp; <?php echo $task=='EditPackage'?__('Update Package','wpdmpro'):__('Create Package','wpdmpro'); ?></button>

</div>
</div>

</div>
</div>

</form>

</div>


<script type="text/javascript" src="<?php echo plugins_url('/download-manager/assets/js/chosen.jquery.min.js'); ?>"></script>
      <script type="text/javascript">
      
      jQuery(document).ready(function() {

        jQuery('select').chosen();
        jQuery('span.infoicon').css({color:'transparent',width:'16px',height:'16px',cursor:'pointer'}).tooltip({placement:'right',html:true});
        jQuery('span.infoicon').tooltip({placement:'right'});
        jQuery('.nopro').click(function(){
            if(this.checked) jQuery('.wpdmlock').removeAttr('checked');
        });
        
        jQuery('.wpdmlock').click(function(){      
         
            if(this.checked) {   
            jQuery('#'+jQuery(this).attr('rel')).slideDown();
            jQuery('.nopro').removeAttr('checked');
            } else {
            jQuery('#'+jQuery(this).attr('rel')).slideUp();    
            }
        });
          
       // jQuery( "#pdate" ).datepicker({dateFormat:'yy-mm-dd'});
       // jQuery( "#udate" ).datepicker({dateFormat:'yy-mm-dd'});
        
        jQuery('#wpdm-pf').submit(function(){
             try {
                var editor = tinymce.get('post_content');
                editor.save();
             }catch(e){}
             jQuery('#psp').removeClass('fa-save').addClass('fa-spinner fa-spin');
             jQuery('#publish').attr('disabled','disabled');
             jQuery('#wpdm-pf').ajaxSubmit({
                 //dataType: 'json',
                 beforeSubmit: function() { jQuery('#sving').fadeIn(); },
                 success: function(res) {  jQuery('#sving').fadeOut(); jQuery('#nxt').slideDown();
                     if(res.result=='_ap_wpdm') {
                         location.href = "<?php the_permalink(); ?>/edit-package/"+res.id+"/";
                         jQuery('#wpdm-pf').prepend('<div class="alert alert-success">Package Created Successfully. Opening Edit Window ... <a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a></div>');
                     }
                     else {
                         jQuery('#psp').removeClass('fa-spinner fa-spin').addClass('fa-save');
                         jQuery('#publish').removeAttr('disabled');
                         jQuery('#wpdm-pf').prepend('<div class="alert alert-success">Package Updated Successfully <a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a></div>');
                     }
                 }
                 
                 
             });
             return false;
        });


       
      });
      
      jQuery('#upload-main-preview').click(function() {           
            tb_show('', '<?php echo admin_url('media-upload.php?type=image&TB_iframe=1&width=640&height=551'); ?>');
            window.send_to_editor = function(html) {           
              var imgurl = jQuery('img',"<p>"+html+"</p>").attr('src');                     
              jQuery('#img').html("<img src='"+imgurl+"' style='max-width:100%'/><input type='hidden' name='file[preview]' value='"+imgurl+"' >");
              tb_remove();
              }
            return false;
        });

     
 

      </script>

</div>