<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com/
 * @since      1.0.0
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/public/partials
 */
?>
<?php
$options            = get_option('swyi_options');
$optionsWall        = get_option('swyi_options_wall');
$optionsFeature     = get_option('swyi_options_feature');
$optionsPlayer      = get_option('swyi_options_player');
$optionsStatus      = get_option('swyi_options_status');
$layout 		    = ( isset( $args['layout'] ) ? $args['layout'] : $options['swyi_layout'] );
$layout             = ( $layout ? $layout : 'showcase' );
$title 				= ( isset( $options['swyi_title'] ) ? $options['swyi_title'] : '' );
$title 				= ( isset( $args['title'] ) ? $args['title'] : $title );
$subtitle 			= ( isset( $options['subtitle'] ) ? $options['subtitle'] : '' );
$subtitle 			= ( isset( $args['subtitle'] ) ? $args['subtitle'] : $subtitle );
$tileLayout         = ( isset( $options['swyi_tile_layout'] ) ? $options['swyi_tile_layout'] : '' );
$tileLayout         = ( isset( $args['tile-layout'] ) ? $args['tile-layout'] : $tileLayout );
$hoverEffect        = ( isset( $options['swyi_hover_effect'] ) ? $options['swyi_hover_effect'] : '' );
$hoverEffect        = ( isset( $args['hover-effect'] ) ? $args['hover-effect'] : $hoverEffect );
$maxWidth           = ( isset( $options['swyi_max_width'] ) ? $options['swyi_max_width'] : '' );
$maxWidth           = ( isset( $args['max-width'] ) ? $args['max-width'] : $maxWidth );

if ($layout == 'feature') {
    if (syi_fs()->is_plan_or_trial('premium', true) || syi_fs()->is_plan_or_trial('pro', true)) {
        $layout = $layout;
    } else {
        $layout = 'wall';
    }
}

$titleMarkup        = '';
$subtitleMarkup     = '';
if ($title !== '') {
    $titleMarkup = '<h2 class="cp-streamweasels-youtube__heading">'.$title.'</h2>';
}
if ($subtitle !== '') {
    $subtitleMarkup = '<h3 class="cp-streamweasels-youtube__subheading">'.$subtitle.'</h3>';
}

// Wall Settings
$tileColumnCount    = ( isset( $optionsWall['swyi_wall_column_count'] ) ? $optionsWall['swyi_wall_column_count'] : '4' );
$tileColumnCount    = ( isset( $args['wall-column-count'] ) ? $args['wall-column-count'] : $tileColumnCount );
$tileColumnSpacing  = ( isset( $optionsWall['swyi_wall_column_spacing'] ) ? $optionsWall['swyi_wall_column_spacing'] : '10' );
$tileColumnSpacing  = ( isset( $args['wall-column-spacing'] ) ? $args['wall-column-spacing'] : $tileColumnSpacing );

// Feature Settings
$embedPosition      = ( isset( $optionsFeature['swyi_feature_embed_position'] ) ? $optionsFeature['swyi_feature_embed_position'] : 'inside' ); 
$embedPosition      = ( isset( $args['feature-embed-position'] ) ? $args['feature-embed-position'] : $embedPosition ); 
$enableSkew         = ( isset( $optionsFeature['swyi_feature_skew'] ) ? $optionsFeature['swyi_feature_skew'] : '0' ); 
$enableSkew         = ( isset( $args['feature-skew'] ) ? $args['feature-skew'] : $enableSkew );

