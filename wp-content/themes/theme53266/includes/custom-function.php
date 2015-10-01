<?php
	// Loads child theme textdomain
	load_child_theme_textdomain( CURRENT_THEME, CHILD_DIR . '/languages' );

	// Loads custom scripts.
	// require_once( 'custom-js.php' );
    
    
    //scripts addings
    add_action( 'wp_enqueue_scripts', 'custom_scripts' );
     
    function custom_scripts() {   
        wp_enqueue_script( 'theme_script', get_stylesheet_directory_uri() . '/js/theme_script.js', array('jquery'), '1.0' );
        wp_enqueue_script( 'cherryIsotopeView', get_stylesheet_directory_uri() . '/js/cherryIsotopeView.js', array('jquery'), '1.0' );
    } 
    
    
     add_action( 'after_setup_theme', 'after_cherry_child_setup' );
     function after_cherry_child_setup() {
      $nfu_options = get_option( 'nsu_form' );
      if ( !$nfu_options ) {
       $nfu_options_array = array();
       $nfu_options_array['email_label']         = ' ';
       $nfu_options_array['email_default_value'] = 'Signup for mail';
       $nfu_options_array['submit_button']       = 'ok';
       update_option( 'nsu_form', $nfu_options_array );
      }
     } 
    
    // stickup     
    add_filter( 'cherry_stickmenu_selector', 'cherry_change_selector' );
    	function cherry_change_selector($selector) {
    		$selector = '.extra_head';
    		return $selector;
   	}  
    
    
    
/**
 * Post Grid
 *
 */
