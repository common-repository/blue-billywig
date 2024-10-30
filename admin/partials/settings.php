<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}





$settings_api_secret       = get_option( 'blue-billywig-api-secret' );
$settings_api_id           = get_option( 'blue-billywig-api-id' );
$settings_api_publication  = get_option( 'blue-billywig-publication' );
$settings_api_playout      = get_option( 'blue-billywig-playout' );
$settings_api_embed        = get_option( 'blue-billywig-embed' );
$settings_count_page_items = get_option( 'blue-billywig-page-count' );
// $playout_list_data         = apply_filters( 'bb_get_playout_list', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ) );
$playout_list_data = apply_filters( 'blue_billywig_get_playout_list', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ) );

// var_dump($playout_list_data);
$playout_skin = get_option('bb_get_playout_list');
// $playout_skin =  get_playout_list( $settings_api_secret, $settings_api_id , $settings_api_publication  );
// var_dump($playout_skin);
?>

<div class="bb-content-wrapper bb-settings-page">
	<h2>Blue Billywig Plugin Options</h2>
	<form action="#" class="bb-settings-page-form">
		<?php wp_nonce_field( 'blue-billywig-settings-form', 'blue-billywig-nonce' ); ?>
		<h3>API Options</h3>
		<p>You can find your API key in Blue Billywig OVP > User (top-right) > Publication > API keys (left sidebar) > (select api key).</p>
		<p>Read more: <a href="https://support.bluebillywig.com/sapi-reference-guide/api-key-management/" target="_blank">support.bluebillywig.com/sapi-reference-guide/api-key-management</a></p>
		<div class="bb-fields-wrapper">
			<label class="bb-label">
				<span>API-Secret</span>
				<input type="text" name="bb-api-secret" placeholder="API-Secret"
				<?php
				if ( $settings_api_secret ) {
					echo 'value="' . esc_attr( $settings_api_secret ) . '"';}
				?>
				/>
			</label>
			<label class="bb-label">
				<span>API-ID</span>
				<input type="text" name="bb-api-id" placeholder="API-ID"
				<?php
				if ( $settings_api_id ) {
					echo 'value="' . esc_attr( $settings_api_id ) . '"';}
				?>
				/>
			</label>
			<label class="bb-label">
				<span>Publication</span>
				<input type="text" name="bb-publication" placeholder="Publication"
				<?php
				if ( $settings_api_publication ) {
					echo 'value="' . esc_attr( $settings_api_publication ) . '"';}
				?>
				/>
				<small>[publication.bbvms.com]</small>
			</label>
			<label class="bb-label">
				<span>Items per page</span>
				<input type="number" step="1" min="1" name="bb-page-count" placeholder=""
				<?php
				if ( $settings_count_page_items ) {
					echo 'value="' . esc_attr( $settings_count_page_items ) . '"';
				} else {
					echo 'value="15"'; }
				?>
				/>
			</label>
		</div>
		<h3>Defaults</h3>
		<p>These values are used as default values when embedding media clips</p>
		<div class="bb-fields-wrapper">
    <label class="bb-label">
        <span>Playout</span>
        <?php
        // Debugging the data structure
        // var_dump($settings_api_playout);
        // var_dump($playout_list_data);
        ?>
        <select name="bb-playout" class="bb-filter-select">
            <?php if (isset($playout_list_data['items']) && count($playout_list_data['items']) > 0) : ?>
                <?php foreach ($playout_list_data['items'] as $value) : ?>
                    <option value="<?php echo esc_attr($value['label']); ?>"
                        <?php
                        if ($settings_api_playout === $value['label']) {
                            echo 'selected';
                        }
                        ?>
                    ><?php echo esc_attr($value['name']); ?></option>
                <?php endforeach; ?>
            <?php else : ?>
                <option value="default"
                    <?php
                    if ('default' === $settings_api_playout) {
                        echo 'selected';
                    }
                    ?>
                >Default</option>
            <?php endif; ?>
        </select>
    </label>
    <label class="bb-label">
        <span>Embed</span>
        <select name="bb-embed" class="bb-filter-select">
            <option value="javascript"
                <?php
                if ('javascript' === $settings_api_embed) {
                    echo 'selected';
                }
                ?>
            >Javascript</option>
            <option value="iframe"
                <?php
                if ('iframe' === $settings_api_embed) {
                    echo 'selected';
                }
                ?>
            >Iframe</option>
            <option value="url"
                <?php
                if ('url' === $settings_api_embed) {
                    echo 'selected';
                }
                ?>
            >URL</option>
            <option value="amp"
                <?php
                if ('amp' === $settings_api_embed) {
                    echo 'selected';
                }
                ?>
            >AMP</option>
            <option value="oembed"
                <?php
                if ('oembed' === $settings_api_embed) {
                    echo 'selected';
                }
                ?>
            >oEmbed</option>
        </select>
    </label>
</div>

		<button class="bb-button" type="submit"><img src="<?php echo BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/img/save-btn-icon.svg'; ?>" alt=""> Save</button>
	</form>
</div>