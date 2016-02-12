
<link rel="stylesheet" href="<?php echo plugins_url('/download-manager/assets/css/demo_table.css'); ?>"/>
<script language="JavaScript"
        src="<?php echo plugins_url('/download-manager/assets/js/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript">
    function filelist_dt() {
        jQuery("#wpdm-files").dataTable({
            "iDisplayLength": -1,
            "aLengthMenu": [
                [-1],
                ["All"]
            ],
            "aoColumns": [
                { "bSortable": false },
                null,
                null,
                { "bSortable": false }

            ] });
    }
    jQuery(document).ready(function () {
        filelist_dt();
        jQuery("#wpdm-files tbody").sortable();
        jQuery("#adpcon").sortable({placeholder: "adp-ui-state-highlight"});


    });
</script>
<div class="w3eden">
<table width="100%">
    <tr>
        <td width="80%" valign="top">
            <div id="currentfiles">

                <?php

                $files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));
                
                if (!is_array($files)) $files = array();

                ?>

                <table  class="table table-striped table-bordered table-hover" id="wpdm-files">
                    <thead>
                    <tr>
                        <th style="width: 10px;text-align: center"><i class="fa fa-cog"></i></th>
                        <th style="width: 40%;"><?php echo __("Filename", "wpdmpro"); ?></th>
                        <th style="width: 40%;"><?php echo __("Title", "wpdmpro"); ?></th>
                        <th style="width: 130px;background: transparent;"><?php echo __("Password", "wpdmpro"); ?></th>
                    </tr>
                    </thead>
                    <?php
                    $file_index = 0;
                    $fileinfo = get_post_meta($post->ID, '__wpdm_fileinfo', true);
                    if (!$fileinfo) $fileinfo = array();
                    foreach ($files as $id => $value): ++$file_index;
                        if (!@is_array($fileinfo[$value])) $fileinfo[$value] = array('title'=>'','password'=>'');
                          $svalue = $value;
                        if(strlen($value)>50){
                            $svalue = substr($value, 0,23)."...".substr($value, strlen($value)-27);
                        }
                        ?>
                        <tr class="cfile">
                            <td style="width: 10px;text-align: center">
                                <input class="fa" type="hidden" value="<?php echo $value; ?>" name="file[files][<?php echo $id; ?>]">
                                <i align="left" rel="del" class="fa fa-trash-o text-danger action-ico"></i>
                            </td>
                            <td style="width: 40%;" title="<?php echo $value; ?>"><?php echo $svalue; ?></td>
                            <td style="width: 40%;"><input type="text" class="form-control input-sm" name='file[fileinfo][<?php echo $id; ?>][title]' value="<?php echo !isset($fileinfo[$id]['title'])? esc_html($fileinfo[$value]['title']) : $fileinfo[$id]['title']; ?>" />
                            </td>
                            <td style="width: 130px;"><div class="input-group"><input size="10" class="form-control input-sm" type="text"
                                                             id="indpass_<?php echo $file_index; ?>"
                                                             name='file[fileinfo][<?php echo $id; ?>][password]'
                                                             value="<?php echo !isset($fileinfo[$id]['password'])?esc_html($fileinfo[$value]['password']):$fileinfo[$id]['password']; ?>"><span class="input-group-btn"><button
                                     class="genpass btn btn-default btn-sm" type="button"
                                    title='Generate Password'
                                    onclick="return generatepass('indpass_<?php echo $file_index; ?>')"><i class="fa fa-key"></i></button></span></div></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </table>


                <?php if ($files): ?>
                    <script type="text/javascript">


                        jQuery('body').on('click','i[rel=del], i[rel=undo]',function () {

                            if (jQuery(this).attr('rel') == 'del') {

                                jQuery(this).parents('tr.cfile').removeClass('cfile').addClass('dfile').find('input.fa').attr('name', 'del[]');
                                jQuery(this).attr('rel', 'undo').attr('class', 'fa fa-refresh action-ico text-primary').attr('title', 'Undo Delete');

                            } else {


                                jQuery(this).parents('tr.dfile').removeClass('dfile').addClass('cfile').find('input.fa').attr('name', 'file[files][]');
                                jQuery(this).attr('rel', 'del').attr('class', 'fa fa-trash-o action-ico text-danger').attr('title', 'Delete File');


                            }


                        });


                    </script>


                <?php endif; ?>


            </div>
        </td>

    </tr>
</table>
</div>
