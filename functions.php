<?php
/**
 * Plumber functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Plumber
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function plumber_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Plumber, use a find and replace
		* to change 'plumber' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'plumber', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'plumber' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'plumber_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'plumber_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function plumber_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'plumber_content_width', 640 );
}
add_action( 'after_setup_theme', 'plumber_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


//custom code

//connect styles and scripts
function cyberrete_enqueue_styles_and_scripts() {
	$css_path     = get_template_directory() . '/src/css/main.css';
	$js_path      = get_template_directory() . '/src/js/main.js';
	$data_json    = get_template_directory() . '/data.json';
	$css_version  = file_exists( $css_path ) ? filemtime( $css_path ) : null;
	$js_version   = file_exists( $js_path ) ? filemtime( $js_path ) : null;
	$data_version = file_exists( $data_json ) ? filemtime( $data_json ) : null;

	wp_enqueue_style(
		'plumber-fonts',
		'https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Work+Sans:wght@500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'swiper-css',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		array(),
		'11.0.0'
	);
	wp_enqueue_style(
		'sf-ui-display-font',
		'https://fonts.cdnfonts.com/css/sf-ui-display',
		array(),
		null
	);
	wp_enqueue_style( 'plumber-main-css', get_template_directory_uri() . '/src/css/main.css', array( 'plumber-fonts', 'sf-ui-display-font' ), $css_version, 'all' );
	wp_enqueue_script(
		'swiper-js',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
		array(),
		'11.0.0',
		true
	);
	wp_enqueue_script(
		'lottie-web',
		'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js',
		array(),
		'5.12.2',
		true
	);
	wp_enqueue_script( 'plumber-main-js', get_template_directory_uri() . '/src/js/main.js', array( 'swiper-js', 'lottie-web' ), $js_version, true );

	wp_localize_script(
		'plumber-main-js',
		'plumberTheme',
		array(
			'initialDataUrl' => add_query_arg(
				array( 'ver' => $data_version ),
				get_template_directory_uri() . '/data.json'
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cyberrete_enqueue_styles_and_scripts' );

// add acf content
require get_template_directory() . '/inc/theme-acf.php';
if ( ! function_exists( 'mytheme_register_nav_menu' ) ) {

	function mytheme_register_nav_menu() {
		register_nav_menus(
			array(
				'Main-menu'        => __( 'Primary Menu', 'plumber' ),
				'Main-footer-menu' => __( 'Footer Menu', 'plumber' ),
				'footer_menu_1'    => __( 'Footer — Menu 1', 'plumber' ),
				'footer_menu_2'    => __( 'Footer — Menu 2', 'plumber' ),
			)
		);
	}
	add_action( 'after_setup_theme', 'mytheme_register_nav_menu', 0 );
}

//add svg file
function allow_svg_uploads( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_uploads' );