// Player Settings
$welcomeBgColour    = ( isset( $optionsPlayer['swyi_player_welcome_bg_colour'] ) ? $optionsPlayer['swyi_player_welcome_bg_colour'] : '#fff' );	
$welcomeBgColour    = ( isset( $args['player-welcome-bg-colour'] ) ? $args['player-welcome-bg-colour'] : $welcomeBgColour );	
$welcomeLogo        = ( isset( $optionsPlayer['swyi_player_welcome_logo'] ) ? $optionsPlayer['swyi_player_welcome_logo'] : '' );	
$welcomeLogo        = ( isset( $args['player-welcome-logo'] ) ? $args['player-welcome-logo'] : $welcomeLogo );	
$welcomeImage       = ( isset( $optionsPlayer['swyi_player_welcome_image'] ) ? $optionsPlayer['swyi_player_welcome_image'] : '' );	
$welcomeImage       = ( isset( $args['player-welcome-image'] ) ? $args['player-welcome-image'] : $welcomeImage );	
$welcomeText        = ( isset( $optionsPlayer['swyi_player_welcome_text']  ) ? $optionsPlayer['swyi_player_welcome_text']  : '' );	
$welcomeText        = ( isset( $args['player-welcome-text'] ) ? $args['player-welcome-text'] : $welcomeText );	
$welcomeText2       = ( isset( $optionsPlayer['swyi_player_welcome_text_2'] ) ? $optionsPlayer['swyi_player_welcome_text_2'] : '' );	
$welcomeText2       = ( isset( $args['player-welcome-text-2'] ) ? $args['player-welcome-text-2'] : $welcomeText2 );	
$welcomeTextColour  = ( isset( $optionsPlayer['swyi_player_welcome_text_colour'] ) ? $optionsPlayer['swyi_player_welcome_text_colour'] : '' );	
$welcomeTextColour  = ( isset( $args['player-welcome-text-colour'] ) ? $args['player-welcome-text-colour'] : $welcomeTextColour );	
$playerStreamPos    = ( isset( $optionsPlayer['swyi_player_stream_position'] ) ? $optionsPlayer['swyi_player_stream_position'] : 'left' );	
$playerStreamPos    = ( isset( $args['player-stream-list-position'] ) ? $args['player-stream-list-position'] : $playerStreamPos );

$hideOffline          = ( isset( $optionsStatus['swyi_status_hide_when_offline'] ) ? $optionsStatus['swyi_status_hide_when_offline'] : '0' ); 
$hideOffline          = ( isset( $args['status-hide-offline'] ) ? $args['status-hide-offline'] : $hideOffline);
$placement            = ( isset( $optionsStatus['swyi_status_placement'] ) ? $optionsStatus['swyi_status_placement'] : 'absolute' ); 
$placement            = ( isset( $args['status-placement'] ) ? $args['status-placement'] : $placement); 
$verticalPlacement    = ( isset( $optionsStatus['swyi_status_vertical_placement'] ) ? $optionsStatus['swyi_status_vertical_placement'] : 'top' ); 
$verticalPlacement    = ( isset( $args['status-vertical-placement'] ) ? $args['status-vertical-placement'] : $verticalPlacement); 
$horizontalPlacement  = ( isset( $optionsStatus['swyi_status_horizontal_placement'] ) ? $optionsStatus['swyi_status_horizontal_placement'] : 'left' ); 
$horizontalPlacement  = ( isset( $args['status-horizontal-placement'] ) ? $args['status-horizontal-placement'] : $horizontalPlacement ); 
$customLogo           = ( isset( $optionsStatus['swyi_status_custom_logo'] ) ? $optionsStatus['swyi_status_custom_logo'] : '' ); 
$customLogo           = ( isset( $args['status-custom-logo'] ) ? $args['status-custom-logo'] : $customLogo ); 
$logoBackgroundColour = ( isset( $optionsStatus['swyi_status_logo_background_colour']  ) ? $optionsStatus['swyi_status_logo_background_colour']  : '' ); 
$logoBackgroundColour = ( isset( $args['status-logo-background-colour'] ) ? $args['status-logo-background-colour'] : $logoBackgroundColour ); 
$disableCarousel      = ( isset( $optionsStatus['swyi_status_disable_carousel'] ) ? $optionsStatus['swyi_status_disable_carousel'] : '0' ); 
$disableCarousel      = ( isset( $args['status-disable-carousel'] ) ? $args['status-disable-carousel'] : $disableCarousel ); 

