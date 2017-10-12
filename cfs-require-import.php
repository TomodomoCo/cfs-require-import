<?php
/*
Plugin Name: Custom Field Suite: Require & Import
Plugin URI: https://www.vanpattenmedia.com/
Description: Plugin boilerplate to require Custom Field Suite and import custom fields on activation
Version: 1.0
Author: Chris Van Patten / Van Patten Media Inc.
Author URI: https://www.vanpattenmedia.com/
*/

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

class CfsRequireImport {
	
	function __construct() {
		register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'import_cfs_fields' ) );
		add_action('admin_notices', array( $this, 'print_import_notice'));
	}

	public static function import_cfs_fields() {
		$fields = file_get_contents( trailingslashit( dirname( __FILE__ ) ) . 'fields.json' );

		$options = array(
			'import_code' => json_decode( $fields , true ),
		);

		$result = CFS()->field_group->import( $options );
		
		set_transient('cfs_import_result', $result, 60);
	}
	
	public static function print_import_notice() {
		 if ( get_transient( 'cfs_import_result' ) ) {
			 echo '<div class="updated"><p>' . get_transient( 'cfs_import_result' ) . '</p></div>';
			 delete_transient( 'cfs_import_result' );
		 }
	}
}


if ( is_plugin_active('custom-field-suite/cfs.php') ) {

	new CfsRequireImport();

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
