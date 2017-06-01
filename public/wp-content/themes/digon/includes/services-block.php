<section>
	<div class="grid-list-position">
	<div class="grid-list-services section-wrap clearfix">
		<div class="grid-list-services">
			<ul>
				<li class="service-block service-block1">
				<a href="<?php echo of_get_option('step_1_link'); ?>">
					<div class="gridservice_colwrap gridservice_col1">
						<div class="service_elemental service-icon1">
						</div>
						<h3>
							
								<?php echo of_get_option('step_1_title'); ?>
							
						</h3>
						<p class="description entry-content">
							<?php echo of_get_option('step_1_desc'); ?>
						</p>
					</div>
					
					<?php if (of_get_option('step_1_image') ) { ?>
					<div class="gridservice_image gridservice_image1">
						<img src="<?php echo of_get_option('step_1_image'); ?>" alt="service image" />
					</div>
					<?php } ?>
				</a>
				</li>

				<li class="service-block service-block2">
				<a href="<?php echo of_get_option('step_2_link'); ?>">	
					<div class="gridservice_colwrap gridservice_col2">
						
						
						<div class="service_elemental service-icon2">
						</div>
						<h3>
								<?php echo of_get_option('step_2_title'); ?>
							
						</h3>
						<p class="description entry-content">
							<?php echo of_get_option('step_2_desc'); ?>
						</p>
					</div>
					
					<?php if (of_get_option('step_2_image') ) { ?>
					<div class="gridservice_image gridservice_image2">
						<img src="<?php echo of_get_option('step_2_image'); ?>" alt="service image" />
					</div>
					<?php } ?>
				</a>
				</li>
				
				<li class="service-block service-block3">
				<a href="<?php echo of_get_option('step_3_link'); ?>">
					<div class="gridservice_colwrap gridservice_col3">
					
						
						<div class="service_elemental service-icon3">
						</div>
						<h3>
							
							<?php echo of_get_option('step_3_title'); ?>
							
						</h3>
						<p class="description entry-content">
							<?php echo of_get_option('step_3_desc'); ?>
						</p>
					</div>
					
					<?php if (of_get_option('step_3_image') ) { ?>
					<div class="gridservice_image gridservice_image3">
						<img src="<?php echo of_get_option('step_3_image'); ?>" alt="service image" />
					</div>
					<?php } ?>
				</a>
				</li>
			</ul>
		</div>
	</div>
	</div>
</section>