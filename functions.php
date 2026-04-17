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
    $css_path     = get_template_directory() . '/dist/main.min.css';
    $js_path      = get_template_directory() . '/dist/main.min.js';
    $data_json    = get_template_directory() . '/data.json';
    $css_version  = file_exists( $css_path ) ? filemtime( $css_path ) : null;
    $js_version   = file_exists( $js_path ) ? filemtime( $js_path ) : null;
    $data_version = file_exists( $data_json ) ? filemtime( $data_json ) : null;
    $should_load_lottie = is_front_page() && ! wp_is_mobile();
    $google_fonts_url   = add_query_arg(
        array(
            'family'  => 'Nunito:wght@400;500;600;700;800|Work+Sans:wght@500;600;700;800|Geist:wght@400;500;600;700',
            'display' => 'swap',
        ),
        'https://fonts.googleapis.com/css2'
    );

    wp_enqueue_style(
        'plumber-fonts',
        $google_fonts_url,
        array(),
        null
    );
    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11.0.0'
    );
    wp_enqueue_style( 'plumber-main-css', get_template_directory_uri() . '/dist/main.min.css', array( 'plumber-fonts' ), $css_version, 'all' );
    
    if ( $should_load_lottie ) {
        wp_enqueue_script(
            'lottie-web',
            'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js',
            array(),
            '5.12.2',
            true
        );
        wp_script_add_data( 'lottie-web', 'defer', true );
    }

    $main_js_dependencies = array();
    if ( $should_load_lottie ) {
        $main_js_dependencies[] = 'lottie-web';
    }

    wp_enqueue_script( 'plumber-main-js', get_template_directory_uri() . '/dist/main.min.js', $main_js_dependencies, $js_version, true );
    wp_script_add_data( 'plumber-main-js', 'defer', true );

    wp_localize_script(
        'plumber-main-js',
        'plumberTheme',
        array(
            'initialDataUrl' => add_query_arg(
                array( 'ver' => $data_version ),
                get_template_directory_uri() . '/data.json'
            ),
            'swiperBundleUrl' => 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        )
    );
}
add_action( 'wp_enqueue_scripts', 'cyberrete_enqueue_styles_and_scripts' );

function plumber_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' !== $relation_type ) {
        return $urls;
    }

    $urls[] = array(
        'href'        => 'https://fonts.googleapis.com',
        'crossorigin' => 'anonymous',
    );
    $urls[] = array(
        'href'        => 'https://fonts.gstatic.com',
        'crossorigin' => 'anonymous',
    );
    $urls[] = array(
        'href'        => 'https://cdn.jsdelivr.net',
        'crossorigin' => 'anonymous',
    );
    $urls[] = array(
        'href'        => 'https://cdnjs.cloudflare.com',
        'crossorigin' => 'anonymous',
    );

    return $urls;
}
add_filter( 'wp_resource_hints', 'plumber_resource_hints', 10, 2 );

function plumber_optimize_frontend_assets() {
    if ( is_admin() ) {
        return;
    }

    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );

    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }
}
add_action( 'wp_enqueue_scripts', 'plumber_optimize_frontend_assets', 100 );

function plumber_disable_unused_wp_frontend_scripts() {
    if ( is_admin() ) {
        return;
    }

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    wp_deregister_script( 'wp-embed' );
}
add_action( 'init', 'plumber_disable_unused_wp_frontend_scripts' );

function plumber_page_uses_contact_integrations() {
    if ( is_admin() ) {
        return true;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return false;
    }

    $post = get_post( $post_id );
    if ( $post instanceof WP_Post ) {
        $content = (string) $post->post_content;
        if (
            has_shortcode( $content, 'contact-form-7' ) ||
            has_shortcode( $content, 'wpcf7' ) ||
            has_shortcode( $content, 'wpgmza' ) ||
            has_shortcode( $content, 'wpgmza_map' ) ||
            has_shortcode( $content, 'leaflet-map' )
        ) {
            return true;
        }
    }

    if ( function_exists( 'have_rows' ) && have_rows( 'blocks', $post_id ) ) {
        while ( have_rows( 'blocks', $post_id ) ) {
            the_row();
            $layout = (string) get_row_layout();
            if ( in_array( $layout, array( 'contact', 'contact_page' ), true ) ) {
                if ( function_exists( 'reset_rows' ) ) {
                    reset_rows();
                }
                return true;
            }
        }
        if ( function_exists( 'reset_rows' ) ) {
            reset_rows();
        }
    }

    return false;
}

