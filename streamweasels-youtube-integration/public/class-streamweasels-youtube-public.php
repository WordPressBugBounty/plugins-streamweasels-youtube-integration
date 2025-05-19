<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.streamweasels.com/
 * @since      1.0.0
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/public
 */
class Streamweasels_Youtube_Public {
    private $plugin_name;

    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        if ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ) {
            wp_enqueue_style(
                $this->plugin_name . '-flipster',
                plugin_dir_url( __FILE__ ) . 'dist/flipster.min.css',
                array(),
                $this->version,
                'all'
            );
        }
        wp_enqueue_style(
            $this->plugin_name . '-slick',
            plugin_dir_url( __FILE__ ) . 'dist/slick.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-youtube-public.min.css',
            array(),
            $this->version,
            'all'
        );
        // The following options are used as CSS variables on the page
        $options = get_option( 'swyi_options' );
        $optionsWall = get_option( 'swyi_options_wall' );
        $optionsFeature = get_option( 'swyi_options_feature' );
        $optionsShowcase = get_option( 'swyi_options_showcase' );
        $optionsStatus = get_option( 'swyi_options_status' );
        $colourTheme = sanitize_text_field( ( isset( $options['swyi_colour_theme'] ) && !empty( $options['swyi_colour_theme'] ) ? $options['swyi_colour_theme'] : 'light' ) );
        if ( $colourTheme == 'light' ) {
            $tileBgColourDefault = '#F7F7F8';
            $tileTitleColourDefault = '#1F1F23';
            $tileSubtitleColourDefault = '#53535F';
        } else {
            $tileBgColourDefault = '#0E0E10';
            $tileTitleColourDefault = '#DEDEE3';
            $tileSubtitleColourDefault = '#adb8a8';
        }
        $tileColumnCount = sanitize_text_field( ( isset( $optionsWall['swyi_wall_column_count'] ) && !empty( $optionsWall['swyi_wall_column_count'] ) ? $optionsWall['swyi_wall_column_count'] : '4' ) );
        $tileColumnSpacing = sanitize_text_field( ( isset( $optionsWall['swyi_wall_column_spacing'] ) && !empty( $optionsWall['swyi_wall_column_spacing'] ) ? $optionsWall['swyi_wall_column_spacing'] : '10' ) );
        $maxWidth = sanitize_text_field( ( isset( $options['swyi_max_width'] ) && !empty( $options['swyi_max_width'] ) ? $options['swyi_max_width'] : 'none' ) );
        $tileBgColour = sanitize_text_field( ( isset( $options['swyi_tile_bg_colour'] ) && !empty( $options['swyi_tile_bg_colour'] ) ? $options['swyi_tile_bg_colour'] : $tileBgColourDefault ) );
        $tileTitleColour = sanitize_text_field( ( isset( $options['swyi_tile_title_colour'] ) && !empty( $options['swyi_tile_title_colour'] ) ? $options['swyi_tile_title_colour'] : $tileTitleColourDefault ) );
        $tileSubtitleColour = sanitize_text_field( ( isset( $options['swyi_tile_subtitle_colour'] ) && !empty( $options['swyi_tile_subtitle_colour'] ) ? $options['swyi_tile_subtitle_colour'] : $tileSubtitleColourDefault ) );
        $tileRoundedCorners = sanitize_text_field( ( isset( $options['swyi_tile_rounded_corners'] ) && !empty( $options['swyi_tile_rounded_corners'] ) ? $options['swyi_tile_rounded_corners'] : '0' ) );
        $tileHoverColour = sanitize_text_field( ( isset( $options['swyi_hover_colour'] ) && !empty( $options['swyi_hover_colour'] ) ? $options['swyi_hover_colour'] : '#FF0000' ) );
        $logoBgColour = sanitize_text_field( ( isset( $options['swyi_logo_bg_colour'] ) && !empty( $options['swyi_logo_bg_colour'] ) ? $options['swyi_logo_bg_colour'] : '#fff' ) );
        $logoBorderColour = sanitize_text_field( ( isset( $options['swyi_logo_border_colour'] ) && !empty( $options['swyi_logo_border_colour'] ) ? $options['swyi_logo_border_colour'] : '#fff' ) );
        $controlsBgColour = sanitize_text_field( ( isset( $optionsFeature['swyi_feature_controls_bg_colour'] ) && !empty( $optionsFeature['swyi_feature_controls_bg_colour'] ) ? $optionsFeature['swyi_feature_controls_bg_colour'] : '#000' ) );
        $controlsArrowColour = sanitize_text_field( ( isset( $optionsFeature['swyi_feature_controls_arrow_colour'] ) && !empty( $optionsFeature['swyi_feature_controls_arrow_colour'] ) ? $optionsFeature['swyi_feature_controls_arrow_colour'] : '#fff' ) );
        $controlsBgColour = sanitize_text_field( ( isset( $optionsShowcase['swyi_showcase_controls_bg_colour'] ) && !empty( $optionsShowcase['swyi_showcase_controls_bg_colour'] ) ? $optionsShowcase['swyi_showcase_controls_bg_colour'] : '#000' ) );
        $controlsArrowColour = sanitize_text_field( ( isset( $optionsShowcase['swyi_showcase_controls_arrow_colour'] ) && !empty( $optionsShowcase['swyi_showcase_controls_arrow_colour'] ) ? $optionsShowcase['swyi_showcase_controls_arrow_colour'] : '#fff' ) );
        $statusVerticalDistance = sanitize_text_field( $optionsStatus['swyi_status_vertical_distance'] ?? '25' );
        $statusHorizontalDistance = sanitize_text_field( $optionsStatus['swyi_status_horizontal_distance'] ?? '25' );
        $statusLogoBackgroundColour = sanitize_text_field( $optionsStatus['swyi_status_logo_background_colour'] ?? '#6441A4' );
        $statusLogoAccentColour = sanitize_text_field( $optionsStatus['swyi_status_accent_colour'] ?? '#6441A4' );
        $statusCarouselBackgroundColour = sanitize_text_field( $optionsStatus['swyi_status_carousel_background_colour'] ?? '#fff' );
        $statusCarouselArrowColour = sanitize_text_field( $optionsStatus['swyi_status_carousel_arrow_colour'] ?? '#000' );
        $streamWeaselsCssVars = '
					:root {
						--yt-max-width : ' . esc_attr( $maxWidth ) . ';
						--yt-tile-bg-colour : ' . esc_attr( $tileBgColour ) . ';
						--yt-tile-title-colour :' . esc_attr( $tileTitleColour ) . ';            
						--yt-tile-subtitle-colour: ' . esc_attr( $tileSubtitleColour ) . ';
						--yt-tile-rounded-corners: ' . esc_attr( $tileRoundedCorners ) . ';
						--yt-tile-column-count: ' . esc_attr( $tileColumnCount ) . ';
						--yt-tile-column-spacing: ' . esc_attr( $tileColumnSpacing ) . ';
						--yt-hover-colour: ' . esc_attr( $tileHoverColour ) . ';
						--yt-logo-bg-colour: ' . esc_attr( $logoBgColour ) . ';
						--yt-logo-border-colour: ' . esc_attr( $logoBorderColour ) . ';
						--yt-feature-controls-bg-colour: ' . esc_attr( $controlsBgColour ) . ';
						--yt-feature-controls-arrow-colour: ' . esc_attr( $controlsArrowColour ) . ';		
						--yt-showcase-controls-bg-colour: ' . esc_attr( $controlsBgColour ) . ';
						--yt-showcase-controls-arrow-colour: ' . esc_attr( $controlsArrowColour ) . ';
						--yt-status-vertical-distance: ' . esc_attr( $statusVerticalDistance ) . ';
						--yt-status-horizontal-distance: ' . esc_attr( $statusHorizontalDistance ) . ';
						--yt-status-logo-accent-colour: ' . esc_attr( $statusLogoAccentColour ) . ';
						--yt-status-logo-background-colour: ' . esc_attr( $statusLogoBackgroundColour ) . ';
						--yt-status-carousel-background-colour: ' . esc_attr( $statusCarouselBackgroundColour ) . ';
						--yt-status-carousel-arrow-colour: ' . esc_attr( $statusCarouselArrowColour ) . ';																
					}
				';
        wp_add_inline_style( $this->plugin_name, $streamWeaselsCssVars );
    }

    public function enqueue_scripts() {
        if ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ) {
            wp_enqueue_script(
                $this->plugin_name . '-flipsterjs',
                plugin_dir_url( __FILE__ ) . 'dist/flipster.min.js',
                array('jquery'),
                $this->version,
                true
            );
        }
        wp_enqueue_script(
            $this->plugin_name . '-slickjs',
            plugin_dir_url( __FILE__ ) . 'dist/slick.min.js',
            array('jquery'),
            $this->version,
            true
        );
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-youtube-public.min.js',
            array('jquery'),
            $this->version,
            true
        );
        wp_enqueue_script(
            'youTube-API',
            'https://www.youtube.com/iframe_api',
            array('jquery'),
            '',
            false
        );
        $options = get_option( 'swyi_options' );
        $enableCache = ( isset( $options['swyi_enable_cache'] ) ? $options['swyi_enable_cache'] : 0 );
        $cacheData = '';
        if ( $enableCache ) {
            $cacheData = get_transient( 'swyi_video_cache' );
        }
        wp_add_inline_script( $this->plugin_name, 'const streamWeaselsYouTubeVars = ' . json_encode( array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'thumbnail' => plugin_dir_url( __FILE__ ) . 'img/sw-blank.png',
            'cacheData' => $cacheData,
        ) ), 'before' );
    }

    public function generate_fresh_nonce() {
        $nonce = wp_create_nonce( 'wp_rest' );
        wp_send_json_success( array(
            'nonce' => $nonce,
        ) );
        wp_die();
    }

    public function register_ajax_handler() {
        add_action( 'wp_ajax_swyi_get_fresh_nonce', array($this, 'generate_fresh_nonce') );
        add_action( 'wp_ajax_nopriv_swyi_get_fresh_nonce', array($this, 'generate_fresh_nonce') );
    }

    public function streamWeasels_shortcode() {
        // Setup the streamweasels shortcode
        add_shortcode( 'streamweasels-youtube', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-youtube', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-youtube-integration', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-youtube-embed', array($this, 'get_streamweasels_shortcode_embed') );
    }

    public function get_streamweasels_shortcode_embed( $args ) {
        // random 4-digit number is needed when multiple shortcodes on one page
        $autoplay = ( isset( $args['autoplay'] ) ? $args['autoplay'] : 'false' );
        $muted = ( isset( $args['muted'] ) ? $args['muted'] : 'false' );
        $aspectRatio = ( isset( $args['type'] ) && $args['type'] == 'short' ? '9/16' : '16/9' );
        $id = ( isset( $args['id'] ) ? $args['id'] : '' );
        $width = ( isset( $args['width'] ) ? $args['width'] : '100%' );
        $height = ( isset( $args['height'] ) ? $args['height'] : '100%' );
        $output = '<iframe src="https://www.youtube.com/embed/' . esc_attr( $id ) . '" style="width:' . esc_attr( $width ) . ';height:' . esc_attr( $height ) . ';aspect-ratio:' . esc_attr( $aspectRatio ) . '"></iframe>';
        return $output;
    }

    public function get_streamweasels_shortcode( $args ) {
        // random 4-digit number is needed when multiple shortcodes on one page
        $uuid = rand( 1000, 9999 );
        $options = get_option( 'swyi_options' );
        // Call streamweasels_content to set the inline scripts
        $this->streamweasels_content( $args, $uuid );
        // check the desired layout and return HTML
        ob_start();
        include 'partials/streamweasels-youtube-public-display.php';
        return ob_get_clean();
    }

    public function streamweasels_content( $args, $uuid ) {
        // stop early in the customizer
        // this is needed to prevent the customizer from loading the YouTube API
        if ( is_customize_preview() ) {
            return false;
        }
        $options = get_option( 'swyi_options' );
        $translations = get_option( 'swyi_translations' );
        $optionsWall = get_option( 'swyi_options_wall' );
        $optionsShowcase = get_option( 'swyi_options_showcase' );
        $optionsFeature = get_option( 'swyi_options_feature' );
        $youtubeAPIKey = sanitize_text_field( ( isset( $options['swyi_api_key'] ) ? $options['swyi_api_key'] : '' ) );
        $youtubeAPIKey = sanitize_text_field( ( isset( $args['api-key'] ) ? $args['api-key'] : $youtubeAPIKey ) );
        // Main Settings
        if ( empty( $args['channel'] ) && empty( $args['playlist'] ) && empty( $args['livestream'] ) ) {
            $youtubeChannelID = sanitize_text_field( $options['swyi_channel_id'] ?? '' );
            $youtubePlaylistID = sanitize_text_field( $options['swyi_playlist_id'] ?? '' );
            $youtubeLiveStreamID = sanitize_text_field( $options['swyi_livestream_id'] ?? '' );
        } else {
            $youtubeChannelID = sanitize_text_field( $args['channel'] ?? $args['channels'] ?? '' );
            $youtubePlaylistID = sanitize_text_field( $args['playlist'] ?? '' );
            $youtubeLiveStreamID = sanitize_text_field( $args['livestream'] ?? '' );
        }
        $limit = sanitize_text_field( $args['limit'] ?? $options['swyi_limit'] ?? '' );
        $layout = sanitize_text_field( $args['layout'] ?? $options['swyi_layout'] ?? 'wall' );
        $titleOverride = sanitize_text_field( $args['channel-title'] ?? '' );
        $hideShorts = sanitize_text_field( $args['hide-shorts'] ?? $optionsWall['swyi_wall_hide_shorts'] ?? 0 );
        $slideCount = sanitize_text_field( $args['showcase-slide-count'] ?? $args['slide-count'] ?? $optionsShowcase['swyi_showcase_slide_count'] ?? '' );
        $shortIds = '';
        $embed = 'page';
        $autoplay = 0;
        if ( $layout == 'feature' ) {
            if ( syi_fs()->is_plan_or_trial( 'premium', true ) || syi_fs()->is_plan_or_trial( 'pro', true ) ) {
                $layout = $layout;
            } else {
                $layout = 'wall';
            }
        }
        if ( $limit > 50 ) {
            $limit = 50;
        }
        // Overrides
        if ( $layout == 'showcase' || $layout == 'feature' ) {
            $hoverColour = '';
            if ( $autoplay == 1 ) {
                $autoload = 1;
            } else {
                $autoload = 0;
            }
        }
        if ( $layout == 'feature' ) {
            $embedPosition = ( isset( $args['feature-embed-position'] ) ? $args['feature-embed-position'] : $optionsFeature['swyi_feature_embed_position'] );
            if ( $embedPosition == '' || $embedPosition == 'inside' ) {
                $embed = 'inside';
            }
        }
        $offlineAddonCheck = 0;
        if ( $layout == 'status' ) {
            if ( syi_fs()->can_use_premium_code() && $embed == 'page' ) {
                $embed = 'popup';
            }
            if ( !syi_fs()->can_use_premium_code() && $embed == 'page' ) {
                $embed = 'youtube';
            }
            $offlineAddonCheck = 1;
        }
        if ( $layout == 'showcase' ) {
            $embed = 'inside';
            if ( syi_fs()->can_use_premium_code() ) {
                $limit = $limit;
            } else {
                if ( $limit > 6 ) {
                    $limit = 6;
                } else {
                    $limit = $limit;
                }
            }
        }
        // Translations
        $translationsLive = sanitize_text_field( ( isset( $translations['swyi_translations_live'] ) && !empty( $translations['swyi_translations_live'] ) ? $translations['swyi_translations_live'] : 'LIVE' ) );
        $translationsViews = sanitize_text_field( ( isset( $translations['swyi_translations_views'] ) && !empty( $translations['swyi_translations_views'] ) ? $translations['swyi_translations_views'] : 'views' ) );
        $translationsNextPage = sanitize_text_field( ( isset( $translations['swyi_translations_next_page'] ) && !empty( $translations['swyi_translations_next_page'] ) ? $translations['swyi_translations_next_page'] : 'Next page' ) );
        $translationsPrevPage = sanitize_text_field( ( isset( $translations['swyi_translations_prev_page'] ) && !empty( $translations['swyi_translations_prev_page'] ) ? $translations['swyi_translations_prev_page'] : 'Previous page' ) );
        //strip spaces and double commas
        $youtubeLiveStreamID = str_replace( ',,', ',', $youtubeLiveStreamID );
        $youtubeLiveStreamID = str_replace( ' ', '', $youtubeLiveStreamID );
        // For block themes, register a dummy script to allow inline scripts to be added
        if ( !wp_script_is( $this->plugin_name, 'registered' ) ) {
            wp_register_script( $this->plugin_name . '-blocks', '' );
            wp_enqueue_script( $this->plugin_name . '-blocks' );
            $inlineScriptHandle = $this->plugin_name . '-blocks';
        } else {
            $inlineScriptHandle = $this->plugin_name;
        }
        if ( $hideShorts && $layout == 'wall' && $youtubeChannelID !== '' && $youtubeAPIKey !== '' && $youtubePlaylistID == '' && $youtubeLiveStreamID == '' ) {
            $youTubeAPI = new SWYI_YouTube_API();
            $shortIds = $youTubeAPI->check_shorts( $youtubeAPIKey, $youtubeChannelID );
        }
        wp_add_inline_script( $inlineScriptHandle, 'const streamWeaselsYouTubeVars' . $uuid . ' = ' . json_encode( array(
            'YouTubeChannelID'     => esc_attr( $youtubeChannelID ),
            'YouTubePlaylistID'    => esc_attr( $youtubePlaylistID ),
            'YouTubeLiveStreamID'  => esc_attr( $youtubeLiveStreamID ),
            'limit'                => (int) $limit,
            'layout'               => esc_attr( $layout ),
            'slideCount'           => (int) $slideCount,
            'titleOverride'        => wp_kses_post( $titleOverride ),
            'pagination'           => 0,
            'embed'                => esc_attr( $embed ),
            'embedMuted'           => 0,
            'showOffline'          => (int) $offlineAddonCheck,
            'showOfflineText'      => 'No Streams Online!',
            'showOfflineImage'     => '',
            'autoload'             => 0,
            'autoplay'             => 0,
            'logoImage'            => '',
            'logoBgColour'         => '',
            'logoBorderColour'     => '',
            'tileSorting'          => '',
            'tileBgColour'         => '',
            'tileTitleColour'      => '',
            'tileSubtitleColour'   => '',
            'hoverColour'          => '',
            'enableCache'          => '',
            'hideShorts'           => (int) $hideShorts,
            'shortsIds'            => esc_attr( $shortIds ),
            'translationsLive'     => wp_kses_post( $translationsLive ),
            'translationsViews'    => wp_kses_post( $translationsViews ),
            'translationsNextPage' => wp_kses_post( $translationsNextPage ),
            'translationsPrevPage' => wp_kses_post( $translationsPrevPage ),
        ) ) . ';', 'before' );
    }

    public function swyi_status_show_global() {
        $options = get_option( 'swyi_options' );
        $optionsStatus = get_option( 'swyi_options_status' );
        $youtubeLiveStreamID = sanitize_text_field( ( isset( $optionsStatus['swyi_livestream_id'] ) ? $optionsStatus['swyi_livestream_id'] : '' ) );
        $showGlobal = sanitize_text_field( ( isset( $optionsStatus['swyi_status_show_global'] ) && !empty( $optionsStatus['swyi_status_show_global'] ) ? $optionsStatus['swyi_status_show_global'] : '0' ) );
        if ( $showGlobal ) {
            echo do_shortcode( '[sw-youtube layout="status" livestream="' . $youtubeLiveStreamID . '" status-placement="absolute"]' );
        }
    }

}