$showStreamsLeft    = (($playerStreamPos == '' || $playerStreamPos == 'none' || $playerStreamPos == 'left') ? '<div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--'.$tileLayout.' cp-streamweasels-youtube__streams--hover-'.$hoverEffect.' cp-streamweasels-youtube__streams--position-'.$playerStreamPos.'"></div>' : '');
$showStreamsRight   = ($playerStreamPos == 'right' ? '<div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--'.$tileLayout.' cp-streamweasels-youtube__streams--hover-'.$hoverEffect.' cp-streamweasels-youtube__streams--position-'.$playerStreamPos.'"></div>' : '');

// Showcase Settings
if ($layout == 'showcase' || $layout == 'feature') {
    $hoverEffect = 'play';
}
?>

<div class="cp-streamweasels-youtube cp-streamweasels-youtube--<?php echo esc_attr($layout); ?> cp-streamweasels-youtube--<?php echo esc_attr($embedPosition); ?> cp-streamweasels-youtube--<?php echo esc_attr($uuid); ?> cp-streamweasels-youtube--hover-<?php echo esc_attr($hoverEffect); ?> cp-streamweasels-youtube--placement-<?php echo esc_attr($placement); ?> cp-streamweasels-youtube--position-<?php echo esc_attr($verticalPlacement); ?> cp-streamweasels-youtube--position-<?php echo esc_attr($horizontalPlacement); ?> cp-streamweasels-youtube--hide-<?php echo esc_attr($hideOffline); ?>"  style="<?php echo esc_attr(($maxWidth !== 'none') ? 'max-width:'.$maxWidth.'px; margin: 0 auto;' : ''); ?>" data-online="0" data-offline="0" data-total="0" data-uuid="<?php echo esc_attr($uuid); ?>" data-skew="<?php echo esc_attr($enableSkew); ?>">
    <div class="cp-streamweasels-youtube__inner">

        <?php if ($layout == 'wall') { ?>
            <?php echo wp_kses_post($titleMarkup); ?>
            <?php echo wp_kses_post($subtitleMarkup); ?>        
            <div class="cp-streamweasels-youtube__loader">
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
            </div>
            <div class="cp-streamweasels-youtube__player">
                <div class="cp-streamweasels-youtube__player-target"></div>
            </div>
            <div class="cp-streamweasels-youtube__offline-wrapper"></div>
            <div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--<?php echo esc_attr($tileLayout ? $tileLayout : 'detailed'); ?> cp-streamweasels-youtube__streams--hover-<?php echo esc_attr($hoverEffect ? $hoverEffect : 'none'); ?>" style="grid-gap:<?php echo esc_attr($tileColumnSpacing ? $tileColumnSpacing.'px;' : '10px;'); ?> grid-template-columns: repeat(<?php echo esc_attr($tileColumnCount ? $tileColumnCount : '4'); ?>, minmax(100px, 1fr));"></div>     
            <div class="cp-streamweasels-youtube__pagination"></div>
        <?php } else if ($layout == 'feature') { ?>
            <?php echo wp_kses_post($titleMarkup); ?>
            <?php echo wp_kses_post($subtitleMarkup); ?>        
            <div class="cp-streamweasels-youtube__loader">
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
            </div>
            <?php if ($layout == 'feature' && $embedPosition == 'above') { ?>
                <div class="cp-streamweasels-youtube__player">
                    <div class="cp-streamweasels-youtube__player-target"></div>
                </div>
            <?php } ?>
            <div class="cp-streamweasels-youtube__offline-wrapper"></div>
            <div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--<?php echo esc_attr($tileLayout ? $tileLayout : 'detailed'); ?> cp-streamweasels-youtube__streams--hover-<?php echo esc_attr($hoverEffect ? $hoverEffect : 'none'); ?>" style="grid-gap:<?php echo esc_attr($tileColumnSpacing ? $tileColumnSpacing.'px;' : '10px;'); ?> grid-template-columns: repeat(<?php echo esc_attr($tileColumnCount ? $tileColumnCount : '4'); ?>, minmax(100px, 1fr));"></div>
            <?php if ($embedPosition == 'below') { ?>
                <div class="cp-streamweasels-youtube__player">
                    <div class="cp-streamweasels-youtube__player-target"></div>
                </div>
            <?php } ?>        
            <div class="cp-streamweasels-youtube__pagination"></div>
        <?php } else if ($layout == 'player') { ?>
            <?php echo wp_kses_post($titleMarkup); ?>
            <?php echo wp_kses_post($subtitleMarkup); ?>              
            <div class="cp-streamweasels-youtube__inner-wrapper">
                <div class="cp-streamweasels-youtube__player-wrapper"> 
                    <?php echo wp_kses_post($showStreamsLeft); ?>
                    <div class="cp-streamweasels-youtube__player" style="<?php echo esc_attr(($welcomeBgColour) ? 'background-color:'.$welcomeBgColour.';' : ''); ?>">
                        <div class="cp-streamweasels-youtube__player-target"></div>
                        <div class="cp-streamweasels-youtube__offline-wrapper">
                            <div class="cp-streamweasels-youtube__welcome">
                                <?php echo ($welcomeImage ? '<img src="'.esc_url($welcomeImage).'">' : ''); ?>
                                <div class="cp-streamweasels-youtube__welcome-wrapper">
                                    <?php echo ($welcomeLogo ? '<img src="'.esc_url($welcomeLogo).'">' : ''); ?>
                                    <?php if ($welcomeText !== '' || $welcomeText2 !== '') { ?>
                                        <div class="cp-streamweasels-youtube__welcome-text  cp-streamweasels-youtube__welcome-text--<?php echo esc_attr($welcomeLogo ? 'with-logo' : 'no-logo'); ?>">
                                            <?php echo ($welcomeText ? '<p class="cp-streamweasels-youtube__welcome-text--line-1" style="color:'.esc_attr($welcomeTextColour).'">'.wp_kses_post($welcomeText).'</p>' : ''); ?>
                                            <?php echo ($welcomeText2 ? '<p class="cp-streamweasels-youtube__welcome-text--line-2" style="color:'.esc_attr($welcomeTextColour).'">'.wp_kses_post($welcomeText2).'</p>' : ''); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <?php echo wp_kses_post($showStreamsRight); ?>
                </div>
            </div>
            <div class="cp-streamweasels-youtube__pagination"></div>
        <?php } else if ($layout == 'showcase') { ?>
                <?php echo wp_kses_post($titleMarkup); ?>
                <?php echo wp_kses_post($subtitleMarkup); ?> 
                <div class="cp-streamweasels-youtube__loader">
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                </div>
                <div class="cp-streamweasels-youtube__player"></div>
                <div class="cp-streamweasels-youtube__offline-wrapper"></div>
                <div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--<?php echo esc_attr($tileLayout ? $tileLayout : 'detailed'); ?> cp-streamweasels-youtube__streams--hover-<?php echo esc_attr($hoverEffect ? $hoverEffect : 'none'); ?>"></div>
        <?php } else if ($layout == 'status') { ?>
            <div class="cp-streamweasels-youtube__loader">
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
                <div class="spinner-item"></div>
            </div>
            <div class="cp-streamweasels-youtube__player"></div>
            <div class="cp-streamweasels-youtube__offline-wrapper"></div>
            <div class="cp-streamweasels-youtube__twitch-logo cp-streamweasels-youtube__twitch-logo--<?php echo esc_attr(!$customLogo ? 'twitch' : 'custom'); ?>" style="background-color:<?php echo esc_attr($logoBackgroundColour); ?>">
                <?php echo ($customLogo ? '<img src="'.esc_url($customLogo).'">' : ''); ?>
            </div>
            <div class="cp-streamweasels-youtube__streams cp-streamweasels-youtube__streams--<?php echo esc_attr($tileLayout); ?> cp-streamweasels-youtube__streams--hover-<?php echo esc_attr($hoverEffect); ?> cp-streamweasels-youtube__streams--carousel-<?php echo esc_attr($disableCarousel); ?>"></div>
        <?php } ?>
    </div>
</div>