function plumber_page_uses_swiper() {
    if ( is_front_page() || is_singular( 'our-services' ) ) {
        return true;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id || ! function_exists( 'have_rows' ) ) {
        return false;
    }

    if ( have_rows( 'blocks', $post_id ) ) {
        while ( have_rows( 'blocks', $post_id ) ) {
            the_row();
            $layout = (string) get_row_layout();
            if ( in_array( $layout, array( 'why_choose', 'our_services', 'services_page' ), true ) ) {
                if ( function_exists( 'reset_rows' ) ) {
                    reset_rows();
                }
                return true;
            }
        }
        if ( function_exists( 'reset_rows' ) ) {
            reset_rows();
        }
    }

    return false;
}

function plumber_is_excluded_heavy_asset_src( $src ) {
    $asset_markers = array(
        '/atlas-novus/components.css',
        '/atlas-novus/common.css',
        '/atlas-novus/compat.css',
        '/remodal-default-theme.css',
        '/remodal.css',
        '/jquery.dataTables.min.css',
        '/font-awesome.min.css',
        '/open-layers-latest.css',
        '/polyfill/fa-',
        '/atlas-novus',
        '/open-layers',
        '/leaflet',
        '/wpgmza',
        '/datatables',
        '/remodal',
    );

    foreach ( $asset_markers as $marker ) {
        if ( false !== strpos( $src, $marker ) ) {
            return true;
        }
    }

    return false;
}

function plumber_conditionally_skip_heavy_plugin_styles( $src, $handle ) {
    if ( is_admin() || plumber_page_uses_contact_integrations() ) {
        return $src;
    }

    if ( plumber_is_excluded_heavy_asset_src( (string) $src ) ) {
        return false;
    }

    return $src;
}
add_filter( 'style_loader_src', 'plumber_conditionally_skip_heavy_plugin_styles', 20, 2 );

function plumber_conditionally_skip_heavy_plugin_scripts( $src, $handle ) {
    if ( is_admin() || plumber_page_uses_contact_integrations() ) {
        return $src;
    }

    if ( plumber_is_excluded_heavy_asset_src( (string) $src ) ) {
        return false;
    }

    return $src;
}
add_filter( 'script_loader_src', 'plumber_conditionally_skip_heavy_plugin_scripts', 20, 2 );

function plumber_async_noncritical_styles( $html, $handle, $href, $media ) {
    if ( is_admin() ) {
        return $html;
    }

    $async_handles = array( 'plumber-fonts', 'swiper-css' );
    $should_async  = in_array( $handle, $async_handles, true ) || plumber_is_excluded_heavy_asset_src( (string) $href );

    if ( ! $should_async || empty( $href ) ) {
        return $html;
    }

    $escaped_href = esc_url( $href );

    return "<link rel='preload' as='style' id='{$handle}-css' href='{$escaped_href}' onload=\"this.onload=null;this.rel='stylesheet'\" media='all' />\n<noscript><link rel='stylesheet' id='{$handle}-css' href='{$escaped_href}' media='all' /></noscript>\n";
}
add_filter( 'style_loader_tag', 'plumber_async_noncritical_styles', 20, 4 );

function plumber_maybe_load_wpcf7_assets() {
    return plumber_page_uses_contact_integrations();
}
add_filter( 'wpcf7_load_js', 'plumber_maybe_load_wpcf7_assets' );
add_filter( 'wpcf7_load_css', 'plumber_maybe_load_wpcf7_assets' );

// add acf content
require get_template_directory() . '/inc/theme-acf.php';
if ( ! function_exists( 'mytheme_register_nav_menu' ) ) {

	function mytheme_register_nav_menu() {
		register_nav_menus(
			array(
				'Main-menu'          => __( 'Primary Menu', 'plumber' ),
				'Main-footer-menu'   => __( 'Footer Menu', 'plumber' ),
				'footer_menu_1'      => __( 'Footer — Menu 1', 'plumber' ),
				'footer_menu_2'      => __( 'Footer — Menu 2', 'plumber' ),
				'footer_quick_links' => __( 'Footer — Quick Links', 'plumber' ),
				'footer_services'    => __( 'Footer — Services', 'plumber' ),
			)
		);
	}
	add_action( 'after_setup_theme', 'mytheme_register_nav_menu', 0 );
}

function plumber_year_shortcode() {
	return gmdate( 'Y' );
}
add_shortcode( 'year', 'plumber_year_shortcode' );

//add svg file
function allow_svg_uploads( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'allow_svg_uploads' );