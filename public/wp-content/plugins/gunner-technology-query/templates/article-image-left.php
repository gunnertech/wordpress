<?php $columns = isset($instance['columns']) ? intval(preg_replace('/span/','',$instance['columns'])) : 7; ?>
<?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>
<?php if ($query->have_posts()): ?>
  <div class="articles">
    <?php while ($query->have_posts()): $query->the_post(); $more = ($instance['show_full_content'] ? 1 : 0); $count++; ?>
      <article class="row <?php echo has_post_thumbnail(get_the_ID()) ? 'has-featured-image' : '' ?>">
      
        <h3 class="span<?php echo $columns ?>"><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h3>
        <div style="clear:both;"></div>
        
        <?php if(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
          <figure class="span4">
            <a title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
            <figcaption>
              <?php if($attachment && isset($attachment->post_title)): ?>
                <?php echo apply_filters( 'the_title', $attachment->post_title ) ?>
              <?php endif; ?>
            </figcaption>
          </figure>
        <?php endif; ?>
        
        <div class="<?php echo has_post_thumbnail(get_the_ID()) ? 'span'.(intval($columns)-4) : ('span'.$columns) ?>">
          <p><?php
            printf( __( '<span class="author-info"><span class="meta-sep">By</span> %3$s </span><span class="by separator"></span> %2$s <span class="author separator"></span> ', 'gt' ),
              'meta-prep meta-prep-author',
              sprintf( '<br /><small class="entry-meta">Updated: <span class="entry-date">%1$s</span> <span class="entry-time">%2$s</span></small>',
                get_the_date(),
                get_the_time()
              ),
              sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
                get_author_posts_url( get_the_author_meta( 'ID' ) ),
                sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
                get_the_author()
              )
            );
          ?>
          </p>
          <?php $instance['show_full_content'] ? the_content() : the_excerpt() ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
  
  <?php if(!empty($instance['more_slug']) && !empty($instance['more_url'])): ?>
    <a class="<?php echo empty($instance['use_text_btn']) ? 'btn' : '' ?> more" href="<?php echo $instance['more_url'] ?>"><?php echo $instance['more_slug'] ?></a>
  <?php endif; ?>
<?php endif; ?>
