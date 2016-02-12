<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

if(count($files)>15)
include(dirname(__FILE__)."/attach-file-datatable.php");
else {
?>
<div id="ftabs">
<ul>
    <li><a href="#upload"><?php echo __('Upload','wpdmpro'); ?></a></li>
    <?php  if(current_user_can('access_server_browser')){ ?>
    <li><a href="#browse"><?php echo __('Browse','wpdmpro'); ?></a></li>
    <?php } ?>
    <li><a href="#remote"><?php echo __('URL','wpdmpro'); ?></a></li>
</ul>

<div id="upload">
<div id="plupload-upload-ui" class="hide-if-no-js">
        <div id="drag-drop-area">
            <div class="drag-drop-inside">
                <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                <p>&mdash; <?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?> &mdash;</p>
                <p class="drag-drop-buttons">
                    <input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /><br/>
                    <small>[ Max: <?php echo (int)(wp_max_upload_size()/1048576); ?> MB ]</small>
                </p>
            </div>
        </div>
    </div>

    <?php

    $plupload_init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'plupload-browse-button',
        'container'           => 'plupload-upload-ui',
        'drop_element'        => 'drag-drop-area',
        'file_data_name'      => 'package_file',
        'multiple_queues'     => true,
        'max_file_size'       => wp_max_upload_size().'b',
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
        'multipart'           => true,
        'urlstream_upload'    => true,

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
            '_ajax_nonce' => wp_create_nonce('wpdm_admin_upload_file'),
            'action'      => 'wpdm_admin_upload_file',            // the ajax action name
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

                var html = jQuery('#wpdm-file-entry').html();
                var ext = response.split('.');
                ext = ext[ext.length-1];
                var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/"+ext+".png";
                html = html.replace(/##filepath##/g, response);
                html = html.replace(/##fileindex##/g, ID);
                html = html.replace(/##preview##/g, icon);
                jQuery('#currentfiles').prepend(html);



            });

        });

    </script>
    <div id="filelist"></div>

    <div class="clear"></div>
</div>

<div id="browse">
    <?php if(current_user_can('access_server_browser')) wpdm_file_browser(); ?>
</div>
<div id="remote" class="w3eden">
<div class="input-group"><input type="url" id="rurl" class="form-control" placeholder="Insert URL"><span class="input-group-btn"><button type="button" id="rmta" class="btn btn-default"><i class="fa fa-plus-circle"></i></button></span></div>
</div>
</div>

<script type="text/html" id="wpdm-file-entry">
    <div class="cfile">
        <input class="faz" type="hidden" value="##filepath##" name="file[files][##fileindex##]">
        <div class="panel panel-default">
            <div class="panel-heading"><button type="button" class="btn btn-xs btn-default pull-right" rel="del"><i class="fa fa-times text-danger"></i></button> <span title="##filepath##">##filepath##</span></div>
            <div class="panel-body">
                <div class="media">
                    <div class="pull-left">

                        <img class="file-ico"  onerror="this.src='<?php echo WPDM_BASE_URL.'assets/file-type-icons/_blank.png';?>';" src="##preview##" />
                    </div>
                    <div class="media-body">
                        <input placeholder="<?php _e('File Title','wpdmpro'); ?>" title="<?php _e('File Title','wpdmpro'); ?>" class="form-control" type="text" name='file[fileinfo][##fileindex##][title]' value="##filepath##" /><br/>
                        <div class="input-group">
                            <input placeholder="<?php _e('File Password','wpdmpro'); ?>"  title="<?php _e('File Password','wpdmpro'); ?>" class="form-control inline" type="text" id="indpass_##fileindex##" name='file[fileinfo][##fileindex##][password]' value="">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" class="genpass" title='Generate Password' onclick="return generatepass('indpass_##fileindex##')"><i class="fa fa-ellipsis-h"></i></button>
                                    </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
jQuery(function(){
        jQuery( "#ftabs" ).tabs();

        jQuery('#rmta').click(function(){
            var d = new Date();
            var ID = d.getTime();
        var file = jQuery('#rurl').val();
        var filename = file;
            jQuery('#rurl').val('');
            if(file == ''){
                alert("Invalid url");
            return false;
            }

            var html = jQuery('#wpdm-file-entry').html();
            var ext = file.split('.');
            ext = ext[ext.length-1];
            if(ext.indexOf('://')) ext = 'url';
            else
            if(ext.length==1 || ext==filename || ext.length>4 || ext=='') ext = '_blank';

            var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/"+ext.toLowerCase()+".png";
            html = html.replace(/##filepath##/g, file);
            html = html.replace(/##fileindex##/g, ID);
            html = html.replace(/##preview##/g, icon);
            jQuery('#currentfiles').prepend(html);


    });

});

</script>

<?php }

do_action("wpdm_attach_file_metabox");