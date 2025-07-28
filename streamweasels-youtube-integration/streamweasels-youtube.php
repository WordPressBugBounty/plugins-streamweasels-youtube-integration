<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.streamweasels.com/
 * @since             1.0.0
 * @package           Streamweasels_Youtube
 *
 * @wordpress-plugin
 * Plugin Name:       SW YouTube Integration - Blocks and Shortcodes for Embedding YouTube
 * Plugin URI:        https://www.streamweasels.com/
 * Description:       Embed YouTube content like Shorts, Video and Live Streams with our collection of YouTube Blocks and Shortcodes.
 * Version:           1.4.1
 * Author:            StreamWeasels
 * Author URI:        https://www.streamweasels.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       streamweasels-youtube
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STREAMWEASELS_YOUTUBE_VERSION', '1.4.1' );
if ( !defined( 'SWYI_PLUGIN_DIR' ) ) {
    define( 'SWYI_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
}
if ( function_exists( 'syi_fs' ) ) {
    syi_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    function syi_fs() {
        global $syi_fs;
        if ( !isset( $syi_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $syi_fs = fs_dynamic_init( array(
                'id'             => '10981',
                'slug'           => 'streamweasels-youtube-integration',
                'premium_slug'   => 'streamweasels-youtube-integration-paid',
                'type'           => 'plugin',
                'public_key'     => 'pk_e3edb343eda5c0b485d6e92c02326',
                'is_premium'     => false,
                'premium_suffix' => '(Paid)',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                'menu'           => array(
                    'slug'    => 'streamweasels-youtube',
                    'support' => false,
                ),
                'is_live'        => true,
            ) );
        }
        return $syi_fs;
    }

    // Init Freemius.
    syi_fs();
    // Disable monthly breakdown in pricing — show annual figure only.
    syi_fs()->add_filter( 'pricing/show_annual_in_monthly', '__return_false' );
    // Signal that SDK was initiated.
    do_action( 'syi_fs_loaded' );
    // Plugin Folder Path
    if ( !defined( 'SWYI_PLUGIN_DIR' ) ) {
        define( 'SWYI_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-streamweasels-youtube-activator.php
     */
    function activate_streamweasels_youtube() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-youtube-activator.php';
        Streamweasels_Youtube_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-streamweasels-youtube-deactivator.php
     */
    function deactivate_streamweasels_youtube() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-youtube-deactivator.php';
        Streamweasels_Youtube_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'activate_streamweasels_youtube' );
    register_deactivation_hook( __FILE__, 'deactivate_streamweasels_youtube' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-youtube.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_streamweasels_youtube() {
        $plugin = new Streamweasels_Youtube();
        $plugin->run();
    }

    run_streamweasels_youtube();
}