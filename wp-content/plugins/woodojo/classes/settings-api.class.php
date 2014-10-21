<?php
/**
 * WooDojo Settings API Class
 *
 * A settings API (wrapping the WordPress Settings API) for use with WooDojo components.
 *
 * @package WordPress
 * @subpackage WooDojo
 * @category Settings
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * var $token
 * var $page_slug
 * var $screens_path
 * var $name
 * var $menu_label
 * var $settings
 * var $sections
 * var $fields
 * var $errors
 * 
 * private $has_colourpicker
 *
 * - __construct()
 * - setup_settings()
 * - init_sections()
 * - init_fields()
 * - create_sections()
 * - create_fields()
 * - determine_method()
 * - parse_fields()
 * - register_settings_screen()
 * - settings_screen()
 * - get_settings()
 * - settings_fields()
 * - settings_errors()
 * - settings_description()
 * - form_field_text()
 * - form_field_checkbox()
 * - form_field_textarea()
 * - form_field_select()
 * - form_field_radio()
 * - form_field_multicheck()
 * - form_field_colourpicker()
 * - form_field_info()
 * - validate_fields()
 * - validate_field_text()
 * - validate_field_checkbox()
 * - validate_field_multicheck()
 * - validate_field_colourpicker()
 * - validate_field_url()
 * - add_error()
 * - parse_errors()
 * - get_array_field_types()
 * - enqueue_scripts()
 */
class WooDojo_Settings_API {
	var $token;
	var $page_slug;
	var $screens_path;
	var $name;
	var $menu_label;
	var $settings;
	var $sections;
	var $fields;
	var $errors;

	private $has_colourpicker;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct () {
		global $woodojo;
		$this->token = 'woodojo';
		$this->page_slug = 'woodojo-settings-api';
		$this->screens_path = $woodojo->base->screens_path;
		
		$this->sections = array();
		$this->fields = array();
		$this->remaining_fields = array();
		$this->errors = array();

		$this->has_colourpicker = false;
	} // End __construct()
	
	/**
	 * setup_settings function.
	 * 
	 * @access public
	 * @return void
	 */
	public function setup_settings () {
		add_action( 'admin_menu', array( &$this, 'register_settings_screen' ) );
		add_action( 'admin_init', array( &$this, 'settings_fields' ) );
		add_action( 'admin_print_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_print_styles', array( &$this, 'enqueue_styles' ) );
		
		$this->init_sections();
		$this->init_fields();
		$this->get_settings();
	} // End setup_settings()
	
	/**
	 * init_sections function.
	 * 
	 * @access public
	 * @return void
	 */
	public function init_sections () {
		// Override this function in your class and assign the array of sections to $this->sections.
		_e( 'Override init_sections() in your class.', 'woodojo' );
	} // End init_sections()
	
	/**
	 * init_fields function.
	 * 
	 * @access public
	 * @return void
	 */
	public function init_fields () {
		// Override this function in your class and assign the array of sections to $this->fields.
		_e( 'Override init_fields() in your class.', 'woodojo' );
	} // End init_fields()
	
	/**
	 * create_sections function.
	 * 
	 * @access public
	 * @return void
	 */
	public function create_sections () {
		if ( count( $this->sections ) > 0 ) {
			foreach ( $this->sections as $k => $v ) {
				add_settings_section( $k, $v['name'], array( &$this, 'section_description' ), $this->token );
			}
		}
	} // End create_sections()
	
	/**
	 * create_fields function.
	 * 
	 * @access public
	 * @return void
	 */
	public function create_fields () {
		if ( count( $this->sections ) > 0 ) {
			// $this->parse_fields( $this->fields );
			
			foreach ( $this->fields as $k => $v ) {
				$method = $this->determine_method( $v, 'form' );
				$name = $v['name'];
				if ( $v['type'] == 'info' ) { $name = ''; }
				add_settings_field( $k, $name, $method, $this->token, $v['section'], array( 'key' => $k, 'data' => $v ) );

				// Let the API know that we have a colourpicker field.
				if ( $v['type'] == 'colourpicker' && $this->has_colourpicker == false ) { $this->has_colourpicker = true; }
			}
		}
	} // End create_fields()
	
