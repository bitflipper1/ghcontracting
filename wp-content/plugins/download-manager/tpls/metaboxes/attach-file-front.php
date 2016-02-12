<div id="ftabs">
<ul class="nav nav-tabs">
    <li class="active"><a href="#upload" data-toggle="tab"><?php echo __('Upload','wpdmpro'); ?></a></li>
    <!-- li><a href="#browse" data-toggle="tab">Browse</a></li -->
    <li><a href="#remote" data-toggle="tab"><?php echo __('URL','wpdmpro'); ?></a></li>
</ul>
<div class="tab-content">
<div id="upload" class="tab-pane active">
<div id="plupload-upload-ui" class="hide-if-no-js">
        <div id="drag-drop-area">
            <div class="drag-drop-inside">
                <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
            </div>
        </div>
    </div>

    <?php
    $slimit = get_option('__wpdm_max_upload_size',0);
    if($slimit>0)
    $slimit = wp_convert_hr_to_bytes($slimit.'M');
    else
    $slimit = wp_max_upload_size();
    
    $plupload_init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'plupload-browse-button',
        'container'           => 'plupload-upload-ui',
        'drop_element'        => 'drag-drop-area',
        'file_data_name'      => 'async-upload',
        'multiple_queues'     => true,
        'max_file_size'       => $slimit.'b',
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' =>  get_option('__wpdm_allowed_file_types','*'))),
        'multipart'           => true,
        'urlstream_upload'    => true,

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
            '_ajax_nonce' => wp_create_nonce('frontend-file-upload'),
            'action'      => 'wpdm_frontend_file_upload',            // the ajax action name
        ),
    );

    // we should probably not apply this filter, plugins may expect wp's media uploader...
    $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

    <script type="text/javascript">

        jQuery(document).ready(function($){

            // create the uploader and pass the config from above
            var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

            // checks if browser supports drag and drop upload, makes some css adjustments if necessary
            uploader.bind('Init', function(up){
                var uploaddiv = jQuery('#plupload-upload-ui');

                if(up.features.dragdrop){
                    uploaddiv.addClass('drag-drop');
                    jQuery('#drag-drop-area')
                        .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                        .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                }else{
                    uploaddiv.removeClass('drag-drop');
                    jQuery('#drag-drop-area').unbind('.wp-uploader');
                }
            });

            uploader.init();

            // a file was added in the queue
            uploader.bind('FilesAdded', function(up, files){
                //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                plupload.each(files, function(file){
                    jQuery('#filelist').append(
                        '<div class="file" id="' + file.id + '"><b>' +

                            file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                            '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
                });

                up.refresh();
                up.start();
            });

            uploader.bind('UploadProgress', function(up, file) {

                jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });


            // a file was uploaded
            uploader.bind('FileUploaded', function(up, file, response) {

                // this is your ajax response, update the DOM with it or something...
                //console.log(response);
                //response
                jQuery('#' + file.id ).remove();
                var d = new Date();
                var ID = d.getTime();
                response = response.response;
                var nm = response;
                if(response.length>20) nm = response.substring(0,7)+'...'+response.substring(response.length-10);
                jQuery('table#wpdm-files').append("<tr id='"+ID+"' class='cfile'><td><input type='hidden' id='in_"+ID+"' name='file[files][]' value='"+response+"' /><i id='del_"+ID+"'  rel='del' class='fa fa-trash-o text-danger'></td><td>"+response+"</td><td width='40%'><input style='width:99%' type='text' name='file[fileinfo]["+response+"][title]' value='"+response+"' onclick='this.select()'><input size='10' type='hidden' id='indpass_"+ID+"' name='file[fileinfo]["+response+"][password]' value=''></td></tr>");
                jQuery('#wpdm-files tbody tr:last-child').attr('id',ID).addClass('cfile');

                jQuery('#'+ID).fadeIn();
                jQuery('#del_'+ID).click(function(){
                    if(jQuery(this).attr('rel')=='del'){
                        jQuery('#'+ID).removeClass('cfile').addClass('dfile');
                        jQuery('#in_'+ID).attr('name','del[]');
                        jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/download-manager/images/add.png').attr('title','Undo Delete');
                    } else if(jQuery(this).attr('rel')=='undo'){
                        jQuery('#'+ID).removeClass('dfile').addClass('cfile');
                        jQuery('#in_'+ID).attr('name','files[files][]');
                        jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/download-manager/images/minus.png').attr('title','Delete File');
                    }


                });



            });

        });

    </script>
    <div id="filelist"></div>

    <div class="clear"></div>
</div>

<div id="browse" class="tab-pane">
    <?php //wpdm_file_browser(); ?>
</div>
<div id="remote" class="tab-pane">
<div class="input-group input-group-sm">
<input placeholder="Valid File URL" type="text" class="form-control group-item" id="rurl"><span class="input-group-btn"><button type="button" id="rmta" class="btn btn-sm btn-default  group-item">Attach</button></span>
</div>
</div>
</div>
</div>
<script>
jQuery(function(){


        jQuery('#rmta').click(function(){
        var ID = 'file_' + parseInt(Math.random()*1000000);
        var file = jQuery('#rurl').val();
        var filename = file;
            jQuery('#rurl').val('');
            if(file == ''){
                alert("Invalid url");
                return false;
            }
            jQuery('table#wpdm-files').append("<tr id='"+ID+"' class='cfile'><td style='text-align: centerw'><input type='hidden' id='in_"+ID+"' name='file[files][]' value='"+file+"' /><i id='del_"+ID+"' class='fa fa-trash-o text-danger' rel='del' align=left></i></td><td>"+file+"</td><td width='40%'><input style='width:99%' type='text' name='file[fileinfo]["+file+"][title]' value='"+file+"' onclick='this.select()'><input size='10' type='hidden' id='indpass_"+ID+"' name='file[fileinfo]["+file+"][password]' value=''></td></tr>");

            jQuery('#wpdm-files tbody tr:last-child').attr('id',ID).addClass('cfile');

        jQuery("#wpdm-files tbody").sortable();

        jQuery('#'+ID).fadeIn();
        jQuery('#del_'+ID).click(function(){
            if(jQuery(this).attr('rel')=='del'){
                jQuery('#'+ID).removeClass('cfile').addClass('dfile');
                jQuery('#in_'+ID).attr('name','del[]');
                jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/download-manager/images/add.png').attr('title','Undo Delete');
            } else if(jQuery(this).attr('rel')=='undo'){
                jQuery('#'+ID).removeClass('dfile').addClass('cfile');
                jQuery('#in_'+ID).attr('name','file[files][]');
                jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/download-manager/images/minus.png').attr('title','Delete File');
            }


        });


    });

});

</script>
