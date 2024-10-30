<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

use BlueBillywigPlugin\Admin\Helper;

$publication = esc_attr( get_option( 'blue-billywig-publication' ) );
?>
<div class="bb-content-modal-wrapper">
	<div class="bb-content-wrapper">
		<button type="button" name="bb-close-modal" class="bb-close-modal"><img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/close-icon.svg'; ?>"  alt="Close modal"></button>
		<ul class="bb-tabs">
			<li class="bb-tab active" data-id="bb-videos">Media Clips</li>
			<li class="bb-tab" data-id="bb-playlists">Playlists</li>
			<li class="bb-tab" data-id="bb-upload">Upload</li>
			<li class="bb-last-tab-btn"><a href="https://' . $publication . '.bbvms.com/ovp/#/login" class="bb-button bb-btn-blue" target="_blank"><img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/close-icon.svg'; ?>" alt="Go to platform">Go to platform</a></li>
		</ul>
		<div class="bb-tab-area bb-loading" id="bb-videos" style="display: block;">
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
			</div>
			<div class="bb-videos"></div>
			<nav class="bb-pagination-video">
				<ul class="bb-pages">
				</ul>
			</nav>
		</div>
		<div class="bb-tab-area bb-loading" id="bb-playlists">
			<div class="bb-filters-upload">
				<form action="#" class="bb-filters-search">
					<input type="text" name="bb-search-playlists" placeholder="Search" class="bb-search-input" />
					<div class="bb-filters">
						<div class="bb-count"><span></span> items</div>
						<input type="text" name="bb-playlists-published-date" placeholder="Search" class="bb-filter-datepicker" value="Published date" />
						<button type="button" name="bb-upload-clear" class="bb-btn-red bb-filter-clear">Clear filters</button>
					</div>
				</form>
			</div>
			<div class="bb-playlists"></div>
			<nav class="bb-pagination-playlists">
				<ul class="bb-pages"></ul>
			</nav>
		</div>
		<div class="bb-tab-area" id="bb-upload">
			<div class="bb-drop-wrapper">
				<form action="#" class="bb-drop-form">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( Helper::return_bytes_fn( ini_get( 'post_max_size' ) ) ); ?>" />
					<label class="bb-drop-content">
						<p><b>Drop files anywhere to upload</b></p>
						<p>or</p>
						<div class="bb-upload-input">
							<input type="file" id="file" name="file" class="bb-visually-hidden">
							<span>Select file</span>
						</div>
					</label>
				</form>
			</div>
			<div class="bb-drop-wrapper bb-drop-form-submit">
				<form action="#" class="bb-drop-metadata">
					<h2>Edit upload data</h2>
					<label class="bb-drop-label">
						<span>Title</span>
						<input type="text" name="bb-drop-title" placeholder="Title" value="">
					</label>
					<label class="bb-drop-label">
						<span>Tags</span>
						<input type="text" name="bb-drop-tags" placeholder="Tags" value="">
					</label>
					<label class="bb-drop-label">
						<span>Description</span>
						<textarea name="bb-upload-description" placeholder="Description" value=""></textarea>
					</label>
					<label class="bb-drop-label">
						<span>Status</span>
						<select name="bb-upload-status" class="bb-filter-select">
							<option value="published" selected>Published</option>
							<option value="draft">Draft</option>
						</select>
					</label>
					<label class="bb-drop-label bb-label-submit">
						<button type="submit" name="bb-upload-drop" class="bb-upload-input"><img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/upload-icon.svg'; ?>" alt="Upload Media Clip"> Upload Media Clip</button>
					</label>
				</form>
			</div>
		</div>
	</div>
</div>