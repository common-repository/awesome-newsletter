<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// The admin-specific functionality of the plugin.
class awesome_Newsletter_Admin {
	// The ID of this plugn
	private $plugin_name;
	// The version of this plugn 
	private $version;
	// load the display
	private $dispaly;
	// initialize the class and set ti's properties
	public function __construct ( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;		
		require_once plugin_dir_path(  __FILE__ ) . 'partials/class-awesome-newsletter-admin-display.php';
		$this->display = new awesome_Newsletter_Admin_Display();
	}
	// Register the stylsheets for the admin are
	public function enqueue_styles () {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/awesome-newsletter-admin.css', array(), $this->version, 'all' );
	}
	// Register the JavaScript for the admin area.
	public function enqueue_scripts() { 
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/awesome-newsletter-admin.js', array( 'jquery' ), $this->version, false );
		$ajaxurl 	=	admin_url( 'admin-ajax.php' );
		wp_localize_script( $this->plugin_name, 'urls', array(
			'ajaxurl'	=>	$ajaxurl
		) );
	}
	// add menu to the wordpress admin for this plugin settings page
	public function add_admin_setting_menu () {
		add_menu_page( 'awesome Newsletter', 'awesome Newsletter', 'manage_options', 'awesome-newsletter', array( $this, 'awesome_newsletter_setting_page'), plugin_dir_url( __FILE__) .'/images/newsletter-icon.png' );
		// sub menu pages under main menu
		add_submenu_page( 'awesome-newsletter', 'Email List', 'Email List', 'manage_options', 'awesome-newsletter-list-email', array( $this, 'awesome_newsletter_list_email' ) );
		add_submenu_page( 'awesome-newsletter', 'Send Newsletter', 'Send Newsletter', 'manage_options', 'awesome-newsletter-send-email', array( $this, 'awesome_newsletter_send_email' ) );
	}	

	public function awesome_newsletter_setting_page () {	

		// get existing title and plceholder from the options table
		$db_titlle 				= 	!empty ( get_option( 'aw_title' ) ) ? get_option( 'aw_title' ) : __( 'Newsletter', 'awesome-newsletter' );
		$db_placeholder 		= 	!empty ( get_option( 'aw_placeholder' ) ) ? get_option( 'aw_placeholder' ) : __( 'Email Address', 'awesome-newsletter' );
		$db_incorrect 			=	!empty ( get_option( 'aw_incorrect' ) ) ? get_option( 'aw_incorrect' ) : __( 'Your email address is incorrect', 'awesome-newsletter' );
		$db_already_exist 		=	!empty ( get_option( 'aw_already_exist' ) ) ? get_option( 'aw_already_exist' ) : __( 'Email address is already exist', 'awesome-newsletter' );
		$db_register_success	=	!empty ( get_option( 'aw_register_success' ) ) ? get_option( 'aw_register_success' ) : __( 'Successfully added to our newsletter', 'awesome-newsletter' );
		$db_header_name			=	!empty ( get_option( 'aw_header_name' ) ) ? get_option( 'aw_header_name' ) : get_bloginfo( 'name' );
		$db_header_email		=	!empty ( get_option( 'aw_header_email' ) ) ? get_option( 'aw_header_email' ) : 'support@'.$_SERVER['SERVER_NAME'];

		// load the settings tab form		
		$this->display->aw_settings_tab( $db_titlle, $db_placeholder, $db_incorrect, $db_already_exist, $db_register_success, $db_header_name, $db_header_email ); 	
	}

	// show the list of email
	public function awesome_newsletter_list_email () {
		// load the email list tab
		$this->display->aw_email_list();
	}

	// show the send email form
	public function awesome_newsletter_send_email () {
		$aw_email_to_send 			=	'';
		if( isset ( $_GET['e'] ) ) {
			$aw_email_to_send 		=	esc_html ( $_GET['e'] );
		}		
		// load the send email form
		$this->display->aw_send_email_form( $aw_email_to_send );
	}	

