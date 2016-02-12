<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

if(count($files)>15)
include(dirname(__FILE__)."/attached-files-datatable.php");
else {
?>


<div id="currentfiles" class="w3eden">



                    <?php
                    $file_index = 0;
                    $fileinfo = get_post_meta($post->ID, '__wpdm_fileinfo', true);
                    if (!$fileinfo) $fileinfo = array();
                    foreach ($files as $id => $value): ++$file_index;
                        if (!isset($fileinfo[$value]) || !@is_array($fileinfo[$value])) $fileinfo[$value] = array('title'=>'','password'=>'');
                          $svalue = $value;
                        if(strlen($value)>50){
                            $svalue = substr($value, 0,23)."...".substr($value, strlen($value)-27);
                        }
                        $imgext = array('png','jpg','jpeg', 'gif');
                        $ext = explode(".", $value);
                        $ext = end($ext);
                        $ext = strtolower($ext);
                        $filepath = file_exists($value)?$value:UPLOAD_DIR.$value;
                        $thumb = "";
                        if(in_array($ext, $imgext))
                            $thumb = wpdm_dynamic_thumb($filepath, array(48, 48));

                        if($ext=='' || !file_exists(WPDM_BASE_DIR.'assets/file-type-icons/'.$ext.'.png'))
                            $ext = '_blank';
                        ?>
                        <div class="cfile">
                            <div class="panel panel-default">
                                <input class="faz" type="hidden" value="<?php echo $value; ?>" name="file[files][<?php echo $id; ?>]">
                                <div class="panel-heading"><button type="button" class="btn btn-xs btn-default pull-right" rel="del"><i class="fa fa-times text-danger"></i></button> <span title="<?php echo $value; ?>"><?php echo strlen($value)<100?$value:substr($value, 0, 80).'...'; ?></span></div>
                                <div class="panel-body">
                                    <div class="media">
                                        <div class="pull-left">

                                            <img class="file-ico" onerror="this.src='<?php echo WPDM_BASE_URL.'assets/file-type-icons/_blank.png';?>';" src="<?php echo $thumb?$thumb:WPDM_BASE_URL.'assets/file-type-icons/'.$ext.'.png';?>" />
                                        </div>
                                    <div class="media-body">
                                    <input placeholder="<?php _e('File Title','wpdmpro'); ?>" title="<?php _e('File Title','wpdmpro'); ?>" class="form-control" type="text" name='file[fileinfo][<?php echo $id; ?>][title]' value="<?php echo !isset($fileinfo[$id]['title'])?esc_html($fileinfo[$value]['title']):$fileinfo[$id]['title']; ?>" /><br/>
                                    <div class="input-group">
                                    <input placeholder="<?php _e('File Password','wpdmpro'); ?>"  title="<?php _e('File Password','wpdmpro'); ?>" class="form-control inline" type="text" id="indpass_<?php echo $file_index; ?>" name='file[fileinfo][<?php echo $id; ?>][password]' value="<?php echo !isset($fileinfo[$id]['password'])?esc_html($fileinfo[$value]['password']):$fileinfo[$id]['password']; ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" class="genpass" title='Generate Password' onclick="return generatepass('indpass_<?php echo $file_index; ?>')"><i class="fa fa-ellipsis-h"></i></button>
                                    </span>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endforeach;
                    ?>



                <?php if ($files): ?>
                    <script type="text/javascript">


                        jQuery('body').on('click','button[rel=del], button[rel=undo]', function () {

                            if (jQuery(this).attr('rel') == 'del') {

                                jQuery(this).parents('div.panel').removeClass('panel-default').addClass('panel-danger').find('input.faz').attr('name', 'del[]');
                                jQuery(this).attr('rel', 'undo').html('<i class="fa fa-refresh text-danger"></i>');

                            } else {


                                jQuery(this).parents('div.panel').removeClass('panel-danger').addClass('panel-default').find('input.faz').attr('name', 'file[files][]');
                                jQuery(this).attr('rel', 'del').html('<i class="fa fa-times text-danger"></i>');


                            }

                            return false;
                        });

                        jQuery(function(){
                            jQuery('#currentfiles').sortable();
                        });


                    </script>


                <?php endif; ?>


            </div>
<?php } ?>