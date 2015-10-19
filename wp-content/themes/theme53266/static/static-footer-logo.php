<?php /* Static Name: Footer Logo */ ?>
<!-- BEGIN LOGO -->
<div class="logo footer-logo">
	<?php if(of_get_option('f_logo_url') == ''){ ?>
		<a href="<?php echo home_url(); ?>/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo2.jpg" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('description'); ?>"></a>
	<?php } else { ?>
		<a href="<?php echo home_url(); ?>/"><img src="<?php echo of_get_option('f_logo_url', '' ); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('description'); ?>"></a>
	<?php } ?>
</div>
<!-- END LOGO -->