	/**
	 * determine_method function.
	 * 
	 * @access protected
	 * @param array $data
	 * @return array or string
	 */
	protected function determine_method ( $data, $type = 'form' ) {
		$method = '';
		
		if ( ! in_array( $type, array( 'form', 'validate' ) ) ) { return; }
		
		// Check for custom functions.
		if ( isset( $data[$type] ) ) {
			if ( function_exists( $data[$type] ) ) {
				$method = $data[$type];
			}
			
			if ( $method == '' && method_exists( $this, $data[$type] ) ) {
				if ( $type == 'form' ) {
					$method = array( &$this, $data[$type] );
				} else {
					$method = $data[$type];
				}
			}
		}
		
		if ( $method == '' && method_exists ( $this, $type . '_field_' . $data['type'] ) ) {
			if ( $type == 'form' ) {
				$method = array( &$this, $type . '_field_' . $data['type'] );
			} else {
				$method = $type . '_field_' . $data['type'];
			}
		}
		
		if ( $method == '' && function_exists ( $this->token . '_' . $type . '_field_' . $data['type'] ) ) {
			$method = $this->token . '_' . $type . '_field_' . $data['type'];
		}
		
		if ( $method == '' ) {
			if ( $type == 'form' ) {
				$method = array( &$this, $type . '_field_text' );
			} else {
				$method = $type . '_field_text';
			}
		}
		
		return $method;
	} // End determine_method()
	
	/**
	 * parse_fields function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $fields
	 * @return void
	 */
	public function parse_fields ( $fields ) {
		foreach ( $fields as $k => $v ) {
			if ( isset( $v['section'] ) && ( $v['section'] != '' ) && ( isset( $this->sections[$v['section']] ) ) ) {
				if ( ! isset( $this->sections[$v['section']]['fields'] ) ) {
					$this->sections[$v['section']]['fields'] = array();
				}
				
				$this->sections[$v['section']]['fields'][$k] = $v;
			} else {
				$this->remaining_fields[$k] = $v;
			}
		}
	} // End parse_fields()
	
	/**
	 * register_settings_screen function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings_screen () {
		global $woodojo;
		
		$hook = add_submenu_page( 'woodojo', $this->name, $this->menu_label, 'manage_options', $this->page_slug, array( &$this, 'settings_screen' ) );
		
		$this->hook = $hook;

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == $this->page_slug ) ) {
			add_action( 'admin_notices', array( &$this, 'settings_errors' ) );
		}
		add_action( 'admin_print_styles', array( $woodojo->admin, 'admin_styles' ) );
	} // End register_settings_screen()
	
	/**
	 * settings_screen function.
	 * 
	 * @access public
	 * @return void
	 */
	public function settings_screen () {
		require_once( $this->screens_path . 'settings-api.php' );
	} // End settings_screen()
	
	/**
	 * get_settings function.
	 * 
	 * @access public
	 * @return void
	 */
	public function get_settings () {
		if ( ! is_array( $this->settings ) ) {
			$this->settings = get_option( $this->token, array() );
		}
		
		foreach ( $this->fields as $k => $v ) {
			if ( ! isset( $this->settings[$k] ) && isset( $v['default'] ) ) {
				$this->settings[$k] = $v['default'];
			}
		}
		
		return $this->settings;
	} // End get_settings()
	
	/**
	 * settings_fields function.
	 * 
	 * @access public
	 * @return void
	 */
	public function settings_fields () {
		register_setting( $this->token, $this->token, array( &$this, 'validate_fields' ) );
		$this->create_sections();
		$this->create_fields();
	} // End settings_fields()
	
	/**
	 * settings_errors function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_errors () {
		echo settings_errors( $this->token . '-errors' );
	} // End settings_errors()
	
	/**
	 * section_description function.
	 * 
	 * @access public
	 * @return void
	 */
	public function section_description ( $section ) {
		if ( isset( $this->sections[$section['id']]['description'] ) ) {
			echo wpautop( $this->sections[$section['id']]['description'] );
		}
	} // End section_description_main()
	
