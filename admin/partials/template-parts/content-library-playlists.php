<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

?>

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