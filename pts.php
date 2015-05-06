<?php
/**
 * Plugin Name: Pricing Table Shortcode
 * Plugin URI: https://wordpress.org/plugins/pricing-tables-shortcode/
 * Description: A pricing table plugin, that sells.
 * Version: 1.0
 * Author: Yusri Mathews
 * Author URI: http://yusrimathews.co.za/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Pricing Table Shortcode Plugin
 * Copyright (C) 2015, Yusri Mathews - yo@yusrimathews.co.za
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

function pts_activation(){
	global $current_user;
	$user_id = $current_user->ID;

	update_user_meta( $user_id, 'pts_plugin_activation', date( 'F j, Y' ), true );
	update_user_meta( $user_id, 'pts_rate_ignore', 'false' );
	update_user_meta( $user_id, 'pts_donate_ignore', 'false' );
}
register_activation_hook( __FILE__, 'pts_activation' );

include_once('inc/notices.php');

add_action( 'init', 'pts_cpt' );
function pts_cpt() {
	$labels = array(
		'name'               => _x( 'Pricing Table', '', 'pts' ),
		'singular_name'      => _x( 'Tier', '', 'pts' ),
		'menu_name'          => _x( 'Pricing Table', '', 'pts' ),
		'name_admin_bar'     => _x( 'Pricing Table', '', 'pts' ),
		'add_new'            => _x( 'Add New', '', 'pts' ),
		'add_new_item'       => __( 'Add New Tier', 'pts' ),
		'new_item'           => __( 'New Tier', 'pts' ),
		'edit_item'          => __( 'Edit Tier', 'pts' ),
		'view_item'          => __( 'View Tier', 'pts' ),
		'all_items'          => __( 'All Tiers', 'pts' ),
		'search_items'       => __( 'Search Tiers', 'pts' ),
		'parent_item_colon'  => __( 'Parent Tiers:', 'pts' ),
		'not_found'          => __( 'No tiers found.', 'pts' ),
		'not_found_in_trash' => __( 'No tiers found in Trash.', 'pts' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'			 => 'dashicons-admin-generic',
		'supports'           => array( 'title' )
	);

	register_post_type( 'pts', $args );
}

add_action( 'media_buttons', 'pts_sbtn', 99 );
function pts_sbtn(){
	if( get_post_type( get_the_ID() ) == 'page' ){
		echo '<a href="#" id="pts_sbtn" class="button"><i class="fa fa-columns"></i> Add Pricing Table</a>';
	}
}

add_action( 'admin_enqueue_scripts', 'pts_scripts_admin' );
function pts_scripts_admin(){
	wp_enqueue_style( 'pts-font-awesome', plugin_dir_url( __FILE__ ) . 'vendor/font-awesome/4.3.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'pts-menu-css', plugin_dir_url( __FILE__ ) . 'css/menu.min.css', array( 'pts-font-awesome' ) );

	if( get_post_type( get_the_ID() ) == 'page' ){
		wp_enqueue_style( 'pts-admin-css', plugin_dir_url( __FILE__ ) . 'css/admin.min.css' );
		wp_enqueue_script( 'pts-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin.min.js', array( 'jquery' ) );
	}

	if( get_post_type( get_the_ID() ) == 'pts' ){
		wp_enqueue_style( 'pts-metaboxes-css', plugin_dir_url( __FILE__ ) . 'css/metaboxes.min.css' );
	}
}

include_once('inc/metaboxes.php');

add_shortcode( 'pts', 'pts_shortcode' );
function pts_shortcode( $atts ){
	if( !is_404() ){
		$page_object = get_post( get_the_ID() ); 
		$page_content = $page_object->post_content;
	}

	if( is_page( get_the_ID() ) && has_shortcode( $page_content, 'pts' ) ){

		$layoutStyle = ( isset( $atts['layoutstyle'] ) ? $atts['layoutstyle'] : 'none' );

		$shortcodeOutput = '<div id="pts-' . $layoutStyle . '">';
			if( $layoutStyle == 'simple' ){

				query_posts( array(
					'post_type' => 'pts',
					'order'		=> 'ASC',
					'showposts' => -1
				) );

				$ptsCounter = $ptsCounterLoop = 0;

				global $wp_query; 
				$ptsTotal = $wp_query->found_posts;

				if( $ptsTotal > 0 && $ptsTotal <= 3 ){
					$ptsWidth = ( 100 / $ptsTotal ) - 2;
				} else {
					$ptsWidth = 33.33 - 2;
				}

				while( have_posts() ) : the_post();
					$ptsCounter++;
					$ptsCounterLoop++;

					if( $ptsCounterLoop == 4 ){
						$ptsCounterLoop = 1;
					}

					if( $ptsCounterLoop == 1 ){
						$shortcodeOutput .= '<div class="pts-row">';
					}

					$shortcodeOutput .= '<div class="pts-item' . ( $ptsCounterLoop == 3 || $ptsCounter == $ptsTotal ? ' pts-last' : '' ) . ( $ptsCounterLoop == 2 ? ' pts-center' : '' ) . ( $ptsCounterLoop == 1 ? ' pts-first' : '' ) . ( get_post_meta( get_the_ID(), '_pts_da_emphasize', true ) == 'on' ? ' pts-emphasize' : '' ) . ' tier-' . get_the_ID() . '" style="width: ' . $ptsWidth . '%;">';
						$shortcodeOutput .= '<h3>' . get_the_title() . '</h3>';

						$focusText = get_post_meta( get_the_ID(), '_pts_focus_text', true );
						$focusTerms = get_post_meta( get_the_ID(), '_pts_focus_terms', true );

                    	$shortcodeOutput .= ( !empty( $focusText ) ? '<h5 class="pts-terms">' . $focusText . ' </h5>' : '' );
                    	$shortcodeOutput .= ( !empty( $focusTerms ) ? '<small class="pts-terms">' . $focusTerms . '</small>' : '' );

                    	$tierIncludes = get_post_meta( get_the_ID(), '_pts_tier_includes_group', true );
                    	$tierDescribe = get_post_meta( get_the_ID(), '_pts_describe_text', true );
                    	if( $tierIncludes ){
                    		$shortcodeOutput .= '<ul>';
							foreach( $tierIncludes as $v ){
								$text = ( isset( $v['_pts_tier_includes_text'] ) ? $v['_pts_tier_includes_text'] : '' );
								$terms = ( isset( $v['_pts_tier_includes_terms'] ) ? $v['_pts_tier_includes_terms'] : '' );

							    $shortcodeOutput .= ( !empty( $text ) ? '<li>' . $text . '</li>' : '' );
							    $shortcodeOutput .= ( !empty( $terms ) ? '<li><small>' . $terms . '</small></li>' : '' );
							}
							$shortcodeOutput .= '</ul>';
                    	} elseif( !empty( $tierDescribe ) ){
                    		$shortcodeOutput .= '<p>' . strip_tags( $tierDescribe, '<br>' ) . '</p>';
                    	}

                    	$btnText = get_post_meta( get_the_ID(), '_pts_btn_text', true );
                    	$btnUrl = get_post_meta( get_the_ID(), '_pts_btn_url', true );

                    	if( !empty( $btnText ) ){
                    		$shortcodeOutput .= '<a class="pts-btn" href="' . $btnUrl . '" title="' . $btnText . '">' . $btnText . '</a>';
                    	}
					$shortcodeOutput .= '</div>';

					if( $ptsCounterLoop == 3 || $ptsCounter == $ptsTotal ){
						$shortcodeOutput .= '<div class="pts-clear"></div>';
						$shortcodeOutput .= '</div>';
					}
				endwhile;

			} elseif( $layoutStyle == 'modern' ){

				$minHeight = ( isset( $atts['minheight'] ) ? $atts['minheight'] : '0' );
				$minHeightEm = ( isset( $atts['minheight'] ) ? $atts['minheight'] + 60 : '0' );

				query_posts( array(
					'post_type' => 'pts',
					'order'		=> 'ASC',
					'showposts' => -1
				) );

				$ptsCounter = $ptsCounterLoop = 0;

				global $wp_query; 
				$ptsTotal = $wp_query->found_posts;

				if( $ptsTotal > 0 && $ptsTotal <= 3 ){
					$ptsWidth = 100 / $ptsTotal;
				} else {
					$ptsWidth = 33.33;
				}

				$ptsWidthEm = $ptsWidth + 3;

				while( have_posts() ) : the_post();
					$ptsCounter++;
					$ptsCounterLoop++;

					if( $ptsCounterLoop == 4 ){
						$ptsCounterLoop = 1;
					}

					if( $ptsCounterLoop == 1 ){
						$shortcodeOutput .= '<div class="pts-row">';
					}

					$shortcodeOutput .= '<div class="pts-item' . ( $ptsCounterLoop == 3 || $ptsCounter == $ptsTotal ? ' pts-last' : '' ) . ( $ptsCounterLoop == 2 ? ' pts-center' : '' ) . ( $ptsCounterLoop == 1 ? ' pts-first' : '' ) . ( get_post_meta( get_the_ID(), '_pts_da_emphasize', true ) == 'on' ? ' pts-emphasize' : '' ) . ' tier-' . get_the_ID() . '" style="width: ' . ( get_post_meta( get_the_ID(), '_pts_da_emphasize', true ) == 'on' ? $ptsWidth : $ptsWidth ) . '%; min-height: ' . ( get_post_meta( get_the_ID(), '_pts_da_emphasize', true ) == 'on' ? $minHeightEm : $minHeight ) . 'px;">';
						$shortcodeOutput .= '<h3>' . get_the_title() . '</h3>';

						$focusText = get_post_meta( get_the_ID(), '_pts_focus_text', true );
						$focusTerms = get_post_meta( get_the_ID(), '_pts_focus_terms', true );

                    	if( !empty( $focusText ) && !empty( $focusTerms ) ){
                    		$shortcodeOutput .= '<h5 class="pts-terms">' . $focusText . ' <small>' . $focusTerms . '</small></h5>';
                    	} else if( !empty( $focusText ) && empty( $focusTerms ) ){
                    		$shortcodeOutput .= '<h5 class="pts-terms">' . $focusText . '</h5>';
                    	} else if( empty( $focusText ) && !empty( $focusTerms ) ){
                    		$shortcodeOutput .= '<h5 class="pts-terms"><small>' . $focusTerms . '</small></h5>';
                    	}

                    	$tierIncludes = get_post_meta( get_the_ID(), '_pts_tier_includes_group', true );
                    	$tierDescribe = get_post_meta( get_the_ID(), '_pts_describe_text', true );
                    	if( $tierIncludes ){
                    		$shortcodeOutput .= '<ul>';
							foreach( $tierIncludes as $v ){
								$text = ( isset( $v['_pts_tier_includes_text'] ) ? $v['_pts_tier_includes_text'] : '' );
								$terms = ( isset( $v['_pts_tier_includes_terms'] ) ? $v['_pts_tier_includes_terms'] : '' );

							    $shortcodeOutput .= ( !empty( $text ) ? '<li>' . $text . '</li>' : '' );
							    $shortcodeOutput .= ( !empty( $terms ) ? '<li><small>' . $terms . '</small></li>' : '' );
							}
							$shortcodeOutput .= '</ul>';
                    	} elseif( !empty( $tierDescribe ) ){
                    		$shortcodeOutput .= '<p>' . strip_tags( $tierDescribe, '<br>' ) . '</p>';
                    	}

                    	$btnText = get_post_meta( get_the_ID(), '_pts_btn_text', true );
                    	$btnUrl = get_post_meta( get_the_ID(), '_pts_btn_url', true );

                    	if( !empty( $btnText ) ){
                    		$shortcodeOutput .= '<a class="pts-btn" href="' . $btnUrl . '" title="' . $btnText . '">' . $btnText . '</a>';
                    	}
					$shortcodeOutput .= '</div>';

					if( $ptsCounterLoop == 3 || $ptsCounter == $ptsTotal ){
						$shortcodeOutput .= '<div class="pts-clear"></div>';
						$shortcodeOutput .= '</div>';
					}
				endwhile;

			} else {
				$shortcodeOutput .= '<p>Something is wrong with your Pricing Table shortcode. Please use our built in shortcode generator to assist you.</p>';
			}
		$shortcodeOutput .= '</div>';

		return $shortcodeOutput;

	} else {
		return '<p>These Pricing Table shortcodes can only be used on pages.</p>';
	}
}

add_action( 'wp_enqueue_scripts', 'pts_scripts' );
function pts_scripts(){
	if( !is_404() ){
		$page_object = get_post( get_the_ID() ); 
		$page_content = $page_object->post_content;
	}

	if( is_page( get_the_ID() ) && has_shortcode( $page_content, 'pts' ) ){
		wp_enqueue_style( 'pts-public-css', plugin_dir_url( __FILE__ ) . 'css/public.min.css' );
	}
}
