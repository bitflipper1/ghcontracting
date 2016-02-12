
 <style>
 .frm td{
     padding:5px;
     border-bottom: 1px solid #eeeeee;
    
     font-size:10pt;
     
 }
 h4{
     color: #336699;
     margin-bottom: 0px;
 }
 em{
     color: #888;
 }
.wp-switch-editor{
    height: 27px !important;
}
 </style>



                    <?php /* Options are deprecated from v4.0.0
                <div class="panel panel-default">
                    <div class="panel-heading">Administration Options</div>
                    <div class="panel-body">

                            <div class="form-group">
                                <label><?php echo __('Allow only','wpdmpro'); ?> </label><br/>
                                <select name="access_level">
                                        <option value="level_10">Administrator</option>
                                        <option value="level_5" <?php echo get_option('access_level',true)=='level_5'?'selected':''?>>Editor</option>
                                        <option value="level_2" <?php echo get_option('access_level',true)=='level_2'?'selected':''?>>Author</option>
                                    </select> <?php echo __('and upper level users to administrate this plugin','wpdmpro'); ?>

                            </div>

                        <div class="form-group">
                            <label><?php echo __('Multi-User','wpdmpro'); ?> </label></br>
                                    <select name="wpdm_multi_user">
                                        <option value="0"><?php echo __('Disabled','wpdmpro'); ?></option>
                                        <option value="1" <?php echo get_option('wpdm_multi_user')=='1'?'selected':''?>><?php echo __('Enabled','wpdmpro'); ?></option>
                                    </select><br/>
                                    <em><?php echo __('if multi-user enabled, only users with role "administrator" will able to see/mamane all wpdm packages, all other allowed users will able to manage their own  packages only'); ?><br/>
                                        <?php echo __('If multi-user disabled, all allowed users will able to see, manage all wpdm packages'); ?></em>
                             </div>
                        <div class="form-group">
                            <label><?php echo __('Custom WPDM Super Admin','wpdmpro'); ?> </label>
                                    <input type="text" class="form-control" name="__wpdm_custom_admin" value="<?php echo get_option('__wpdm_custom_admin',''); ?>"><br/>
                                    <em><?php echo __('Incase, if you want to allow any specific user(s) to administrate wpdm, enter his/her usernames above, usernames should separated by comma (",").'); ?></em>
                            </div>



                    </div>
                </div>
*/ ?>

                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('URL Structure','wpdmpro'); ?></div>
                    <div class="panel-body">
                        <p><em><?php echo __('If you like, you may enter custom structures for your wpdm category and package URLs here. For example, using "<b>packages</b>" as your category base would make your category links like http://example.org/<b>packages</b>/category-slug/. If you leave these blank the defaults will be used.'); ?><br/>
                                <?php echo __("Caution: Use unique word for each url base. Also, don't create any page or post with same slug you used for WPDM URL Bases below.",'wpdmpro'); ?>
                        </em></p>
                        <div class="form-group">
                            <label><?php echo __('WPDM Category URL Base','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="__wpdm_curl_base" value="<?php echo get_option('__wpdm_curl_base','downloads'); ?>" />
                         </div>
                        <div class="form-group">
                            <label><?php echo __('WPDM Package URL Base','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="__wpdm_purl_base" value="<?php echo get_option('__wpdm_purl_base','download'); ?>" />
                         </div>
                        <div class="form-group">
                            <label><?php echo __('WPDM Archive Page','wpdmpro'); ?></label><br/>
                            <select id="wpdmap" class="form-control" name="__wpdm_has_archive" style="width: 120px">
                                <option value="0"><?php _e('Disabled','wpdmpro'); ?></option>
                                <option value="1" <?php echo get_option('__wpdm_has_archive')=='1'?'selected':''?>><?php _e('Enabled','wpdmpro'); ?></option>
                            </select>

                        </div>
                        <div class="form-group" id="aps" <?php echo get_option('__wpdm_has_archive')=='1'?'':'style="display:none;"'?>>
                            <label><?php _e('Archive Page Slug','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="__wpdm_archive_page_slug" value="<?php echo get_option('__wpdm_archive_page_slug','all-downloads'); ?>" />
                            <em></em>
                        </div>


                    </div>
                </div>



                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('Access Settings','wpdmpro'); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __('When user is not allowed to download:','wpdmpro'); ?></label><br/>
                            <select name="_wpdm_hide_all">
                                <option value="0"><?php echo __('Only Block Download Link','wpdmpro'); ?></option>
                                <option value="1" <?php echo get_option('_wpdm_hide_all',0)==1?'selected=selected':''; ?>><?php echo __('Hide Everything','wpdmpro'); ?></option>
                            </select>
                         </div>

                       <div class="form-group">
                            <label><?php echo __('File Browser Root:','wpdmpro'); ?></label><span title="<?php echo __("Root dir for server file browser.<br/><b>*Don't add tailing slash (/)</b>",'wpdmpro'); ?>" class="info infoicon">(?)</span>
                           <div class="input-group">
                           <input type="text" class="form-control" value="<?php echo get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']); ?>" name="_wpdm_file_browser_root" id="_wpdm_file_browser_root" />
                            <span class="input-group-btn">
                                    <button class="btn btn-default ttip" title="<?php _e('Reset Base Dir'); ?>" type="button" onclick="jQuery('#_wpdm_file_browser_root').val('<?php echo rtrim(ABSPATH,'/'); ?>');"><i class="fa fa-repeat"></i></button>
                                </span>
                       </div>
                       </div>

                        <div class="form-group">
                            <label><?php echo __('File Browser Access:','wpdmpro'); ?></label><br/>
                            <select style="width: 100%" name="_wpdm_file_browser_access[]" multiple="multiple" data-placeholder="<?php _e('Who will have access to server file browser','wpdmpro'); ?>">
                                <?php

                                $currentAccess = maybe_unserialize(get_option( '_wpdm_file_browser_access', array('administrator')));
                                $selz = '';

                                ?>

                                <?php
                                global $wp_roles;
                                $roles = array_reverse($wp_roles->role_names);
                                foreach( $roles as $role => $name ) {



                                    if(  $currentAccess ) $sel = (in_array($role,$currentAccess))?'selected=selected':'';
                                    else $sel = '';



                                    ?>
                                    <option value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></option>
                                <?php } ?>
                            </select>
                        </div>


                        <div class="form-group">
                            <label><a name="fbappid"></a><?php echo __('Facebook APP ID','wpdmpro'); ?></label>
                           <input type="text" class="form-control" name="_wpdm_facebook_app_id" value="<?php echo get_option('_wpdm_facebook_app_id'); ?>">
                           <em>Create new facebook app from <a target="_blank" href='https://developers.facebook.com/apps'>here</a></em>
                         </div>

                        <div class="form-group">
                            <label><a name="liappid"></a><?php echo __('LinkedIn Client ID','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="_wpdm_linkedin_client_id" value="<?php echo get_option('_wpdm_linkedin_client_id'); ?>">
                            <em>Create new linkedin app from <a target="_blank" href='https://www.linkedin.com/developer/apps'>here</a></em>
                        </div>
                        <fieldset>
                        <legend><?php echo __('reCAPTCHA Lock Settings','wpdmpro'); ?></legend>
                        <div class="form-group">
                            <label><a name="liappid"></a><?php echo __('reCAPTCHA Site Key','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="_wpdm_recaptcha_site_key" value="<?php echo get_option('_wpdm_recaptcha_site_key'); ?>">
                            <em>Register a new site for reCAPTCHA from <a target="_blank" href='https://www.google.com/recaptcha/admin#list'>here</a></em>
                        </div>
                        <div class="form-group">
                            <label><a name="liappid"></a><?php echo __('reCAPTCHA Secret Key','wpdmpro'); ?></label>
                            <input type="text" class="form-control" name="_wpdm_recaptcha_secret_key" value="<?php echo get_option('_wpdm_recaptcha_secret_key'); ?>">
                            <em>Register a new site for reCAPTCHA from <a target="_blank" href='https://www.google.com/recaptcha/admin#list'>here</a></em>
                        </div>
                        </fieldset>

                    </div>
                </div>

 <div class="panel panel-default">
     <div class="panel-heading"><?php _e('Email Verification Settings','wpdmpro'); ?></div>
     <div class="panel-body">

         <div class="form-group">
             <label><?php echo __('Blocked Domains:','wpdmpro'); ?></label><br/>
             <textarea name="__wpdm_blocked_domains" class="input form-control"><?php echo get_option('__wpdm_blocked_domains',''); ?></textarea>
             <em>One domain per line</em>
         </div>

        <div class="form-group">
             <label><?php echo __('Blocked Emails:','wpdmpro'); ?></label><br/>
             <textarea name="__wpdm_blocked_emails" class="input form-control"><?php echo get_option('__wpdm_blocked_emails',''); ?></textarea>
             <em>One email per line</em>
         </div>


     </div>
 </div>



 <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('Upload Settings','wpdmpro'); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __('When File Already Exists:','wpdmpro'); ?></label><br/>
                            <select name="__wpdm_overwrrite_file">
                                <option value="0"><?php echo __('Rename New File'); ?></option>
                                <option value="1" <?php echo get_option('__wpdm_overwrrite_file',0)==1?'selected=selected':''; ?>><?php echo __('Overwrite', 'wpdmpro'); ?></option>
                            </select>
                         </div>
                        <hr/>
                        <div class="form-group">
                            <input type="hidden" value="0" name="__wpdm_sanitize_filename" />
                            <label><input style="margin: 0 10px 0 0" <?php checked(1, get_option('__wpdm_sanitize_filename',0)); ?> type="checkbox" value="1" name="__wpdm_sanitize_filename"><?php _e('Sanitize Filename','wpdmpro'); ?></label><br/>
                            <em><?php _e('Check the option if you want to sanitize uploaded file names to remove illegal chars','wpdmpro'); ?></em>
                            <br/>

                        </div>


                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading"><?php echo __('Messages','wpdmpro'); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __('Plugin Update Notice:','wpdmpro'); ?></label><br>
                            <select name="wpdm_update_notice">
                                <option value="0"><?php echo __('Enabled', 'wpdmpro'); ?></option>
                                <option value="disabled" <?php selected(get_option('wpdm_update_notice'),'disabled'); ?>><?php echo __('Disabled', 'wpdmpro'); ?></option>
                            </select>
                         </div>

                        <div class="form-group">
                            <label><?php echo __('Permission Denied Message for Packages:','wpdmpro'); ?></label>
                                <textarea id="wpdm_permission_msg" name="wpdm_permission_msg" class="form-control"><?php echo stripslashes(get_option('wpdm_permission_msg'));?></textarea>
                         </div>

                        <!-- div class="form-group">
                            <label><?php echo __('Category Access Blocked Message:','wpdmpro'); ?></label>
                            <textarea class="form-control" cols="70" rows="6" name="__wpdm_category_access_blocked"><?php echo stripcslashes(get_option('__wpdm_category_access_blocked',__('You are not allowed to access this category!','wpdmpro'))); ?></textarea><br>

                        </div -->

                 <div class="form-group">
                            <label><?php echo __('Login Required Message:','wpdmpro'); ?></label>
                     <textarea class="form-control" cols="70" rows="6" name="wpdm_login_msg"><?php echo get_option('wpdm_login_msg', false)?stripslashes(get_option('wpdm_login_msg')):('<div class="w3eden"><div class="panel panel-default"><div class="panel-body"><span class="text-danger">Login is required to access this page</span></div><div class="panel-footer text-right"><a href="'.wp_login_url().'?redirect_to=[this_url]" class="btn btn-danger btn-sm"><i class="fa fa-lock"></i> Login</a></div></div></div>'); ?></textarea><br>
                     <input  type="checkbox" name="__wpdm_login_form" value="1" <?php echo get_option('__wpdm_login_form',0)==1?'checked=checked':'';?> > <?php echo __('Show Only Login Form Instead of Login Required Message','wpdmpro'); ?>

                 </div>

                    </div>
                </div>

 <div class="panel panel-default">
     <div class="panel-heading"><?php echo __('File Download','wpdmpro'); ?></div>
     <div class="panel-body">

         <div class="form-group">
             <label><input type="checkbox" <?php checked(get_option('__wpdm_ind_stats'),1); ?> style="margin: 0" name="__wpdm_ind_stats" value="1"> <?php _e('Increase download count on individual file download','wpdmpro'); ?></label><br/>
             <em><?php _e('Increase download count for the multi-file package when someone downloads even a file from list', 'wpdmpro'); ?></em>
         </div><hr/>
         <div class="form-group"><input type="hidden" name="__wpdm_individual_file_download" value="0">
             <label><input type="radio" <?php checked(get_option('__wpdm_individual_file_download', 1),1); ?> style="margin: 0" name="__wpdm_individual_file_download" value="1">  <?php _e('Enable Single File Download','wpdmpro'); ?> &nbsp; </label>
             <label><input type="radio" <?php checked(get_option('__wpdm_individual_file_download', 1),0); ?> style="margin: 0" name="__wpdm_individual_file_download" value="0">  <?php _e('Disable Single File Download','wpdmpro'); ?></label><br/>
             <em><?php _e('Check this option if you want to enable/disable single file download from multi-file package', 'wpdmpro'); ?></em>
         </div><hr/>
         <div class="form-group"><input type="hidden" name="__wpdm_cache_zip" value="0">
             <label><input type="checkbox" <?php checked(get_option('__wpdm_cache_zip'),1); ?> style="margin: 0" name="__wpdm_cache_zip" value="1">  <?php _e('Cache created zip file from multi-file package','wpdmpro'); ?></label><br/>
             <em><?php _e('Check this option if you want to cache the zip file created from multi-file package when someone tries to download', 'wpdmpro'); ?></em>
         </div><hr/>

         <div class="form-group">
             <label><?php echo __('Download Speed:','wpdmpro'); ?></label>
             <div class="input-group">
                 <input type=text class="form-control" name="__wpdm_download_speed" value="<?php echo intval(get_option('__wpdm_download_speed',4096)); ?>" />
                 <span class="input-group-addon">KB</span>
             </div>
         </div>
         <hr/>
         <em class="note"><?php _e('If you get broken download, then try enabling/disabling following options, as sometimes server may not support output buffering or partial downloads','wpdmpro'); ?>:</em>
         <hr/>
         <div class="form-group">
             <label><?php _e('Resumable Downloads','wpdmpro'); ?></label><br/>
             <select name="__wpdm_download_resume">
                 <option value="1"><?php _e("Enabled","wpdmpro"); ?></option>
                 <option value="2" <?php selected(get_option('__wpdm_download_resume'), 2); ?>><?php _e("Disabled","wpdmpro"); ?></option>
             </select>
         </div>
         <div class="form-group">
             <label><?php _e('Output Buffering','wpdmpro'); ?></label><br/>
             <select name="__wpdm_support_output_buffer">
                 <option value="1"><?php _e("Enabled","wpdmpro"); ?></option>
                 <option value="0" <?php selected(get_option('__wpdm_support_output_buffer'), 0); ?>><?php _e("Disabled","wpdmpro"); ?></option>
             </select>
         </div>

         <div class="form-group"><hr/>
             <input type="hidden" value="0" name="__wpdm_open_in_browser" />
             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_open_in_browser'),1); ?> value="1" name="__wpdm_open_in_browser"><?php _e('Open in Browser','wpdmpro'); ?></label><br/>
             <em><?php _e('Try to Open in Browser instead of download when someone clicks on download link','wpdmpro'); ?></em>
             <br/>

         </div>
         <hr/>
         <div class="form-group">
             <label><?php _e("Skip Lock for Loggedin User:","wpdmpro"); ?></label><br/>
             <select style="width: 100%" name="__wpdm_skip_locks[]" multiple="multiple" data-placeholder="<?php _e('Select...','wpdmpro'); ?>">
                 <option value="password" <?php if(in_array('password', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Password</option>
                 <option value="email" <?php if(in_array('email', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Email</option>
                 <option value="facebooklike" <?php if(in_array('facebooklike', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Facebook Like</option>
                 <option value="linkedin" <?php if(in_array('linkedin', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Linkedin Share</option>
                 <option value="gplusone" <?php if(in_array('gplusone', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Google+</option>
                 <option value="tweet" <?php if(in_array('tweet', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Tweet</option>
                 <option value="follow" <?php if(in_array('follow', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Twitter Follow</option>
            </select>

         </div>
     </div>
 </div>



 <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("Misc Settings","wpdmpro"); ?></div>
                    <div class="panel-body">
                        <?php $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array())); ?>
                        <input type="hidden" name="__wpdm_disable_scripts[]" value="" >
                        <fieldset>
                            <legend><?php _e("Disable Style & Script","wpdmpro"); ?></legend>
                            <ul>
                                <li><label><input <?php if(in_array('wpdm-bootstrap-js', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-bootstrap-js" name="__wpdm_disable_scripts[]"> <?php _e("Bootstrap JS","wpdmpro"); ?></label></li>
                                <li><label><input <?php if(in_array('wpdm-bootstrap-css', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-bootstrap-css" name="__wpdm_disable_scripts[]"> <?php _e("Bootstrap CSS","wpdmpro"); ?></label></li>
                                <li><label><input <?php if(in_array('wpdm-font-awesome', $wpdmss)) echo 'checked=checked'; ?> type="checkbox" value="wpdm-font-awesome" name="__wpdm_disable_scripts[]"> <?php _e("Font Awesome","wpdmpro"); ?></label></li>
                            </ul>
                            <em><?php _e('Because, sometimes your theme may have those scripts/styles enqueued already','wpdmpro'); ?></em>
                        </fieldset>

                        <table cellpadding="5" cellspacing="0" class="frm" width="100%">

                            <?php do_action('basic_settings'); ?>

                        </table>

                    </div>



                </div>

                <?php do_action('basic_settings_section'); ?>


<script>
    jQuery(function($){
        $('#wpdmap').change(function(){

            if(this.value==1)
                $('#aps').slideDown();
            else
                $('#aps').slideUp();
        });
    });
</script>