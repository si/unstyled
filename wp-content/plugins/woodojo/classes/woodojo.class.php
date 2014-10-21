<?php
/**
 * WooDojo Class
 *
 * The main WooDojo class.
 *
 * @package WordPress
 * @subpackage WooDojo
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * var $version
 * var $base
 * var $admin
 * var $frontend
 * var $settings
 *
 * - __construct()
 * - load_localisation()
 * - activation()
 * - after_activation()
 * - load_plugin_first()
 * - register_plugin_version()
 */
class WooDojo {
	var $file;
	var $version;
	var $base;
	var $admin;
	var $frontend;
	var $settings;
	var $updater;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		$this->version = '';
		$this->file = $file;

		require_once( 'base.class.php' );
		require_once( 'api.class.php' );
		require_once( 'utils.class.php' );
		require_once( 'settings-api.class.php' );
		require_once( 'updater.class.php' );
		
		$this->base = new WooDojo_Base();
		$this->api = new WooDojo_API( $this->base->token );
		$this->updater = new WooDojo_Updater( $file );
		
		add_action( 'init', array( &$this, 'load_localisation' ) );

		if ( is_admin() ) {
			require_once( 'admin.class.php' );
			$this->admin = new WooDojo_Admin();
		} else {
			require_once( 'frontend.class.php' );
			$this->frontend = new WooDojo_Frontend();
		}

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );
		add_action( 'activated_plugin', array( &$this, 'after_activation' ), 10, 2 );
		// register_activation_hook();
	} // End __construct()

	/**
	 * load_localisation function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		$lang_dir = trailingslashit( str_replace( 'classes', 'lang', basename( dirname(__FILE__) ) ) );
		load_plugin_textdomain( 'woodojo', false, $lang_dir );
	} // End load_localisation()

	/**
	 * activation function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * after_activation function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function after_activation ( $plugin, $network_wide ) {
		if ( $plugin == plugin_basename( trim( $this->file ) ) ) {
			$this->load_plugin_first();

			remove_action( 'activated_plugin', array( &$this, 'after_activation' ), 10, 2 );
		}
	} // End after_activation()

	/**
	 * load_plugin_first function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_plugin_first () {
	    $this_plugin = plugin_basename( trim( $this->file ) );
	    $active_plugins = get_option( 'active_plugins' );
	    $this_plugin_key = array_search( $this_plugin, $active_plugins );
	    if ( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue
	    	array_splice( $active_plugins, $this_plugin_key, 1 );
	    	array_unshift( $active_plugins, $this_plugin );
	    	update_option( 'active_plugins', $active_plugins );
	    }
	} // End load_plugin_first()

	/**
	 * register_plugin_version function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->base->token . '-version', $this->version );
		}
	} // End register_plugin_version()
}
?>