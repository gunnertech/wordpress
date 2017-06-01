<?php $has_dropped_video = false; ?>
<?php if ( $instance['title'] ) { echo $before_title . $instance['title'] . $after_title; } ?>

<?php $post_count = 0; ?>
<div id="myCarousel" class="carousel slide" data-interval="15000">
  <?php $post_count = 0; ?>
  <?php if ($query->have_posts()): ?>
    <div class="carousel-inner">
      <?php while ($query->have_posts()): $query->the_post(); $video_id = false; ?>
        <div class="item <?php echo ($post_count == 0 ? 'active' : '') ?>" data-item-number="<?php echo $post_count ?>">
          <?php
          if($featured_video_url = get_post_meta(get_the_ID(), "featured_video_url", true)) {
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $featured_video_url, $match)) {
              $video_id = $match[1];
            }
          }
          
          ?>
          <?php if($video_id): ?>
            <div id="ytapiplayer-<?php echo $post_count ?>"></div>
            <?php if(!$has_dropped_video): ?>
              <script src="//dhwlijwe9jil7.cloudfront.net/wp-includes/js/swfobject.js.gzip?ver=2.2-20120417&amp;v=126"></script>
              <script>
              function onYouTubePlayerReady(playerId) {
                ytplayer = document.getElementById(playerId);
                ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
              }

              function onytplayerStateChange(newState) {
                if(newState == 1) {
                  jQuery('.carousel div.active .content-holder').hide();
                  jQuery('.carousel .carousel-control').hide();
                  try {
                    jQuery('.carousel').carousel(parseInt(jQuery('.carousel div.active').data("item-number")));
                    jQuery('.carousel').carousel("pause");
                  } catch(e) {
                    setTimeout(function(){
                      jQuery('.carousel').carousel(parseInt(jQuery('.carousel div.active').data("item-number")));
                      jQuery('.carousel').carousel("pause");
                    },2000);
                  }     
                } else {
                  jQuery('.carousel .active .content-holder').show();
                  jQuery('.carousel .carousel-control').show();
                }
              }
              </script>
            <?php $has_dropped_video = true; endif; ?>
            <script>
            var params = { allowScriptAccess: "always" };
            var atts = { id: "myytplayer-<?php echo $post_count ?>" };
            swfobject.embedSWF("http://www.youtube.com/v/<?php echo $video_id ?>?enablejsapi=1&playerapiid=myytplayer-<?php echo $post_count ?>&version=3&showinfo=0&rel=0",
              "ytapiplayer-<?php echo $post_count ?>", "578", "325", "8", null, null, params, atts);
            </script>
            <div style="height:25px; background-color:black; margin-top: -3px" class="video-black-bar"></div>
          <?php elseif(has_post_thumbnail(get_the_ID())): $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
            <a title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>" href="<?php the_permalink() ?>">
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
            </a>
          <?php endif; ?>
          <div class="content-holder">
            <h3><?php the_title() ?></h3>
            <?php $instance['show_full_content'] ? the_content() : the_excerpt() ?>
          </div>
        </div>
      <?php $post_count++; endwhile; $post_count = 0; ?>
    </div>
    <?php if ($query->have_posts()): ?>
      <ol class="icons">
        <?php while ($query->have_posts()): $query->the_post(); $attachment = wp_get_attachment_image( get_post_thumbnail_id(get_the_ID()), 'large'); ?>
          <li style="width: <?php echo 100/intval($instance["posts_per_page"]) ?>%;" data-target="#myCarousel" data-slide-to="<?php echo $post_count ?>" class="<?php echo $post_count == 0 ? "active" : "" ?>">
            <div>
              <?php echo preg_replace(array('/width="\d+"/','/height="\d+"/'),array("",""),get_the_post_thumbnail(get_the_ID(), $instance['image_size'] )) ?>
              <?php if($short_headline = get_post_meta(get_the_ID(), "short_headline", true)): ?>
                <h5><?php echo $short_headline; ?></h5>
              <?php else: ?>
                <h5><?php the_title() ?></h5>
              <?php endif; ?>
            </div>
          </li>
          <?php $post_count++; ?>
        <?php endwhile; ?>
      </ol>
    <?php endif; ?>
    <!--a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
    <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a-->
  <?php endif; ?>
</div>