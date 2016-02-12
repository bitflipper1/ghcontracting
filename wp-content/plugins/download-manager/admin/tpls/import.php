<div class="wrap w3eden">
    <div class="panel panel-primary"  id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-users"></i> &nbsp; <?php echo __("Bulk Import", "wpdmpro"); ?></b>

        </div>
        <div class="panel-body">
<script type="text/javascript" src="<?php echo plugins_url().'/download-manager/assets/js/jqueryFileTree.js';?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url().'/download-manager/assets/css/jqueryFileTree.css';?>" />
<link rel="stylesheet" href="<?php echo plugins_url('/download-manager/assets/css/chosen.css'); ?>" />
<script language="JavaScript" src="<?php echo plugins_url('/download-manager/assets/js/chosen.jquery.min.js'); ?>"></script>
<script language="JavaScript" src="<?php echo plugins_url('/download-manager/assets/js/jquery.cookie.js'); ?>"></script>
<style type="text/css">.jqueryFileTree li{line-height: 20px;}</style>
<div style="margin-top: 50px">
<div class="row">
<div class="col-md-3">

    <form action="admin.php?task=wpdm-import-csv" method="post" enctype="multipart/form-data">

    <div class="panel panel-default">
    <div class="panel-heading"><b>Import form CSV File:</b></div>
    <div class="panel-body">
        <input type="file" name="csv" />
        <code>Download sample csv file: <a href="<?php echo plugins_url('/download-manager/sample.csv'); ?>">sample.csv</a></code>
    </div>
        <div class="panel-footer">
            <input type="submit" value="Import CSV" class="btn btn-primary" />

        </div>
    </div>

