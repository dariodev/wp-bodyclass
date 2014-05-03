<?php
/*
Plugin Name: WP Body Class Enhanced
Plugin URI: http://www.zxthemes.com
Description: Enhances the body_class adding some extra classes to the body (post/page slugs, post categories, archive parent categories, multisite id and user role name) useful in developing custom themes.
Version: 1.0
Author: Dario Devcic
Author URI: http://www.zxthemes.com
License: GPL2
*/

function zxt_enhance_body_class($classes) {

	$post_name_prefix = 'postname-';
	$page_name_prefix = 'pagename-';
	$single_term_prefix = 'single-';
	$single_parent_prefix = 'parent-';
	$category_parent_prefix = 'parent-category-';
	$term_parent_prefix = 'parent-term-';
	$site_prefix = 'site-';
	$role_prefix = 'role-';
	
	global $wp_query;
	if ( is_single() ) {
		$wp_query->post = $wp_query->posts[0];
		setup_postdata($wp_query->post);
		$classes[] = $post_name_prefix . $wp_query->post->post_name;

		$taxonomies = array_filter( get_post_taxonomies($wp_query->post->ID), "is_taxonomy_hierarchical" );	
		foreach ( $taxonomies as $taxonomy ) {
			$tax_name = ( $taxonomy != 'category') ? $taxonomy . '-' : '';
			$terms = get_the_terms($wp_query->post->ID, $taxonomy);
			if ( $terms ) {
				foreach( $terms as $term ) {
					if ( !empty($term->slug ) )
						$classes[] = $single_term_prefix . $tax_name . sanitize_html_class($term->slug, $term->term_id);
					while ( $term->parent ) {
						$term = &get_term( (int) $term->parent, $taxonomy );
						if ( !empty( $term->slug ) )
							$classes[] = $single_parent_prefix . $tax_name . sanitize_html_class($term->slug, $term->term_id);
					}
				}
			}
		}
	} elseif ( is_archive() ) {
		if ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			while ( $cat->parent ) {
				$cat = &get_category( (int) $cat->parent);
				if ( !empty( $cat->slug ) )
					$classes[] = $category_parent_prefix . sanitize_html_class($cat->slug, $cat->cat_ID);
			}
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			while ( $term->parent ) {
				$term = &get_term( (int) $term->parent, $term->taxonomy );
				if ( !empty( $term->slug ) )
					$classes[] = $term_parent_prefix . sanitize_html_class($term->slug, $term->term_id);
			}
		}
	} elseif ( is_page() ) {
		$wp_query->post = $wp_query->posts[0];
		setup_postdata($wp_query->post);
		$classes[] = $page_name_prefix . $wp_query->post->post_name;
	}
	
	if ( is_multisite() ) {
		global $blog_id;
		$classes[] = $site_prefix . $blog_id;
	}
	
    global $current_user;
    $user_role = array_shift($current_user->roles);
    $classes[] = $role_prefix . $user_role;
    	
	return $classes;
}

add_filter('body_class', 'zxt_enhance_body_class');
?>