if (!function_exists('posts_grid_shortcode')) {

	function posts_grid_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'type'            => 'post',
			'category'        => '',
			'custom_category' => '',
			'tag'             => '',
			'columns'         => '3',
			'rows'            => '3',
			'order_by'        => 'date',
			'order'           => 'DESC',
			'thumb_width'     => '370',
			'thumb_height'    => '250',
			'meta'            => '',
			'excerpt_count'   => '15',
			'link'            => 'yes',
			'link_text'       => __('Read more', CHERRY_PLUGIN_DOMAIN),
			'custom_class'    => ''
		), $atts));

		$spans = $columns;
		$rand  = rand();
        

		// columns
		switch ($spans) {
			case '1':
				$spans = 'span12';
				break;
			case '2':
				$spans = 'span6';
				break;
			case '3':
				$spans = 'span4';
				break;
			case '4':
				$spans = 'span3';
				break;
			case '6':
				$spans = 'span2';
				break;
		}

		// check what order by method user selected
		switch ($order_by) {
			case 'date':
				$order_by = 'post_date';
				break;
			case 'title':
				$order_by = 'title';
				break;
			case 'popular':
				$order_by = 'comment_count';
				break;
			case 'random':
				$order_by = 'rand';
				break;
		}

		// check what order method user selected (DESC or ASC)
		switch ($order) {
			case 'DESC':
				$order = 'DESC';
				break;
			case 'ASC':
				$order = 'ASC';
				break;
		}

		// show link after posts?
		switch ($link) {
			case 'yes':
				$link = true;
				break;
			case 'no':
				$link = false;
				break;
		}

			global $post;
			global $my_string_limit_words;

			$numb = $columns * $rows;

			// WPML filter
			$suppress_filters = get_option('suppress_filters');

			$args = array(
				'post_type'         => $type,
				'category_name'     => $category,
				$type . '_category' => $custom_category,
				'tag'               => $tag,
				'numberposts'       => $numb,
				'orderby'           => $order_by,
				'order'             => $order,
				'suppress_filters'  => $suppress_filters
			);

			$posts      = get_posts($args);
			$i          = 0;
			$count      = 1;
			$output_end = '';
			$countul = 0;

			if ($numb > count($posts)) {
				$output_end = '</ul>';
			}

			$output = '<ul class="posts-grid row-fluid unstyled '. $custom_class .' ul-item-'.$countul.'">';


			foreach ( $posts as $j => $post ) {
				$post_id = $posts[$j]->ID;
				//Check if WPML is activated
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					global $sitepress;

					$post_lang = $sitepress->get_language_for_element( $post_id, 'post_' . $type );
					$curr_lang = $sitepress->get_current_language();
					// Unset not translated posts
					if ( $post_lang != $curr_lang ) {
						unset( $posts[$j] );
					}
					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) {
						$posts[$j] = get_post( icl_object_id( $posts[$j]->ID, $type, true ) );
					}
				}

				setup_postdata($posts[$j]);
				$excerpt        = get_the_excerpt();
				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, $thumb_width, $thumb_height, true);
				$mediaType      = get_post_meta($post_id, 'tz_portfolio_type', true);
				$prettyType     = 0;

				if ($count > $columns) {
					$count = 1;
					$countul ++;
					$output .= '<ul class="posts-grid row-fluid unstyled '. $custom_class .' ul-item-'.$countul.'">';
				}
                
                /******* custom fields ************/
                
                $portfolioInfo   = get_post_meta($post->ID, 'tz_portfolio_info', true);
                $subtitle = get_post_meta($post->ID, 'subtitle', true);
                $team_info = get_post_meta($post->ID, 'my_team_info', true);

				$output .= '<li class="'. $spans .' list-item-'.$count.'">';
					if(has_post_thumbnail($post_id) && $mediaType == 'Image') {

						$prettyType = 'prettyPhoto-'.$rand;

						$output .= '<figure class="featured-thumbnail thumbnail">';
						$output .= '<a href="'.$url.'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
						$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
						$output .= '<span class="zoom-icon"></span></a></figure>';
					} elseif ($mediaType != 'Video' && $mediaType != 'Audio') {

						$thumbid = 0;
						$thumbid = get_post_thumbnail_id($post_id);

						$images = get_children( array(
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'post_type'      => 'attachment',
							'post_parent'    => $post_id,
							'post_mime_type' => 'image',
							'post_status'    => null,
							'numberposts'    => -1
						) );

						if ( $images ) {

							$k = 0;
							//looping through the images
							foreach ( $images as $attachment_id => $attachment ) {
								$prettyType = "prettyPhoto-".$rand ."[gallery".$i."]";
								//if( $attachment->ID == $thumbid ) continue;

								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
								$img = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true ); //resize & crop img
								$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
								$image_title = $attachment->post_title;

								if ( $k == 0 ) {
									if (has_post_thumbnail($post_id)) {
										$output .= '<figure class="featured-thumbnail thumbnail">';
										$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
										$output .= '<img src="'.$image.'" alt="'.get_the_title($post_id).'" />';
									} else {
										$output .= '<figure class="featured-thumbnail thumbnail">';
										$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
										$output .= '<img  src="'.$img.'" alt="'.get_the_title($post_id).'" />';
									}
								} else {
									$output .= '<figure class="featured-thumbnail thumbnail" style="display:none;">';
									$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
								}
								$output .= '<span class="zoom-icon"></span></a></figure>';
								$k++;
							}
						} elseif (has_post_thumbnail($post_id)) {
							$prettyType = 'prettyPhoto-'.$rand;
							$output .= '<figure class="featured-thumbnail thumbnail">';
							$output .= '<a href="'.$url.'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
							$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
							$output .= '<span class="zoom-icon"></span></a></figure>';
						}
					} else {

						// for Video and Audio post format - no lightbox
						$output .= '<figure class="featured-thumbnail thumbnail"><a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">';
						$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
						$output .= '</a></figure>';
					}
                
                if ($custom_class != "extragrid_2"){// begin CUSTOM class
					$output .= '<div class="clear"></div>';
                    }
                    

                $output .= '<div class="extradesc">';
                
					$output .= '<h5><a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">';
						$output .= get_the_title($post_id);
					$output .= '</a></h5>';
                    
                    /******* custom_teaminfo *********/
  	                 if ($team_info  != ''){
        				$output .= '<h6>';
            				$output .= $team_info;
            				$output .= '</h6>';
                    }
                    /******* custom_teaminfo *********/
  	                 if ($subtitle  != ''){
        				$output .= '<h6>';
            				$output .= $subtitle;
            				$output .= '</h6>';
                    }
                    
					if ($meta == 'yes') {
						// begin post meta
						$output .= '<div class="post_meta">';

							// post category
							$output .= '<span class="post_category">';
							if ($type!='' && $type!='post') {
								$terms = get_the_terms( $post_id, $type.'_category');
								if ( $terms && ! is_wp_error( $terms ) ) {
									$out = array();
									$output .= '<em>Posted in </em>';
									foreach ( $terms as $term )
										$out[] = '<a href="' .get_term_link($term->slug, $type.'_category') .'">'.$term->name.'</a>';
										$output .= join( ', ', $out );
								}
							} else {
								$categories = get_the_category($post_id);
								if($categories){
									$out = array();
									$output .= '<em>Posted in </em>';
									foreach($categories as $category)
										$out[] = '<a href="'.get_category_link($category->term_id ).'" title="'.$category->name.'">'.$category->cat_name.'</a> ';
										$output .= join( ', ', $out );
								}
							}
							$output .= '</span>';

							// post date
							$output .= '<span class="post_date">';
							$output .= '<time datetime="'.get_the_time('Y-m-d\TH:i:s', $post_id).'">' .get_the_date(). '</time>';
							$output .= '</span>';

							// post author
							$output .= '<span class="post_author">';
							$output .= '<em> by </em>';
							$output .= '<a href="'.get_author_posts_url(get_the_author_meta( 'ID' )).'">'.get_the_author_meta('display_name').'</a>';
							$output .= '</span>';

							// post comment count
							$num = 0;
							$queried_post = get_post($post_id);
							$cc = $queried_post->comment_count;
							if( $cc == $num || $cc > 1 ) : $cc = $cc.' Comments';
							else : $cc = $cc.' Comment';
							endif;
							$permalink = get_permalink($post_id);
							$output .= '<span class="post_comment">';
							$output .= '<a href="'. $permalink . '" class="comments_link">' . $cc . '</a>';
							$output .= '</span>';
						$output .= '</div>';
						// end post meta
					}
					$output .= cherry_get_post_networks(array('post_id' => $post_id, 'display_title' => false, 'output_type' => 'return'));
					if($excerpt_count >= 1){
						$output .= '<p class="excerpt">';
							$output .= wp_trim_words($excerpt,$excerpt_count);
						$output .= '</p>';
					}
                    
                    
      	             if ($portfolioInfo != ''){
            				$output .= '<h6>';
                				$output .= $portfolioInfo;
                				$output .= '</h6>';
                     }
                    
                    
					if($link){
						$output .= '<a href="'.get_permalink($post_id).'" class="btn btn-primary" title="'.get_the_title($post_id).'">';
						$output .= $link_text;
						$output .= '</a>';
					}
                    $output .= '</div>';
					$output .= '</li>';
					if ($j == count($posts)-1) {
						$output .= $output_end;
					}
				if ($count % $columns == 0) {
					$output .= '</ul><!-- .posts-grid (end) -->';
				}
			$count++;
			$i++;

		} // end for
		wp_reset_postdata(); // restore the global $post variable

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('posts_grid', 'posts_grid_shortcode');
}
    
    /*-----------------------------------------------------------------------------------*/
    /* Custom Comments Structure
    /*-----------------------------------------------------------------------------------*/
    if ( !function_exists( 'mytheme_comment' ) ) {
    	function mytheme_comment($comment, $args, $depth) {
    		$GLOBALS['comment'] = $comment;
    	?>
    	<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">
    		<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
    			<div class="wrapper">
    				<div class="comment-author vcard">
    					<?php echo get_avatar( $comment->comment_author_email, 70 ); ?>
    					<?php printf('<span class="author">%1$s</span>', get_comment_author_link()) ?>
    				</div>
    				<?php if ($comment->comment_approved == '0') : ?>
    					<em><?php echo theme_locals("your_comment") ?></em>
    				<?php endif; ?>
    				<div class="extra-wrap">
    					<?php comment_text() ?>  
            			<div class="wrapper extra2">
            				<div class="reply">
            					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            				</div>
            				<div class="comment-meta commentmetadata"><?php printf('%1$s', get_comment_date('d/m/Y')) ?></div>
            			</div>
    				</div>
    			</div>
    		</div>
    <?php }
    }       
       
 
     //------------------------------------------------------
    //  Related Posts
    //------------------------------------------------------
	if(!function_exists('cherry_related_posts')){
		function cherry_related_posts($args = array()){
			global $post;
			$default = array(
				'post_type' => get_post_type($post),
				'class' => 'related-posts',
				'class_list' => 'related-posts_list',
				'class_list_item' => 'related-posts_item',
				'display_title' => true,
				'display_link' => true,
				'display_thumbnail' => true,
				'width_thumbnail' => 170,
				'height_thumbnail' => 168,
				'before_title' => '<h3 class="related-posts_h">',
				'after_title' => '</h3>',
				'posts_count' => 4
			);
			extract(array_merge($default, $args));

			$post_tags = wp_get_post_terms($post->ID, $post_type.'_tag', array("fields" => "slugs"));
			$tags_type = $post_type=='post' ? 'tag' : $post_type.'_tag' ;
			$suppress_filters = get_option('suppress_filters');// WPML filter
			$blog_related = apply_filters( 'cherry_text_translate', of_get_option('blog_related'), 'blog_related' );
			if ($post_tags && !is_wp_error($post_tags)) {
				$args = array(
					"$tags_type" => implode(',', $post_tags),
					'post_status' => 'publish',
					'posts_per_page' => $posts_count,
					'ignore_sticky_posts' => 1,
					'post__not_in' => array($post->ID),
					'post_type' => $post_type,
					'suppress_filters' => $suppress_filters
					);
				query_posts($args);
				if ( have_posts() ) {
					$output = '<div class="'.$class.'">';
					$output .= $display_title ? $before_title.$blog_related.$after_title : '' ;
					$output .= '<ul class="'.$class_list.' clearfix">';
					while( have_posts() ) {
						the_post();
						$thumb   = has_post_thumbnail() ? get_post_thumbnail_id() : PARENT_URL.'/images/empty_thumb.gif';
						$blank_img = stripos($thumb, 'empty_thumb.gif');
						$img_url = $blank_img ? $thumb : wp_get_attachment_url( $thumb,'full');
						$image   = $blank_img ? $thumb : aq_resize($img_url, $width_thumbnail, $height_thumbnail, true) or $img_url;

						$output .= '<li class="'.$class_list_item.'">';
						$output .= $display_thumbnail ? '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink().'" title="'.get_the_title().'"><img data-src="'.$image.'" alt="'.get_the_title().'" /></a></figure>': '' ;
						$output .= $display_link ? '<a href="'.get_permalink().'" >'.get_the_title().'</a>': '' ;
						$output .= '</li>';
					}
					$output .= '</ul></div>';
					echo $output;
				}
				wp_reset_query();
			}
		}
	}    
 
    
 
    // Extra Wrap
     if (!function_exists('extra_wrap_shortcode')) {
          function extra_wrap_shortcode($atts, $content = null) {
          extract(shortcode_atts(array(
          'custom_class' => '',
          ), $atts));
              $output = '<div class="extra-wrap '.$custom_class.'">';
              $output .= do_shortcode($content);
              $output .= '</div>';
            
              return $output;
          }
          add_shortcode('extra_wrap', 'extra_wrap_shortcode');
     }
    



	/**
	 * Isotope view
	 *
	 */

	if ( !function_exists('shortcode_isotope_view') ) {
		function shortcode_isotope_view( $args ) {

			extract( shortcode_atts( array(
				'post_type'		 	=> 'post',
				'posts_count'    	=> 'all',
				'columns'		 	=> 3,
				'filter'		 	=> 'false',
				'filter_all_title'  => 'All',
				'fullwidth'		    => 'false',
				'layout'   			=> 'masonry',
				'thumb_width'		=> 750,
				'thumb_height'	 	=> 0,
				'excerpt_count' 	=> 0,
				'more_btn' 			=> '',
				'custom_class'   	=> ''
			), $args) );

			$rand_id = uniqid();
			$terms_category = $post_type == 'post' ? 'category' : $post_type . '_category';
			$posts_count = strval($posts_count) == 'all' ? -1 : intval( $posts_count );
			$excerpt_count = strval($excerpt_count) == 'all' ? -1 :  $excerpt_count;

			if($fullwidth == 'true') $fullwidth_class = " fullwidth-object";

			$output = '<div id="isotopeview-shortcode-' .$rand_id. '" class="isotopeview-shortcode'.$fullwidth_class.' '.$custom_class.'">';

				if($filter == 'true'){
					$output .= '<div class="isotopeview_filter_buttons">';
						$output .= '<div class="filter_button current-category" data-filter="*">'. $filter_all_title .'</div>';
						$terms = get_terms($terms_category);
						foreach ( $terms as $term ) {
						    $output .= '<div class="filter_button" data-filter="'.$term->slug.'">'.$term->name.'</div>';
						}
					$output .= '</div>';
				}


				// WP_Query arguments
				$args = array(
					'posts_per_page'      => $posts_count,
					'post_type'           => $post_type
				);

				// The Query
				$isotopeview_query = new WP_Query( $args );

				$output .= '<div class="isotopeview_wrapper" data-columns="'.$columns.'">';
					if ( $isotopeview_query->have_posts()) :
						$index = 1;
						while ( $isotopeview_query->have_posts() ) : $isotopeview_query->the_post();
							$post_id = $isotopeview_query->post->ID;

							$post_categories =  wp_get_post_terms( $post_id, $terms_category );
							$post_categories_slug = '';

							$portfolioPostFormat = get_post_meta($post_id, 'tz_portfolio_type', true);
							$blogPostFormat = get_post_format( $post_id );
							$prettyType ="isotopeViewPrettyPhoto";

							if (($blogPostFormat='gallery') || ($portfolioPostFormat == 'Slideshow') || ($portfolioPostFormat == 'Grid Gallery'))
								$prettyType = "isotopeViewPrettyPhoto[gallery".$index."]";

							foreach($post_categories as $c){
								$post_categories_slug .= ' ' .$c->slug;
							}

							if ( has_post_thumbnail( $post_id ) ) {
								$output .= '<div class="isotopeview-item isotopeview-item-'.$index.' '.$post_categories_slug.'">';
									$output .= '<figure class="thumbnail">';
										$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
										$url            = $attachment_url['0'];
										$image          = aq_resize($url, $thumb_width, $thumb_height, true);
	
										$output .= '<a rel="'.$prettyType.'" href="' .  $url . '" title="' . esc_html( get_the_title( $post_id ) ) . '" >';
											$output .= '<div class="enlarge-icon"></div><img src="'.$image.'" alt="" />';
										$output .= '</a>';
									$output .= '</figure>';

									if (($blogPostFormat='gallery') || ($portfolioPostFormat == 'Slideshow') || ($portfolioPostFormat == 'Grid Gallery')) {
										$images = get_children( array(
											'orderby'        => 'menu_order',
											'order'          => 'ASC',
											'post_type'      => 'attachment',
											'post_parent'    => $post_id,
											'post_mime_type' => 'image',
											'post_status'    => null,
											'numberposts'    => -1
										));

										if ( $images ) {
											foreach ( $images as $attachment_id => $attachment ) {
												$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
												$image            = aq_resize( $image_attributes[0], $image_size['width'], $image_size['height'], true );
												$image_title      = $attachment->post_title;
												$link_href = $image_attributes[0];

												$output .= '<a href="'.$link_href.'"  style="display:none" rel="'.$prettyType.'"></a>';
											}
										}
									}

									$output .= '<div class="isotopeview-item-content">';
										$output .= postTitleBuilder($post_id);
										$output .= postExcerptBuilder($post_id, $excerpt_count);
										$output .= postMoreLinkBuilder($post_id, $more_btn);
									$output .= '</div>';
								$output .= '</div>';

								$index++;
							}
						endwhile;
					endif; 
				$output .= '</div>';

			$output .= '</div>';

			$output .= '<script type="text/javascript">
				            jQuery(document).ready(function($) {
					            $("#isotopeview-shortcode-' .$rand_id. '").cherryIsotopeView({
					            	columns    : '. $columns .',
					            	fullwidth  : '. $fullwidth .',
					            	layout     : "'. $layout .'"
					            });
				            });
					    </script>';
			
			wp_reset_postdata();
			
			return $output;
		}
		add_shortcode( 'isotope_view', 'shortcode_isotope_view' );
	}

	// Title builder
	function postTitleBuilder($postID){
		$output = '';
		$post_title      = esc_html( get_the_title( $postID ) );
		$post_title_attr = esc_attr( strip_tags( get_the_title( $postID ) ) );
		if ( !empty($post_title{0}) ) {
			$output .= '<h5><a href="' . getPostPermalink($postID) . '" title="' . $post_title_attr . '">';
				$output .= $post_title;
			$output .= '</a></h5>';
		}
		return $output;
	}

	// Excerpt builder
	function postExcerptBuilder($postID, $excerpt_count){
		if($excerpt_count != 0){
			$output = '';

			if ( has_excerpt($postID) ) {
				$excerpt = wp_strip_all_tags( get_the_excerpt() );
			} else {
				$excerpt = wp_strip_all_tags( strip_shortcodes (get_the_content() ) );
			}

			if ( !empty($excerpt{0}) ) {
				$output .= $excerpt_count == -1 ? '<p class="excerpt">' . $excerpt . '</p>' : '<p class="excerpt">' . my_string_limit_words( $excerpt, $excerpt_count ) . '</p>';
			}

			return $output;
		}
	}

	// Link builder
	function postMoreLinkBuilder($postID, $linkText){
		$resultDOM = '';
		$linkText = esc_html( wp_kses_data( $linkText ) );
		$post_title_attr = esc_attr( strip_tags( get_the_title( $postID ) ) );
			if ( $linkText != '' ) {
				$resultDOM .= '<a href="' . get_permalink( $postID ) . '" class="btn btn-primary" title="' . $post_title_attr . '">';
					$resultDOM .= __( $linkText, CHERRY_PLUGIN_DOMAIN );
				$resultDOM .= '</a>';
			}
		return $resultDOM;
	}


	// get post's permalink 
	function getPostPermalink($postID){
		if ( get_post_meta( $postID, 'tz_link_url', true ) ) {
			$post_permalink = ( $format == 'format-link' ) ? esc_url( get_post_meta( $postID, 'tz_link_url', true ) ) : get_permalink( $postID );
		} else {
			$post_permalink = get_permalink( $postID );
		}
		return $post_permalink;
	}
    
?>