<div class="wrap w3eden">
     <div class="panel panel-primary" id="wpdm-wrapper-panel">
         <div class="panel-heading">
             <b><i class="fa fa-magic"></i> &nbsp; <?php echo __("Templates", "wpdmpro"); ?></b>
             <div class="pull-right">
                 <a href="edit.php?post_type=wpdmpro&page=templates&_type=page&task=NewTemplate" class="btn btn-sm btn-default <?php echo wpdm_query_var('_type')=='page'?'active':''; ?>"><i class="fa fa-plus"></i> <?php echo __("Create Page Template", "wpdmpro"); ?></a> <a href="edit.php?post_type=wpdmpro&page=templates&_type=link&task=NewTemplate" class="btn btn-sm btn-default <?php echo wpdm_query_var('_type')=='link'?'active':''; ?>"><i class="fa fa-plus"></i> <?php echo __("Create Link Template", "wpdmpro"); ?></a>
             </div>
             <div style="clear: both"></div>
         </div>
         <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
             <li><a href="edit.php?post_type=wpdmpro&page=templates&_type=link" id="basic"><?php echo __("Link Templates", "wpdmpro"); ?></a></li>
             <li><a href="edit.php?post_type=wpdmpro&page=templates&_type=page" id="basic"><?php echo __("Page Templates", "wpdmpro"); ?></a></li>
             <li class="active"><a href="" id="basic"><?php echo __('Template Editor','wpdmpro'); ?></a></li>

         </ul>
         <div class="tab-content" style="padding-top: 15px;">



<div style="padding: 15px;">
   
<div style="margin-left:10px;float: left;width:66%">
<form action="" method="post">
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo wpdm_query_var('task','txt')=='NewTemplate'?'New':'Edit'; ?> <?php echo $_GET['_type']=='page'?__('Page','wpdmpro'):__('Link','wpdmpro'); ?> <?php echo __('Template','wpdmpro'); ?></div>
<table cellspacing="0" class="table">

   
   <?php
    $default['link'] = '[thumb_50x50]  
<br style="clear:both"/>    
<b>[popup_link]</b><br/>
<b>[download_count]</b> downloads';    

$default['popup'] = '[thumb_400x200]
<fieldset class="pack_stats">
<legend><b>Package Statistics</b></legend>
<table class="table table-bordered">
<tr><td>Total Downloads:</td><td>[download_count]</td></tr>
<tr><td>Stock Limit:</td><td>[quota]</td></tr>
<tr><td>Total Files:</td><td>[file_count]</td></tr>
</table>
</fieldset>
<br style="clear:both"/>

[download_link]';

    $default['page'] = '[thumb_700x400]
<br style="clear:both"/>
[description]
<fieldset class="pack_stats">
<legend><b>Package Statistics</b></legend>
<table class="table table-bordered">
<tr><td>Total Downloads:</td><td>[download_count]</td></tr>
<tr><td>Stock Limit:</td><td>[quota]</td></tr>
<tr><td>Total Files:</td><td>[file_count]</td></tr>
</table>
</fieldset><br>
[download_link]';
    $ttype = isset($_GET['_type'])?esc_attr($_GET['_type']):'link';
    $tpl = maybe_unserialize(get_option("_fm_{$ttype}_templates",array()));
    if(wpdm_query_var('tplid','txt')!=""){
        $tpl = isset($tpl[wpdm_query_var('tplid','txt')])?$tpl[wpdm_query_var('tplid','txt')]:array();
    }
    $tpl['content'] = isset($tpl['content'])?$tpl['content']:$default[$ttype];

    if(isset($_GET['clone'])&&$_GET['clone']!=''){

        $ltpldir = get_stylesheet_directory().'/download-manager/'.$ttype.'-templates/';
        if(!file_exists($ltpldir) || !file_exists($ltpldir.basename($_GET['clone'])))
            $ltpldir = WPDM_BASE_DIR.'tpls/'.$ttype.'-templates/';

        if(file_exists($ltpldir.basename($_GET['clone']))) {
            $template = file_get_contents($ltpldir . basename($_GET['clone']));
            $regx = isset($_GET['_type']) && $_GET['_type'] == 'link' ? "/<\!\-\-[\s]*WPDM[\s]+Link[\s]+Template[\s]*:([^\-\->]+)\-\->/" : "/<\!\-\-[\s]*WPDM[\s]+Template[\s]*:([^\-\->]+)\-\->/";
            $type = ucfirst($_GET['_type']);
            $tpl['title'] = "New {$ttype} Template";
            $tpl['content'] = preg_replace($regx, "", $template);
        }
    }

