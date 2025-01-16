<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com/
 * @since      1.0.0
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php 
switch ( get_admin_page_title() ) {
    case '[Layout] Wall':
        $activePage = 'wall';
        break;
    case '[Layout] Player':
        $activePage = 'player';
        break;
    case '[Layout] Rail':
        $activePage = 'rail';
        break;
    case '[Layout] Feature':
        $activePage = 'feature';
        break;
    case '[Layout] Status':
        $activePage = 'status';
        break;
    case '[Layout] Nav':
        $activePage = 'nav';
        break;
    case '[Layout] Vods':
        $activePage = 'vods';
        break;
    case '[Layout] Showcase':
        $activePage = 'showcase';
        break;
    default:
        $activePage = 'wall';
}
?>

<div class="cp-streamweasels-youtube wrap">
    <div class="cp-streamweasels-youtube__header">
        <div class="cp-streamweasels-youtube__header-logo">
            <img src="<?php 
echo esc_url( plugin_dir_url( __FILE__ ) . '../img/sw-full-logo.png' );
?>">
        </div>
        <div class="cp-streamweasels-youtube__header-title">
            <h3>StreamWeasels YouTube</h3>
            <p>YouTube Integration <?php 
?>for WordPress</p>
        </div>        
    </div>
    <div class="cp-streamweasels-youtube__wrapper">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <div class="setup-instructions">
                                <div class="setup-instructions--left">
                                    <h3>Setup Guide</h3>
                                    <p>StreamWeasels YouTube Integration connects to the <strong>YouTube API</strong>, this unlocks new possibilities, better performance and more reliability!</p>
                                    <h4>YouTube API Key</h4>
                                    <p>The YouTube API requires a valid API key sent with every request, in order to pull data from YouTube. In order to get your own API key, you must follow a few steps. This process takes only a few minutes and is fairly simple.</p>
                                    <p>To create your API key, you can follow along with our text guide: <a href="https://support.streamweasels.com/article/26-how-to-setup-a-youtube-api-key" target="_blank">How to create a YouTube API key</a>.</p>
                                    <h4>StreamWeasels Blocks</h4>
                                    <p>If your site uses the Block Editor (Gutenberg) you can add our YouTube Blocks directly to your page. Look out for the YouTube Integration Block and the YouTube Embed Block, and learn more in our <a href="https://support.streamweasels.com/article/83-youtube-integration-blocks-guide" target="_blank">YouTube Integration Blocks Guide</a>.</p> 
                                    <h4>StreamWeasels Shortcodes</h4>
                                    <p>You can simply use the shortcode [sw-youtube] to display your YouTube Integration on your page, using all the settings set here on this page. Learn more in our <a href="https://support.streamweasels.com/article/75-youtube-integration-shortcode-guide" target="_blank">YouTube Integration Shortcodes Guide</a>.</p> 
                                    <h4>Advanced Shortcodes</h4>
                                    <p>For more complicated integrations, for example if you have more than one YouTube Integration on your site, you can use shortcode attributes to change the settings directly on your shortcode.<br><br><strong>For example</strong>:<br><br>
                                    <code>[sw-youtube  layout="<?php 
echo esc_attr( $activePage );
?>" channel="UCAuUUnT6oDeKwE6v1NGQxug"]</code></p>
                                    <p>The complete list of shortcode attributes can be viewed in our <a href="https://support.streamweasels.com/article/75-youtube-integration-shortcode-guide" target="_blank">YouTube Integration Shortcodes Guide</a>.</p>
                                </div>
                                <div class="setup-instructions--right">
                                    <h3>Video Guide</h3>
                                    <iframe width="560" height="315" src="https://www.youtube.com/embed/uA2zoyVVQMs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>                                
                            </div>
                        </div>
                    </div>     
                    <form id="sw-form" method="post" action="options.php">
                        <?php 
if ( get_admin_page_title() == 'StreamWeasels' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_api_fields',
        'dashicons-youtube',
        'This plugin requires an active YouTube API key to work. <a href="https://support.streamweasels.com/article/26-how-to-setup-a-youtube-api-key" target="_blank">Click here</a> to learn more about YouTube API keys.',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_shortcode_fields',
        'dashicons-shortcode',
        'You can add YouTube Integration to your page with either YouTube Integration <a href="https://support.streamweasels.com/article/83-youtube-integration-blocks-guide" target="_blank">Blocks</a> or <a href="https://support.streamweasels.com/article/75-youtube-integration-shortcode-guide" target="_blank">Shortcodes</a>. For shortcodes, simply use the shortcode <code>[sw-youtube]</code> for your YouTube Integration. For more complicated integrations you can change the attributes directly on your shortcode. <a href="https://support.streamweasels.com/article/75-youtube-integration-shortcode-guide" target="_blank">Click here</a> to view our full list of StreamWeasels shortcode attributes.</p>',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_main_fields',
        'dashicons-slides',
        'Here you can define the channel to display in your YouTube integration.',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_layout_fields',
        'dashicons-slides',
        'Here you can select the layout of your YouTube integration. Take a look at our <a href="https://www.streamweasels.com/youtube-wordpress-plugins/" target="_blank">StreamWeasels Layout Guide</a> for more information and our free and PRO layouts.',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_embed_fields',
        'dashicons-video-alt3',
        'Here you can change the settings for the YouTube embed in your YouTube integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_livestream_fields',
        'dashicons-marker',
        'Here you can change the settings for the embed in your YouTube integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_autoplay_fields',
        'dashicons-controls-play',
        'Here you can change the settings for the autoplay in your YouTube Integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_appearance_fields',
        'dashicons-admin-appearance',
        'Here you can change the overall appearance of your YouTube integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_tile_fields',
        'dashicons-grid-view',
        'Here you can change the finer appearance details of your YouTube integration. ',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_hover_fields',
        'dashicons-search',
        'Here you can change what happens when you hover over channels in your YouTube integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_cache_fields',
        'dashicons-database',
        'Cache data from the YouTube API to drastically reduce the number of API requests. Channel content is cached for 24 hours, then automatically updated.',
        'pro'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_debug_fields',
        'dashicons-admin-tools',
        'If your StreamWeasels plugin is encountering errors with the YouTube API, those errors will be output below. You can get in touch with us <a href="https://www.streamweasels.com/contact/" target="_blank">here</a>, please include a copy of any errors that might be relevant from below.',
        'free'
    );
    ?>
                        <?php 
}
?> 
                        <?php 
