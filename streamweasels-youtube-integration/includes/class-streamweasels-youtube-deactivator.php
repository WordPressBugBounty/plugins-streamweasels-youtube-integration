<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.streamweasels.com/
 * @since      1.0.0
 *
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Streamweasels_Youtube
 * @subpackage Streamweasels_Youtube/includes
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Youtube_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Delete the debug log when de-activated
		delete_option( 'swyi_debug_log' );
	}

}
