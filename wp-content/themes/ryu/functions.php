<?php
/**
 * Ryu functions and definitions
 *
 * @package Ryu
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 696; /* pixels */

/*
 * Load Jetpack compatibility file.
 */
require( get_template_directory() . '/inc/jetpack.php' );

if ( ! function_exists( 'ryu_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function ryu_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	require( get_template_directory() . '/inc/extras.php' );

	/**
	 * Customizer additions
	 */
	require( get_template_directory() . '/inc/customizer.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on ryu, use a find and replace
	 * to change 'ryu' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'ryu', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Adding several sizes for Post Thumbnails
	 */
	add_image_size( 'ryu-featured-thumbnail', 1272, 0 );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ryu' ),
	) );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'gallery' ) );
}
endif; // ryu_setup
add_action( 'after_setup_theme', 'ryu_setup' );

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for WordPress 3.3
 * using feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Remove the 3.3 support when WordPress 3.6 is released.
 *
 * Hooks into the after_setup_theme action.
 */
function ryu_register_custom_background() {
	$args = array(
		'default-color' => 'fff',
		'default-image' => '',
	);

	$args = apply_filters( 'ryu_custom_background_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-background', $args );
	} else {
		define( 'BACKGROUND_COLOR', $args['default-color'] );
		if ( ! empty( $args['default-image'] ) )
			define( 'BACKGROUND_IMAGE', $args['default-image'] );
		add_custom_background();
	}
}
add_action( 'after_setup_theme', 'ryu_register_custom_background' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function ryu_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Top Widget Area One', 'ryu' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Top Widget Area Two', 'ryu' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Top Widget Area Three', 'ryu' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );

	register_sidebar( array(
		'name'          => __( 'Top Widget Area Four', 'ryu' ),
		'id'            => 'sidebar-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'ryu_widgets_init' );

/**
 * Register Google fonts for Ryu
 */
function ryu_fonts() {
	/* translators: If there are characters in your language that are not supported
	   by Lato, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'ryu' ) ) {

		$protocol = is_ssl() ? 'https' : 'http';

		wp_register_style( 'ryu-lato', "$protocol://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic", array(), null );
	}

	/* translators: If there are characters in your language that are not supported
	   by Playfair Display, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Playfair Display font: on or off', 'ryu' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Playfair Display character subset specific to your language, translate this to 'cyrillic'. Do not translate into your own language. */
		$subset = _x( 'no-subset', 'Playfair Display font: add new subset (cyrillic)', 'ryu' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Playfair+Display:400,700,900,400italic,700italic,900italic',
			'subset' => $subsets,
		);
		wp_register_style( 'ryu-playfair-display', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}
}
add_action( 'init', 'ryu_fonts' );

/**
 * Enqueue scripts and styles
 */
function ryu_scripts() {
	wp_enqueue_style( 'ryu-style', get_stylesheet_uri() );

	wp_enqueue_style( 'ryu-lato' );

	wp_enqueue_style( 'ryu-playfair-display' );

	if ( has_nav_menu( 'primary' ) ) {
		wp_enqueue_script( 'ryu-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	}

	wp_enqueue_script( 'ryu-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'ryu-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
	wp_enqueue_script( 'ryu-theme', get_template_directory_uri() . '/js/ryu.js', array( 'jquery' ), '20130319', true );
}
add_action( 'wp_enqueue_scripts', 'ryu_scripts' );

/**
 * Enqueue Google fonts style to admin screen for custom header display.
 */
function ryu_admin_fonts( $hook_suffix ) {
	if ( 'appearance_page_custom-header' != $hook_suffix )
		return;

	wp_enqueue_style( 'ryu-lato' );

	wp_enqueue_style( 'ryu-playfair-display' );
}
add_action( 'admin_enqueue_scripts', 'ryu_admin_fonts' );

/**
 * Implement the Custom Header feature
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function ryu_top_sidebar_class() {

	$count = 0;

	if ( is_active_sidebar( 'sidebar-1' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-2' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
		case '4':
			$class = 'four';
			break;
	}

	if ( $class )
		echo 'class="wrap clear ' . $class . '"';
}
/**
 * Implement Tonesque
 */
require( get_template_directory() . '/inc/tonesque.php' );

/**
 * Define common regex lookup patterns
 */
if ( ! defined( 'WP_THEMES_IMAGE_REGEX' ) )
	define( 'WP_THEMES_IMAGE_REGEX', '/(<img.+src=[\'"]([^\'"]+)[\'"].*?>)/i' );

if ( ! function_exists( 'ryu_image_grabber' ) ):
/**
 * Return the HTML output for first image found for a post.
 *
 * @param int post_id ID for parent post
 * @param string the_content
 * @return boolean|string image url or false if no match
 */
function ryu_image_grabber( $post_id, $the_content = '' ) {
	global $wpdb;
	$image_src = '';
	if ( empty( $the_content ) )
		$the_content = get_the_content();

	$first_image = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'attachment' AND INSTR(post_mime_type, 'image') ORDER BY menu_order ASC LIMIT 0,1", (int) $post_id ) );

	// Try to get the image for the linked image (not attached)
	$output = preg_match( WP_THEMES_IMAGE_REGEX, $the_content, $matches );

	if ( isset( $matches[2] ) )
		$image_src = $matches[2];

	if ( ! empty( $image_src ) )
		return $image_src;

	return false;
}
endif; // Check ryu_image_grabber()