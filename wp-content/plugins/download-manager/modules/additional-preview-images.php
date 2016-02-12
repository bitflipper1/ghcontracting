<?php
function wpdm_additional_preview_images($post){

   
?>

<div id="adpcon">
    <a id="add-prev-img" href="#" style='float:left;margin:3px;padding:3px;width: 68px; height: 68px;border: 2px dashed #ddd;background: url(<?php echo WPDM_BASE_URL; ?>assets/images/plus.png) no-repeat center center'></a>
<?php

    $mpvs = get_post_meta($post->ID,'__wpdm_additional_previews', true);

    $mmv = 0;
    
    if(is_array($mpvs)){
       
        foreach($mpvs as $mpv){
         
            ?>
             <div id='<?php echo ++$mmv; ?>' style='float:left;margin:3px;' class='adp'>
             <input type='hidden'  id='in_<?php echo $mmv; ?>' name='file[additional_previews][]' value='<?php echo $mpv; ?>' />
             <img style='position:absolute;z-index:9999;cursor:pointer;' id='del_<?php echo $mmv; ?>' rel="<?php echo $mmv; ?>" src='<?php echo plugins_url(); ?>/download-manager/assets/images/minus.png' class="del_adp" align=left />
             <img src='<?php echo wpdm_dynamic_thumb($mpv, array(78,78)); ?>'/>
             <div style='clear:both'></div>
             </div>
            <?php
        }
    }
?>
</div> 
 

<div class="clear"></div>

<script type="text/javascript">
      
      jQuery(document).ready(function() {
          
          var file_frame;

  jQuery('body').on('click', '#add-prev-img', function( event ){
     
    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' )
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      var imgurl = attachment.url;
      var newDate = new Date;
        var ID = newDate.getTime();
        jQuery('#adpcon').append("<div id='"+ID+"' style='display:none;float:left;margin:3px;padding:5px;height:68px;width:68px;background: url("+imgurl+") no-repeat;background-size:cover;' class='adp'><input type='hidden' id='in_"+ID+"' name='file[additional_previews][]' value='"+imgurl+"' /><nobr><b><img style='position:absolute;z-index:9999;cursor:pointer;' id='del_"+ID+"' src='<?php echo plugins_url(); ?>/download-manager/assets/images/minus.png' rel='del' align=left /></b></nobr><div style='clear:both'></div></div>");
        jQuery('#'+ID).fadeIn();
        jQuery('#del_'+ID).click(function(){
            if(confirm('Are you sure?')){                                     
                jQuery('#'+ID).fadeOut().remove();
            }
            
        });
               
      // Do something with attachment.id and/or attachment.url here
    });

    // Finally, open the modal
    file_frame.open();
    return false;
  });
 
        
     
       
                        
        jQuery('.del_adp').click(function(){
                                if(confirm('Are you sure?')){                                     
                                    jQuery('#'+jQuery(this).attr('rel')).fadeOut().remove();
                                }
                                
                            });
   
      });
  
      </script>
<?php    
}    


function wpdm_delete_preview(){    
    die();
}


function wpdm_push_additional_preview_images($file){
    $file['additional_previews'] = $file['more_previews'];
    $img = '';
    $id = uniqid();
    $k = 0;
    if($file['additional_previews']){
    foreach($file['additional_previews'] as $p){
        ++$k;
        $img .= "<a href='{$p}' id='more_previews_a_{$k}' class='more_previews_a wpdm-lightbox' data-lightbox-gallery='gallery_{$id}' ><img id='more_previews_{$k}' class='more_previews' src='".plugins_url().'/download-manager/timthumb.php?w='.get_option('_wpdm_athumb_w').'&h='.get_option('_wpdm_athumb_h').'&zc=1&src='.$p."'/></a>";
    }}
    $file['athumbs'] = $img;    
    return $file;
}

function wpdm_get_additional_preview_images($file, $w, $h){

    $file['additional_previews'] = maybe_unserialize(get_post_meta($file['ID'],'__wpdm_additional_previews', true));
    $k = 0;
    $img = '';
    $id = uniqid();
    if($file['additional_previews']){
    foreach($file['additional_previews'] as $p){
        ++$k;
        $img .= "<a href='{$p}' id='more_previews_a_{$k}' class='more_previews_a imgpreview wpdm-lightbox' data-lightbox-gallery='gallery_{$id}' rel='previews' ><img id='more_previews_{$k}' class='more_previews img-rounded' src='". wpdm_dynamic_thumb($p, array($w, $h)) ."'/></a>";
    }}
    $js = ""; // "<script>jQuery(function($){ $('a.more_previews_a').nivoLightbox(); });</script>";
    return $img.$js;
}

function wpdm_additional_preview_images_mb($meta_boxes){
    $meta_boxes['apv'] = array("title"=>"Additional Previews","callback"=>"wpdm_additional_preview_images","position"=>"side","priority"=>"low");
    return $meta_boxes;
}

add_filter("wpdm_meta_box","wpdm_additional_preview_images_mb");

