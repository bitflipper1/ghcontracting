

 
            <div id="currentfiles">

                <?php

                $files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));
                
                if (!is_array($files)) $files = array();

                ?>

                <table class="table" id="wpdm-files">
                    <thead>
                    <tr>
                        <th style="width: 50px;background: transparent;text-align: center"><i class="fa fa-cog"></i></th>
                        <th><?php echo __("Filename", "wpdmpro"); ?></th>
                        <th style="min-width: 40%"><?php echo __("Title", "wpdmpro"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $file_index = 0;
                    $fileinfo = get_post_meta($post->ID, '__wpdm_fileinfo', true);
                    if (!$fileinfo) $fileinfo = array();
                    foreach ($files as $value): ++$file_index;
                        if (!@is_array($fileinfo[$value])) $fileinfo[$value] = array('title'=>'','password'=>'');

                        ?>
                        <tr class="cfile">
                            <td style="width: 50px;text-align: center">
                                <input class="fa" type="hidden" value="<?php echo $value; ?>" name="file[files][]">
                                <i class="fa fa-trash-o text-danger" rel="del"></i>
                            </td>
                            <td><div style="height: 20px;overflow: hidden" title="<?php echo $value; ?>" class="ttip"><?php echo $value; ?></div></td>
                            <td><input type="text" style="width:99%;height:25px;max-width:99%;min-width:99%;" name='file[fileinfo][<?php echo $value; ?>][title]' class="form-control" value="<?php echo $fileinfo[$value]['title']; ?>" />
                                <input type="hidden" name='file[fileinfo][<?php echo $value; ?>][password]'  value="<?php echo $fileinfo[$value]['password']; ?>" />
                            </td>

                        </tr>
                    <?php
                    endforeach;
                    ?>
                    </tbody>
                </table>


                <?php if ($files): ?>
                    <script type="text/javascript">

                        jQuery('.ttip').tooltip();
                        jQuery('i.fa[rel=del], i.fa[rel=undo]').click(function () {

                            if (jQuery(this).attr('rel') == 'del') {

                                jQuery(this).parents('tr.cfile').removeClass('cfile').addClass('dfile').find('input.fa').attr('name', 'del[]');
                                jQuery(this).attr('rel', 'undo').attr('class', 'fa fa-refresh text-info').attr('title', 'Undo Delete');

                            } else {


                                jQuery(this).parents('tr.dfile').removeClass('dfile').addClass('cfile').find('input.fa').attr('name', 'file[files][]');
                                jQuery(this).attr('rel', 'del').attr('class', 'fa fa-trash-o text-danger').attr('title', 'Delete File');


                            }


                        });


                    </script>


                <?php endif; ?>


            </div>