if ( get_admin_page_title() == 'Translations' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_translations' );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_translations_fields',
        'dashicons-translation',
        'This page allows you to translate strings found within the StreamWeasels plugins.',
        'free'
    );
    ?>
                        <?php 
}
?>                         
                        <?php 
if ( get_admin_page_title() == '[Layout] Wall' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options_wall' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_wall_fields',
        'dashicons-grid-view',
        'Here you can change the settings for your Wall layout.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == '[Layout] Feature' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options_feature' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_feature_fields',
        'dashicons-grid-view',
        'Here you can change the settings for your Feature layout.',
        'free'
    );
    ?>
                        <?php 
}
?>     
                        <?php 
if ( get_admin_page_title() == '[Layout] Player' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options_player' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_player_fields',
        'dashicons-grid-view',
        'Here you can change the settings for your Player layout.',
        'free'
    );
    ?>
                        <?php 
}
?>     
                        <?php 
if ( get_admin_page_title() == '[Layout] Showcase' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options_showcase' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_showcase_fields',
        'dashicons-grid-view',
        'Here you can change the settings for your Showcase layout.',
        'free'
    );
    ?>
                        <?php 
}
?>      
                        <?php 
if ( get_admin_page_title() == '[Layout] Status' ) {
    ?>
                            <?php 
    settings_fields( 'swyi_options_status' );
    ?>                             
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_status_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="status"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_status_placement_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Status.',
        'free'
    );
    ?>
                            <?php 
    $this->swyi_do_settings_sections(
        'swyi_status_appearance_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Status.',
        'free'
    );
    ?>
                        <?php 
}
?>                                                                                                                                                                                                                                                                
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <?php 
include 'streamweasels-youtube-admin-sidebar.php';
?>
            </div>
        </div>
    </div>
</div>