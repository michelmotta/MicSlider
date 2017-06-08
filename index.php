<?php
/**
 * Plugin Name: MicSlider
 * Plugin URI: https://www.facebook.com/michel.mottadasilva
 * Description: This plugin is a simple slider. It creates slider menu where you can publish sliders and add custon links to the slider created.
 * Version: 0.1
 * Author: Michel Motta da Silva
 * Author URI: https://www.facebook.com/michel.mottadasilva
 * License: GPL2
 */

add_action('init', 'micslider_post_type'); 

function micslider_post_type() { 
    $labels = array(
        'name' => _x('MicSlider', 'post type general name'),
        'singular_name' => _x('MicSlider', 'post type singular name'),
        'add_new' => _x('Adicionar Novo', 'Novo item'),
        'add_new_item' => __('Novo Item'),
        'edit_item' => __('Editar Item'),
        'new_item' => __('Novo Item'),
        'view_item' => __('Ver Item'),
        'search_items' => __('Procurar Itens'),
        'not_found' =>  __('Nenhum registro encontrado'),
        'not_found_in_trash' => __('Nenhum registro encontrado na lixeira'),
        'parent_item_colon' => '',
        'menu_name' => 'MicSlider'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'public_queryable' => true,
        'show_ui' => true,           
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
				'register_meta_box_cb' => 'micslider_meta_box',       
        'supports' => array('title','thumbnail')
      );

	register_post_type( 'micslider' , $args );
	flush_rewrite_rules();
}

function micslider_meta_box(){        
  add_meta_box('micslider_meta_box', __('Informações Complementares'), 'micslider_meta_box_callback', 'micslider', 'normal', 'default');
}

function micslider_meta_box_callback(){
	 echo 'This is a meta box';      
}
