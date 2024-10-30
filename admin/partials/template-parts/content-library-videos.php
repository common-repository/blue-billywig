<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>
<div class="bb-tab-area bb-loading" id="bb-videos" style="display: block;">
	<div class="bb-upload-video-form">
		<?php require 'content-file-upload.php'; ?>
	</div>
	<div class="bb-filters-upload"> 
		<form action="#" class="bb-filters-search">
			<input type="text" name="bb-search-video" placeholder="Search" class="bb-search-input" />
			<div class="bb-filters">
				<div class="bb-count"><span></span> items</div>
				<input type="text" name="bb-video-published-date" placeholder="Search" class="bb-filter-datepicker" value="Published date" /> 
				<select name="bb-video-media-type" class="bb-filter-select">
					<option value="all" selected>All</option>
					<option value="video">Video</option>
					<option value="audio">Audio</option>
					<option value="image">Image</option>
					<option value="document">Document</option>
				</select> 
				<button type="button" name="bb-upload-clear" class="bb-btn-red bb-filter-clear">Clear filters</button>
			</div>
		</form>
		<button type="button" name="bb-upload-video" class="bb-upload-input"><img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/upload-icon.svg'; ?>" alt="Upload Media Clip"> Upload Media Clip</button>
	</div>
	<div class="bb-videos"></div>
	<nav class="bb-pagination-video">
		<ul class="bb-pages"> 
		</ul>
	</nav>
</div>