	/**
	 * form_field_text function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_text ( $args ) {
		$options = $this->get_settings();

		echo '<input id="' . $args['key'] . '" name="' . $this->token . '[' . $args['key'] . ']" size="40" type="text" value="' . $options[$args['key']] . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<span class="description">' . $args['data']['description'] . '</span>' . "\n";
		}
	} // End form_field_text()
	
	/**
	 * form_field_checkbox function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_checkbox ( $args ) {
		$options = $this->get_settings();

		$has_description = false;
		if ( isset( $args['data']['description'] ) ) {
			$has_description = true;
			echo '<label for="' . $this->token . '[' . $args['key'] . ']">' . "\n";
		}
		echo '<input id="' . $args['key'] . '" name="' . $this->token . '[' . $args['key'] . ']" type="checkbox" value="1"' . checked( $options[$args['key']], '1', false ) . ' />' . "\n";
		if ( $has_description ) {
			echo $args['data']['description'] . '</label>' . "\n";
		}
	} // End form_field_text()
	
	/**
	 * form_field_textarea function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_textarea ( $args ) {
		$options = $this->get_settings();

		echo '<textarea id="' . $args['key'] . '" name="' . $this->token . '[' . $args['key'] . ']" cols="42" rows="5">' . $options[$args['key']] . '</textarea>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . $args['data']['description'] . '</span></p>' . "\n";
		}
	} // End form_field_textarea()
	
	/**
	 * form_field_select function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_select ( $args ) {
		$options = $this->get_settings();
		
		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . $args['key'] . '" name="' . $this->token . '[' . $args['key'] . ']">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( $options[$args['key']], $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;
			
			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . $args['data']['description'] . '</span></p>' . "\n";
			}
		}
	} // End form_field_select()
	
	/**
	 * form_field_radio function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_radio ( $args ) {
		$options = $this->get_settings();
		
		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . $this->token . '[' . $args['key'] . ']" value="' . esc_attr( $k ) . '"' . checked( $options[$args['key']], $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;
			
			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . $args['data']['description'] . '</span>' . "\n";
			}
		}
	} // End form_field_radio()
	
	/**
	 * form_field_multicheck function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_multicheck ( $args ) {
		$options = $this->get_settings();
		
		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $k, (array)$options[$args['key']] ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . $this->token . '[' . $args['key'] . '][]" value="' . esc_attr( $k ) . '"' . $checked . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;
			
			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . $args['data']['description'] . '</span>' . "\n";
			}
		}
	} // End form_field_multicheck()

	/**
	 * form_field_colourpicker function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_colourpicker ( $args ) {
		$options = $this->get_settings();

		$value = $options[$args['key']];
		if ( $value == '' ) { $value = '#000000'; }

		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . $args['key'] . ']" size="26" maxlength="7" class="colourpicker-input" type="text" value="' . $value . '" />' . "\n";
		echo '<input type="button" id="select-' . esc_attr( $args['key'] ) . '" class="button" value="' . esc_attr__( 'Select Colour', 'woodojo' ) . '" />' . "\n";

		echo '<a href="#" class="pickcolor hide-if-no-js" id="' . esc_attr( $args['key'] ) . '-example"></a>';
		
		if ( isset( $args['data']['default'] ) && ( $args['data']['default'] != '' ) ) {
			echo '<br /><span id="default-' . esc_attr( $args['key'] ) . '" class="default-colour">' . __( 'Default colour:', 'woodojo' ) . ' <span class="colour">' . strtolower( $args['data']['default'] ) . '</span></span>' . "\n";
		}

		echo '<div class="picker-div" id="picker-' . esc_attr( $args['key'] ) . '"></div>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p class="description">' . $args['data']['description'] . '</p>' . "\n";
		}
	} // End form_field_colourpicker()

	/**
	 * form_field_info function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_info ( $args ) {
		$class = '';
		if ( isset( $args['data']['class'] ) ) {
			$class = ' ' . esc_attr( $args['data']['class'] );
		}
		$html = '<div id="' . $args['key'] . '" class="info-box' . $class . '">' . "\n";
		if ( isset( $args['data']['name'] ) && ( $args['data']['name'] != '' ) ) {
			$html .= '<h3 class="title">' . $args['data']['name'] . '</h3>' . "\n";
		}
		if ( isset( $args['data']['description'] ) && ( $args['data']['description'] != '' ) ) {
			$html .= '<p>' . $args['data']['description'] . '</p>' . "\n";
		}
		$html .= '</div>' . "\n";

		echo $html;
	} // End form_field_info()

	/**
	 * validate_fields function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param array $input
	 * @uses $this->parse_errors()
	 * @return array $options
	 */
	public function validate_fields ( $input ) {
		$options = $this->get_settings();
		
		foreach ( $this->fields as $k => $v ) {
			// Make sure checkboxes are present even when false.
			if ( $v['type'] == 'checkbox' && ! isset( $input[$k] ) ) { $input[$k] = false; }
			
			if ( isset( $input[$k] ) ) {
				// Perform checks on required fields.
				if ( isset( $v['required'] ) && ( $v['required'] == true ) ) {
					if ( in_array( $v['type'], $this->get_array_field_types() ) && ( count( (array) $input[$k] ) <= 0 ) ) {
						$this->add_error( $k, $v );
						continue;
					} else {
						if ( $input[$k] == '' ) {
							$this->add_error( $k, $v );
							continue;
						}
					}
				}
				$method = $this->determine_method( $v, 'validate' );
				if ( function_exists ( $method ) ) {
					$options[$k] = $method( $input[$k] );
				} else {
					if ( method_exists( $this, $method ) ) {
						$options[$k] = $this->$method( $input[$k] );
					}
				}
			}
		}
		
		// Parse error messages into the Settings API.
		$this->parse_errors();
		return $options;
	} // End validate_fields()
	