</form>
<div class="panel panel-default">
    <div class="panel-heading"><b>Select Dir:</b></div>
    <div class="panel-body">
        <div id="dtree" style="height: 350px;overflow: auto;"></div>
    </div>
    <div id="path" class="panel-footer">
        <form method="post">
            <div class="input-group">
            <input type="text" class="form-control" name="wpdm_importdir" value="<?php echo get_option('wpdm_importdir'); ?>" id="pathd" size="50" />
                <span class="input-group-btn">
            <input type="submit" id="slctdir" value="Browse Files" class="btn btn-default">
                    </span>
                </div>
        </form><script language="JavaScript">
            <!--
            jQuery(document).ready( function() {
                jQuery('#dtree').fileTree({
                    root: '<?php echo get_option('_wpdm_file_browser_root',ABSPATH); ?>/',
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
</div>




    
</div>
<div class="col-md-9">    <?php $wpdmimported = isset($_COOKIE['wpdmimported'])?explode(",", $_COOKIE['wpdmimported']):array(); ?>
<form action="" method="post">

<div class="panel panel-default">
  <table class="table">
    <thead>
    <tr>
        <td colspan="6">
            <select name="cats" id="cats" multiple="multiple" style="width:400px;max-width: 40%;" data-placeholder="Assign Categories">
                <?php $terms = get_terms('wpdmcategory','hide_empty=0'); print_r($terms);
                foreach($terms as $term){
                    echo "<option value='{$term->term_id}'>{$term->name}</option>";
                }
                ?>
            </select>
            <select name="access" id="access" style="width:400px;max-width: 40%;" multiple="multiple" data-placeholder="Allow Access to Role(s)">
                <?php


                ?>

                <option value="guest" selected="selected"> All Visitors</option>
                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach( $roles as $role => $name ) {




                    ?>
                    <option value="<?php echo $role; ?>" > <?php echo $name; ?></option>
                <?php } ?>
            </select>

            &nbsp;<input type="button" id="idel" value="Import Selected Files" class="btn btn-primary" ></td>
    </tr>
      <tr>
        <th width="20" class="check-column"><input type="checkbox" class="multicheck"></th>
        <th >File name</th>
        <th >Title</th>        
        <th >Description</th>        
        <th width=100>Password</th>
        <th width=100>Size</th>
         
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th width="20" class="check-column"><input type="checkbox" class="multicheck"></th>
        <th >File name</th>
        <th >Title</th>        
        <th >Description</th>        
        <th  width=100>Password</th>
        <th width=100>Size</th>
         
      </tr>
    </tfoot>
    <tbody id="the-list" class="list:post">
      <?php 
	$k = 0;
    $limit = 50;
    $total = isset($fileinfo)?count($fileinfo):0;
    $p = isset($_GET['paged'])?$_GET['paged']:1;
	$s = ($p-1)*$limit;
    $max = $s+$limit;
    if($max>$total) $max = $total;
	for($index=$s; $index<$max; $index++): $value = $fileinfo[$index]; $tmptitle = ucwords(str_replace(array("-","_",".")," ",$value['name'])); ?>
      <tr for="file-<?php echo $index; ?>" valign="top" class="importfilelist" id="<?php echo $index; ?>">
        <th   class=" check-column" style="padding-bottom: 0px;"><input type="checkbox" rel="<?php echo $index; ?>" id="file-<?php echo $index; ?>" class="checkbox dim" value="<?php echo $value['name'] ?>"></th>
        <td><label for="file-<?php echo $index; ?>"><strong><?php echo $value['name'] ?></strong></label> <?php if(in_array($value['name'],$wpdmimported)) echo '<span style="margin-left:10px;background:#E2FFE5;color:#000;font-size:11px;font-family:\'Courier New\';padding:2px 7px;">imported</span>'; ?></td>
        <td><input size="20" class="form-control input-sm" type="text" id="title<?php echo $index; ?>" name="file[<?php echo $index; ?>][title]" value="<?php echo $tmptitle; ?>"></td>
        <td><input size="40" class="form-control input-sm" type="text" id="desc<?php echo $index; ?>" name="file[<?php echo $index; ?>][description]"></td>
        <td><input size="10" class="form-control input-sm" type="text" id="password<?php echo $index; ?>" name="file[<?php echo $index; ?>][password]"></td>
        <td>
		<?php echo number_format(@filesize(get_option('wpdm_importdir').$value['name'])/(1024*1024),4); ?> MB
		</td>
         
      </tr>
      <?php
	  
	  $k++;
	  endfor; ?>
	  
	  
	  
	  
    </tbody>
  </table>
    <div class="panel-footer">
        <input type="button" id="idel1" value="Import Selected Files" class="btn btn-primary" >
    </div>
</div>
  <?php
  
$page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($total/$limit),
    'current' => $p
));


?>

<div id="ajax-response"></div>

<div class="tablenav">

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
    number_format_i18n( ( $_GET['paged'] - 1 ) * $limit + 1 ),
    number_format_i18n( min( $_GET['paged'] * $limit, $total ) ),
    number_format_i18n( $total ),
    $page_links
); echo $page_links_text; ?></div>
<?php } ?>

 
</div>
   

   
</form>     
</div></div></div>
</div>
</div>
    </div>
<script type="text/javascript">
  
     jQuery('#idel,#idel1').click(function(){
         jQuery('.dim').each(function(){
             if(this.checked)
             dimport(jQuery(this).attr('rel'),jQuery(this).val());
         });
     });
     
     function dimport(id,file){
       var wpdmimported = [];  
       jQuery('#'+id).fadeTo('slow', 0.4);        
       jQuery.post(ajaxurl,{action:'wpdm_dimport',fname:file, title:jQuery('#title'+id).val(),password:jQuery('#password'+id).val(),access:jQuery('#access').val(),description:jQuery('#desc'+id).val(),category:jQuery('#cats').val()},function(res){
          jQuery('#'+id).fadeOut().remove(); 
          wpdmimported = jQuery.cookie('wpdmimported');
          wpdmimported = wpdmimported + "," + file;          
          jQuery.cookie('wpdmimported',wpdmimported,{expires:360});
       })
     }
     jQuery('select').chosen({});
     </script>
</div>