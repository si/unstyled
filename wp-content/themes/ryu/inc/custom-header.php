<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * @package Ryu
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Rework this function to remove WordPress 3.4 support when WordPress 3.6 is released.
 *
 * @uses ryu_header_style()
 * @uses ryu_admin_header_style()
 * @uses ryu_admin_header_image()
 *
 * @package Ryu
 */
function ryu_custom_header_setup() {
	$args = array(
		'default-image'          => ryu_get_default_header_image(),
		'default-text-color'     => '000',
		'width'                  => 120,
		'height'                 => 120,
		'flex-width'             => true,
		'flex-height'            => true,
		'wp-head-callback'       => 'ryu_header_style',
		'admin-head-callback'    => 'ryu_admin_header_style',
		'admin-preview-callback' => 'ryu_admin_header_image',
	);

	$args = apply_filters( 'ryu_custom_header_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-header', $args );
	} else {
		// Compat: Versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR',    $args['default-text-color'] );
		define( 'HEADER_IMAGE',        $args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $args['height'] );
		add_custom_image_header( $args['wp-head-callback'], $args['admin-head-callback'], $args['admin-preview-callback'] );
	}
}
add_action( 'after_setup_theme', 'ryu_custom_header_setup' );

/**
 * Shiv for get_custom_header().
 *
 * get_custom_header() was introduced to WordPress
 * in version 3.4. To provide backward compatibility
 * with previous versions, we will define our own version
 * of this function.
 *
 * @todo Remove this function when WordPress 3.6 is released.
 * @return stdClass All properties represent attributes of the curent header image.
 *
 * @package Ryu
 */

if ( ! function_exists( 'get_custom_header' ) ) {
	function get_custom_header() {
		return (object) array(
			'url'           => get_header_image(),
			'thumbnail_url' => get_header_image(),
			'width'         => HEADER_IMAGE_WIDTH,
			'height'        => HEADER_IMAGE_HEIGHT,
		);
	}
}

/**
 * A default header image
 *
 * Use the admin email's gravatar as the default header image.
 */
function ryu_get_default_header_image() {

	// Get default from Discussion Settings.
	$default = get_option( 'avatar_default', 'mystery' ); // Mystery man default
	if ( 'mystery' == $default )
		$default = 'mm';
	elseif ( 'gravatar_default' == $default )
		$default = '';

	$url = ( is_ssl() ) ? 'https://secure.gravatar.com' : 'http://gravatar.com';
	$url .= sprintf( '/avatar/%s/', md5( get_option( 'admin_email' ) ) );
	$url = add_query_arg( array(
		's' => 120,
		'd' => urlencode( $default ),
	), $url );

	return esc_url_raw( $url );
} // ryu_get_default_header_image

if ( ! function_exists( 'ryu_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see ryu_custom_header_setup().
 */
function ryu_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
		.header-image {
			margin-bottom: 0;
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // ryu_header_style

if ( ! function_exists( 'ryu_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see ryu_custom_header_setup().
 */
function ryu_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
		text-align: center;
	}
	<?php if ( ! display_header_text() ) : ?>
	#headimg h1,
	#desc {
		position: absolute !important;
		clip: rect(1px 1px 1px 1px); /* IE7 */
		clip: rect(1px, 1px, 1px, 1px);
	}
	#headimg img {
		margin-bottom: 0;
	}
	<?php endif; ?>
	#headimg h1 {
		font: 700 24px/1.4166666666 Lato, sans-serif;
		letter-spacing: 0.1em;
		margin: 0;
		text-transform: uppercase;
	}
	#headimg h1 a {
		text-decoration: none;
	}
	#desc {
		font: italic 400 14px/2.4285714285 'Playfair Display', serif;
	}
	#headimg img {
		margin-bottom: 16px;
	}
	#headimg img[src*="gravatar"] {
		border-radius: 50%;
	}
	</style>
<?php
}
endif; // ryu_admin_header_style

if ( ! function_exists( 'ryu_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see ryu_custom_header_setup().
 */
function ryu_admin_header_image() { ?>
	<div id="headimg">
		<?php $style = ' style="color:#' . get_header_textcolor() . ';"'; ?>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" class="no-grav" alt="" />
		<?php endif; ?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
<?php }
endif; // ryu_admin_header_image