	// validate send email form
	public function aw_send_message_action () {
		// verify nonce first
		if( check_admin_referer( 'aw_send_message_action', 's' ) ) {

			$aw_to 					=	sanitize_text_field ( $_POST['aw_to'] );
			$aw_subject 			= 	sanitize_text_field ( $_POST['aw_subject'] );			
			$aw_multiple_email		=	isset( $_POST['aw_multiple_email']) ? $_POST['aw_multiple_email'] : '';
			$aw_choose_option		=	isset( $_POST['aw_choose_option'] ) ? sanitize_text_field ( $_POST['aw_choose_option'] ) : '';
			$aw_no_of_post			=	absint ( $_POST['aw_no_of_post'] );
			$aw_message_before_body =	stripslashes ( $_POST['aw_message_before_body'] );
			$aw_message_after_body 	=	stripslashes ( $_POST['aw_message_after_body'] );
			$aw_template 			=	sanitize_text_field ( $_POST['aw_template'] );
			$aw_message 			=	stripslashes ( $_POST['aw_message'] );
			$aw_errors 				=	array();
			
			if( isset ( $aw_to, $aw_subject, $aw_choose_option, $aw_message ) ) {
				if( empty ( $aw_to ) && empty ( $aw_subject ) && empty( $aw_choose_option ) && empty ( $aw_multiple_email ) && empty ( $aw_message ) ) {
					$aw_errors[] = __( 'All fields are required', 'awesome-newsletter' );
				} else {
					if( !empty ( $aw_to ) ) {
						if ( !is_email ( $aw_to ) ) {
							$aw_errors[] = __( 'Email address is incorrect', 'awesome-newsletter' );
						}
					}
					if ( $aw_multiple_email ) {
						foreach ( $aw_multiple_email as $aw_email ) {
							$aw_email = sanitize_email( $aw_email );
							if( !is_email( $aw_email ) ) {
								$aw_errors[] = __( 'One of your multiple emails is incorrect', 'awesome-newsletter' );
							}
						}
					}
					if( empty( $aw_to ) && empty ( $aw_multiple_email ) ) {
						$aw_errors[] = __( 'Write email address or choose from the multiple email addresses', 'awesome-newsletter' );
					}
					if( empty ( $aw_subject ) ) {
						$aw_errors[] = __( 'Subject is required', 'awesome-newsletter' );
					} elseif ( strlen ( $aw_subject ) > 150 ) {
						$aw_errors[] = __( 'Subject must be less 150 characters long', 'awesome-newsletter' );
					}
					if( empty ( $aw_choose_option ) ) {
						$aw_errors[] = __( 'Choose what do you want to send', 'awesome-newsletter' );
					} else {
						if ( $aw_choose_option == 'post' ) {
							if ( empty( $aw_no_of_post ) ) {
								$aw_errors[] = __( 'Chooose how many post(s) you want to send', 'awesome-newsletter' );
							} 
							if( empty( $aw_template ) ) {
								$aw_errors[] = __( 'Choose a template for the newsletter email', 'awesome-newsletter' );
							}
						} elseif ( $aw_choose_option == 'custom-message' ) {
							if( empty ( $aw_message ) ) {
								$aw_errors[] = __( 'Message is required', 'awesome-newsletter' );
							}
						}
					}			
				}

				if( !empty ( $aw_errors ) ) {
					echo "<div class='aw_error_message'>";
						foreach ( $aw_errors as $aw_error ) {
							echo "<p>$aw_error</p>";
						}
					echo "</div>";
				} else {					
					
					if ( empty( $aw_multiple_email ) ) {
						$aw_to 				=	$aw_to;
					} else {
						array_push ( $aw_multiple_email, $aw_to );
						$aw_multiple_email 	= 	array_unique ( $aw_multiple_email );
						$aw_to 				=	implode( ',' , $aw_multiple_email );
					}
					
					// Send email
					$this->display->aw_send_email( $aw_to, $aw_subject, $aw_choose_option, $aw_no_of_post, $aw_message_before_body, $aw_template, $aw_message_after_body, $aw_message );
				}
			}			
		}
		wp_die();			
	}

