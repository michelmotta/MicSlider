<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <?php 
    if($categoria){
      $args = array(
        'post_type' => 'micslider',
        'showposts' => $quantidade,
        'tax_query' => array(
          array(
            'taxonomy' => 'micslider_cat',
            'field'    => 'name',
            'terms'    => $categoria,
          ),
        ),
      );
    }else{
      $args = array(
        'post_type' => 'micslider',
        'showposts' => $quantidade
      );
    }
    
    $wp_query = new WP_Query( $args );
    $count = 0;
  ?>

  <?php if ($wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
    <?php 
      $count++;
      $link = get_post_meta(get_the_ID(), 'micslider-link', true); 
    ?>  
      <div class="item <?php if($count == 1){echo "active";}?>">
          <div class="banner_image">
            <?php
              if($link)
                echo '<center><a href="' . $link .'"><img src="' . get_the_post_thumbnail_url() . '"/></a></center>';   
              else
                echo '<center><img src="' . get_the_post_thumbnail_url() . '"/></center>';
            ?>
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