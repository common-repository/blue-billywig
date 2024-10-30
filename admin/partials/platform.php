<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

<div class="bb-content-wrapper"> 
	<div class="bb-platform-wrapper">
		<?php if ( get_option( 'blue-billywig-publication' ) ) : ?> 
			<a href="https://<?php echo esc_attr( get_option( 'blue-billywig-publication' ) ); ?>.bbvms.com/ovp/#/login" class="bb-button bb-btn-blue" target="_blank">
				<img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/add-playlist-icon.svg'; ?>" alt="Add playlist"> 
				Login to OVP
			</a>
		<?php endif; ?>
	</div>
</div>