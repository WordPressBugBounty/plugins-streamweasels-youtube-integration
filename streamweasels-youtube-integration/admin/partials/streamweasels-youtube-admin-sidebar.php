<div class="meta-box-sortables">
    <div class="postbox">
        <h3>StreamWeasels Links</h3>
        <div class="inside">
            <p>WordPress Themes and Plugins for YouTube.</p>
            <ul>

                <li>
                    <a href="https://support.streamweasels.com/article/74-getting-started-with-youtube-integration" target="_blank">Getting Started with YouTube Integration!</a>
                </li>
                <li>
                    <a href="https://support.streamweasels.com/article/75-youtube-integration-shortcode-guide" target="_blank">StreamWeasels Shortcodes Guide</a>
                </li>                
                <li>
                    <a href="https://support.streamweasels.com/article/83-youtube-integration-blocks-guide" target="_blank">StreamWeasels Blocks Guide</a>
                </li>
                <li>
                    <a href="https://www.streamweasels.com/youtube-wordpress-plugins/" target="_blank">StreamWeasels Layout Guide</a>
                </li>                                       
                <li>
                    <a href="https://www.youtube.com/channel/UCo885jUiOeyhtHDFUbdx8rQ" target="_blank">Check out our YouTube Guides</a>
                </li>       
                <li>
                    <a href="https://twitter.com/StreamWeasels" target="_blank">Follow us on Twitter</a>
                </li>
                <li>
                    <a href="https://www.streamweasels.com/contact/" target="_blank">Need Help? Get in touch!</a>
                </li>                                                          
            </ul>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-discord"></span>Join us on Discord</h3>
        <div class="inside">
            <h4>Got a question? Ask us in Discord.</h4>
            <p>Get support from StreamWeasels developers and connect with like-minded YouTube & WordPress users.</p>
            <div style="text-align:center">
                <?php 
?>
                    <a class="button button-primary" href="https://discord.gg/HSwfPbm" target="_blank" style="background-color: #6E85D2;">JOIN DISCORD</a>
                <?php 
?>
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-info" style="color: #022E4C;"></span>New Status Bar Plugin!</h3>
        <div class="inside">
            <p>Check out our new Twitch / YouTube / Kick Online Status Bar plugin for WordPress!</p>
            <h4>StreamWeasels Status Bar</h4>
            <img src="<?php 
echo esc_url( SWYI_PLUGIN_DIR . '/admin/img/status-bar-thumbnail.png' );
?>" style="max-width:128px;margin:0 auto;display:block">
            <p>Display Twitch / YouTube / Kick Online Status!</p>
            <div style="text-align:center">
                <a class="button button-primary" style="background-color: #026E95;border-color: #022E4C;" target="_blank" href="/wp-admin/plugin-install.php?s=streamweasels status bar&tab=search&type=term">Install Online Status Bar</a>    
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-kick" style="color: #53fc18;"></span>New Kick Plugin</h3>
        <div class="inside">
            <p>Check out our new Kick integration plugin for WordPress!</p>
            <h4>StreamWeasels Kick Integration</h4>
            <img src="<?php 
echo esc_url( SWYI_PLUGIN_DIR . '/admin/img/kick-thumbnail.png' );
?>" style="max-width:128px;margin:0 auto;display:block">
            <p>Display Kick Channels, Streams and Live Status!</p>
            <div style="text-align:center">
                <a class="button button-primary" style="background-color: #53fc18;border-color: #2d5228;color: #000" target="_blank" href="/wp-admin/plugin-install.php?s=streamweasels kick integration&tab=search&type=term">Install Kick Integration</a>    
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3>YouTube Integration Layouts</h3>
        <div class="inside">
            <p>Upgrade your YouTube Integration with one of our PRO layouts.</p>
            <?php 
if ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ) {
    $featureCheck = '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>';
} else {
    $featureCheck = '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Not Active';
}
?>
            <ul>
                <li>
                    <strong>FREE Layouts</strong>
                </li>    
                <hr>
                <br>                               
                <li>
                    <a href="#fs_addons" style="display:inline-block;width:40%">YouTube Wall</a><span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>
                </li>
                <li>
                    <a href="#fs_addons" style="display:inline-block;width:40%">YouTube Showcase</a><span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>
                </li>                
                <li>
                    <a href="#fs_addons" style="display:inline-block;width:40%">YouTube Player</a><span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>
                </li>                                              
                <li>
                    <a href="#fs_addons" style="display:inline-block;width:40%">YouTube Status</a><span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>
                </li>                                                                                             
            </ul>     
            <ul>
                <li>
                    <strong>PRO Layouts</strong>
                </li>    
                <hr>
                <br>                               
                <li>
                    <a href="#paid-layouts" style="display:inline-block;width:40%">YouTube Feature</a> <?php 
echo wp_kses( $featureCheck, [
    'span'   => [
        'style' => [],
        'class' => [],
    ],
    'strong' => [],
] );
?>
                </li>                                                                                                       
            </ul>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3>Health Check</h3>
        <div class="inside">
            <p>There are a few things we need to check before your plugin will work.</p>
            <?php 
$connection_token = ( isset( $this->options['swyi_api_key'] ) ? $this->options['swyi_api_key'] : '' );
$connection_token_code = ( isset( $this->options['swyi_api_key_code'] ) ? $this->options['swyi_api_key_code'] : '' );
if ( $connection_token_code == '200' ) {
    $license_status_colour = 'green';
    $license_status_label = 'YouTube API Connected!';
} else {
    if ( $connection_token_code == '400' ) {
        $license_status_colour = 'red';
        $license_status_label = 'API Key Invalid';
    } else {
        $license_status_colour = 'gray';
        $license_status_label = 'Not Connected';
    }
}
if ( function_exists( 'curl_version' ) ) {
    $curlVersion = curl_version();
    $curlCheck = '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Enabled (' . $curlVersion['version'] . ')</strong></span>';
}
if ( apply_filters( 'rest_enabled', true ) && apply_filters( 'rest_jsonp_enabled', true ) ) {
    $restCheck = '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Enabled</strong></span>';
} else {
    $restCheck = '<span style="color:red;"><strong><span class="dashicons dashicons-no"></span>Disabled</strong></span>';
}
?>
            <ul>
                <li>
                    <strong style="display:inline-block;width:40%">YouTube API</strong><span style="color: <?php 
echo esc_attr( $license_status_colour );
?>; font-weight: bold;"><?php 
echo esc_html( $license_status_label );
?></span>
                </li>       
                <li>
                    <strong style="display:inline-block;width:40%">PHP cURL</strong> <?php 
echo wp_kses( $curlCheck, [
    'span'   => [
        'style' => [],
        'class' => [],
    ],
    'strong' => [],
] );
?>
                </li>                                                                                    
                <li>
                    <strong style="display:inline-block;width:40%">REST API</strong> <?php 
echo wp_kses( $restCheck, [
    'span'   => [
        'style' => [],
        'class' => [],
    ],
    'strong' => [],
] );
?>
                </li>
            </ul>
        </div>
    </div>
</div>