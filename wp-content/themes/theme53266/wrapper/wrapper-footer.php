<?php /* Wrapper Name: Footer */ ?>

<div class="row">
    <div class="span6">
        <div class="boxfooter" data-motopress-type="static" data-motopress-static-file="static/static-footer-logo.php">
			<?php get_template_part("static/static-footer-logo"); ?>
		</div>
    	<div data-motopress-type="static" data-motopress-static-file="static/static-footer-text.php">
    		<?php get_template_part("static/static-footer-text"); ?>
    	</div>
    </div>
<!--    <div class="span6">-->
<!--        <div data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar-1">-->
<!--    		--><?php //dynamic_sidebar("footer-sidebar-1"); ?>
<!--    	</div>-->
<!--    </div>-->
	<div class="span6 social-media">
		<a href="https://instagram.com/tintmyrideca/" target="_blank"><i class="fa fa-instagram fa-4x"></i></a>
		<a href="https://www.facebook.com/TINT-MY-RIDE-CA-701115213348705/" target="_blank"><i class="fa fa-facebook fa-4x"></i></a>
		<a href="http://www.yelp.com/biz/tint-my-ride-fresno" target="_blank"><i class="fa fa-yelp fa-4x"></i></a>
	</div>
<!--    <div class="span4">-->
<!--        --><?php //echo do_shortcode('[nsu_form]'); ?>
<!--    </div>-->
</div>
<div class="row">
    <div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-footer-nav.php">
		<?php get_template_part("static/static-footer-nav"); ?>
	</div>
</div>