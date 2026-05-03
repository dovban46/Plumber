<?php
/**
 * Primary navigation walker: text link + toggle button when item has children.
 *
 * @package Plumber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extends core walker for split mobile UX (tap label = navigate, tap icon = open submenu).
 */
class Plumber_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * @see Walker_Nav_Menu::start_el()
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item.
	 * @param stdClass $args              An object of wp_nav_menu() arguments.
	 * @param int      $current_object_id Optional. ID of the current menu item.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$menu_item = $data_object;

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
		$classes[] = 'menu-item-' . $menu_item->ID;

		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth );

		$li_atts          = array();
		$li_atts['id']    = ! empty( $id ) ? $id : '';
		$li_atts['class'] = ! empty( $class_names ) ? $class_names : '';

		$li_atts       = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, $depth );
		$li_attributes = $this->build_atts( $li_atts );

		$output .= $indent . '<li' . $li_attributes . '>';

		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );

		$the_title_filtered = $title;

		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

		$atts           = array();
		$atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';
		$atts['rel']    = ! empty( $menu_item->xfn ) ? $menu_item->xfn : '';

		if ( ! empty( $menu_item->url ) ) {
			$privacy_url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
			if ( $privacy_url && $privacy_url === $menu_item->url ) {
				$atts['rel'] = empty( $atts['rel'] ) ? 'privacy-policy' : $atts['rel'] . ' privacy-policy';
			}

			$atts['href'] = $menu_item->url;
		} else {
			$atts['href'] = '';
		}

		$atts['aria-current'] = $menu_item->current ? 'page' : '';

		if ( ! empty( $menu_item->attr_title )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $menu_item->title ) )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $the_title_filtered ) )
			&& trim( strtolower( $menu_item->attr_title ) ) !== trim( strtolower( $title ) )
		) {
			$atts['title'] = $menu_item->attr_title;
		} else {
			$atts['title'] = '';
		}

		$has_children = in_array( 'menu-item-has-children', $classes, true );

		$link_class = 'menu-item__text-link';
		if ( ! empty( $atts['class'] ) ) {
			$link_class .= ' ' . $atts['class'];
		}
		$atts['class'] = trim( $link_class );

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );
		$attributes = $this->build_atts( $atts );

		$item_output = isset( $args->before ) ? $args->before : '';

		if ( ! $has_children ) {
			$item_output .= '<a' . $attributes . '>';
			$item_output .= isset( $args->link_before ) ? $args->link_before : '';
			$item_output .= '<span class="menu-item__label">' . $title . '</span>';
			$item_output .= isset( $args->link_after ) ? $args->link_after : '';
			$item_output .= '</a>';
		} else {
			$item_output .= '<div class="menu-item__row">';
			$item_output .= '<a' . $attributes . '>';
			$item_output .= isset( $args->link_before ) ? $args->link_before : '';
			$item_output .= '<span class="menu-item__label">' . $title . '</span>';
			$item_output .= isset( $args->link_after ) ? $args->link_after : '';
			$item_output .= '</a>';
			$item_output .= '<button type="button" class="menu-item__submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle submenu', 'plumber' ) . '">';
			$item_output .= '<span class="menu-item__chevron" aria-hidden="true"></span>';
			$item_output .= '</button>';
			$item_output .= '</div>';
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
	}
}
