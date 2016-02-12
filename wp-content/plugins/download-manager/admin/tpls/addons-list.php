
<div class="wrap w3eden">


<div class="container-fluid">
<div class="row" id="addonlist">
    <div class="col-md-12"><div class="panel panel-default">
            <div class="panel-heading"><h3><?php _e('WPDM Add-Ons','wpdmpro'); ?></h3></div>
            <div class="panel-body"><ul class="nav nav-pills" id="filter-mods"><li class="active"><a href="#all" rel="all">All Add-Ons</a></li>

<?php
foreach($cats as $cat){
    echo "<li><a href='#' rel='{$cat->slug}'>{$cat->name}</a></li>";
}
?>
</ul></div></div></div>
    <div class="col-md-12">
<ul class='list-group'>
<?php foreach($data->post_extra as $package){
 ?>
    <li class="list-group-item all <?php echo implode(" ", $package->cats); ?>">


        <b><a href="<?php echo $package->link; ?>"><?php echo $package->title; ?></a></b> [ <?php echo $package->pinfo->version; ?> ]


        <div class="pull-right">
            <?php if($package->price>0){ ?>
            <a class="btn-purchase" data-toggle="modal" data-backdrop="true" data-target="#addonmodal" href="#" rel="<?php echo $package->ID; ?>" style="border: 0;border-radius: 2px"><i class="fa fa-shopping-cart"></i> &nbsp;Buy Now &nbsp; <span class="label label-warning" style="font-size: 8pt;padding: 1px 5px;margin-top: 1px"><?php echo $package->currency.$package->price; ?></span> </a>
            <?php } else { ?>
                <a class="btn-install" data-toggle="modal" rel="<?php echo $package->ID; ?>" data-backdrop="true" data-target="#addonmodal" href="#" style="border: 0;border-radius: 2px"><i class="fa fa-download"></i> &nbsp;Download & Install</a>
            <?php } ?>
        </div>


        </li>
<?php
}
?>
</ul>
</div>
</div>

    </div>
    <div class="modal fade" id="addonmodal" tabindex="-1" role="dialog" aria-labelledby="addonmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add-On Installer</h4>
                </div>
                <div class="modal-body" id="modalcontents">
                    <i class="fa fa-spinner fa-spin"></i> Please Wait...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <a type="button" id="prcbtn" target="_blank" href="http://www.wpdownloadmanager.com/cart/" class="btn btn-success" style="display: none" onclick="jQuery('#addonmodal').modal('hide')">Checkout</a>
                </div>
            </div>
        </div>
    </div>
    </div>

<script>
    jQuery(function(){
        jQuery('.nav-pills a').click(function(){
                jQuery('#addonlist .all').fadeOut();
                jQuery('.'+this.rel).fadeIn();
                jQuery('#prcbtn').hide();
                jQuery('.nav-pills li').removeClass('active');
                jQuery(this).parent().addClass('active');
        });

        jQuery(".btn-install, .btn-purchase").click(function(){
            jQuery('#modalcontents').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
        });
        jQuery('#addonmodal').on('shown.bs.modal', function (e) {
            if(jQuery(e.relatedTarget).hasClass('btn-install')){
                jQuery('.modal-dialog').css('width','500px');
                jQuery('.modal-footer .btn-danger').html('Close');
                jQuery('#modalcontents').css('padding','20px').css('background','#ffffff');
                jQuery.post(ajaxurl,{action:'wpdm-install-addon', addon: e.relatedTarget.rel}, function(res){
                    jQuery('#modalcontents').html(res.replace('Return to Plugin Installer',''));
                });
            }

            if(jQuery(e.relatedTarget).hasClass('btn-purchase')){
                jQuery('.modal-dialog').css('width','800px');
                jQuery('.modal-footer').css('margin',0);
                jQuery('.modal-footer .btn-danger').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
                jQuery('#modalcontents').css('padding',0).css('background','#f2f2f2').html("<iframe onload=\"jQuery('.modal-footer .btn-danger').html('Continue Shopping...');jQuery('#prcbtn').show();\" style='width: 100%;padding-top: 20px; background: #f2f2f2;height: 300px;border: 0' src='http://www.wpdownloadmanager.com/?addtocart="+e.relatedTarget.rel+"'></iframe>");
            }
        })


    });
</script>

