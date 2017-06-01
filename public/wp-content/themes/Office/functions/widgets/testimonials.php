<?php
/******************************************
/* Recent Posts Widget
******************************************/
class office_testimonials extends WP_Widget {
							
    /** constructor */
    function office_testimonials() {
        parent::WP_Widget(false, $name = 'Office - Testimonials');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $number = apply_filters('widget_title', $instance['number']);
        $offset = apply_filters('widget_title', $instance['offset']);
        ?>
              <?php echo $before_widget; ?>
                        <?php echo $before_title; ?>
                        <?php echo $title; ?>
                        <?php echo $after_title; ?>
							<div class="widget-recent-testimonials">
                            	<div id="testimonials-slider" class="flexslider clearfix">
									<ul class="slides">
									<?php
                                        global $post;
                                        $tmp_post = $post;
                                        $args = array(
                                            'post_type' => 'testimonials',
                                            'numberposts' => $number,
                                            'offset'=> $offset,
                                            'order' => 'rand'
                                        );
                                        $myposts = get_posts( $args );
                                        foreach( $myposts as $post ) : setup_postdata($post);
                                        ?>
                                            <li class="testimonial-slide">
                                                <div class="testimonial-content"><?php the_content(); ?></div>
                                                <div class="testimonial-by"><?php the_title(); ?></div>
                                            </li>
                                            <!-- testimonial-slide -->
                                        <?php endforeach; ?>
                                        <?php $post = $tmp_post; ?>
                                	</ul>
                                </div>
                                <!-- /flex-slider -->
							</div>
                            <!-- /widget-recent-testimonials -->
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
	$instance['number'] = strip_tags($new_instance['number']);
	$instance['offset'] = strip_tags($new_instance['offset']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Testimonials', 'id' => '', 'number'=> 4));			
        $title = esc_attr($instance['title']);
        $number = esc_attr($instance['number']);
        $offset = esc_attr($instance['offset']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'office'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title','office'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number to Show:', 'office'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Offset (the number of posts to skip):', 'office'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('offset'); ?>" name="<?php echo $this->get_field_name('offset'); ?>" type="text" value="<?php echo $offset; ?>" />
        </p>
        <?php 
    }


} // class office_testimonials
// register Recent Posts widget
add_action('widgets_init', create_function('', 'return register_widget("office_testimonials");'));	
?>