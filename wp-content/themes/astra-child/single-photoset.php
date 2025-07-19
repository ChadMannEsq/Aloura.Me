<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
	<!-- Date -->
	<section class="common_header">
		<span class="header_text_bold"><?php echo get_the_date('d M Y'); ?></span>
		<h1 class="header_title"><?php the_title(); ?></h1>
		<p class="header_text_bold"><?php $vendor_terms = get_the_term_list(get_the_ID(), 'vendor', 'with ', ', ', ''); ?>
			<span class="header_text_bold"><?php echo $vendor_terms; ?> </span>
		</p>
	</section>
	<section class="photoset_gallery_section">
		<!-- custom taxonomy "vendor" -->
		<!-- <?php $vendor_terms = get_the_term_list(get_the_ID(), 'vendor', 'with ', ', ', ''); ?> -->
		<!-- <span><?php echo $vendor_terms; ?> </span> -->
		<div class="video_wrap">
			<p class="page_preview">Preview</p>
			<div class="gallery_images">
				<?php
				$photoset_images = get_post_meta(get_the_ID(), 'photoset_images', true);
				if (!empty($photoset_images)) {
					echo '<div class="photoset-gallery ">';
					foreach ($photoset_images as $image_id) {
						$image_url = wp_get_attachment_url($image_id);
						echo '<a href="' . esc_url($image_url) . '" class="photoset-item"><img src="' . esc_url($image_url) . '" alt=""></a>';
					}
					echo '</div>';
				}
				?>
			</div>
		</div>
	</section>
	
	<section class="photoset_gallery_section">
		<div class="film-video-content">
			<?php the_content(); ?>
		</div>
	</section>

	<!-- custom taxonomy "model" -->
	<section class="film-feature-model photoset_gallery_section">
		<div class="more_modal_content">
			<p>FEATURED MODEL:</p>
		</div>
		<div class="film-model-inner featured_model_inner">
			<?php
			$terms = get_the_terms($post->ID, 'model');
			if (!empty($terms) && !is_wp_error($terms)) {
				foreach ($terms as $term) {
					$image_id = get_term_meta($term->term_id, 'model-image-id', true);
					if ($image_id) {
						$image_url = wp_get_attachment_image_src($image_id, 'full');
						if (!empty($image_url)) {
							?>
								<div class="model-image">
									<a href="<?php echo get_term_link($term); ?>">
										<img src="<?php echo $image_url[0]; ?>" />
										<span class="model-name">
											<?php echo $term->name; ?>
										</span>
									</a>
								</div>
							<?php
						}
					}
				}
			}
			?>
		</div>
		</section>
	<hr class="singlephoto-seprator">
					
	<section class="film-feature-model photoset_gallery_section">
		<div class="more_modal_content">
			<div class="more_film_title_wrap">
				<p>MORE PHOTOSETS:</p>
				<button class="more_films_button"><a href="<?php echo home_url(); ?>/photosets">SEE ALL <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/right-symbol.png" /></a></button>
			</div>
		</div>
		<div class="film-model-inner related_model_inner">
			<?php
			// Get the current post ID and all post IDs in ascending order
			$current_post_id = get_the_ID();
			$all_posts = get_posts(array(
				'post_type'      => 'photoset',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'ASC',
				'fields'         => 'ids',
			));
			$total_posts = count($all_posts);
			$current_post_index = array_search($current_post_id, $all_posts);

			// Determine the next posts to display
			$next_posts = array();
			if ($current_post_index == $total_posts - 2) {
				$next_posts = array($all_posts[$total_posts - 1], $all_posts[0]);
			} elseif ($current_post_index == $total_posts - 1) {
				$next_posts = array_slice($all_posts, 0, 2);
			} else {
				$next_posts = array_slice($all_posts, $current_post_index + 1, 2);
				if (count($next_posts) < 2) {
					$next_posts[] = $all_posts[0];
				}
			}

			// Display the next posts
			foreach ($next_posts as $post_id) :
				$post = get_post($post_id);
				$terms = wp_get_post_terms($post_id, 'vendor');
				$film_vendor = '';

				if (!is_wp_error($terms) && !empty($terms)) {
					$term_names = wp_list_pluck($terms, 'name');
					$film_vendor = implode(', ', $term_names);
				}
				setup_postdata($post); ?>
					
					<div class="more_films_image_wrap">
						<?php if (has_post_thumbnail($post_id)) : ?>
							<a href="<?php echo get_permalink($post_id); ?>">
								<?php echo get_the_post_thumbnail($post_id, 'full'); ?>
							</a>
						<?php endif; ?>
						<div class="more_film_details">
							<a class="cusrsor-pointer" href="<?php echo get_permalink($post_id); ?>">
								<p class="film_vendor_name">With <?php echo $film_vendor; ?></p>
								<h4 class="vendor_title"><?php echo get_the_title($post_id); ?></h4>
							</a>
						</div>
					</div>
			<?php endforeach;
			wp_reset_postdata(); ?>
		</div>
	</section>
	

<?php endwhile; ?>

<?php get_footer(); ?>