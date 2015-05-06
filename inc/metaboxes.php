<?php

function pts_metaboxes( $meta_boxes ){
    $prefix = '_pts_';

    $meta_boxes['focus_metabox'] = array(
        'id' => 'focus_metabox',
        'title' => 'Focus',
        'pages' => array('pts'),
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'fields' => array(
            array(
                'name' => 'Text',
                'desc' => '',
                'id' => $prefix . 'focus_text',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'Terms',
                'desc' => '',
                'id' => $prefix . 'focus_terms',
                'type' => 'text_medium'
            ),
        ),
    );

    $meta_boxes['tier_includes_metabox'] = array(
        'id' => 'tier_includes_metabox',
        'title' => 'Tier Includes',
        'pages' => array('pts'),
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'fields' => array(
			array(
			    'id'          => $prefix . 'tier_includes_group',
			    'type'        => 'group',
			    'description' => '',
			    'options'     => array(
			        'group_title'   => __( '{#}. Include', 'pts' ),
			        'add_button'    => __( 'Add Include', 'pts' ),
			        'remove_button' => __( 'Remove Include', 'pts' ),
			        'sortable'      => true,
			    ),
			    'fields'      => array(
		            array(
		                'name' => 'Text',
		                'desc' => '',
		                'id' => $prefix . 'tier_includes_text',
		                'type' => 'text_medium'
		            ),
		            array(
		                'name' => 'Terms',
		                'desc' => '',
		                'id' => $prefix . 'tier_includes_terms',
		                'type' => 'text_medium'
		            ),
			    ),
			),
        ),
    );

    $meta_boxes['describe_metabox'] = array(
        'id' => 'describe_metabox',
        'title' => 'Tier Description',
        'pages' => array('pts'),
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'fields' => array(
            array(
                'name' => 'Description',
                'desc' => 'You can use this textarea if you\'re not using Tier Includes, just to add a description to a tier instead.',
                'id' => $prefix . 'describe_text',
                'type' => 'textarea'
            ),
        ),
    );

    $meta_boxes['link_metabox'] = array(
        'id' => 'link_metabox',
        'title' => 'Button',
        'pages' => array('pts'),
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'fields' => array(
            array(
                'name' => 'Text',
                'desc' => '',
                'id' => $prefix . 'btn_text',
                'type' => 'text_medium'
            ),
            array(
                'name' => 'URL',
                'desc' => '',
                'id' => $prefix . 'btn_url',
                'type' => 'text_url'
            ),
        ),
    );

    $meta_boxes['attributes_metabox'] = array(
        'id' => 'attributes_metabox',
        'title' => 'Display Attributes',
        'pages' => array('pts'),
        'context' => 'side',
        'priority' => 'default',
        'show_names' => true,
        'fields' => array(
            array(
                'name' => 'Emphasize',
                'desc' => 'Use this field to emphasize a certain tier, your most popular.',
                'id' => $prefix . 'da_emphasize',
                'type' => 'checkbox'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'pts_metaboxes' );

add_action( 'init', 'pts_initialize_metaboxes', 9999 );
function pts_initialize_metaboxes(){
    if ( !class_exists( 'cmb_Meta_Box' ) ){
        require_once( 'metaboxes/init.php' );
    }
}
