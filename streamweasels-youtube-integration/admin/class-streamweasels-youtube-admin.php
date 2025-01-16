<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.streamweasels.com/
 * @since      1.0.0
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/admin
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Youtube_Admin {
    public $plugin_name;

    public $version;

    public $options;

    public $optionsWall;

    public $optionsFeature;

    public $optionsShowcase;

    public $optionsPlayer;

    public $optionsStatus;

    public $translations;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = $this->swyi_get_options();
        $this->optionsWall = $this->swyi_get_options_wall();
        $this->optionsFeature = $this->swyi_get_options_feature();
        $this->optionsShowcase = $this->swyi_get_options_showcase();
        $this->optionsPlayer = $this->swyi_get_options_player();
        $this->optionsStatus = $this->swyi_get_options_status();
        $this->translations = $this->swyi_get_translations();
    }

    public function add_rest_endpoints() {
        $fetchData = new SWYI_YouTube_API();
        register_rest_route( 'streamweasels-youtube/v1', '/data/', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'sw_rest_endpoints'),
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ) );
        // rest route for fetching streams
        register_rest_route( 'streamweasels-youtube/v1', '/fetch-videos', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swyi_fetch_videos'),
            'permission_callback' => '__return_true',
        ) );
        // rest route for checking live status
        register_rest_route( 'streamweasels-youtube/v1', '/fetch-live', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swyi_check_live_status'),
            'permission_callback' => '__return_true',
        ) );
    }

    public function sw_rest_endpoints( $data ) {
        $weaselsData = array();
        $weaselsData['accessToken'] = ( isset( $this->options['swyi_api_key'] ) ? $this->options['swyi_api_key'] : '' );
        $weaselsData['proStatus'] = syi_fs()->can_use_premium_code();
        // Check if user is logged in
        if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ) {
            return new WP_Error('rest_not_logged_in', 'You must be logged in to access this data.', array(
                'status' => 401,
            ));
        }
        if ( empty( $weaselsData ) ) {
            return new WP_Error('no_streamweasels_data', 'StreamWeasels Data Missing', array(
                'status' => 404,
            ));
        }
        return new WP_REST_Response($weaselsData, 200);
    }

    public function enqueue_blocks() {
        // Register your custom block using register_block_type
        $blocks_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'build/';
        register_block_type( $blocks_json_path . 'youtube-integration/block.json', array(
            'render_callback' => array($this, 'enqueue_youtube_integration_cb'),
        ) );
        register_block_type( $blocks_json_path . 'youtube-embed/block.json', array(
            'render_callback' => array($this, 'enqueue_youtube_embed_cb'),
        ) );
    }

    public function enqueue_youtube_integration_cb( $attr ) {
        $output = '<div ' . get_block_wrapper_attributes() . '>';
        $output .= do_shortcode( '[sw-youtube-integration layout="' . $attr['layout'] . '" channel="' . $attr['channels'] . '" playlist="' . $attr['playlist'] . '" livestream="' . $attr['livestream'] . '" limit="' . $attr['limit'] . '"]' );
        $output .= '</div>';
        return $output;
    }

    public function enqueue_youtube_embed_cb( $attr ) {
        $attr['autoplay'] = ( isset( $attr['autoplay'] ) && !empty( $attr['autoplay'] ) ? $attr['autoplay'] : 'false' );
        $attr['muted'] = ( isset( $attr['muted'] ) && !empty( $attr['muted'] ) ? $attr['muted'] : 'false' );
        $attr['width'] = ( isset( $attr['width'] ) && !empty( $attr['width'] ) ? $attr['width'] : '' );
        $attr['height'] = ( isset( $attr['height'] ) && !empty( $attr['height'] ) ? $attr['height'] : '100%' );
        $attr['id'] = ( isset( $attr['id'] ) && !empty( $attr['id'] ) ? $attr['id'] : 'aqz-KE-bpKQ' );
        $attr['type'] = ( isset( $attr['type'] ) && !empty( $attr['type'] ) ? $attr['type'] : 'video' );
        if ( $attr['type'] == 'video' && $attr['width'] == '' ) {
            $attr['width'] = '100%';
        }
        if ( $attr['type'] == 'short' && $attr['width'] == '' ) {
            $attr['width'] = '480px';
        }
        if ( substr( $attr['width'], -2 ) !== 'px' && substr( $attr['width'], -1 ) !== '%' ) {
            $attr['width'] .= 'px';
        }
        if ( substr( $attr['height'], -2 ) !== 'px' && substr( $attr['height'], -1 ) !== '%' ) {
            $attr['height'] .= 'px';
        }
        $output = '<div ' . get_block_wrapper_attributes() . '>';
        $output .= do_shortcode( '[sw-youtube-embed id="' . esc_attr( $attr['id'] ) . '" type="' . esc_attr( $attr['type'] ) . '" autoplay="' . esc_attr( $attr['autoplay'] ) . '" muted="' . esc_attr( $attr['muted'] ) . '" width="' . esc_attr( $attr['width'] ) . '" height="' . esc_attr( $attr['height'] ) . '"]' );
        $output .= '</div>';
        return $output;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Streamweasels_Youtube_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Streamweasels_Youtube_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-youtube-admin.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-addons',
            plugin_dir_url( __FILE__ ) . '../freemius/assets/css/admin/add-ons.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name . '-yt-admin',
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-youtube-admin-free.min.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_enqueue_media();
    }

    public function addon_cron_setup() {
        add_action( 'swyi_cron', array($this, 'swyi_run_cron') );
        if ( !wp_next_scheduled( 'swyi_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'swyi_cron' );
        }
    }

    public function swyi_run_cron() {
        $channel_id = ( isset( $this->options['swyi_channel_id'] ) ? $this->options['swyi_channel_id'] : '' );
        $playlist_id = ( isset( $this->options['swyi_playlist_id'] ) ? $this->options['swyi_playlist_id'] : '' );
        $enableCache = ( isset( $this->options['swyi_enable_cache'] ) ? $this->options['swyi_enable_cache'] : 0 );
        if ( $enableCache && ($channel_id !== '' || $playlist_id !== '') ) {
            $cache = new SWYI_YouTube_API_Cache();
            $uploadID = $cache->get_channel_upload_id();
            $cachedVideos = $cache->get_channel_videos( $uploadID, true );
            $this->swyi_youtube_debug_field( 'cron - swyi_video_cache not found. regenerating cache.' );
        }
    }

    public function display_admin_upsell() {
        $display_status = get_transient( 'swyi_notice_closed5' );
        $display_status2 = ( isset( $this->options['swyi_dismiss_for_good5'] ) ? $this->options['swyi_dismiss_for_good5'] : '' );
        if ( !$display_status ) {
            if ( !$display_status2 ) {
                echo '<div class="notice is-dismissible swyi-notice">';
                echo '<div class="swyi-notice__content">';
                echo '<h2>Introducing StreamWeasels Status Bar!</h2>';
                echo '<img src="' . plugin_dir_url( __FILE__ ) . '../admin/img/status-bar-example.png">';
                echo '<p>Add a sticky, customisable Status Bar to the top of your website and let your users know when you\'re live on Twitch, Kick or YouTube!</p>';
                echo '<p>Check out <strong>StreamWeasels Status Bar</strong> for WordPress - <a href="/wp-admin/plugin-install.php?s=streamweasels status bar&tab=search&type=term" target="_blank"><strong>Download for free now</strong></a>.</p>';
                echo '<p><a class="dismiss-for-good" href="#">Don\'t show this again!</a></p>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    public function swyi_admin_notice_dismiss() {
        set_transient( 'swyi_notice_closed5', true, 604800 );
        wp_die();
    }

    public function swyi_admin_notice_dismiss_for_good() {
        $swyi_options = get_option( 'swyi_options' );
        $swyi_options['swyi_dismiss_for_good5'] = true;
        update_option( 'swyi_options', $swyi_options );
        wp_die();
    }

    /**
     * Register the admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        add_menu_page(
            'StreamWeasels',
            'YouTube Integration',
            'manage_options',
            'streamweasels-youtube',
            array($this, 'swyi_showAdmin'),
            'dashicons-youtube'
        );
        add_submenu_page(
            'streamweasels-youtube',
            'Translations',
            'Translations',
            'manage_options',
            'streamweasels-youtube-translations',
            array($this, 'swyi_showAdmin')
        );
        add_submenu_page(
            'streamweasels-youtube',
            '[Layout] Wall',
            '[Layout] Wall',
            'manage_options',
            'streamweasels-youtube-wall',
            array($this, 'swyi_showAdmin')
        );
        add_submenu_page(
            'streamweasels-youtube',
            '[Layout] Showcase',
            '[Layout] Showcase',
            'manage_options',
            'streamweasels-youtube-showcase',
            array($this, 'swyi_showAdmin')
        );
        add_submenu_page(
            'streamweasels-youtube',
            '[Layout] Player',
            '[Layout] Player',
            'manage_options',
            'streamweasels-youtube-player',
            array($this, 'swyi_showAdmin')
        );
        add_submenu_page(
            'streamweasels-youtube',
            '[Layout] Status',
            '[Layout] Status',
            'manage_options',
            'streamweasels-youtube-status',
            array($this, 'swyi_showAdmin')
        );
        $tooltipArray = array(
            'YouTube Channel ID'          => 'Channel ID <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="channel=\'\'"></span>',
            'YouTube Playlist ID'         => 'Playlist ID <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="playlist=\'\'"></span>',
            'YouTube Livestream'          => 'Livestream IDs <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="livestream=\'\'"></span>',
            'Limit'                       => 'Limit <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="limit=\'\'"></span>',
            'Pagination'                  => 'Pagination <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="pagination=\'\'"></span>',
            'Shortcode'                   => '',
            'Colour Theme'                => 'Colour Theme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="colour-theme=\'\'"></span>',
            'Layout'                      => 'Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="layout=\'\'"></span>',
            'Embed'                       => 'Embed <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed=\'\'"></span>',
            'Embed Colour Scheme'         => 'Embed Colour Scheme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-theme=\'\'"></span>',
            'Display Chat'                => 'Display Chat <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-chat=\'\'"></span>',
            'Display Title'               => 'Display Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-title=\'\'"></span>',
            'Title Position'              => 'Title Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title-position=\'\'"></span>',
            'Start Muted'                 => 'Start Muted <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-muted=\'\'"></span>',
            'Show Offline'                => 'Show Offline Streams <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline=\'\'"></span>',
            'Offline Message'             => 'Offline Message <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-text=\'\'"></span>',
            'Show Offline Image'          => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-image=\'\'"></span>',
            'Autoload Stream'             => 'Autoload Video <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoload=\'\'"></span>',
            'Autoplay Stream'             => 'Autoplay Video <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay=\'\'"></span>',
            'Autoplay Select'             => 'Autoplay Select <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay-select=\'\'"></span>',
            'Featured Streamer'           => 'Featured Streamer <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="featured-stream=\'\'"></span>',
            'Title'                       => 'Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title=\'\'"></span>',
            'Subtitle'                    => 'Subtitle <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="subtitle=\'\'"></span>',
            'Offline Image'               => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="offline-image=\'\'"></span>',
            'Logo'                        => 'Custom Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-image=\'\'"></span>',
            'Profile'                     => 'Profile Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="profile-image=\'\'"></span>',
            'Logo Background Colour'      => 'Logo Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-bg-colour=\'\'"></span>',
            'Logo Border Colour'          => 'Logo Border Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-border-colour=\'\'"></span>',
            'Max Width'                   => 'Max Width <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="max-width=\'\'"></span>',
            'Tile Layout'                 => 'Tile Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-layout=\'\'"></span>',
            'Tile Sorting'                => 'Tile Sorting <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-sorting=\'\'"></span>',
            'Tile Live'                   => 'Live Info <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="live-info=\'\'"></span>',
            'Background Colour'           => 'Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-bg-colour=\'\'"></span>',
            'Title Colour'                => 'Title Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-title-colour=\'\'"></span>',
            'Subtitle Colour'             => 'Subtitle Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-subtitle-colour=\'\'"></span>',
            'Rounded Corners'             => 'Rounded Corners <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-rounded-corners=\'\'"></span>',
            'Hover Effect'                => 'Hover Effect <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-effect=\'\'"></span>',
            'Hover Colour'                => 'Hover Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-colour=\'\'"></span>',
            'Column Count'                => 'Column Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-count=\'\'"></span>',
            'Column Spacing'              => 'Column Spacing <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-spacing=\'\'"></span>',
            'Embed Position'              => 'Embed Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-embed-position=\'\'"></span>',
            'Controls Background Colour'  => 'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-controls-bg-colour=\'\'"></span>',
            'Controls Arrow Colour'       => 'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-controls-arrow-colour=\'\'"></span>',
            'Controls Background Colour2' => 'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="showcase-controls-bg-colour=\'\'"></span>',
            'Controls Arrow Colour2'      => 'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="showcase-controls-arrow-colour=\'\'"></span>',
            'Welcome Background Colour'   => 'Welcome Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-bg-colour=\'\'"></span>',
            'Welcome Image'               => 'Welcome Background Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-image=\'\'"></span>',
            'Welcome Logo'                => 'Welcome Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-logo=\'\'"></span>',
            'Welcome Text'                => 'Welcome Text <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text=\'\'"></span>',
            'Welcome Text 2'              => 'Welcome Text 2 <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text-2=\'\'"></span>',
            'Welcome Text Colour'         => 'Welcome Text Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text-colour=\'\'"></span>',
            'Stream List Position'        => 'Stream List Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-stream-list-position=\'\'"></span>',
            'Show Global'                 => 'Show on Every Page',
            'Hide Offline'                => 'Hide Offline <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-hide-offline=\'\'"></span>',
            'Placement'                   => 'Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-placement=\'\'"></span>',
            'Vertical Placement'          => 'Vertical Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-vertical-placement=\'\'"></span>',
            'Horizontal Placement'        => 'Horizontal Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-horizontal-placement=\'\'"></span>',
            'Vertical Distance'           => 'Vertical Distance <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-vertical-distance=\'\'"></span>',
            'Horizontal Distance'         => 'Horizontal Distance <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-horizontal-distance=\'\'"></span>',
            'Custom Logo'                 => 'Custom Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-custom-logo=\'\'"></span>',
            'Logo Background Colour'      => 'Logo Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-logo-background-colour=\'\'"></span>',
            'Accent Colour'               => 'Accent Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-accent-colour=\'\'"></span>',
            'Carousel Background Colour'  => 'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-carousel-background-colour=\'\'"></span>',
            'Carousel Arrow Colour'       => 'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-carousel-arrow-colour=\'\'"></span>',
            'Disable Carousel'            => 'Disable Carousel <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-disable-carousel=\'\'"></span>',
            'Enable Cache'                => 'Enable Cache <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="enable-cache=\'\'"></span>',
            'Cache Channel'               => 'Cache Channel',
            'Hide Shorts'                 => 'Hide Shorts <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hide-shorts=\'\'"></span>',
            'Slide Count'                 => 'Slide Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="showcase-slide-count=\'\'"></span>',
            'Skew'                        => 'Skew <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-skew=\'\'"></span>',
        );
        add_settings_section(
            'swyi_shortcode_settings',
            'Shortcode',
            false,
            'swyi_shortcode_fields'
        );
        add_settings_section(
            'swyi_translations_settings',
            'Translations',
            false,
            'swyi_translations_fields'
        );
        add_settings_section(
            'swyi_api_settings',
            'YouTube API Settings',
            false,
            'swyi_api_fields'
        );
        add_settings_section(
            'swyi_main_settings',
            'Main Settings',
            false,
            'swyi_main_fields'
        );
        add_settings_section(
            'swyi_layout_settings',
            'Layout Settings',
            false,
            'swyi_layout_fields'
        );
        add_settings_section(
            'swyi_wall_settings',
            'Wall Settings',
            false,
            'swyi_wall_fields'
        );
        add_settings_section(
            'swyi_showcase_settings',
            'Showcase Settings',
            false,
            'swyi_showcase_fields'
        );
        add_settings_section(
            'swyi_feature_settings',
            'Feature Settings',
            false,
            'swyi_feature_fields'
        );
        add_settings_section(
            'swyi_player_settings',
            'Player Settings',
            false,
            'swyi_player_fields'
        );
        add_settings_section(
            'swyi_embed_settings',
            'Embed Settings',
            false,
            'swyi_embed_fields'
        );
        add_settings_section(
            'swyi_livestream_settings',
            'Livestream Settings',
            false,
            'swyi_livestream_fields'
        );
        add_settings_section(
            'swyi_autoplay_settings',
            'Autoplay Settings',
            false,
            'swyi_autoplay_fields'
        );
        add_settings_section(
            'swyi_appearance_settings',
            'Appearance Settings',
            false,
            'swyi_appearance_fields'
        );
        add_settings_section(
            'swyi_tile_settings',
            'Tile Settings',
            false,
            'swyi_tile_fields'
        );
        add_settings_section(
            'swyi_hover_settings',
            'Hover Settings',
            false,
            'swyi_hover_fields'
        );
        add_settings_section(
            'swyi_cache_settings',
            'Cache Settings',
            false,
            'swyi_cache_fields'
        );
        add_settings_section(
            'swyi_debug_settings',
            'Debug Settings',
            false,
            'swyi_debug_fields'
        );
        add_settings_section(
            'swyi_status_placement_settings',
            '[Layout] Status Placement Settings',
            false,
            'swyi_status_placement_fields'
        );
        add_settings_section(
            'swyi_status_appearance_settings',
            '[Layout] Status Apearance Settings',
            false,
            'swyi_status_appearance_fields'
        );
        add_settings_section(
            'swyi_status_shortcode_settings',
            'Shortcode',
            false,
            'swyi_status_shortcode_fields'
        );
        register_setting( 'swyi_options', 'swyi_options', array($this, 'swyi_options_validate') );
        register_setting( 'swyi_options_wall', 'swyi_options_wall', array($this, 'swyi_options_validate_wall') );
        register_setting( 'swyi_options_showcase', 'swyi_options_showcase', array($this, 'swyi_options_validate_showcase') );
        register_setting( 'swyi_options_feature', 'swyi_options_feature', array($this, 'swyi_options_validate_feature') );
        register_setting( 'swyi_options_player', 'swyi_options_player', array($this, 'swyi_options_validate_player') );
        register_setting( 'swyi_options_status', 'swyi_options_status', array($this, 'swyi_options_validate_status') );
        register_setting( 'swyi_translations', 'swyi_translations', array($this, 'swyi_translations_validate') );
        // YouTube API Fields
        add_settings_field(
            'swyi_api_connection_status',
            'Connection Status',
            array($this, 'swyi_api_connection_status_cb'),
            'swyi_api_fields',
            'swyi_api_settings'
        );
        add_settings_field(
            'swyi_api_key',
            'YouTube API Key',
            array($this, 'swyi_api_key_cb'),
            'swyi_api_fields',
            'swyi_api_settings'
        );
        // Main Fields
        add_settings_field(
            'swyi_channel_id',
            $tooltipArray['YouTube Channel ID'],
            array($this, 'swyi_channel_id_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_playlist_id',
            $tooltipArray['YouTube Playlist ID'],
            array($this, 'swyi_playlist_id_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_livestream_id',
            $tooltipArray['YouTube Livestream'],
            array($this, 'swyi_livestream_id_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_limit',
            $tooltipArray['Limit'],
            array($this, 'swyi_limit_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_pagination',
            $tooltipArray['Pagination'],
            array($this, 'swyi_pagination_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_shortcode',
            $tooltipArray['Shortcode'],
            array($this, 'swyi_shortcode_cb'),
            'swyi_shortcode_fields',
            'swyi_shortcode_settings'
        );
        add_settings_field(
            'swyi_colour_theme',
            $tooltipArray['Colour Theme'],
            array($this, 'swyi_colour_theme_cb'),
            'swyi_main_fields',
            'swyi_main_settings'
        );
        add_settings_field(
            'swyi_layout',
            $tooltipArray['Layout'],
            array($this, 'swyi_layout_cb'),
            'swyi_layout_fields',
            'swyi_layout_settings'
        );
        // Wall Fields
        add_settings_field(
            'swyi_wall_column_count',
            $tooltipArray['Column Count'],
            array($this, 'swyi_wall_column_count_cb'),
            'swyi_wall_fields',
            'swyi_wall_settings'
        );
        add_settings_field(
            'swyi_wall_column_spacing',
            $tooltipArray['Column Spacing'],
            array($this, 'swyi_wall_column_spacing_cb'),
            'swyi_wall_fields',
            'swyi_wall_settings'
        );
        add_settings_field(
            'swyi_wall_hide_shorts',
            $tooltipArray['Hide Shorts'],
            array($this, 'swyi_wall_hide_shorts_cb'),
            'swyi_wall_fields',
            'swyi_wall_settings'
        );
        // Player Fields
        add_settings_field(
            'swyi_player_welcome_bg_colour',
            $tooltipArray['Welcome Background Colour'],
            array($this, 'swyi_player_welcome_bg_colour'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_welcome_logo',
            $tooltipArray['Welcome Logo'],
            array($this, 'swyi_player_welcome_logo_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_welcome_image',
            $tooltipArray['Welcome Image'],
            array($this, 'swyi_player_welcome_image_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_welcome_text',
            $tooltipArray['Welcome Text'],
            array($this, 'swyi_player_welcome_text_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_welcome_text_2',
            $tooltipArray['Welcome Text 2'],
            array($this, 'swyi_player_welcome_text_2_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_welcome_text_colour',
            $tooltipArray['Welcome Text Colour'],
            array($this, 'swyi_player_welcome_text_colour_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        add_settings_field(
            'swyi_player_stream_position',
            $tooltipArray['Stream List Position'],
            array($this, 'swyi_player_stream_position_cb'),
            'swyi_player_fields',
            'swyi_player_settings'
        );
        // Showcase Fields
        add_settings_field(
            'swyi_showcase_controls_bg_colour',
            $tooltipArray['Controls Background Colour2'],
            array($this, 'swyi_showcase_controls_bg_colour_cb'),
            'swyi_showcase_fields',
            'swyi_showcase_settings'
        );
        add_settings_field(
            'swyi_showcase_controls_arrow_colour',
            $tooltipArray['Controls Arrow Colour2'],
            array($this, 'swyi_showcase_controls_arrow_colour_cb'),
            'swyi_showcase_fields',
            'swyi_showcase_settings'
        );
        add_settings_field(
            'swyi_showcase_slide_count',
            $tooltipArray['Slide Count'],
            array($this, 'swyi_showcase_slide_count_cb'),
            'swyi_showcase_fields',
            'swyi_showcase_settings'
        );
        // Feature Fields
        if ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ) {
            add_settings_field(
                'swyi_feature_embed_position',
                $tooltipArray['Embed Position'],
                array($this, 'swyi_feature_embed_position_cb'),
                'swyi_feature_fields',
                'swyi_feature_settings'
            );
            add_settings_field(
                'swyi_feature_controls_bg_colour',
                $tooltipArray['Controls Background Colour'],
                array($this, 'swyi_feature_controls_bg_colour_cb'),
                'swyi_feature_fields',
                'swyi_feature_settings'
            );
            add_settings_field(
                'swyi_feature_controls_arrow_colour',
                $tooltipArray['Controls Arrow Colour'],
                array($this, 'swyi_feature_controls_arrow_colour_cb'),
                'swyi_feature_fields',
                'swyi_feature_settings'
            );
            add_settings_field(
                'swyi_feature_skew',
                $tooltipArray['Skew'],
                array($this, 'swyi_feature_skew_cb'),
                'swyi_feature_fields',
                'swyi_feature_settings'
            );
        }
        add_settings_field(
            'swyi_status_show_global',
            $tooltipArray['Show Global'],
            array($this, 'swyi_status_show_global_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_hide_when_offline',
            $tooltipArray['Hide Offline'],
            array($this, 'swyi_status_hide_when_offline_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_placement',
            $tooltipArray['Placement'],
            array($this, 'swyi_status_placement_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_vertical_placement',
            $tooltipArray['Vertical Placement'],
            array($this, 'swyi_status_vertical_placement_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_horizontal_placement',
            $tooltipArray['Horizontal Placement'],
            array($this, 'swyi_status_horizontal_placement_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_vertical_distance',
            $tooltipArray['Vertical Distance'],
            array($this, 'swyi_status_vertical_distance_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_horizontal_distance',
            $tooltipArray['Horizontal Distance'],
            array($this, 'swyi_status_horizontal_distance_cb'),
            'swyi_status_placement_fields',
            'swyi_status_placement_settings'
        );
        add_settings_field(
            'swyi_status_custom_logo',
            $tooltipArray['Custom Logo'],
            array($this, 'swyi_status_custom_logo_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        add_settings_field(
            'swyi_status_logo_background_colour',
            $tooltipArray['Logo Background Colour'],
            array($this, 'swyi_status_logo_background_colour_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        add_settings_field(
            'swyi_status_accent_colour',
            $tooltipArray['Accent Colour'],
            array($this, 'swyi_status_accent_colour_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        add_settings_field(
            'swyi_status_carousel_background_colour',
            $tooltipArray['Carousel Background Colour'],
            array($this, 'swyi_status_carousel_background_colour_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        add_settings_field(
            'swyi_status_carousel_arrow_colour',
            $tooltipArray['Carousel Arrow Colour'],
            array($this, 'swyi_status_carousel_arrow_colour_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        add_settings_field(
            'swyi_status_disable_carousel',
            $tooltipArray['Disable Carousel'],
            array($this, 'swyi_status_disable_carousel_cb'),
            'swyi_status_appearance_fields',
            'swyi_status_appearance_settings'
        );
        // add_settings_field('swyi_status_enable_classic', $tooltipArray['Enable Classic'], array($this, 'swyi_status_enable_classic_cb'), 'swyi_status_classic_fields', 'swyi_status_classic_settings');
        // add_settings_field('swyi_status_classic_online_text', $tooltipArray['Classic Online Text'], array($this, 'swyi_status_classic_online_text_cb'), 'swyi_status_classic_fields', 'swyi_status_classic_settings');
        // add_settings_field('swyi_status_classic_offline_text', $tooltipArray['Classic Offline Text'], array($this, 'swyi_status_classic_offline_text_cb'), 'swyi_status_classic_fields', 'swyi_status_classic_settings');
        // Embed Settings
        add_settings_field(
            'swyi_embed',
            $tooltipArray['Embed'],
            array($this, 'swyi_embed_cb'),
            'swyi_embed_fields',
            'swyi_embed_settings'
        );
        add_settings_field(
            'swyi_embed_muted',
            $tooltipArray['Start Muted'],
            array($this, 'swyi_embed_muted_cb'),
            'swyi_embed_fields',
            'swyi_embed_settings'
        );
        // Livestream Settings
        add_settings_field(
            'swyi_show_offline',
            $tooltipArray['Show Offline'],
            array($this, 'swyi_show_offline_cb'),
            'swyi_livestream_fields',
            'swyi_livestream_settings'
        );
        add_settings_field(
            'swyi_show_offline_text',
            $tooltipArray['Offline Message'],
            array($this, 'swyi_show_offline_text_cb'),
            'swyi_livestream_fields',
            'swyi_livestream_settings'
        );
        add_settings_field(
            'swyi_show_offline_image',
            $tooltipArray['Show Offline Image'],
            array($this, 'swyi_show_offline_image_cb'),
            'swyi_livestream_fields',
            'swyi_livestream_settings'
        );
        // Autoplay Settings
        add_settings_field(
            'swyi_autoload',
            $tooltipArray['Autoload Stream'],
            array($this, 'swyi_autoload_cb'),
            'swyi_autoplay_fields',
            'swyi_autoplay_settings'
        );
        add_settings_field(
            'swyi_autoplay',
            $tooltipArray['Autoplay Stream'],
            array($this, 'swyi_autoplay_cb'),
            'swyi_autoplay_fields',
            'swyi_autoplay_settings'
        );
        // add_settings_field('swyi_autoplay_select', $tooltipArray['Autoplay Select'], array($this, 'swyi_autoplay_select_cb'), 'swyi_autoplay_fields', 'swyi_autoplay_settings');
        // add_settings_field('swyi_featured_stream', $tooltipArray['Featured Streamer'], array($this, 'swyi_featured_stream_cb'), 'swyi_autoplay_fields', 'swyi_autoplay_settings');
        // Appearance Settings
        add_settings_field(
            'swyi_title',
            $tooltipArray['Title'],
            array($this, 'swyi_title_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        add_settings_field(
            'swyi_subtitle',
            $tooltipArray['Subtitle'],
            array($this, 'swyi_subtitle_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        add_settings_field(
            'swyi_logo_image',
            $tooltipArray['Logo'],
            array($this, 'swyi_logo_image_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        add_settings_field(
            'swyi_logo_bg_colour',
            $tooltipArray['Logo Background Colour'],
            array($this, 'swyi_logo_bg_colour_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        add_settings_field(
            'swyi_logo_border_colour',
            $tooltipArray['Logo Border Colour'],
            array($this, 'swyi_logo_border_colour_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        add_settings_field(
            'swyi_max_width',
            $tooltipArray['Max Width'],
            array($this, 'swyi_max_width_cb'),
            'swyi_appearance_fields',
            'swyi_appearance_settings'
        );
        // Tile Settings
        add_settings_field(
            'swyi_tile_layout',
            $tooltipArray['Tile Layout'],
            array($this, 'swyi_tile_layout_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        add_settings_field(
            'swyi_tile_sorting',
            $tooltipArray['Tile Sorting'],
            array($this, 'swyi_tile_sorting_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        add_settings_field(
            'swyi_tile_bg_colour',
            $tooltipArray['Background Colour'],
            array($this, 'swyi_tile_bg_colour_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        add_settings_field(
            'swyi_tile_title_colour',
            $tooltipArray['Title Colour'],
            array($this, 'swyi_tile_title_colour_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        add_settings_field(
            'swyi_tile_subtitle_colour',
            $tooltipArray['Subtitle Colour'],
            array($this, 'swyi_tile_subtitle_colour_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        add_settings_field(
            'swyi_tile_rounded_corners',
            $tooltipArray['Rounded Corners'],
            array($this, 'swyi_tile_rounded_corners_cb'),
            'swyi_tile_fields',
            'swyi_tile_settings'
        );
        // Hover  Settings
        add_settings_field(
            'swyi_hover_effect',
            $tooltipArray['Hover Effect'],
            array($this, 'swyi_hover_effect_cb'),
            'swyi_hover_fields',
            'swyi_hover_settings'
        );
        add_settings_field(
            'swyi_hover_colour',
            $tooltipArray['Hover Colour'],
            array($this, 'swyi_hover_colour_cb'),
            'swyi_hover_fields',
            'swyi_hover_settings'
        );
        // Cache  Settings
        add_settings_field(
            'swyi_enable_cache',
            $tooltipArray['Enable Cache'],
            array($this, 'swyi_enable_cache_cb'),
            'swyi_cache_fields',
            'swyi_cache_settings'
        );
        add_settings_field(
            'swyi_cache_channel',
            $tooltipArray['Cache Channel'],
            array($this, 'swyi_cache_channel_cb'),
            'swyi_cache_fields',
            'swyi_cache_settings'
        );
        //add_settings_field('swyi_cache_content', $tooltipArray['Cache Content'], array($this, 'swyi_cache_content_cb'), 'swyi_cache_fields', 'swyi_cache_settings');
        // Translation Fields
        add_settings_field(
            'swyi_translations_live',
            'Live',
            array($this, 'swyi_translations_live_cb'),
            'swyi_translations_fields',
            'swyi_translations_settings'
        );
        add_settings_field(
            'swyi_translations_views',
            'Views',
            array($this, 'swyi_translations_views_cb'),
            'swyi_translations_fields',
            'swyi_translations_settings'
        );
        add_settings_field(
            'swyi_translations_next_page',
            'Next Page',
            array($this, 'swyi_translations_next_page_cb'),
            'swyi_translations_fields',
            'swyi_translations_settings'
        );
        add_settings_field(
            'swyi_translations_prev_page',
            'Previous Page',
            array($this, 'swyi_translations_prev_page_cb'),
            'swyi_translations_fields',
            'swyi_translations_settings'
        );
        // Error  Settings
        add_settings_field(
            'swyi_debug',
            'Error Log',
            array($this, 'swyi_debug_cb'),
            'swyi_debug_fields',
            'swyi_debug_settings'
        );
    }

    public function swyi_showAdmin() {
        include 'partials/streamweasels-youtube-admin-display.php';
    }

    public function swyi_api_connection_status_cb() {
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
        ?>
		<span style="color: <?php 
        echo esc_attr( $license_status_colour );
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label );
        ?></span>
		<input type="hidden"  id="sw-api-key-code" name="swyi_options[swyi_api_key_code]" value="<?php 
        echo esc_html( $connection_token_code );
        ?>" />
		<?php 
    }

    public function swyi_api_key_cb() {
        $api_key = ( isset( $this->options['swyi_api_key'] ) ? $this->options['swyi_api_key'] : '' );
        ?>

		<input type="text" id="sw-api-key" name="swyi_options[swyi_api_key]" size='40' value="<?php 
        echo esc_html( $api_key );
        ?>" />

		<?php 
    }

    public function swyi_channel_id_cb() {
        $channel_id = ( isset( $this->options['swyi_channel_id'] ) ? $this->options['swyi_channel_id'] : '' );
        $channel_id_code = ( isset( $this->options['swyi_channel_id_code'] ) ? $this->options['swyi_channel_id_code'] : '' );
        ?>
		<div>
			<input type="text" id="sw-channel-id" name="swyi_options[swyi_channel_id]" size='40' placeholder="example: UCAuUUnT6oDeKwE6v1NGQxug" value="<?php 
        echo esc_html( $channel_id );
        ?>" />
			<?php 
        if ( !empty( $channel_id ) && !empty( $channel_id_code ) ) {
            ?>
				<?php 
            if ( isset( $channel_id_code['valid'] ) && $channel_id_code['valid'] > 0 ) {
                ?>
					<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Channel<?php 
                echo ( $channel_id_code['valid'] > 1 ? 's' : '' );
                ?> found (<?php 
                echo esc_html( $channel_id_code['valid'] );
                ?>)</p>
				<?php 
            }
            ?>
				<?php 
            if ( isset( $channel_id_code['invalid'] ) && $channel_id_code['invalid'] > 0 ) {
                ?>
					<p class="sw-success"><span style="color:red;" class="dashicons dashicons-no-alt"></span>Invalid channel<?php 
                echo ( $channel_id_code['invalid'] > 1 ? 's' : '' );
                ?> found (<?php 
                echo esc_html( $channel_id_code['invalid'] );
                ?>)</p>
				<?php 
            }
            ?>
			<?php 
        }
        ?>
		</div>

		<p>To display <strong>Shorts</strong> or <strong>Channel</strong> videos, enter a single or multiple YouTube Channel IDs. Seperate multiple Channel IDs with a comma (,). You can convert any YouTube username to ID <a href="https://www.streamweasels.com/tools/youtube-channel-id-and-user-id-convertor/?utm_source=wordpress&utm_medium=youtube-integration&utm_campaign=settings">here</a>.</p>

		<?php 
    }

    public function swyi_playlist_id_cb() {
        $channel_id = ( isset( $this->options['swyi_playlist_id'] ) ? $this->options['swyi_playlist_id'] : '' );
        $channel_id_code = ( isset( $this->options['swyi_playlist_id_code'] ) ? $this->options['swyi_playlist_id_code'] : '' );
        ?>

		<div>
			<input type="text" id="sw-playlist-id" name="swyi_options[swyi_playlist_id]" size='40' placeholder="example: UUAuUUnT6oDeKwE6v1NGQxug" value="<?php 
        echo esc_html( $channel_id );
        ?>" />
			<?php 
        if ( !empty( $channel_id ) && !empty( $channel_id_code ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Playlist Found</p>
			<?php 
        }
        ?>
			<?php 
        if ( !empty( $channel_id ) && empty( $channel_id_code ) ) {
            ?>
				<p class="sw-success"><span style="color:red;" class="dashicons dashicons-no-alt"></span>Playlist Not Found!</p>
			<?php 
        }
        ?>			
		</div>		
		<p>To display a <strong>Playlist</strong>, enter the YouTube Playlist ID. You can find more information on playlist IDs <a href="https://www.streamweasels.com/tools/youtube-channel-id-and-user-id-convertor#playlist">here</a>.</p>

		<?php 
    }

    public function swyi_livestream_id_cb() {
        $livestream_id = ( isset( $this->options['swyi_livestream_id'] ) ? $this->options['swyi_livestream_id'] : '' );
        $channel_id_code = ( isset( $this->options['swyi_livestream_id_code'] ) ? $this->options['swyi_livestream_id_code'] : '' );
        ?>

		<div>
			<input type="text" id="sw-livestream-id" name="swyi_options[swyi_livestream_id]" size='40' placeholder="example: UCXuqSBlHAE6Xw-yeJA0Tunw,UCAuUUnT6oDeKwE6v1NGQxug" value="<?php 
        echo esc_html( $livestream_id );
        ?>" />
			<?php 
        if ( !empty( $livestream_id ) && !empty( $channel_id_code ) ) {
            ?>
				<?php 
            if ( isset( $channel_id_code['valid'] ) && $channel_id_code['valid'] > 0 ) {
                ?>
					<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Channel<?php 
                echo ( $channel_id_code['valid'] > 1 ? 's' : '' );
                ?> found (<?php 
                echo esc_html( $channel_id_code['valid'] );
                ?>)</p>
				<?php 
            }
            ?>
				<?php 
            if ( isset( $channel_id_code['invalid'] ) && $channel_id_code['invalid'] > 0 ) {
                ?>
					<p class="sw-success"><span style="color:red;" class="dashicons dashicons-no-alt"></span>Invalid channel<?php 
                echo ( $channel_id_code['invalid'] > 1 ? 's' : '' );
                ?> found (<?php 
                echo esc_html( $channel_id_code['invalid'] );
                ?>)</p>
				<?php 
            }
            ?>
			<?php 
        }
        ?>		
		</div>		
		<p>To display <strong>Live Streams</strong>, enter a single or multiple YouTube channel IDs to display live status. Seperate multiple Channel IDs with a comma (,). You can convert any YouTube username to ID <a href="https://www.streamweasels.com/tools/youtube-channel-id-and-user-id-convertor/?utm_source=wordpress&utm_medium=youtube-integration&utm_campaign=settings">here</a>.</p>

		<?php 
    }

    public function swyi_colour_theme_cb() {
        $colourTheme = ( isset( $this->options['swyi_colour_theme'] ) ? $this->options['swyi_colour_theme'] : '' );
        ?>
		
		<select id="sw-colour-theme" name="swyi_options[swyi_colour_theme]">
			<option value="light" <?php 
        echo selected( $colourTheme, 'light', false );
        ?>><?php 
        esc_html_e( 'Light Theme', 'streamweasels-youtube-integration' );
        ?></option>	
            <option value="dark" <?php 
        echo selected( $colourTheme, 'dark', false );
        ?>><?php 
        esc_html_e( 'Dark Theme', 'streamweasels-youtube-integration' );
        ?></option>
        </select>
		<p>Select the colour theme for your YouTube content. These colours match YouTube's own Light and Dark mode.</p>
		<?php 
    }

    public function swyi_limit_cb() {
        $limit = ( isset( $this->options['swyi_limit'] ) ? $this->options['swyi_limit'] : '' );
        ?>
		
		<input type="text" id="sw-limit" name="swyi_options[swyi_limit]" size='40' placeholder="example: 15" value="<?php 
        echo esc_html( $limit );
        ?>" />
		<p>Limit the maximum number of videos to display per page.</p>
		<?php 
    }

    public function swyi_pagination_cb() {
        $pagination = ( isset( $this->options['swyi_pagination'] ) ? $this->options['swyi_pagination'] : '' );
        if ( syi_fs()->can_use_premium_code() == false ) {
            $paginationCheck = 'disabled';
            $paginationCheckStyles = "style='cursor:not-allowed;'";
        } else {
            $paginationCheck = '';
            $paginationCheckStyles = '';
        }
        ?>
		<input type="hidden" name="swyi_options[swyi_pagination]" value="0"/>
		<input <?php 
        echo esc_html( $paginationCheck ) . ' ' . esc_html( $paginationCheckStyles );
        ?> type="checkbox" id="sw-pagination" name="swyi_options[swyi_pagination]" value="1" <?php 
        checked( 1, $pagination, true );
        ?>/>
		<p>Enable pagination (left & right arrows to display more videos).</p>
		<p>This currently only works when used with a single channel.</p>
		<?php 
        if ( syi_fs()->can_use_premium_code() == false ) {
            ?>
			<p class="sw-pro">This feature is only available for PAID Users. <a href="admin.php?page=streamweasels-youtube-pricing&trial=true">Free Trial</a>.</p>
		<?php 
        }
    }

    public function swyi_layout_cb() {
        // $swyi_layout_options = swyi_twitch_get_layout_options();
        $layout = ( isset( $this->options['swyi_layout'] ) ? $this->options['swyi_layout'] : '' );
        ?>

		<select id="sw-layout" name="swyi_options[swyi_layout]">
			<option value="showcase" <?php 
        echo selected( $layout, 'showcase', false );
        ?>><?php 
        esc_html_e( 'Shorts Showcase', 'streamweasels-youtube-integration' );
        ?></option>
			<option value="wall" <?php 
        echo selected( $layout, 'wall', false );
        ?>><?php 
        esc_html_e( 'Wall', 'streamweasels-youtube-integration' );
        ?></option>
			<option value="player" <?php 
        echo selected( $layout, 'player', false );
        ?>><?php 
        esc_html_e( 'Player', 'streamweasels-youtube-integration' );
        ?></option>
			<option value="status" <?php 
        echo selected( $layout, 'status', false );
        ?>><?php 
        esc_html_e( 'Status', 'streamweasels-youtube-integration' );
        ?></option>
			<option <?php 
        echo ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ? '' : 'disabled' );
        ?> style="cursor:not-allowed" value="feature" <?php 
        echo selected( $layout, 'feature', false );
        ?>><?php 
        esc_html_e( 'Feature', 'streamweasels-youtube-integration' );
        ?></option>
		</select>		
		
		<div id="fs_addons" class="wrap fs-section">
			<h3>Free Layouts</h3>
			<p>StreamWeasels YouTube Integration comes with <strong>four free layouts.</strong></p>		
			<br>	
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-wall">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo esc_url( plugin_dir_url( __FILE__ ) . '/img/youtube-showcase-600x200.png' );
        ?>');">
								<span class="fs-badge fs-installed-addon-badge">Active</span>        
							</li>
							<li class="fs-title">Shorts Showcase</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Vertical layout for displaying YouTube Shorts.</li>
						</ul>
					</div>
				</li>					
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-wall">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo esc_url( plugin_dir_url( __FILE__ ) . '/img/youtube-wall-600x200.jpg' );
        ?>');">
								<span class="fs-badge fs-installed-addon-badge">Active</span>        
							</li>
							<li class="fs-title">YouTube Wall</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Classic YouTube layout for displaying many videos at once.</li>
						</ul>
					</div>
				</li>			
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-wall">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo esc_url( plugin_dir_url( __FILE__ ) . '/img/youtube-player-600x200.png' );
        ?>');">
								<span class="fs-badge fs-installed-addon-badge">Active</span>        
							</li>
							<li class="fs-title">YouTube Player</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Player layout to focus on the embedded experience.</li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="stream-status-for-twitch">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo esc_url( plugin_dir_url( __FILE__ ) . '/img/youtube-status-600x200.png' );
        ?>');">
							<span class="fs-badge fs-installed-addon-badge">Active</span>        
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">YouTube Status</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Simply display YouTube live status on every page of your website.</li>
						</ul>
					</div>
				</li>								
			</ul>
			<h3 id="paid-layouts">PRO Layouts</h3>
			<p>Looking for more options? We have <strong>another professional layout</strong> for you to choose from below. More layouts will be added soon!</p>		
			<br>			
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="streamweasels-feature-pro">
					<a href="admin.php?page=streamweasels-youtube-pricing&trial=true" aria-label="More information about YouTube Feature" data-title="YouTube Feature" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo esc_url( plugin_dir_url( __FILE__ ) . '/img/youtube-feature-600x200.jpg' );
        ?>');">
								<?php 
        echo ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">YouTube Feature</li>
							<li class="fs-offer">
							<span class="fs-price">PRO Layout</span>
							</li>
							<li class="fs-description">Slick, professional layout inspired by the YouTube homepage.</li>
							<li class="fs-cta"><a class="button" href="admin.php?page=streamweasels-youtube-pricing&trial=true">Upgrade Now</a></li>
						</ul>
					</div>
					<div class="fs-extras">
						<a href="admin.php?page=streamweasels-youtube-pricing&trial=true">Unlock Layout</a> | 
						<a href="https://www.streamweasels.com/youtube-wordpress-plugins/youtube-feature/?utm_source=wordpress&utm_medium=youtube-integration&utm_campaign=view-demo" target="_blank">View Demo</a>
					</div>
				</li>								
			</ul>
		</div>
		<?php 
    }

    public function swyi_shortcode_cb() {
        ?>
		<div class="postbox-half-wrapper">
			<div class="postbox-half">
				<h3>Simple Shortcode (for one YouTube Integration)</h3>
				<p>If you are simply using one YouTube Integration on your site, you can fill in the settings on this page and use this simple shortcode:</p>
				<span class="swyi-shortcode simple-shortcode">[sw-youtube]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".simple-shortcode" aria-label="Copied!" >Copy Simple Shortcode</a>
			</div>
			<div class="postbox-half">
				<h3>Advanced Shortcode (for many YouTube Integrations)</h3>
				<p>If you are using more than one YouTube Integration on your site, and you need to change the settings on each, use our advanced shortcode:</p>
				<span class="swyi-shortcode advanced-shortcode">[sw-youtube]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".advanced-shortcode" aria-label="Copied!" >Copy Advanced Shortcode</a>
			</div>	
		</div>	
		<?php 
    }

    public function swyi_wall_column_count_cb() {
        $columns = ( isset( $this->optionsWall['swyi_wall_column_count'] ) ? $this->optionsWall['swyi_wall_column_count'] : '4' );
        ?>
		
		<input id="sw-tile-column-count" type="text" name="swyi_options_wall[swyi_wall_column_count]" value="<?php 
        echo esc_html( $columns );
        ?>">
		<span class="range-bar-value"></span>		
		<p>Choose the number of columns for your Wall.</p>
		<?php 
    }

    public function swyi_wall_column_spacing_cb() {
        $columnSpacing = ( isset( $this->optionsWall['swyi_wall_column_spacing'] ) ? $this->optionsWall['swyi_wall_column_spacing'] : '5' );
        ?>
		
		<input id="sw-tile-column-spacing" type="text" name="swyi_options_wall[swyi_wall_column_spacing]" value="<?php 
        echo esc_html( $columnSpacing );
        ?>">
		<span class="range-bar-value"></span>	
		<p>Choose the space between columns for your Wall.</p>

		<?php 
    }

    public function swyi_wall_hide_shorts_cb() {
        $hideShorts = ( isset( $this->optionsWall['swyi_wall_hide_shorts'] ) ? $this->optionsWall['swyi_wall_hide_shorts'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options_wall[swyi_wall_hide_shorts]" value="0"/>
		<input type="checkbox" id="sw-hide-shorts" name="swyi_options_wall[swyi_wall_hide_shorts]" value="1" <?php 
        checked( 1, $hideShorts, true );
        ?>/>
		<p>Choose to hide shorts from your YouTube Wall.</p>

		<?php 
    }

    public function swyi_showcase_controls_bg_colour_cb() {
        $controlsBgColour = ( isset( $this->optionsShowcase['swyi_showcase_controls_bg_colour'] ) ? $this->optionsShowcase['swyi_showcase_controls_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-showcase-controls-bg-colour" name="swyi_options_showcase[swyi_showcase_controls_bg_colour]" size='40' value="<?php 
        echo esc_html( $controlsBgColour );
        ?>" />

		<p>Choose the controls colour of the [Layout] Showcase.</p>

		<?php 
    }

    public function swyi_showcase_controls_arrow_colour_cb() {
        $controlsArrowColour = ( isset( $this->optionsShowcase['swyi_showcase_controls_arrow_colour'] ) ? $this->optionsShowcase['swyi_showcase_controls_arrow_colour'] : '' );
        ?>
		
		<input type="text" id="sw-showcase-controls-arrow-colour" name="swyi_options_showcase[swyi_showcase_controls_arrow_colour]" size='40' value="<?php 
        echo esc_html( $controlsArrowColour );
        ?>" />

		<p>Choose the arrow colour of the [Layout] Showcase.</p>

		<?php 
    }

    public function swyi_showcase_slide_count_cb() {
        $slideCount = ( isset( $this->optionsShowcase['swyi_showcase_slide_count'] ) ? $this->optionsShowcase['swyi_showcase_slide_count'] : '' );
        ?>
		
		<input type="text" id="sw-showcase-slide-count" name="swyi_options_showcase[swyi_showcase_slide_count]" size='10' placeholder="6" value="<?php 
        echo esc_html( $slideCount );
        ?>" />

		<p>Choose to override the number of shorts which display at once. Leave this blank to let the plugin handle it.</p>

		<?php 
    }

    public function swyi_feature_embed_position_cb() {
        $position = ( isset( $this->optionsFeature['swyi_feature_embed_position'] ) ? $this->optionsFeature['swyi_feature_embed_position'] : '' );
        ?>
		
		<select id="sw-player-stream-position" name="swyi_options_feature[swyi_feature_embed_position]">
			<option value="inside" <?php 
        echo selected( $position, 'inside', false );
        ?>>Inside</option>
            <option value="above" <?php 
        echo selected( $position, 'above', false );
        ?>>Above</option>
            <option value="below" <?php 
        echo selected( $position, 'below', false );
        ?>>Below</option>
        </select>
		<p>Choose the position of the embed in your [Layout] Feature.</p>

		<?php 
    }

    public function swyi_feature_controls_bg_colour_cb() {
        $controlsBgColour = ( isset( $this->optionsFeature['swyi_feature_controls_bg_colour'] ) ? $this->optionsFeature['swyi_feature_controls_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-feature-controls-bg-colour" name="swyi_options_feature[swyi_feature_controls_bg_colour]" size='40' value="<?php 
        echo esc_html( $controlsBgColour );
        ?>" />

		<p>Choose the controls colour of the [Layout] Feature.</p>

		<?php 
    }

    public function swyi_feature_controls_arrow_colour_cb() {
        $controlsArrowColour = ( isset( $this->optionsFeature['swyi_feature_controls_arrow_colour'] ) ? $this->optionsFeature['swyi_feature_controls_arrow_colour'] : '' );
        ?>
		
		<input type="text" id="sw-feature-controls-arrow-colour" name="swyi_options_feature[swyi_feature_controls_arrow_colour]" size='40' value="<?php 
        echo esc_html( $controlsArrowColour );
        ?>" />

		<p>Choose the arrow colour of the [Layout] Feature.</p>

		<?php 
    }

    public function swyi_feature_skew_cb() {
        $skew = ( isset( $this->optionsFeature['swyi_feature_skew'] ) ? $this->optionsFeature['swyi_feature_skew'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options_feature[swyi_feature_skew]" value="0"/>
		<input type="checkbox" id="sw-feature" name="swyi_options_feature[swyi_feature_skew]" value="1" <?php 
        checked( 1, $skew, true );
        ?>/>
		<p>Choose to add the 3D skew effect to the Feature layout.</p>

		<?php 
    }

    public function swyi_player_welcome_bg_colour() {
        $welcomeBgColour = ( isset( $this->optionsPlayer['swyi_player_welcome_bg_colour'] ) ? $this->optionsPlayer['swyi_player_welcome_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-bg-colour" name="swyi_options_player[swyi_player_welcome_bg_colour]" size='40' value="<?php 
        echo esc_html( $welcomeBgColour );
        ?>" />

		<p>Choose the background colour of the [Layout] Player.</p>

		<?php 
    }

    public function swyi_player_welcome_image_cb() {
        $welcomeImage = ( isset( $this->optionsPlayer['swyi_player_welcome_image'] ) ? $this->optionsPlayer['swyi_player_welcome_image'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-image" name="swyi_options_player[swyi_player_welcome_image]" size='40' value="<?php 
        echo esc_html( $welcomeImage );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a welcome background image of the [Layout] Player. Ideal image dimensions are 900 x 480.</p>

		<?php 
    }

    public function swyi_player_welcome_logo_cb() {
        $welcomeLogo = ( isset( $this->optionsPlayer['swyi_player_welcome_logo'] ) ? $this->optionsPlayer['swyi_player_welcome_logo'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-logo" name="swyi_options_player[swyi_player_welcome_logo]" size='40' value="<?php 
        echo esc_html( $welcomeLogo );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a welcome logo inside your [Layout] Player. Ideal image dimensions are 100 x 100.</p>

		<?php 
    }

    public function swyi_player_welcome_text_cb() {
        $welcomeText = ( isset( $this->optionsPlayer['swyi_player_welcome_text'] ) ? $this->optionsPlayer['swyi_player_welcome_text'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-text" name="swyi_options_player[swyi_player_welcome_text]" size='40' value="<?php 
        echo esc_html( $welcomeText );
        ?>" />

		<p>Choose the welcome text (line 1) of the [Layout] Player.</p>

		<?php 
    }

    public function swyi_player_welcome_text_2_cb() {
        $welcomeText2 = ( isset( $this->optionsPlayer['swyi_player_welcome_text_2'] ) ? $this->optionsPlayer['swyi_player_welcome_text_2'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-text-2" name="swyi_options_player[swyi_player_welcome_text_2]" size='40' value="<?php 
        echo esc_html( $welcomeText2 );
        ?>" />

		<p>Choose the welcome text (line 2) of the [Layout] Player.</p>

		<?php 
    }

    public function swyi_player_welcome_text_colour_cb() {
        $welcomeTextColour = ( isset( $this->optionsPlayer['swyi_player_welcome_text_colour'] ) ? $this->optionsPlayer['swyi_player_welcome_text_colour'] : '' );
        ?>
		
		<input type="text" id="sw-welcome-text-colour" name="swyi_options_player[swyi_player_welcome_text_colour]" size='40' value="<?php 
        echo esc_html( $welcomeTextColour );
        ?>" />

		<p>Choose the welcome text colour of the [Layout] Player.</p>

		<?php 
    }

    public function swyi_player_stream_position_cb() {
        $position = ( isset( $this->optionsPlayer['swyi_player_stream_position'] ) ? $this->optionsPlayer['swyi_player_stream_position'] : '' );
        ?>
		
		<select id="sw-player-stream-position" name="swyi_options_player[swyi_player_stream_position]">
            <option value="left" <?php 
        echo selected( $position, 'left', false );
        ?>>Left</option>
            <option value="right" <?php 
        echo selected( $position, 'right', false );
        ?>>Right</option>
			<option value="none" <?php 
        echo selected( $position, 'none', false );
        ?>>None</option>
        </select>
		<p>Choose the position of the list of streamers in your [Layout] Player.</p>

		<?php 
    }

    public function swyi_embed_cb() {
        $embed = ( isset( $this->options['swyi_embed'] ) ? $this->options['swyi_embed'] : '' );
        ?>
		
		<select id="sw-embed" name="swyi_options[swyi_embed]">
            <option value="page" <?php 
        echo selected( $embed, 'page', false );
        ?>>Embed on page</option>
            <option value="popup" <?php 
        echo selected( $embed, 'popup', false );
        ?>>Embed in a popup</option>
			<option value="youtube" <?php 
        echo selected( $embed, 'youtube', false );
        ?>>Link to YouTube</option>
        </select>
		<p>When users interact with your YouTube integration, you can choose how to display the embedded content.</p>

		<?php 
    }

    public function swyi_embed_muted_cb() {
        $muted = ( isset( $this->options['swyi_embed_muted'] ) ? $this->options['swyi_embed_muted'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options[swyi_embed_muted]" value="0"/>
		<input type="checkbox" id="sw-embed-muted" name="swyi_options[swyi_embed_muted]" value="1" <?php 
        checked( 1, $muted, true );
        ?>/>
		<p>Choose to start your embedded YouTube content muted.</p>

		<?php 
    }

    public function swyi_show_offline_cb() {
        $offline = ( isset( $this->options['swyi_show_offline'] ) ? $this->options['swyi_show_offline'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options[swyi_show_offline]" value="0"/>
		<input type="checkbox" id="sw-show-offline" name="swyi_options[swyi_show_offline]" value="1" <?php 
        checked( 1, $offline, true );
        ?>/>
		<p>Choose to show all streams, even if they're offline.</p>

		<?php 
    }

    public function swyi_show_offline_text_cb() {
        $offlineText = ( isset( $this->options['swyi_show_offline_text'] ) ? $this->options['swyi_show_offline_text'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-text" name="swyi_options[swyi_show_offline_text]" size='40' value="<?php 
        echo esc_html( $offlineText );
        ?>" />
		<p>Choose to display a custom message at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swyi_show_offline_image_cb() {
        $showOfflineImage = ( isset( $this->options['swyi_show_offline_image'] ) ? $this->options['swyi_show_offline_image'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-image" name="swyi_options[swyi_show_offline_image]" size='40' value="<?php 
        echo esc_html( $showOfflineImage );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a custom image at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swyi_autoload_cb() {
        $autoload = ( isset( $this->options['swyi_autoload'] ) ? $this->options['swyi_autoload'] : 0 );
        ?>
		
		<input type="hidden" name="swyi_options[swyi_autoload]" value="0"/>
		<input type="checkbox" id="sw-autoload" name="swyi_options[swyi_autoload]" value="1" <?php 
        checked( 1, $autoload, true );
        ?> />
		<p>Choose to automatically embed the top video.</p>


		<?php 
    }

    public function swyi_autoplay_cb() {
        $autoplay = ( isset( $this->options['swyi_autoplay'] ) ? $this->options['swyi_autoplay'] : 0 );
        ?>
		
		<input type="hidden" name="swyi_options[swyi_autoplay]" value="0"/>
		<input type="checkbox" id="sw-autoplay" name="swyi_options[swyi_autoplay]" value="1" <?php 
        checked( 1, $autoplay, true );
        ?> />
		<p>Choose to autoplay the embedded video.</p>


		<?php 
    }

    public function swyi_title_cb() {
        $title = ( isset( $this->options['swyi_title'] ) ? $this->options['swyi_title'] : '' );
        ?>
		
		<input type="text" id="sw-title" name="swyi_options[swyi_title]" size='40' value="<?php 
        echo esc_html( $title );
        ?>" />
		<p>Add your own title.</p>

		<?php 
    }

    public function swyi_subtitle_cb() {
        $subtitle = ( isset( $this->options['swyi_subtitle'] ) ? $this->options['swyi_subtitle'] : '' );
        ?>
		
		<input type="text" id="sw-subtitle" name="swyi_options[swyi_subtitle]" size='40' value="<?php 
        echo esc_html( $subtitle );
        ?>" />
		<p>Add your own subtitle.</p>

		<?php 
    }

    public function swyi_logo_image_cb() {
        $logo = ( isset( $this->options['swyi_logo_image'] ) ? $this->options['swyi_logo_image'] : '' );
        ?>
		
		<input type="text" id="sw-logo-image" name="swyi_options[swyi_logo_image]" size='40' value="<?php 
        echo esc_html( $logo );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Add your own logo. This should be a small square image, Ideal image dimensions are 80 x 80.</p>

		<?php 
    }

    public function swyi_logo_bg_colour_cb() {
        $logoBg = ( isset( $this->options['swyi_logo_bg_colour'] ) ? $this->options['swyi_logo_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-bg-colour" name="swyi_options[swyi_logo_bg_colour]" size='40' value="<?php 
        echo esc_html( $logoBg );
        ?>" />
		<p>Add a background colour for your logo.</p>

		<?php 
    }

    public function swyi_logo_border_colour_cb() {
        $logoBorder = ( isset( $this->options['swyi_logo_border_colour'] ) ? $this->options['swyi_logo_border_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-border-colour" name="swyi_options[swyi_logo_border_colour]" size='40' value="<?php 
        echo esc_html( $logoBorder );
        ?>" />
		<p>Add a border colour for your logo.</p>


		<?php 
    }

    public function swyi_max_width_cb() {
        $width = ( isset( $this->options['swyi_max_width'] ) ? $this->options['swyi_max_width'] : '' );
        ?>
		
		<select id="sw-max-width" name="swyi_options[swyi_max_width]">
            <option value="none" <?php 
        echo selected( $width, 'none', false );
        ?>>None</option>
            <option value="1920" <?php 
        echo selected( $width, '1920', false );
        ?>>1920px</option>
            <option value="1680" <?php 
        echo selected( $width, '1680', false );
        ?>>1680px</option>
            <option value="1440" <?php 
        echo selected( $width, '1440', false );
        ?>>1440px</option>
            <option value="1280" <?php 
        echo selected( $width, '1280', false );
        ?>>1280px</option>
            <option value="1024" <?php 
        echo selected( $width, '1024', false );
        ?>>1024px</option>
            <option value="768" <?php 
        echo selected( $width, '768', false );
        ?>>768px</option>
        </select>
		<p>Add a max width to your YouTube integration.</p>


		<?php 
    }

    public function swyi_tile_layout_cb() {
        $layout = ( isset( $this->options['swyi_tile_layout'] ) ? $this->options['swyi_tile_layout'] : '' );
        ?>
		
		<select id="sw-tile-layout" name="swyi_options[swyi_tile_layout]">
            <option value="detailed" <?php 
        echo selected( $layout, 'detailed', false );
        ?>>Detailed</option>
            <option value="compact" <?php 
        echo selected( $layout, 'compact', false );
        ?>>Compact</option>
        </select>
		<p>Choose the layout mode for your YouTube video tiles.</p>

		<?php 
    }

    public function swyi_tile_sorting_cb() {
        $sorting = ( isset( $this->options['swyi_tile_sorting'] ) ? $this->options['swyi_tile_sorting'] : '' );
        ?>
		
		<select id="sw-tile-sorting" name="swyi_options[swyi_tile_sorting]">
			<option value="most" <?php 
        echo selected( $sorting, 'most', false );
        ?>>Most Recent</option>
			<option value="least" <?php 
        echo selected( $sorting, 'least', false );
        ?>>Least Recent</option>
			<option value="alpha" <?php 
        echo selected( $sorting, 'alpha', false );
        ?>>Alphabetical</option>
			<option value="random" <?php 
        echo selected( $sorting, 'random', false );
        ?>>Random</option>
        </select>
		<p>Choose the sorting of the YouTube video tiles.</p>

		<?php 
    }

    public function swyi_tile_bg_colour_cb() {
        $bgColour = ( isset( $this->options['swyi_tile_bg_colour'] ) ? $this->options['swyi_tile_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-bg-colour" name="swyi_options[swyi_tile_bg_colour]" size='40' value="<?php 
        echo esc_html( $bgColour );
        ?>" />
		<p>Change the background colour for your YouTube video tiles.</p>


		<?php 
    }

    public function swyi_tile_title_colour_cb() {
        $titleColour = ( isset( $this->options['swyi_tile_title_colour'] ) ? $this->options['swyi_tile_title_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-title-colour" name="swyi_options[swyi_tile_title_colour]" size='40' value="<?php 
        echo esc_html( $titleColour );
        ?>" />
		<p>Change the title colour for your YouTube video tiles.</p>

		<?php 
    }

    public function swyi_tile_subtitle_colour_cb() {
        $subtitleColour = ( isset( $this->options['swyi_tile_subtitle_colour'] ) ? $this->options['swyi_tile_subtitle_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-subtitle-colour" name="swyi_options[swyi_tile_subtitle_colour]" size='40' value="<?php 
        echo esc_html( $subtitleColour );
        ?>" />
		<p>Change the subtitle colour for your YouTube video tiles.</p>


		<?php 
    }

    public function swyi_tile_rounded_corners_cb() {
        $roundedCorners = ( isset( $this->options['swyi_tile_rounded_corners'] ) ? $this->options['swyi_tile_rounded_corners'] : '5' );
        ?>

		<input id="sw-tile-rounded-corners" type="text" name="swyi_options[swyi_tile_rounded_corners]" value="<?php 
        echo esc_html( $roundedCorners );
        ?>">
		<span class="range-bar-value"></span>
		<p>Add rounded corners to your YouTube video tiles.</p>


		<?php 
    }

    public function swyi_hover_effect_cb() {
        $hoverEffect = ( isset( $this->options['swyi_hover_effect'] ) ? $this->options['swyi_hover_effect'] : '' );
        ?>
		
		<select id="sw-hover-effect" name="swyi_options[swyi_hover_effect]">
            <option value="none" <?php 
        echo selected( $hoverEffect, 'none', false );
        ?>>none</option>
            <option value="YouTube" <?php 
        echo selected( $hoverEffect, 'YouTube', false );
        ?>>YouTube Style</option>
			<option value="play" <?php 
        echo selected( $hoverEffect, 'play', false );
        ?>>Play Button</option>
        </select>
		<p>Change the hover effect for your YouTube video tiles.</p>


		<?php 
    }

    public function swyi_hover_colour_cb() {
        $hoverColour = ( isset( $this->options['swyi_hover_colour'] ) ? $this->options['swyi_hover_colour'] : '' );
        ?>
		
		<input type="text" id="sw-hover-colour" name="swyi_options[swyi_hover_colour]" size='40' value="<?php 
        echo esc_html( $hoverColour );
        ?>" />
		<p>Change the hover colour for your YouTube video tiles.</p>

		<?php 
    }

    public function swyi_enable_cache_cb() {
        $enableCache = ( isset( $this->options['swyi_enable_cache'] ) ? $this->options['swyi_enable_cache'] : 0 );
        ?>
		
		<input type="hidden" name="swyi_options[swyi_enable_cache]" value="0"/>
		<input type="checkbox" id="sw-enable-cache" name="swyi_options[swyi_enable_cache]" value="1" <?php 
        checked( 1, $enableCache, true );
        ?> />

		<?php 
    }

    public function swyi_cache_channel_cb() {
        $cacheData = ( get_transient( 'swyi_video_cache' ) ? get_transient( 'swyi_video_cache' ) : '' );
        $channel_id = ( isset( $this->options['swyi_channel_id'] ) ? $this->options['swyi_channel_id'] : '' );
        $playlist = ( isset( $this->options['swyi_playlist'] ) ? $this->options['swyi_playlist'] : '' );
        $enableCache = ( isset( $this->options['swyi_enable_cache'] ) ? $this->options['swyi_enable_cache'] : 0 );
        $data = $cacheData ?? [];
        $textareaContent = "";
        $channelID = 'No cache data found.';
        if ( $enableCache && !$data && $channel_id == '' && $playlist == '' ) {
            $channelID = 'Add a Channel ID or Playlist to enable caching';
        }
        $totalVideos = count( $data['items'] ?? [] );
        if ( $data && $totalVideos > 0 ) {
            foreach ( $data['items'] as $index => $video ) {
                $title = $video['snippet']['title'];
                $publishedDate = date( "F j, Y, g:i a", strtotime( $video['snippet']['publishedAt'] ) );
                $textareaContent .= "Video " . ($index + 1) . ":\n";
                $textareaContent .= "Title: {$title}\n";
                $textareaContent .= "Published Date: {$publishedDate}\n\n";
            }
            $channelID = $data['channelID'];
        }
        ?>
		
		<div>
			<input type="text" id="sw-cache-channel" disabled name="" size='40' value="<?php 
        echo esc_html( $channelID );
        ?>" />
		</div>
		<?php 
        if ( $data && $totalVideos > 0 ) {
            ?>
			<br>
			<div>
				<input type="text" id="sw-cache-channel" disabled name="" size='40' value="Total Videos: <?php 
            echo esc_html( $totalVideos );
            ?>" />
			</div>
			<br>
			<div>
				<textarea rows="10" cols="60" readonly><?php 
            echo esc_textarea( $textareaContent );
            ?></textarea>
			</div>
		<?php 
        }
    }

    public function swyi_cache_content_cb() {
        $token = ( isset( $this->options['swti_api_access_token'] ) ? $this->options['swti_api_access_token'] : '' );
        ?>
		
		<input type="text" disabled id="sw-client-token" name="" size='40' value="<?php 
        echo esc_html( $token );
        ?>" />

		<input type="hidden" id="sw-refresh-token" name="swti_options[swti_refresh_token]" value="0" />
		<?php 
        submit_button(
            'Refresh Token',
            'delete button-secondary',
            'sw-refresh-token-submit',
            false,
            array(
                'style' => '',
            )
        );
        ?>

		<?php 
    }

    public function swyi_status_show_global_cb() {
        $showGlobal = ( isset( $this->optionsStatus['swyi_status_show_global'] ) ? $this->optionsStatus['swyi_status_show_global'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options_status[swyi_status_show_global]" value="0"/>
		<input type="checkbox" id="sw-show-global" name="swyi_options_status[swyi_status_show_global]" value="1" <?php 
        checked( 1, $showGlobal, true );
        ?>/>
		<p>Choose to display Stream Status on every page, without the use of a shortcode.</p>
		<p>When this is set, Stream Status will display the livestreams configured in the YouTube Integration <a href="/wp-admin/admin.php?page=streamweasels-youtube">settings page</a>.</p>

		<?php 
    }

    public function swyi_status_hide_when_offline_cb() {
        $hideWhenOffline = ( isset( $this->optionsStatus['swyi_status_hide_when_offline'] ) ? $this->optionsStatus['swyi_status_hide_when_offline'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options_status[swyi_status_hide_when_offline]" value="0"/>
		<input type="checkbox" id="sw-hide-offline" name="swyi_options_status[swyi_status_hide_when_offline]" value="1" <?php 
        checked( 1, $hideWhenOffline, true );
        ?>/>
		<p>Choose to hide the Stream Status entirely if no user is online.</p>

		<?php 
    }

    public function swyi_status_placement_cb() {
        $placement = ( isset( $this->optionsStatus['swyi_status_placement'] ) ? $this->optionsStatus['swyi_status_placement'] : 'top' );
        ?>
		
		<select id="sw-placement" name="swyi_options_status[swyi_status_placement]">
			<option value="absolute" <?php 
        echo selected( $placement, 'absolute', false );
        ?>>Absolute</option>	
            <option value="static" <?php 
        echo selected( $placement, 'static', false );
        ?>>Static</option>
        </select>
		<p>Choose if you want your Status to appear in the corner of the window (Absolute) or stay where it is (Static). Static only works when Stream Status is placed with a block or shortcode.</p>

		<?php 
    }

    public function swyi_status_vertical_placement_cb() {
        $verticalPlacement = ( isset( $this->optionsStatus['swyi_status_vertical_placement'] ) ? $this->optionsStatus['swyi_status_vertical_placement'] : 'top' );
        ?>
		
		<select id="sw-vertical-placement" name="swyi_options_status[swyi_status_vertical_placement]">
			<option value="top" <?php 
        echo selected( $verticalPlacement, 'top', false );
        ?>>Top</option>	
            <option value="bottom" <?php 
        echo selected( $verticalPlacement, 'bottom', false );
        ?>>Bottom</option>
        </select>
		<p>Choose where you want your Stream Status to display.</p>

		<?php 
    }

    public function swyi_status_horizontal_placement_cb() {
        $horizontalPlacement = ( isset( $this->optionsStatus['swyi_status_horizontal_placement'] ) ? $this->optionsStatus['swyi_status_horizontal_placement'] : 'left' );
        ?>
		
		<select id="sw-horizontal-placement" name="swyi_options_status[swyi_status_horizontal_placement]">
			<option value="left" <?php 
        echo selected( $horizontalPlacement, 'left', false );
        ?>>Left</option>	
            <option value="right" <?php 
        echo selected( $horizontalPlacement, 'right', false );
        ?>>Right</option>
        </select>	
		<p>Choose where you want your Stream Status to display.</p>

		<?php 
    }

    public function swyi_status_vertical_distance_cb() {
        $verticalDistance = ( isset( $this->optionsStatus['swyi_status_vertical_distance'] ) ? $this->optionsStatus['swyi_status_vertical_distance'] : '' );
        ?>
		
		<input type="text" id="sw-vertical-distance" name="swyi_options_status[swyi_status_vertical_distance]" size='40' placeholder="25" value="<?php 
        echo esc_html( $verticalDistance );
        ?>" />
		<p>Choose the distance (in pixels) from the top/bottom. Defaults to 25.</p>

		<?php 
    }

    public function swyi_status_horizontal_distance_cb() {
        $horizontalDistance = ( isset( $this->optionsStatus['swyi_status_horizontal_distance'] ) ? $this->optionsStatus['swyi_status_horizontal_distance'] : '' );
        ?>
		
		<input type="text" id="sw-horizontal-distance" name="swyi_options_status[swyi_status_horizontal_distance]" size='40' placeholder="25" value="<?php 
        echo esc_html( $horizontalDistance );
        ?>" />
		<p>Choose the distance (in pixels) from the top/bottom. Defaults to 25.</p>

		<?php 
    }

    public function swyi_status_custom_logo_cb() {
        $customLogo = ( isset( $this->optionsStatus['swyi_status_custom_logo'] ) ? $this->optionsStatus['swyi_status_custom_logo'] : '' );
        ?>
		
		<input type="text" id="sw-custom-logo" name="swyi_options_status[swyi_status_custom_logo]" size='40' value="<?php 
        echo esc_html( $customLogo );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose a custom logo, to replace the YouTube logo in the Stream Status.</p>

		<?php 
    }

    public function swyi_status_logo_background_colour_cb() {
        $logoBackgroundColour = ( isset( $this->optionsStatus['swyi_status_logo_background_colour'] ) ? $this->optionsStatus['swyi_status_logo_background_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-background-colour" name="swyi_options_status[swyi_status_logo_background_colour]" size='40' value="<?php 
        echo esc_html( $logoBackgroundColour );
        ?>" />
		<p>Change the colour of the background of the logo box.</p>

		<?php 
    }

    public function swyi_status_accent_colour_cb() {
        $accentColour = ( isset( $this->optionsStatus['swyi_status_accent_colour'] ) ? $this->optionsStatus['swyi_status_accent_colour'] : '' );
        ?>
		
		<input type="text" id="sw-accent-colour" name="swyi_options_status[swyi_status_accent_colour]" size='40' value="<?php 
        echo esc_html( $accentColour );
        ?>" />
		<p>Change the accent colour of the Stream Status.</p>

		<?php 
    }

    public function swyi_status_carousel_background_colour_cb() {
        $carouselBackgroundColour = ( isset( $this->optionsStatus['swyi_status_carousel_background_colour'] ) ? $this->optionsStatus['swyi_status_carousel_background_colour'] : '' );
        ?>
		
		<input type="text" id="sw-carousel-background-colour" name="swyi_options_status[swyi_status_carousel_background_colour]" size='40' value="<?php 
        echo esc_html( $carouselBackgroundColour );
        ?>" />
		<p>Change the background colour of the carousel controls.</p>

		<?php 
    }

    public function swyi_status_carousel_arrow_colour_cb() {
        $carouselArrowColour = ( isset( $this->optionsStatus['swyi_status_carousel_arrow_colour'] ) ? $this->optionsStatus['swyi_status_carousel_arrow_colour'] : '' );
        ?>
		
		<input type="text" id="sw-carousel-arrow-colour" name="swyi_options_status[swyi_status_carousel_arrow_colour]" size='40' value="<?php 
        echo esc_html( $carouselArrowColour );
        ?>" />
		<p>Change the arrow colour of the carousel controls.</p>

		<?php 
    }

    public function swyi_status_disable_carousel_cb() {
        $disableCarousel = ( isset( $this->optionsStatus['swyi_status_disable_carousel'] ) ? $this->optionsStatus['swyi_status_disable_carousel'] : '' );
        ?>
		
		<input type="hidden" name="swyi_options_status[swyi_status_disable_carousel]" value="0"/>
		<input type="checkbox" id="sw-disable-carousel" name="swyi_options_status[swyi_status_disable_carousel]" value="1" <?php 
        checked( 1, $disableCarousel, true );
        ?>/>
		<p>Choose to disable carousel functionality. This will remove the left/right arrows and make Stream Status only ever display one stream.</p>

		<?php 
    }

    /**
     * Shortcode Settings
     *
     */
    public function swyi_translations_live_cb() {
        $live = ( isset( $this->translations['swyi_translations_live'] ) ? $this->translations['swyi_translations_live'] : '' );
        ?>
		
		<input type="text" id="sw-translations-live" name="swyi_translations[swyi_translations_live]" size='40' placeholder="LIVE" value="<?php 
        echo esc_html( $live );
        ?>" />
		<?php 
    }

    public function swyi_translations_next_page_cb() {
        $next = ( isset( $this->translations['swyi_translations_next_page'] ) ? $this->translations['swyi_translations_next_page'] : '' );
        ?>
		
		<input type="text" id="sw-translations-next" name="swyi_translations[swyi_translations_next_page]" size='40' placeholder="Next page" value="<?php 
        echo esc_html( $next );
        ?>" />
		<?php 
    }

    public function swyi_translations_views_cb() {
        $viewers = ( isset( $this->translations['swyi_translations_views'] ) ? $this->translations['swyi_translations_views'] : '' );
        ?>
		
		<input type="text" id="sw-translations-viewers" name="swyi_translations[swyi_translations_views]" size='40' placeholder="views" value="<?php 
        echo esc_html( $viewers );
        ?>" />
		<?php 
    }

    public function swyi_translations_prev_page_cb() {
        $previous = ( isset( $this->translations['swyi_translations_prev_page'] ) ? $this->translations['swyi_translations_prev_page'] : '' );
        ?>
		
		<input type="text" id="sw-translations-previous" name="swyi_translations[swyi_translations_prev_page]" size='40' placeholder="Previous page" value="<?php 
        echo esc_html( $previous );
        ?>" />
		<?php 
    }

    /**
     * Debug Settings
     *
     */
    public function swyi_debug_cb() {
        $dismissForGood5 = ( isset( $this->options['swyi_dismiss_for_good5'] ) ? $this->options['swyi_dismiss_for_good5'] : 0 );
        ?>
		
		<p>
			<textarea rows="6" style="width: 100%;"><?php 
        echo esc_textarea( get_option( 'swyi_debug_log', '' ) );
        ?></textarea>
		</p>
		<p>
			<input type="hidden" id="sw-delete-log" name="swyi_options[swyi_delete_log]" value="0" />
			<input type="hidden" id="sw-dismiss-for-good5" name="swyi_options[swyi_dismiss_for_good5]" value="<?php 
        echo esc_html( $dismissForGood5 );
        ?>" />
			<?php 
        submit_button(
            'Clear logs',
            'delete button-secondary',
            'sw-delete-log-submit',
            false
        );
        ?>
		</p>

		<?php 
    }

    /**
     * Field Validation
     *
     */
    public function swyi_options_validate( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options' );
        if ( !empty( $input['swyi_api_key'] ) ) {
            $new_input['swyi_api_key'] = sanitize_text_field( $input['swyi_api_key'] );
            $SWYI_YouTube_API = new SWYI_YouTube_API();
            $result = $SWYI_YouTube_API->check_token( $input['swyi_api_key'] );
            $new_input['swyi_api_key_code'] = $result;
        }
        if ( !empty( $input['swyi_channel_id'] ) ) {
            $new_input['swyi_channel_id'] = sanitize_text_field( $input['swyi_channel_id'] );
            $SWYI_YouTube_API = new SWYI_YouTube_API();
            $result = $SWYI_YouTube_API->check_channels( $input['swyi_api_key'], $input['swyi_channel_id'] );
            $new_input['swyi_channel_id_code'] = $result;
        }
        if ( !empty( $input['swyi_playlist_id'] ) ) {
            $new_input['swyi_playlist_id'] = sanitize_text_field( $input['swyi_playlist_id'] );
            $SWYI_YouTube_API = new SWYI_YouTube_API();
            $result = $SWYI_YouTube_API->check_playlist( $input['swyi_api_key'], $input['swyi_playlist_id'] );
            $new_input['swyi_playlist_id_code'] = $result;
        }
        if ( !empty( $input['swyi_livestream_id'] ) ) {
            $new_input['swyi_livestream_id'] = sanitize_text_field( $input['swyi_livestream_id'] );
            $SWYI_YouTube_API = new SWYI_YouTube_API();
            $result = $SWYI_YouTube_API->check_channels( $input['swyi_api_key'], $input['swyi_livestream_id'] );
            $new_input['swyi_livestream_id_code'] = $result;
        }
        if ( isset( $input['swyi_colour_theme'] ) ) {
            $new_input['swyi_colour_theme'] = sanitize_text_field( $input['swyi_colour_theme'] );
        }
        if ( !empty( $input['swyi_limit'] ) ) {
            $new_input['swyi_limit'] = sanitize_text_field( $input['swyi_limit'] );
            if ( $new_input['swyi_limit'] > 50 ) {
                $new_input['swyi_limit'] = 50;
            }
        } else {
            $new_input['swyi_limit'] = 16;
        }
        if ( isset( $input['swyi_pagination'] ) ) {
            if ( syi_fs()->can_use_premium_code() ) {
                $new_input['swyi_pagination'] = sanitize_text_field( $input['swyi_pagination'] );
            } else {
                $new_input['swyi_pagination'] = 0;
            }
        }
        if ( isset( $input['swyi_layout'] ) ) {
            $new_input['swyi_layout'] = sanitize_text_field( $input['swyi_layout'] );
        }
        if ( isset( $input['swyi_dismiss_for_good5'] ) ) {
            $new_input['swyi_dismiss_for_good5'] = (int) $input['swyi_dismiss_for_good5'];
        }
        if ( isset( $input['swyi_delete_log'] ) && $input['swyi_delete_log'] == 1 ) {
            $new_input['swyi_dismiss_for_good5'] = 0;
            delete_option( 'swyi_debug_log' );
        }
        return $new_input;
    }

    public function swyi_options_validate_wall( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options_wall' );
        if ( isset( $input['swyi_wall_column_count'] ) ) {
            $new_input['swyi_wall_column_count'] = sanitize_text_field( $input['swyi_wall_column_count'] );
        }
        if ( isset( $input['swyi_wall_column_spacing'] ) ) {
            $new_input['swyi_wall_column_spacing'] = sanitize_text_field( $input['swyi_wall_column_spacing'] );
        }
        if ( isset( $input['swyi_wall_hide_shorts'] ) ) {
            $new_input['swyi_wall_hide_shorts'] = (int) $input['swyi_wall_hide_shorts'];
        }
        return $new_input;
    }

    public function swyi_options_validate_showcase( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options_showcase' );
        if ( isset( $input['swyi_showcase_controls_bg_colour'] ) ) {
            $new_input['swyi_showcase_controls_bg_colour'] = sanitize_text_field( $input['swyi_showcase_controls_bg_colour'] );
        }
        if ( isset( $input['swyi_showcase_controls_arrow_colour'] ) ) {
            $new_input['swyi_showcase_controls_arrow_colour'] = sanitize_text_field( $input['swyi_showcase_controls_arrow_colour'] );
        }
        if ( isset( $input['swyi_showcase_slide_count'] ) ) {
            $new_input['swyi_showcase_slide_count'] = sanitize_text_field( $input['swyi_showcase_slide_count'] );
        }
        return $new_input;
    }

    public function swyi_options_validate_feature( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options_feature' );
        return $new_input;
    }

    public function swyi_options_validate_player( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options_player' );
        if ( isset( $input['swyi_player_welcome_bg_colour'] ) ) {
            $new_input['swyi_player_welcome_bg_colour'] = sanitize_text_field( $input['swyi_player_welcome_bg_colour'] );
        }
        if ( isset( $input['swyi_player_welcome_logo'] ) ) {
            $new_input['swyi_player_welcome_logo'] = sanitize_text_field( $input['swyi_player_welcome_logo'] );
        }
        if ( isset( $input['swyi_player_welcome_image'] ) ) {
            $new_input['swyi_player_welcome_image'] = sanitize_text_field( $input['swyi_player_welcome_image'] );
        }
        if ( isset( $input['swyi_player_welcome_text'] ) ) {
            $new_input['swyi_player_welcome_text'] = sanitize_text_field( $input['swyi_player_welcome_text'] );
        }
        if ( isset( $input['swyi_player_welcome_text_2'] ) ) {
            $new_input['swyi_player_welcome_text_2'] = sanitize_text_field( $input['swyi_player_welcome_text_2'] );
        }
        if ( isset( $input['swyi_player_welcome_text_colour'] ) ) {
            $new_input['swyi_player_welcome_text_colour'] = sanitize_text_field( $input['swyi_player_welcome_text_colour'] );
        }
        if ( isset( $input['swyi_player_stream_position'] ) ) {
            $new_input['swyi_player_stream_position'] = sanitize_text_field( $input['swyi_player_stream_position'] );
        }
        return $new_input;
    }

    public function swyi_options_validate_status( $input ) {
        $new_input = [];
        $options = get_option( 'swyi_options_status' );
        if ( isset( $input['swyi_status_show_global'] ) ) {
            $new_input['swyi_status_show_global'] = (int) $input['swyi_status_show_global'];
        }
        if ( isset( $input['swyi_status_hide_when_offline'] ) ) {
            $new_input['swyi_status_hide_when_offline'] = (int) $input['swyi_status_hide_when_offline'];
        }
        if ( isset( $input['swyi_status_vertical_placement'] ) ) {
            $new_input['swyi_status_vertical_placement'] = sanitize_text_field( $input['swyi_status_vertical_placement'] );
        }
        if ( isset( $input['swyi_status_placement'] ) ) {
            $new_input['swyi_status_placement'] = sanitize_text_field( $input['swyi_status_placement'] );
        }
        if ( isset( $input['swyi_status_horizontal_placement'] ) ) {
            $new_input['swyi_status_horizontal_placement'] = sanitize_text_field( $input['swyi_status_horizontal_placement'] );
        }
        if ( isset( $input['swyi_status_vertical_distance'] ) ) {
            $new_input['swyi_status_vertical_distance'] = sanitize_text_field( $input['swyi_status_vertical_distance'] );
        }
        if ( isset( $input['swyi_status_horizontal_distance'] ) ) {
            $new_input['swyi_status_horizontal_distance'] = sanitize_text_field( $input['swyi_status_horizontal_distance'] );
        }
        if ( isset( $input['swyi_status_custom_logo'] ) ) {
            $new_input['swyi_status_custom_logo'] = sanitize_text_field( $input['swyi_status_custom_logo'] );
        }
        if ( isset( $input['swyi_status_logo_background_colour'] ) ) {
            $new_input['swyi_status_logo_background_colour'] = sanitize_text_field( $input['swyi_status_logo_background_colour'] );
        }
        if ( isset( $input['swyi_status_accent_colour'] ) ) {
            $new_input['swyi_status_accent_colour'] = sanitize_text_field( $input['swyi_status_accent_colour'] );
        }
        if ( isset( $input['swyi_status_carousel_background_colour'] ) ) {
            $new_input['swyi_status_carousel_background_colour'] = sanitize_text_field( $input['swyi_status_carousel_background_colour'] );
        }
        if ( isset( $input['swyi_status_carousel_arrow_colour'] ) ) {
            $new_input['swyi_status_carousel_arrow_colour'] = sanitize_text_field( $input['swyi_status_carousel_arrow_colour'] );
        }
        if ( isset( $input['swyi_status_enable_classic'] ) ) {
            $new_input['swyi_status_enable_classic'] = (int) $input['swyi_status_enable_classic'];
        }
        if ( isset( $input['swyi_status_disable_carousel'] ) ) {
            $new_input['swyi_status_disable_carousel'] = (int) $input['swyi_status_disable_carousel'];
        }
        if ( isset( $input['swyi_status_classic_online_text'] ) ) {
            $new_input['swyi_status_classic_online_text'] = sanitize_text_field( $input['swyi_status_classic_online_text'] );
        }
        if ( isset( $input['swyi_status_classic_offline_text'] ) ) {
            $new_input['swyi_status_classic_offline_text'] = sanitize_text_field( $input['swyi_status_classic_offline_text'] );
        }
        return $new_input;
    }

    /**
     * Field Validation
     *
     */
    public function swyi_translations_validate( $input ) {
        // Translation Settings
        if ( isset( $input['swyi_translations_live'] ) && !empty( $input['swyi_translations_live'] ) ) {
            $new_input['swyi_translations_live'] = $input['swyi_translations_live'];
        } else {
            $new_input['swyi_translations_live'] = 'LIVE';
        }
        if ( isset( $input['swyi_translations_next_page'] ) && !empty( $input['swyi_translations_next_page'] ) ) {
            $new_input['swyi_translations_next_page'] = $input['swyi_translations_next_page'];
        } else {
            $new_input['swyi_translations_next_page'] = 'Next page';
        }
        if ( isset( $input['swyi_translations_views'] ) && !empty( $input['swyi_translations_views'] ) ) {
            $new_input['swyi_translations_views'] = $input['swyi_translations_views'];
        } else {
            $new_input['swyi_translations_views'] = 'views';
        }
        if ( isset( $input['swyi_translations_prev_page'] ) && !empty( $input['swyi_translations_prev_page'] ) ) {
            $new_input['swyi_translations_prev_page'] = $input['swyi_translations_prev_page'];
        } else {
            $new_input['swyi_translations_prev_page'] = 'Previous page';
        }
        return $new_input;
    }

    function swyi_get_options() {
        return get_option( 'swyi_options', array() );
    }

    function swyi_get_options_wall() {
        return get_option( 'swyi_options_wall', array() );
    }

    function swyi_get_options_feature() {
        return get_option( 'swyi_options_feature', array() );
    }

    function swyi_get_options_showcase() {
        return get_option( 'swyi_options_showcase', array() );
    }

    function swyi_get_options_player() {
        return get_option( 'swyi_options_player', array() );
    }

    function swyi_get_options_status() {
        return get_option( 'swyi_options_status', array() );
    }

    function swyi_get_translations() {
        return get_option( 'swyi_translations', array() );
    }

    function swyi_youtube_debug_log( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

    function swyi_youtube_debug_field( $message ) {
        if ( is_array( $message ) ) {
            $message = print_r( $message, true );
        }
        $log = get_option( 'swyi_debug_log', '' );
        $string = date( 'd.m.Y H:i:s' ) . " : " . $message . "\n";
        $log .= $string;
        // Limit the log to the last 100 lines to prevent it from growing too large.
        $log_lines = explode( "\n", $log );
        if ( count( $log_lines ) > 100 ) {
            $log_lines = array_slice( $log_lines, -100, 100 );
        }
        $log = implode( "\n", $log_lines );
        update_option( 'swyi_debug_log', $log );
    }

    function swyi_do_settings_sections(
        $page,
        $icon,
        $desc,
        $status
    ) {
        global $wp_settings_sections, $wp_settings_fields;
        if ( !isset( $wp_settings_sections[$page] ) ) {
            return;
        }
        $allowed_html = [
            'h3'     => [
                'class' => [],
            ],
            'span'   => [
                'class' => [],
            ],
            'p'      => [],
            'table'  => [
                'class' => [],
            ],
            'div'    => [
                'class' => [],
            ],
            'a'      => [
                'href'   => [],
                'target' => [],
                'type'   => [],
                'class'  => [],
            ],
            'button' => [
                'class' => [],
            ],
        ];
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            $premium_status = ( syi_fs()->can_use_premium_code__premium_only() ? 'free' : $status );
            $title = ( !empty( $section['title'] ) ? "<h3 class='hndle'><span class='dashicons {$icon}'></span>{$section['title']}</h3>" : '' );
            $description = ( $desc ? "<p>{$desc}</p>" : '' );
            echo '<div class="postbox postbox-' . esc_attr( str_replace( ' ', '-', strtolower( $section['title'] ) ) ) . ' postbox-' . esc_attr( $premium_status ) . '">';
            echo wp_kses( $title, $allowed_html );
            echo '<div class="inside">';
            echo wp_kses( $description, $allowed_html );
            if ( !empty( $section['callback'] ) ) {
                call_user_func( $section['callback'], $section );
            }
            echo '<table class="form-table">';
            do_settings_fields( $page, $section['id'] );
            echo '</table>';
            if ( $section['title'] !== 'Shortcode' ) {
                submit_button();
            }
            if ( !syi_fs()->is__premium_only() || syi_fs()->is_free_plan() ) {
                if ( $premium_status == 'pro' ) {
                    echo '<div class="postbox-trial-wrapper"><a href="admin.php?page=streamweasels-youtube-pricing" target="_blank" type="button" class="button button-primary">Buy Now</a></div>';
                }
            }
            echo '</div></div>';
        }
    }

}
