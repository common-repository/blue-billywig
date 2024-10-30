<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

use BlueBillywigPlugin\Admin\Helper;

?>

<div class="bb-drop-wrapper">
	<form action="#" class="bb-drop-form">
		<?php if ( ini_get( 'post_max_size' ) ) : ?>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( Helper::return_bytes_fn( ini_get( 'post_max_size' ) ) ); ?>" />
		<?php endif; ?>
		<label class="bb-drop-content">
			<p><b>Drop files anywhere to upload</b></p>
			<p>or</p>
			<div class="bb-upload-input"> 
				<input type="file" id="file" name="file" class="bb-visually-hidden" >
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