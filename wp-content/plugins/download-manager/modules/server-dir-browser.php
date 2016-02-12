<?php

function wpdm_odir_tree(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_odir_tree') return;
    if(!current_user_can('access_server_browser')) { echo "<ul><li>".__('Not Allowed!','wpdmpro')."</li></ul>"; die(); }
    $_POST['dir'] = isset($_POST['dir'])?urldecode($_POST['dir']):'';
    $root = '';
    if( file_exists($root . $_POST['dir']) ) {
	    $files = scandir($root . $_POST['dir']);
	    natcasesort($files);
	    if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		    echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		    // All dirs
		    foreach( $files as $file ) {
			    if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
				    echo "<li class=\"directory collapsed\"><a onclick=\"odirpath(this)\" class=\"odir\" href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
			    }
		    }
		    echo "</ul>";
	    }
    }
    die();
}

function wpdm_dir_browser(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_dir_browser') return;
    if(!current_user_can('access_server_browser')) { echo "<ul><li>".__('Not Allowed!','wpdmpro')."</li></ul>"; die(); }

    ?>
    <script type="text/javascript" src="<?php echo plugins_url().'/download-manager/assets/js/jqueryFileTree.js';?>"></script>
    <link rel="stylesheet" href="<?php echo plugins_url().'/download-manager/assets/css/jqueryFileTree.css';?>" />
    <style type="text/css">.jqueryFileTree li{line-height: 20px;}</style>
    <div class="wrap">
    <div class="icon32" id="icon-categories"><br></div>
    <h2>Browse</h2>
    <div id="dtree" style="width: 200px;float: left;"></div>
    <div id="path" style="width: 380px;float: right;">
    <b>Current Dir:</b>
    <input type="text" id="pathd" size="50" />
    <input type="button" id="slctdir" value="Select" class="button-primary">
    </div>
    <script language="JavaScript">
    <!--
      jQuery(document).ready( function() {
            jQuery('#dtree').fileTree({
                root: '<?php echo get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']); ?>/',
                script: 'admin.php?task=wpdm_odir_tree',
                expandSpeed: 1000,
                folderEvent: 'click',
                collapseSpeed: 1000,
                multiFolder: false
            }, function(file) {
                alert(file);
                var sfilename = file.split('/');
                var filename = sfilename[sfilename.length-1];
                jQuery('#serverfiles').append('<li><label><input checked=checked type="checkbox" value="'+file+'" name="imports[]" class="role"> &nbsp; '+filename+'</label></li>');
                tb_remove();
            });

            jQuery('#TB_ajaxContent').css('width','630px').css('height','90%');

      });
      function odirpath(a){
          jQuery('#pathd').val(a.rel);
      }

      jQuery('#slctdir').click(function(){
          jQuery('#srvdir').val(jQuery('#pathd').val());
          //jQuery('#currentfiles table').load('admin.php?task=wpdm_fetch_dir&dir='+jQuery('#pathd').val());
          tb_remove();
      });
    //-->
    </script>
    </div>
    <?php
    die();
}

function wpmp_dir_browser_metabox($post){
    ?>
    <div class="w3eden"><div class="input-group">
    <input class="form-control" type="text" id="srvdir" value="<?php echo get_post_meta($post->ID,'__wpdm_package_dir', true); ?>" name="file[package_dir]" />
            <div class="input-group-btn">
    <a href="admin.php?page=file-manager&task=wpdm_dir_browser" class="thickbox btn btn-default"><i class="fa fa-folder-open"></i></a>
    </div>
</div> </div>



    <?php
}

function wpdm_get_files($dir, $recur = true){
    $dir = rtrim($dir,"/")."/";
    if($dir == '/' || $dir == '') return array();
    if(!is_dir($dir)) return array();
    $tmpfiles = file_exists($dir)?array_diff( scandir( $dir ), Array( ".", ".." ) ):array();
    $files = array();
    foreach($tmpfiles as $file){
        if( is_dir($dir.$file) && $recur == true) $files = array_merge($files,wpdm_get_files($dir.$file, true));
        else
        $files[] = $dir.$file;
    }
    return $files;

}

function wpdm_fetch_dir(){
    if($_REQUEST['task']!='wpdm_fetch_dir') return;
    if(!current_user_can('access_server_browser')) return "<ul><li>".__('Not Allowed!','wpdmpro')."</li></ul>";
    if($_REQUEST['dir']=='')
    $dir = get_wpdm_meta((int)$_REQUEST['fid'],'package_dir');
    else
    $dir = $_REQUEST['dir'];
    $files = scandir($dir);
    array_shift($files);
    array_shift($files);
    ?>
    <thead>
    <tr>
    <th style="width: 50px;">Action</th>
    <th>Filename</th>
    <th>Title</th>
    <th style="width: 130px;">Password</th>
    </tr>
    </thead>
    <?php
    foreach($files as $file_index=>$file){
        if(!is_dir($dir.$file)){
        ?>
        <tr  class="cfile">
        <td>
        <input class="fa" type="hidden" value="<?php echo $file; ?>" name="files[]">
        <img align="left" rel="del" src="<?php echo plugins_url('download-manager/images/minus.png'); ?>">
        </td>
        <td><?php echo $dir.$file; ?></td>
        <td><input style="width:99%" type="text" name='wpdm_meta[fileinfo][<?php echo $dir.$file; ?>][title]' value="<?php echo $fileinfo[$dir.$file]['title'];?>"></td>
        <td><input size="10" type="text" id="indpass_<?php echo $file_index;?>" name='wpdm_meta[fileinfo][<?php echo $dir.$file; ?>][password]' value="<?php echo $fileinfo[$dir.$file]['password'];?>"> <img style="cursor: pointer;float: right;margin-top: -3px" class="genpass"  title='Generate Password' onclick="return generatepass('indpass_<?php echo $file_index;?>')" src="<?php echo plugins_url('download-manager/images/generate-pass.png'); ?>" alt="" /></td>
        </tr>
        <?php
    }}
    die();
}

if(is_admin()){

    add_action("init","wpdm_dir_browser");
    add_action("init","wpdm_odir_tree");
    //add_action("init","wpdm_fetch_dir");
    add_action("add_new_file_sidebar","wpmp_dir_browser_metabox");
}
