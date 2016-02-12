

                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __('Front-end Settings','wpdmpro'); ?></div>
                     <div class="panel-body">

                         <div class="form-group">
                             <label for="__wpdm_user_dashboard"><?php echo __('User Dashboard Page','wpdmpro'); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_user_dashboard', 'id' => '__wpdm_user_dashboard', 'show_option_none' => __('None Selected', 'wpdmpro'), 'option_none_value' => '' , 'selected' => get_option('__wpdm_user_dashboard'))) ?><br/>
                             <em class="note"><?php printf(__('The page where you used short-code %s', 'wpdmpro'),'<code>[wpdm_user_dashboard]</code>'); ?></em>
                         </div>

                         <div class="form-group">
                             <label for="__wpdm_user_dashboard"><?php echo __('Frontend Uploader Page','wpdmpro'); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_author_dashboard', 'id' => '__wpdm_author_dashboard', 'show_option_none' => __('None Selected', 'wpdmpro'), 'option_none_value' => '' , 'selected' => get_option('__wpdm_author_dashboard'))) ?><br/>
                             <em class="note"><?php printf(__('The page where you used short-code %s', 'wpdmpro'),'<code>[wpdm_frontend]</code>'); ?></em>
                         </div>


                         <div class="form-group">
                             <label><?php echo __('Allowed User Roles to Create Package From Front-end','wpdmpro'); ?></label>
                             <select name="__wpdm_front_end_access[]" class="chzn-select role" multiple="multiple" id="fronend-ui-access" style="min-width: 450px">
                                 <?php

                                 $currentAccess = maybe_unserialize(get_option( '__wpdm_front_end_access', array()));
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
                             <label><?php echo __('Message For Blocked Users:','wpdmpro'); ?></label>
                             <textarea id="__wpdm_front_end_access_blocked" name="__wpdm_front_end_access_blocked" class="form-control"><?php echo stripslashes(get_option('__wpdm_front_end_access_blocked'));?></textarea>
                         </div>


                         <div class="form-group">
                             <label><?php echo __('When Someone Create a Package:','wpdmpro'); ?></label><br/>
                             <select name="__wpdm_ips_frontend">
                                 <option value="publish"><?php echo __('Publish Instantly'); ?></option>
                                 <option value="pending" <?php selected(get_option('__wpdm_ips_frontend'), 'pending'); ?>><?php echo __('Pending for Review', 'wpdmpro'); ?></option>
                             </select>
                         </div>

                         <div class="form-group">
                             <label><?php echo __('When File Already Exists:','wpdmpro'); ?></label><br/>
                             <select name="__wpdm_overwrite_file_frontend">
                                 <option value="0"><?php echo __('Rename New File'); ?></option>
                                 <option value="1" <?php echo get_option('__wpdm_overwrite_file_frontend',0)==1?'selected=selected':''; ?>><?php echo __('Overwrite', 'wpdmpro'); ?></option>
                             </select>
                         </div>

                         <div class="form-group">
                         <label><?php echo __('Allowed File Types From Front-end:','wpdmpro'); ?></label>
                         <input type="text" class="form-control" value="<?php echo get_option('__wpdm_allowed_file_types','*'); ?>" name="__wpdm_allowed_file_types" />
                         </div>

                         <div class="form-group">
                         <label><?php echo __('Max Upload Size From Front-end','wpdmpro'); ?></label>
                         <input type="text" class="form-control" style="width: 100px;display: inline" title="0 for system default" name="__wpdm_max_upload_size" value="<?php echo get_option('__wpdm_max_upload_size',(wp_max_upload_size()/1048576)); ?>"> MB<br/>
                         </div>


                         <div class="form-group"><hr/>
                             <label><?php echo __('New Package Review Email','wpdmpro'); ?></label>
                             <input type="text" class="form-control" name="__wpdm_new_package_email" value="<?php echo get_option('__wpdm_new_package_email'); ?>">
                             <em><?php echo __('This email will receive a notification every time any user add/edit a package from front-end','wpdmpro'); ?></em>
                         </div>

                         <div class="form-group">
                             <label><?php echo __('New Package Review Email Subject','wpdmpro'); ?></label>
                             <input type="text" class="form-control" name="__wpdm_new_package_email_subject" value="<?php echo get_option('__wpdm_new_package_email_subject','A package is waiting for your review!'); ?>">
                         </div>

                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_file_list_paging" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_file_list_paging',0),1); ?> value="1" name="__wpdm_file_list_paging"><?php _e('Enable Search in File List','wpdmpro'); ?></label><br/>
                             <em><?php _e('Check this option if you want enable pagination & seach in file list where there are more then 30 files attached with a package','wpdmpro'); ?></em>
                             <br/>

                         </div>

                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_rss_feed_main" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_rss_feed_main'),1); ?> value="1" name="__wpdm_rss_feed_main"><?php _e('Include Packages in Main RSS Feed','wpdmpro'); ?></label><br/>
                             <em><?php printf(__('Check this option if you want to show wpdm packages in your main <a target="_blank" href="%s">RSS Feed</a>','wpdmpro'), get_bloginfo('rss_url')); ?></em>
                             <br/>

                         </div>






                     </div>
                 </div>

                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __('Category Page Options','wpdmpro'); ?></div>
                     <div class="panel-body">
                        <fieldset id="cpi">
                            <legend><label><input type="radio" name="__wpdm_cpage_style"  <?php checked(get_option('__wpdm_cpage_style'),'basic'); ?> value="basic"> Use Basic Style</label></legend>
                        <div class="clear"></div>
                         <div class="form-group">
                             <?php
                              $cpageinfo = get_option('__wpdm_cpage_info');
                             ?>
                             <input type="hidden" name="__wpdm_cpage_info[]" value="">
                             <label><?php echo __('Select Package Info To Show in Category Page:','wpdmpro'); ?></label><br/>
                             <label><input <?php checked(isset($cpageinfo['version']),1); ?> type="checkbox" name="__wpdm_cpage_info[version]" value="1"> <?php echo __('Show Version','wpdmpro'); ?></label><br/>
                             <label><input <?php checked(isset($cpageinfo['view_count']),1); ?> type="checkbox" name="__wpdm_cpage_info[view_count]" value="1"> <?php echo __('Show View Count','wpdmpro'); ?></label><br/>
                             <label><input <?php checked(isset($cpageinfo['download_count']),1); ?> type="checkbox" name="__wpdm_cpage_info[download_count]" value="1"> <?php echo __('Show Download Count','wpdmpro'); ?></label><br/>
                             <label><input <?php checked(isset($cpageinfo['package_size']),1); ?> type="checkbox" name="__wpdm_cpage_info[package_size]" value="1"> <?php echo __('Show Package Size','wpdmpro'); ?></label><br/>
                             <label><input <?php checked(isset($cpageinfo['download_link']),1); ?> type="checkbox" name="__wpdm_cpage_info[download_link]" value="1"> <?php echo __('Show Download Link','wpdmpro'); ?></label>

                         </div>

                         <div class="form-group">
                             <label><?php echo __('Show Package Info:','wpdmpro'); ?></label><br/>
                             <select name="__wpdm_cpage_excerpt">
                                 <option value="after"><?php echo __('After Excerpt','wpdmpro'); ?></option>
                                 <option value="before" <?php selected(get_option('__wpdm_cpage_excerpt'), 'before'); ?>><?php echo __('Before Excerpt','wpdmpro'); ?></option>
                             </select>
                         </div>
                        </fieldset><br/>

                         <fieldset id="cpi">
                             <legend><label><input type="radio" name="__wpdm_cpage_style" <?php checked(get_option('__wpdm_cpage_style'),'ltpl'); ?> value="ltpl"> Use Link Template</label></legend>



                             <div class="form-group">
                                 <label><?php echo __('Select Link Template:','wpdmpro'); ?></label><br/>

                                     <?php
                                     echo WPDM\admin\menus\Templates::Dropdown(array('name' => '__wpdm_cpage_template', 'selected' => get_option('__wpdm_cpage_template')));
                                     ?>
                                 <br/>
                                 <em><?php echo __('Selected link template will replace the excerpt','wpdmpro'); ?></em>
                             </div>
                         </fieldset>





                     </div>
                 </div>


<style> legend{ font-weight: 800; } fieldset#cpi legend input { margin: 0 !important; }</style>