	// save the setting tab data
	public function aw_setting_tab_action () {
		if ( check_admin_referer( 'aw_settings_tab', 's' ) ) {

			$aw_title 				= isset ( $_POST['aw_title'] ) ? sanitize_text_field ( $_POST['aw_title'] ) : '';
			$aw_placeholder 		= isset ( $_POST['aw_placeholder'] ) ? sanitize_text_field ( $_POST['aw_placeholder'] ) : '';
			$aw_incorrect 			= isset ( $_POST['aw_incorrect'] ) ? sanitize_text_field ( $_POST['aw_incorrect'] ) : '';
			$aw_already_exist 		= isset ( $_POST['aw_already_exist'] ) ? sanitize_text_field ( $_POST['aw_already_exist'] ) : '';
			$aw_register_success 	= isset ( $_POST['aw_register_success'] ) ? sanitize_text_field ( $_POST['aw_register_success'] ) : '';
			$aw_header_name 		= isset ( $_POST['aw_header_name'] ) ? sanitize_text_field ( $_POST['aw_header_name'] ) : '';
			$aw_header_email 		= isset ( $_POST['aw_header_email'] ) ? sanitize_text_field ( $_POST['aw_header_email'] ) : '';
			$aw_errors 				= array();

			$aw_domain_name 		= substr($aw_header_email, strpos($aw_header_email, "@") + 1);			

			if( isset( $aw_title, $aw_placeholder, $aw_incorrect, $aw_already_exist, $aw_register_success, $aw_header_name, $aw_header_email ) ) {
				
				if( empty ( $aw_title ) ) {
					$aw_errors[] = __( 'Title required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_title ) > 25 ) {
					$aw_errors[] = __( 'Title must be less than 25 characters', 'awesome-newsletter' );
				}

				if( empty ( $aw_placeholder ) ){
					$aw_errors[] = __( 'Placeholder required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_placeholder ) > 25 ) {
					$aw_errors[] = __( 'Placeholder must be less than 25 characters', 'awesome-newsletter' );
				}

				if( empty ( $aw_incorrect ) ){
					$aw_errors[] = __( 'Incorrect message text is required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_incorrect ) > 100 ) {
					$aw_errors[] = __( 'Incorrect message text must be less than 100 characters', 'awesome-newsletter' );
				}

				if( empty ( $aw_already_exist ) ){
					$aw_errors[] = __( 'Already exist message text is required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_already_exist ) > 100 ) {
					$aw_errors[] = __( 'Already exist message text must be less than 100 characters', 'awesome-newsletter' );
				}

				if( empty ( $aw_register_success ) ){
					$aw_errors[] = __( 'Register successfully message text is required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_register_success ) > 100 ) {
					$aw_errors[] = __( 'Register successfully message text must be less than 100 characters', 'awesome-newsletter' );
				}

				if( empty ( $aw_header_name ) ){
					$aw_errors[] = __( 'From name is required', 'awesome-newsletter' );
				} elseif( strlen ( $aw_header_name ) > 50 ) {
					$aw_errors[] = __( 'From name must be less than 50 characters long', 'awesome-newsletter' );
				}

				if( empty ( $aw_header_email ) ){
					$aw_errors[] = __( 'From email address is required', 'awesome-newsletter' );
				} elseif( !is_email ( $aw_header_email ) ) {
					$aw_errors[] = __( 'From email address is incorrect', 'awesome-newsletter' );
				} elseif ( $_SERVER['SERVER_NAME'] <> $aw_domain_name &&  $_SERVER['SERVER_NAME'] !== 'localhost' ) {
					$aw_errors[] = __( 'From email address domain name need to match with site domain name', 'awesome-newsletter');
				}
			}

			if( !empty ( $aw_errors ) ) {				
				foreach ( $aw_errors as $aw_error ) {
					echo "<div class='aw_error_message'><p>";
						echo $aw_error;
					echo "</p></div>";
				}
			} else {	
		
				update_option ( 'aw_title', $aw_title );
				update_option ( 'aw_placeholder', $aw_placeholder );
				update_option ( 'aw_incorrect', $aw_incorrect );
				update_option ( 'aw_already_exist', $aw_already_exist );
				update_option ( 'aw_register_success', $aw_register_success );
				update_option ( 'aw_header_name', $aw_header_name );
				update_option ( 'aw_header_email', $aw_header_email );
		
				echo "<div class='aw_update_message'><p>";
					echo _e ( 'Update successfully', 'awesome-newsletter' );
				echo "</p></div>";
			}			
		}
		wp_die();
	}

	// delete email from the email list tab
	public function aw_email_list_tab_action () { 
		// get the nonce value
		$aw_nonce  				=	$_POST['s'];
		// verify the nonce first
		if ( wp_verify_nonce ( $aw_nonce, 'aw_email_list_tab' ) ) {
			global $wpdb;
			$aw_email_id		=	absint( $_POST['id'] );
			$aw_table_prefix 	=	$wpdb->prefix;
			$aw_table_name		=	$aw_table_prefix . 'awesome_nletter_email';
			$aw_where  			=	array (
				'id'	=>	$aw_email_id
			);
			$aw_where_format 	=	array ( '%d' );
			$aw_output 			=	array();
			if ( $wpdb->delete( $aw_table_name, $aw_where, $aw_where_format ) ) {
				$aw_output['deleted']	=	__( 'Removed.', 'awesome-newsletter');				
			} else {
				$aw_output['deleted']	=	__( 'Email cant removing.', 'awesome-newsletter');
			}
			echo json_encode( $aw_output );
			wp_die();
		}
	}
	// set html type email
	public function aw_mail_content_type () {
		 return "text/html";
	}	
	// set the email header from email address
	public function aw_mail_from () {
		$aw_header_email 	=	empty ( get_option ( 'aw_header_email' ) ) ? get_bloginfo( 'admin_email' ) : get_option( 'aw_header_email' );
		return $aw_header_email;
	}
	// set the email header name
	public function aw_mail_from_name () {
		$aw_header_name 	=	empty ( get_option ( 'aw_header_name' ) ) ? get_bloginfo( 'name' ) : get_option( 'aw_header_name' );
		return $aw_header_name;
	}
}