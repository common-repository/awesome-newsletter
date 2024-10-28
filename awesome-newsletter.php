<?php
/*
Plugin Name: Awesome Newsletter
Plugin URI:  http://creativeartbd.com/wordpress-plugin/
Description: Awesome Newsletter, where you can send custom email or latest posts to your subscribers.
Version:     1.0.0
Author:      Awesome Coder
Author URI:  http://awesomecoder/
Text Domain: awesome-newsletter
Domain Path: /languages
License:     GPL2
*/

// if the file is called directly 
if( ! defined( 'WPINC' ) ) {
	die;
}

// plugin version 
define( 'AWESOME_NEWSLETTER_VERSION', '1.0.0' );

// plugin activation hook
function active_awesome_newsletter () {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awesome-newsletter-activator.php';
	awesome_Newsletter_Activator::activate();
}
register_activation_hook( __FILE__, 'active_awesome_newsletter' );

// plugin de-activation hook
function deactive_awesome_newsletter () {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awesome-newsletter-deactivator.php';
	awesome_Newsletter_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactive_awesome_newsletter' );

// include core plugin class
require plugin_dir_path( __FILE__ ) . 'includes/class-awesome-newsletter.php';

// begin execution of the plugin
function run_awesome_newsletter () {
	$plugin 	=	new awesome_Newsletter();
	$plugin->run();
}
run_awesome_newsletter();