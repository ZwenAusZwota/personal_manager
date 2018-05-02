<?php
/**
 * Plugin Name: Personal Manager
 * Plugin URI:  https://github.com/ZwenAusZwota/personal_manager
 * Description: A manager for knowledge of relations between people
 * Version:     0.0.1
 * Author:      Sven Schuberth
 * Author URI:  https://zwen-aus-zwota.de
 * Text Domain: personal_manager
 * Domain Path: /lang
 *
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   Personal_Manager
 * @version   0.0.1
 * @author    Sven Schuberth
 * @copyright Copyright (c) 2018 - 2018, Sven Schuberth
 * @link      https://github.com/ZwenAusZwota/personal_manager
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Singleton class for setting up the plugin.
 *
 * @since  1.0.0
 * @access public
 */

define('PERM_PATH',  trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/');

final class PERM {

	/**
	 * Minimum required PHP version.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	private $php_version = '0.0.1';

	/**
	 * Plugin directory path.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $dir = '';

	/**
	 * Plugin directory URI.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $uri = '';

	/**
	 * User count of all roles.
	 *
	 * @see    members_get_role_user_count()
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $role_user_count = array();

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup();
			$instance->setup_menues();
			$instance->includes();
			$instance->setup_actions();
			
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __toString() {
		return 'PERM';
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'members' ), '1.0.0' );
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'perm' ), '1.0.0' );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( "PERM::{$method}", esc_html__( 'Method does not exist.', 'perm' ), '1.0.0' );
		unset( $method, $args );
		return null;
	}

	/**
	 * Sets up globals.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup() {

		// Main plugin directory path and URI.
		$this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );
	}

	/**
	 * Loads files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function includes() {

		// Check if we meet the minimum PHP version.
		if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

			// Add admin notice.
			add_action( 'admin_notices', array( $this, 'php_admin_notice' ) );

			// Bail.
			return;
		}

		// Load class files.
		//require_once( $this->dir . 'admin/forms.php' );
		

		// Load admin files.
		if ( is_admin() ) {

			// General admin functions.
			/*require_once( $this->dir . 'admin/members/class-members.php' );
			require_once( $this->dir . 'admin/contact/class-contact.php' );
			require_once( $this->dir . 'admin/social_media/class-main.php' );
			*/
		}
	}

	/**
	 * Sets up main plugin actions and filters.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Internationalize the text strings used.
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		// Register activation hook.
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		
		//short-link
		add_shortcode( 'perm-form', array( $this, 'render_perm_form' ) );
	}

	private function setup_menues(){
		
		add_action('admin_menu', array($this, 'perm_main_menu'));
		
	}
	
	public function perm_main_menu(){
		add_menu_page(
	        'Personal Manager', //Seiten titel
	        'PERM',  //Menu-Title
	        'manage_options', //berechtigungen
	        'perm', //slug
	        array($this,'perm_start'), //'memberimport_main_page_html',
	        '', /*plugin_dir_url(__FILE__) . 'images/icon_wporg.png',*/
	        20
	    );		
	}
	
	
	public function perm_start(){
		print_r($members);
		
	?>	<div class="wrap">
<? include PERM_PATH. 'header.php'?>
	<h1>DiB-Manager</h1>
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
		<?php 
		do_action('PERM_LOADED');
		?>
		</div></div></div>
	<?php
	}
	
	public function render_perm_form($attributes, $content = null){
		// Parse shortcode attributes
		$default_attributes = array( 'show_title' => false );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		$show_title = $attributes['show_title'];
 
		if ( !is_user_logged_in() ) {
		    return __( 'You have to be logged in to use this function.', 'perm' );
		}
     
		// Render the login form using an external template
		return $this->get_template_html( 'login_form', $attributes );
	}
	
	/**
	* Renders the contents of the given template to a string and returns it.
	*
	* @param string $template_name The name of the template to render (without .php)
	* @param array  $attributes    The PHP variables for the template
	*
	* @return string               The contents of the template.
	*/
	private function get_template_html( $template_name, $attributes = null ) {
	   if ( ! $attributes ) {
		   $attributes = array();
	   }
	
	   ob_start();
	
	   do_action( 'personalize_login_before_' . $template_name );
	
	   require( 'templates/' . $template_name . '.php');
	
	   do_action( 'personalize_login_after_' . $template_name );
	
	   $html = ob_get_contents();
	   ob_end_clean();
	
	   return $html;
   }
	
	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function i18n() {

		load_plugin_textdomain( 'perm', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . 'lang' );
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activation() {

		// Check PHP version requirements.
		if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

			// Make sure the plugin is deactivated.
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// Add an error message and die.
			wp_die( $this->get_min_php_message() );
		}

		// Get the administrator role.
		$role = get_role( 'administrator' );

		// If the administrator role exists, add required capabilities for the plugin.
		/*if ( ! empty( $role ) ) {

			$role->add_cap( 'restrict_content' ); // Edit per-post content permissions.
			$role->add_cap( 'list_roles'       ); // View roles in backend.

			// Do not allow administrators to edit, create, or delete roles
			// in a multisite setup. Super admins should assign these manually.
			if ( ! is_multisite() ) {
				$role->add_cap( 'create_roles' ); // Create new roles.
				$role->add_cap( 'delete_roles' ); // Delete existing roles.
				$role->add_cap( 'edit_roles'   ); // Edit existing roles/caps.
			}
		}*/
	}

	/**
	 * Returns a message noting the minimum version of PHP required.
	 *
	 * @since  2.0.1
	 * @access private
	 * @return void
	 */
	private function get_min_php_message() {

		return sprintf(
			__( 'Members requires PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'members' ),
			$this->php_version,
			PHP_VERSION
		);
	}

	/**
	 * Outputs the admin notice that the user needs to upgrade their PHP version. It also
	 * auto-deactivates the plugin.
	 *
	 * @since  2.0.1
	 * @access public
	 * @return void
	 */
	public function php_admin_notice() {

		// Output notice.
		printf(
			'<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>',
			esc_html( $this->get_min_php_message() )
		);

		// Make sure the plugin is deactivated.
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}

/**
 * Gets the instance of the `Members_Plugin` class.  This function is useful for quickly grabbing data
 * used throughout the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function perm_plugin() {
	return PERM::get_instance();
}

// Let's roll!
perm_plugin();