?> 
    
    <tbody class="list:post" id="the-list">    
    <tr valign="top" class="alternate author-self status-inherit" id="post-8">
                <td class="author column-author">
                <input type="hidden" name="tplid" value="<?php echo isset($_GET['tplid'])?$_GET['tplid']:uniqid(); ?>" required="required">
                <?php echo __('Title','wpdmpro'); ?>:<br>
                <input type="text" style="width: 99%" name="tpl[title]" required="required" title="Enter an tmeplate name" x-moz-errormessage="Enter an tmeplate name" value="<?php echo isset($tpl['title'])?htmlspecialchars($tpl['title']):''; ?>" class="form-control">
                <ul class="nav nav-tabs" style="margin-top: 10px; ">
                    <li class="active"><a href="#code" data-toggle="tab"><?php echo __('Code','wpdmpro'); ?></a></li>
                    <li><a href="#preview" data-toggle="tab"><?php echo __('Preview','wpdmpro'); ?></a></li>
                </ul>
                <div class="tab-content tpleditor">
                    <div class="tab-pane active" id="code">
                        <nav id="navbar-example" class="navbar navbar-default navbar-static" role="navigation">
                            <div class="container-fluid">

                                <div class="collapse navbar-collapse bs-example-js-navbar-collapse">
                                    <ul class="nav navbar-nav">
                                        <li class="dropdown">
                                            <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Package Info <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[title]">Title</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[description]">Description</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[excerpt_80]">Excerpt</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[page_link]">Page Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[page_url]">Page URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[categories]">Categories</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[tags]">Tags</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown">Package Meta <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[thumb_200x200]">Thumbnail</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[icon]">Icon</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[create_date]">Create Date</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[update_date]">Update Date</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[download_url]">Download URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[download_link]">Download Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[quota]">Stock Limit</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_list]">File List</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_list_extended]">Extended File List</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[version]">Version</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[audio_player]">Aidio Player</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[audio_player_single]">Aidio Player Single</a></li>
                                            </ul>
                                        </li>
                                        <?php do_action('wpdm_template_editor_menu'); ?>
                                    </ul>

                                </div><!-- /.nav-collapse -->
                            </div><!-- /.container-fluid -->
                        </nav> <!-- /navbar-example -->
                        <textarea spellcheck='false' style="max-width: 100%;min-width: 100%;height:250px;font-family:'Courier', monospace" id="templateeditor" class="form-control" name="tpl[content]"><?php echo stripslashes(htmlspecialchars($tpl['content'])); ?></textarea>
                    </div>
                    <div class="tab-pane" id="preview">
                        <i class="fa fa-spinner fa-spin"></i> Loading Preview...
                    </div>
                </div>

                <div id="poststuff" class="postarea">
              <?php //the_editor(stripslashes($tpl['content']),'tpl[content]','content', true, true); ?>                
</div>
				
				


                 
                <input type="submit" value="<?php echo __('Save Template','wpdmpro'); ?>" class="btn btn-primary">
				
				<br/>
				
				
                </td>
                
     </tr>
    </tbody>
</table>
    </div>
</form>
 
          
</div>

     







<div style="margin-left:10px;float: left;width:30%">
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo __('Template Variables','wpdmpro'); ?></div>
                <div  style="height: 550px;overflow-y: auto;">
                   <table id="template_tags" class="table" style="margin-top: -1px">
                   <?php if($ttype=='link'){ ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[popup_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('download link open as popup','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[page_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('download link open as page','wpdmpro'); ?></td></tr>
                   <?php } ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[page_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Package details page url','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[title]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show package title','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[categories]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show categories','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[tags]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show tags','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[icon]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show icon if available','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[thumb_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show preview thumbnail with specified width and height if available,l eg: [thumb_700x400] will show 700px &times; 400px image preview ','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[thumb_url_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('returns preview thumbnail url with specified width and height if available,l eg: [thumb_url_700x400] will return 700px &times; 400px image preview url','wpdmpro'); ?></td></tr>
                   <?php if($ttype!='link'){ ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[gallery_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show additional preview thumbnails in gallery format, each image height and with will be same as specified, eg: [gallery_50x30] will show image gallery of additional previews and each image size will be 50px &timesx40px','wpdmpro'); ?></td></tr>
                   <!--<tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[slider-previews]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show previews in a slider type gallery','wpdmpro'); ?></td></tr>                -->
                   <?php } ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[excerpt_chars]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show a short description of package from description, eg: [excerpt_200] will show short description with first 200 chars of description','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[description]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('package description','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_count]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('download counter','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('download url','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('direct link to download using download link label','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[quota]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('number of downloads to expire download quota','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[file_list]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show list of all files in a package','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[version]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show package version','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[create_date]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show package create date','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[update_date]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('show package update date','wpdmpro'); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[audio_player]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Show mp3 player with your page or link template.','wpdmpro'); ?></td></tr>
                   <?php do_action("wpdm_template_tag_row"); ?>
                   </table>
                </div>

        </div>
</div>

<script>

    jQuery.fn.extend({
        insertAtCaret: function(myValue){
            return this.each(function(i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                }
                else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            });
        }
    });

    jQuery(function(){
        jQuery('a[href="#preview"]').on('shown.bs.tab', function (e) {
            //e.target // activated tab
            //e.relatedTarget // previous tab
            jQuery('#preview').html('<i class="fa fa-spinner fa-spin"></i> Loading Preview...');
            jQuery.post(ajaxurl,{action:'template_preview',template:jQuery('#templateeditor').val()},function(res){
                jQuery('#preview').html(res);
            });


        });

        jQuery('.dropdown-menu a').click(function(e){
            e.preventDefault();
            var tag = jQuery(this).attr('href').replace('#','');
            jQuery('#templateeditor').insertAtCaret(tag);
        });

        jQuery('#template_tags .form-control').on('select', function(){
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        })
    });

</script>



<div style="clear: both"></div>


</div>
</div>
</div>
</div>


<style>
    #template_tags .form-control{
        background: #fafafa;
    }
</style>