	/**
	 * validate_field_text function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_text ( $input ) {
		return trim( esc_attr( $input ) );
	} // End validate_field_text()
	
	/**
	 * validate_field_checkbox function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_checkbox ( $input ) {
		if ( ! isset( $input ) ) {
			return 0;
		} else {
			return (bool)$input;
		}
	} // End validate_field_checkbox()
	
	/**
	 * validate_field_multicheck function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_multicheck ( $input ) {
		$input = (array) $input;
		
		$input = array_map( 'esc_attr', $input );
		
		return $input;
	} // End validate_field_multicheck()
	
	/**
	 * validate_field_colourpicker function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_colourpicker ( $input ) {
		// Colour must be 3 or 6 hexadecimal characters
		if ( isset( $input ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input ) ) {
			$input = '#' . strtolower( ltrim( $input, '#' ) );
		} else {
			$input = '';
		}

		return $input;
	} // End validate_field_colourpicker()

	/**
	 * validate_field_url function.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_url ( $input ) {
		return trim( esc_url( $input ) );
	} // End validate_field_url()

	/**
	 * add_error function.
	 * 
	 * @access protected
	 * @since 1.0.0
	 * @param string $key
	 * @param array $data
	 * @return void
	 */
	protected function add_error ( $key, $data ) {
		if ( isset( $data['error_message'] ) ) {
			$message = $data['error_message'];
		} else {
			$message = sprintf( __( '%s is a required field', 'woodojo' ), $data['name'] );
		}
		$this->errors[$key] = $message;
	} // End add_error()
	
	protected function parse_errors () {
		if ( count ( $this->errors ) > 0 ) {
			foreach ( $this->errors as $k => $v ) {
				add_settings_error( $this->token . '-errors', $k, $v, 'error' );
			}
		} else {
			$message = sprintf( __( '%s settings updated', 'woodojo' ), $this->name );
			add_settings_error( $this->token . '-errors', $this->token, $message, 'updated' );
		}
	} // End parse_errors()
	
	/**
	 * get_array_field_types function.
	 *
	 * @description Return an array of field types expecting an array value returned.
	 * @access protected
	 * @since 1.0.0
	 * @return void
	 */
	protected function get_array_field_types () {
		return array( 'multicheck' );
	} // End get_array_field_types()

	/**
	 * enqueue_scripts function.
	 *
	 * @description Load in JavaScripts where necessary.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		global $woodojo;
		if ( $this->has_colourpicker ) {
			wp_enqueue_script( $this->token . '-colourpickers', $woodojo->base->assets_url . 'js/colourpickers.js', array( 'farbtastic' ), '1.0.0' );
		}
	} // End enqueue_scripts()

	/**
	 * enqueue_styles function.
	 *
	 * @description Load in CSS styles where necessary.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $woodojo;
		if ( $this->has_colourpicker ) {
			wp_enqueue_style( $this->token . '-colourpickers', $woodojo->base->assets_url . 'css/colourpickers.css', '', '1.0.0' );
			wp_enqueue_style( 'farbtastic' );
		}

		wp_enqueue_style( $woodojo->base->token . '-admin' );

		wp_enqueue_style( $this->token . '-settings-api', $woodojo->base->assets_url . 'css/settings.css', '', '1.0.0' );
	} // End enqueue_styles()
}
?>