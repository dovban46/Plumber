<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Plumber
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function plumber_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Page template: our-services-template-default.php (any theme subfolder).
	if ( is_page() ) {
		$page_template = get_page_template_slug();
		if ( $page_template && false !== strpos( $page_template, 'our-services-template-default' ) ) {
			$classes[] = 'plumber-page-our-services-template';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'plumber_body_classes' );

/**
 * Phone link for the fixed site-wide FAB (Theme Options), or hero block on the static front page.
 *
 * @return array<string, string>|null ACF link array or null.
 */
function plumber_get_floating_phone_link() {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$link = get_field( 'floating_phone_link', 'option' );
	if ( is_array( $link ) && ! empty( $link['url'] ) ) {
		return $link;
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id ) {
		// Read blocks as array — do not use have_rows() here; it runs in header before the
		// template loop and would advance/lock ACF's global row state and hide sections (e.g. hero).
		$blocks = get_field( 'blocks', $front_id );
		if ( is_array( $blocks ) ) {
			foreach ( $blocks as $block ) {
				$layout = isset( $block['acf_fc_layout'] ) ? $block['acf_fc_layout'] : '';
				if ( 'hero' !== $layout ) {
					continue;
				}
				$hero_section = isset( $block['hero_section'] ) ? $block['hero_section'] : null;
				if ( is_array( $hero_section ) && ! empty( $hero_section['hero_link_icon']['url'] ) ) {
					return $hero_section['hero_link_icon'];
				}
				return null;
			}
		}
	}

	return apply_filters( 'plumber_floating_phone_link', null );
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function plumber_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'plumber_pingback_header' );
