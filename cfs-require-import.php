<?php
/*
Plugin Name: Require Custom Field Suite
Plugin URI: https://www.vanpattenmedia.com/
Description: Plugin boilerplate to require Custom Field Suite and import custom fields on activation
Version: 1.0
Author: Chris Van Patten / Van Patten Media Inc.
Author URI: https://www.vanpattenmedia.com/
*/

class CfsPluginDemo {

	function __construct() {
		register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'import_cfs_fields' ) );
	}

	public static function import_cfs_fields() {
		$fields = file_get_contents( trailingslashit( dirname( __FILE__ ) ) . 'fields.json' );

		$options = array(
			'import_code' => json_decode( stripslashes( $fields ), true ),
		);

		$result = CFS()->field_group->import( $options );

		add_action( 'admin_notices', function() {
			echo '<div class="updated"><p>' . strip_tags( $result ) . '</p></div>';
		} );
	}
}


if ( class_exists( 'Custom_Field_Suite' ) ) {

	new CfsPluginDemo;

} else {

	// Require CFS
	add_action( 'admin_init', function() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} );

	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Please install <a href="https://wordpress.org/plugins/custom-field-suite/"><strong>Custom Field Suite</strong></a> before activating this plugin</p></div>';

		if ( isset( $_GET['activate'] ) )
			unset( $_GET['activate'] );
	} );

}
