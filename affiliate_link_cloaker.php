<?php

/*
Plugin Name: WP affiliate link
Plugin URI: http://www.wp-developer.net/affiliate-link-cloaker
Description: this plugin will hide your affiliate link.
Version: 1.0
Author: Aijaz Mohammad
Author URI: http://www.wp-developer.net/
License: A "Slug" license name e.g. GPL2
*/

$siteurl = get_option('siteurl');
	define('AFFI_FOLDER', dirname(plugin_basename(__FILE__)));
	define('AFFI_URL', plugins_url());
	define('AFFI_FILE_PATH', dirname(__FILE__));
	define('AFFI_DIR_NAME', basename(AFFI_FILE_PATH));


global $wpdb;

	include('includes/affi_functions.php');

	register_activation_hook(__FILE__,'affi_install');
	register_deactivation_hook(__FILE__ , 'affi_uninstall' );



	/*
	* This Plugin function is used to install the plugin
	*
	*/
	function affi_install()
	{
      global $wpdb;

	}
	/*
	*
	* This Plugin function is useed to uninstall the plugin
	*
	*/
	function affi_uninstall()
	{
     global $wpdb;

	}