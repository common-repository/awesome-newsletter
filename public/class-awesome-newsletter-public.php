<?php
// define class for public facing functionality

class awesome_Newsletter_Public {
	// The ID of this plugin
	private $plugin_name;
	// The version of this plugin
	private $version;
	// set the public display
	private $display;

	// initialize the class and set it's properties
	public function __construct ( $plugin_name, $version ) {		
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		require_once plugin_dir_path( __FILE__ ) . 'partials/class-awesome-newsletter-public-display.php';
		$this->display 		= new awesome_newsletter_public_display();
	}	
	// register the stylesheet
	public function enqueue_styles () {		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/awesome-newsletter-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-icofont', plugin_dir_url( __FILE__ ) . 'css/icofont.min.css', array(), $this->version, 'all' );
	}
	// register the javascript 
	public function enqueue_scripts () {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/awesome-newsletter-public.js', array('jquery'), $this->version, true);
		$ajax_url = admin_url( 'admin-ajax.php' );
		wp_localize_script( $this->plugin_name, 'urls', array(
			'ajaxurl'	=>	$ajax_url
		) );
	}
	// show newsletter form to the front-end
	public function newsletter_form_call () {		
		$aw_title 			=	!empty( get_option( 'aw_title' ) ) ? get_option( 'aw_title' ) : __('Newsletter', 'awesome-newsletter' );
		$aw_placeholder 	= 	!empty( get_option( 'aw_placeholder' ) ) ? get_option( 'aw_placeholder' ) : __('Email Address', 'awesome-newsletter' );
		// load the front-end newsletter form
		$this->display->aw_newsletter_form( $aw_title, $aw_placeholder );		
	}

	// ajax functon for the newsletter front-end form
	public function aw_front_ajax() {		
		// check nonce field first	
		if ( check_ajax_referer( 'aw_action', 's' ) ) {

			$aw_incorrect 			=	!empty ( get_option( 'aw_incorrect' ) ) ? get_option( 'aw_incorrect' ) : __( 'Your email address is incorrect', 'awesome-newsletter' );
			$aw_already_exist 		=	!empty ( get_option( 'aw_already_exist' ) ) ? get_option( 'aw_already_exist' ) : __( 'Email address is already exist', 'awesome-newsletter' );
			$aw_register_success	=	!empty ( get_option( 'aw_register_success' ) ) ? get_option( 'aw_register_success' ) : __( 'Successfully added to our newsletter', 'awesome-newsletter' );

			global $wpdb;
			$aw_email 		=	sanitize_email( $_POST['aw_email'] );
			$aw_table_name =	$wpdb->prefix . 'awesome_nletter_email';
			$aw_sql 		=	"SELECT count( email_address ) FROM $aw_table_name WHERE email_address = '$aw_email' ";
			$aw_exits 		=	$wpdb->get_var( $aw_sql );

			if( !is_email( $aw_email )) {
				echo "<div class='aw_error_message'>$aw_incorrect</div>";
			} elseif( $aw_exits > 0 ) {
				echo "<div class='aw_error_message'>$aw_already_exist</div>";
			} else {
				$wpdb->insert ( $aw_table_name, array (
					'email_address'	=>	$aw_email,
					'added_time'	=>	current_time( 'mysql' )
				), array('%s', '%s'));
				echo "<div class='aw_update_message'>$aw_register_success</div>";
			}
		}
		wp_die();
	}
}