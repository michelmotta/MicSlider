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

function micslider_wp_enqueue_scripts() 
{
  wp_register_style( 'bootstrap', plugins_url( 'MicSlider/css/bootstrap.min.css'), array(), '3.3.7', 'all');
  wp_register_script( 'bootstrap-js', plugins_url( 'MicSlider/js/bootstrap.min.js'), array( 'jquery' ), '3.3.7', true );
}
add_action( 'wp_enqueue_scripts', 'micslider_wp_enqueue_scripts' );


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

	register_post_type('micslider' , $args );
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


function micslider_set_custom_edit_columns($columns) 
{
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => __('Title'),
    'micslider_preview' => __('Imagem'),
    'micslider_image_link' => __('Link da image'),
    'micslider_categories' => __('Categorias'),
    'date' => __('Date')
  );
  
  return $columns;
}

function micslider_custom_column( $column, $post_id ) 
{
  switch ( $column ) {
    case 'micslider_preview' :
      $image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full', false, '' );
      if ($image)
        echo '<img src="' . $image[0] . '" width="100" height="50">';
      else
        echo "Sem imagem";
    break;
    case 'micslider_image_link' :
      $link = $meta_value = get_post_meta( $post_id, 'micslider-link', true );
      if (!empty($link))
        echo '<small>' . $link . '</small>';
      else
        echo "<small>Sem link</small >";
    break;
    case 'micslider_categories' :
      $terms = get_the_terms($post_id, 'micslider_cat');
      if ($terms)
        foreach ($terms as $term) {
          echo $term->name . ',';
        }
      else
        echo "<small>Sem Categoria</small >";
    break;
  }
}

add_filter( 'manage_micslider_posts_columns', 'micslider_set_custom_edit_columns' );
add_action( 'manage_micslider_posts_custom_column' , 'micslider_custom_column', 10, 2 );


function create_micslider_taxonomies() 
{
  $labels = array(
    'name'  => _x('Categoria', 'taxonomy general name'),
    'singular_name' => _x('Categoria', 'taxonomy singular name'),
    'search_items'  => __('Procurar categorias'),
    'all_items' => __('Todos as categorias'),
    'parent_item' => __('Categorias semelhantes'),
    'parent_item_colon' => __('Categoria semelhante:'),
    'edit_item' => __('Editar categoria'),
    'update_item' => __('Atualizar categoria'),
    'add_new_item'  => __('Adicionar nova categoria'),
    'new_item_name' => __('Nova categoria'),
    'menu_name' => __('Categorias')
  );
  $args = array(
    'hierarchical'  => true,
    'labels'  => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'micslider_cat' ),
  );
  register_taxonomy('micslider_cat', array('micslider'), $args );
}
add_action( 'init', 'create_micslider_taxonomies', 0 );

// [micslider categoria="categoria 1"]
function micslider_func($atts, $content = null) 
{
  extract(shortcode_atts(array(
    "categoria" => '',
    "quantidade" => 1
  ), $atts));

  wp_enqueue_style('bootstrap');
  wp_enqueue_script('bootstrap-js');

  ob_start();
  ?>

  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <?php 
      $args = array(
        'post_type' => 'micslider',
        'showposts' => $quantidade,
      );
      $wp_query = new WP_Query( $args );
      $count = 0;
    ?>

    <?php if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
      <?php $count++; ?>  
        <div class="item <?php if($count == 1){echo "active";}?>">
            <div class="banner_image">
              <center><?php the_post_thumbnail();?></center>
            </div>
        </div>
    <?php endwhile; wp_reset_query();  else: ?>
      <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
    <?php endif; ?>
    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>

  <?php
  $content = ob_get_contents();
  ob_end_clean();
  
  return $content;
}
add_shortcode('micslider', 'micslider_func' );