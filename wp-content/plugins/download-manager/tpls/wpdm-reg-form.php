<?php if(!defined('ABSPATH')) die('!');

$reg_redirect =  $_SERVER['REQUEST_URI'];
if(isset($params['redirect'])) $reg_redirect = esc_url($params['redirect']);
if(isset($_GET['redirect_to'])) $reg_redirect = esc_url($_GET['redirect_to']);

if(get_option('users_can_register')){
?>

<form method="post" action="" id="registerform" name="registerform" class="login-form">
<input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />
    <!-- div class="panel panel-primary">
<div class="panel-heading"><b>Register</b></div>
<div class="panel-body" -->
<?php global $wp_query; if(isset($_SESSION['reg_error'])&&$_SESSION['reg_error']!='') {  ?>
<div class="error alert alert-danger">
<b>Registration Failed!</b><br/>
<?php echo $_SESSION['reg_error']; $_SESSION['reg_error']=''; ?>
</div>
<?php } ?>

    <div class="form-group">
        <div class="input-group input-group-lg">
            <span class="input-group-addon" ><i class="fa fa-male"></i></span>
            <input class="form-control input-lg" required="required" placeholder="<?php _e('Full Name','wpdmpro'); ?>" type="text" size="20" id="displayname" value="<?php echo isset($_SESSION['tmp_reg_info']['display_name'])?$_SESSION['tmp_reg_info']['display_name']:''; ?>" name="wpdm_reg[display_name]">
        </div>
    </div>
    <div class="form-group">
        <div class="input-group input-group-lg">
            <span class="input-group-addon" ><i class="fa fa-user"></i></span>
            <input class="form-control" required="required" placeholder="<?php _e('Username','wpdmpro'); ?>" type="text" size="20" class="required" id="user_login" value="<?php echo isset($_SESSION['tmp_reg_info']['user_login'])?$_SESSION['tmp_reg_info']['user_login']:''; ?>" name="wpdm_reg[user_login]">
        </div>
    </div>
    <div class="form-group">

        <div class="input-group input-group-lg">
            <span class="input-group-addon" ><i class="fa fa-envelope"></i></span>
            <input class="form-control input-lg" required="required" type="email" size="25" placeholder="<?php _e('E-mail','wpdmpro'); ?>" id="user_email" value="<?php echo isset($_SESSION['tmp_reg_info']['user_email'])?$_SESSION['tmp_reg_info']['user_email']:''; ?>" name="wpdm_reg[user_email]">
        </div>
                      
    </div>

    <div class="form-group row">
        <div class="col-md-6">
        <div class="input-group input-group-lg">
            <span class="input-group-addon" ><i class="fa fa-key"></i></span>
            <input class="form-control" placeholder="<?php _e('Password','wpdmpro'); ?>" required="required" type="password" size="20" class="required" id="password" value="" name="wpdm_reg[user_pass]">
        </div>
            </div>
        <div class="col-md-6">
            <div class="input-group input-group-lg">
                <span class="input-group-addon" ><i class="fa fa-check-circle"></i></span>
                <input class="form-control input-lg" required="required" placeholder="<?php _e('Confirm Password','wpdmpro'); ?>" type="password" size="20" class="required" equalto="#password" id="confirm_user_pass" value="" name="confirm_user_pass">
            </div>
        </div>
    </div>




    <?php do_action("wpdm_register_form"); ?>
    <?php do_action("register_form"); ?>


    <input type="hidden" value="" name="redirect_to">
    <p class=""><button type="submit" class="btn btn-success btn-lg btn-block" id="registerform-submit" name="wp-submit"><i class="fa fa-user-plus"></i> &nbsp; <?php _e('Join Now!','wpdmpro'); ?></button></p>

    <!-- /div>
    </div -->
</form>

<script language="JavaScript">
<!--
/*
  jQuery(function(){       
      jQuery('#registerform').validate({

            highlight: function(label) {
                jQuery(label).closest('.form-group').addClass('has-error');
            },
             success: function(label) {
                label.closest('.form-group').addClass('has-success');
                label.remove();

            }
      });
  });
*/
//-->
</script>

    <script>
        jQuery(function ($) {
            var llbl = $('#registerform-submit').html();
            $('#registerform').submit(function () {
                $('#registerform-submit').html("<i class='fa fa-spin fa-spinner'></i> <?php __('Loggin In...','wpdmpro'); ?>");
                $(this).ajaxSubmit({
                    success: function (res) {
                        if (!res.match(/success/)) {
                            $('form .alert-danger').hide();
                            $('#registerform').prepend("<div class='alert alert-danger'>"+res+"</div>");
                            $('#registerform-submit').html(llbl);
                        } else {
                            location.href = "<?php echo $reg_redirect; ?>";
                        }
                    }
                });
                return false;
            });
        });
    </script>

<?php } else echo "<div class='alert alert-warning'>". __("Registration is disabled!", "wpdmpro")."</div>"; ?>