<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

<div class="bb-content-wrapper">
	<ul class="bb-tabs">
		<li class="bb-tab active" data-id="bb-videos">Media Clips</li>
		<li class="bb-tab" data-id="bb-playlists">Playlists</li> 
	</ul>
	<?php require 'template-parts/content-library-videos.php'; ?>
	<?php require 'template-parts/content-library-playlists.php'; ?>     
</div>