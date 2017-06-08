<?php
/**
 * Plugin Name: MicSlider
 * Plugin URI: https://github.com/michelmotta/MicSlider
 * Description: This plugin is a simple slider. It creates slider menu where you can publish sliders and add custon links to the slider created.
 * Version: 0.1
 * Author: Michel Motta da Silva
 * Author URI: https://github.com/michelmotta
 * License: GPL2
 */ 

function micslider_post_type() 
{ 
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
      'public' => false,
      'public_queryable' => true,
      'show_ui' => true,           
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'has_archive' => true,
      'menu_icon' => 'dashicons-money',
      'hierarchical' => false,
      'menu_position' => null,
			'register_meta_box_cb' => 'micslider_meta_box',       
      'supports' => array('title','thumbnail')
    );

	register_post_type( 'micslider' , $args );
	flush_rewrite_rules();
}
add_action('init', 'micslider_post_type');


function micslider_meta_box()
{        
  add_meta_box('micslider_meta_box', __('Informações Complementares'), 'micslider_meta_box_callback', 'micslider', 'normal', 'default');
}


function micslider_meta_box_callback($post)
{
	wp_nonce_field(basename( __FILE__ ), 'micslider_nonce');
  $micslider_meta = get_post_meta($post->ID);
?>
 
  <p>
    <label class="">Link Externo</label><br>
    <input type="text" name="micslider-link" class="regular-text" value="<?php if(isset($micslider_meta['micslider-link'])) echo $micslider_meta['micslider-link'][0]; ?>" placeholder="http://site.com"/>
  </p>
 
<?php    
}


function micslider_meta_save($post_id)
{
 
  $is_autosave = wp_is_post_autosave($post_id);
  $is_revision = wp_is_post_revision($post_id);
  $is_valid_nonce = (isset($_POST['micslider_nonce']) && wp_verify_nonce($_POST['micslider_nonce'], basename(__FILE__))) ? 'true' : 'false';

  if($is_autosave || $is_revision || !$is_valid_nonce) 
  {
    return;
  }

  if(isset($_POST['micslider-link'])) 
  {
    update_post_meta($post_id, 'micslider-link', sanitize_text_field($_POST['micslider-link']));
  }
 
}
add_action('save_post', 'micslider_meta_save');