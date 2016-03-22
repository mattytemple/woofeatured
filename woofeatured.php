<?php

/**
	Plugin Name: WooFeatured | WooCommerce Category Featured Products
	Plugin URI: http://www.usingwordpressforbusiness.com/woofeatured/
	Description: Designate featured products to display at the top of your product categories
	Version: 1.0.0
	Author: Matthew Brown
	Author URI: http://www.usingwordpressforbusiness.com
	License: GPLv3
 */
 
 	// Add term page
	function woofeatured_taxonomy_add_new_meta_field() {
	    // this will add the custom meta field to the add new term page
	    ?>
	    <div class="form-field">
	        <label for="woofeatured_products[]"><?php _e( 'Featured Products', 'woofeatured' ); ?></label>
	        <select name="woofeatured_products[]" id="woofeatured_products[]">

	        	<?php

		        		$args = array(
		        				'post_type' => 'product',
		        				'tax_query' => array(
									array(
										'taxonomy' => 'product_cat',
										'terms'    => $term_id,
										'field'    => 'term_id'
									)
								)
		        			);

		        		$woofeatured_products = new WP_Query($args);
		        		while($woofeatured_products->have_posts()) : $woofeatured_products->the_post();

		        			$woofeatured_product_id = get_the_ID();
		        			$woofeatured_product_sku = get_post_meta(get_the_ID(), '_sku', true);
		        			$woofeatured_product_name = get_the_title();

		        			echo '<option value="'.$woofeatured_product_id.'">'.$woofeatured_product_sku.' | '.$woofeatured_product_name.'</option>';

		        		endwhile;

		        	?>

			</select>
	        <p class="description"><?php _e( 'Select the featured products for this category','woofeatured' ); ?></p>
	    </div>
	<?php
	}

	add_action( 'product_cat_add_form_fields', 'woofeatured_taxonomy_add_new_meta_field', 10, 2 );

	// Edit term page
	function woofeatured_taxonomy_edit_meta_field($term) {
	 
	    // put the term ID into a variable
	    $t_id = $term->term_id;
	 
	    // retrieve the existing value(s) for this meta field. This returns an array
	    $term_meta = get_option( "taxonomy_$t_id" ); ?>
	    <tr class="form-field">
	    <th scope="row" valign="top"><label for="woofeatured_products[]"><?php _e( 'Featured Product(s)', 'woofeatured' ); ?></label></th>
	        <td>
	            <select name="woofeatured_products[]" id="woofeatured_products[]" multiple="true" style="width:95%">

	            	<?php

		        		$args = array(
		        				'post_type' => 'product',
		        				'tax_query' => array(
									array(
										'taxonomy' => 'product_cat',
										'terms'    => $t_id,
										'field'    => 'term_id'
									)
								)
		        			);

		        		$woofeatured_products = new WP_Query($args);
		        		while($woofeatured_products->have_posts()) : $woofeatured_products->the_post();

		        			$woofeatured_product_id = get_the_ID();
		        			$woofeatured_product_sku = get_post_meta(get_the_ID(), '_sku', true);
		        			$woofeatured_product_name = get_the_title();

		        			echo '<option value="'.$woofeatured_product_id.'">'.$woofeatured_product_sku.' | '.$woofeatured_product_name.'</option>';

		        		endwhile;

		        	?>

	            </select>
	            <p class="description"><?php _e( 'Select the featured products for this category','woofeatured' ); ?></p>
	        </td>
	    </tr>

	    <?php echo $_POST['woofeatured_products']; ?>

	<?php
	}

	add_action( 'product_cat_edit_form_fields', 'woofeatured_taxonomy_edit_meta_field', 10, 2 );

	// Save extra taxonomy fields callback function.
	function save_taxonomy_custom_meta( $term_id ) {
	    if ( isset( $_POST['woofeatured_products'] ) ) {

	    	$t_id = $term_id;

	    	$woofeatured_products = implode(',', $_POST['woofeatured_products']);
	        // Save the option array.
	        update_option( "woofeatured_$t_id", $woofeatured_products );
	    }
	} 

	add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );  
	add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );


	function woofeatured_display() {

		$category = get_queried_object();
		$t_id = $category->term_id;

		$woofeatured_products = get_option('woofeatured_'. $t_id);
		$woofeatured_products = explode(',', $woofeatured_products);

		echo '<style>';
		echo '.woofeatured_product_block { width:48%;float:left;margin:0px;padding-right:2%;padding-bottom:10px;border-bottom:#f2f2f2 1px solid;margin-bottom:10px; }';
		echo '.woofeatured_product_block_right { padding-right:0px;padding-left:2%; }';
		echo '.woofeatured_product_block img { max-width:40%;float:left;margin:0px;margin-right:10px; }';
		echo '.woofeatured_product_block p.woofeatured_title { font-weight:bold;text-decoration:none; }';
		echo '.woofeatured_product_block a { text-decoration:none; }';
		echo '.woofeatured_product_block p { margin-bottom:10px; }';
		echo '</style>';

		echo '<div style="clear:both;">';

			echo '<h3>';
			echo 'Featured ';
			echo single_term_title();
			echo '</h3>';

			$woofeatured_product_number = 1;

			$args = array(
					'post_type' => 'product',
					'post' => array($woofeatured_products)
				);

				$woofeatured_product_query = new WP_Query($args);
				while ($woofeatured_product_query->have_posts()) : $woofeatured_product_query->the_post();

					$woofeatured_product_class = 'woofeatured_product_block';
					if($woofeatured_product_number % 2 == 0) {
						$woofeatured_product_class = 'woofeatured_product_block woofeatured_product_block_right';
					}

					echo '<div class="'.$woofeatured_product_class.'">';

					$woofeatured_product_id = get_the_ID();
					$woofeatured_product_url = get_permalink();
					$woofeatured_product_title = get_the_title();
					$woofeatured_product_sku = get_post_meta(get_the_ID(), '_sku', true);
					$woofeatured_product_price = get_post_meta(get_the_ID(), '_price', true);
					if(get_post_meta(get_the_ID(), '_sale_price', true)) {
						$woofeatured_product_price = get_post_meta(get_the_ID(), '_sale_price', true);
					}
					$woofeatured_product_image = wp_get_attachment_url( get_post_thumbnail_id($woofeatured_product_id) );

					global $wpdb;
				    $count = $wpdb->get_var("
				    SELECT COUNT(meta_value) FROM $wpdb->commentmeta
				    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				    WHERE meta_key = 'rating'
				    AND comment_post_ID = $woofeatured_product_id
				    AND comment_approved = '1'
				    AND meta_value > 0
					");

					$woofeatured_product_review_count = $count;
					if($woofeatured_product_review_count == '') {
						$woofeatured_product_review_count = 0;
					}

					$rating = $wpdb->get_var("
				    SELECT SUM(meta_value) FROM $wpdb->commentmeta
				    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				    WHERE meta_key = 'rating'
				    AND comment_post_ID = $woofeatured_product_id
				    AND comment_approved = '1'
					");

					echo '<a href="'.$woofeatured_product_url.'">';
					echo '<img src="'.$woofeatured_product_image.'" alt="'.$woofeatured_product_title.'" />';
					echo '</a>';

					echo '<a href="'.$woofeatured_product_url.'">';
					echo '<p class="woofeatured_title">'.$woofeatured_product_title.'</p>';
					echo '</a>';

					echo '<p>';
					echo 'SKU: '.$woofeatured_product_sku.'<br>';
					echo 'You Pay: $ '.$woofeatured_product_price;
					echo '</p>';

					if($woofeatured_product_review_count > 0) {

						$woofeatured_product_avg_rating = number_format($rating / $count, 2);

					echo '<p>';
					echo '<span class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'woocommerce'), $woofeatured_product_avg_rating).'"><span style="width:'.($woofeatured_product_avg_rating*16).'px"><span itemprop="ratingValue" class="rating">'.$woofeatured_product_avg_rating.'</span> </span></span>'.$woofeatured_product_review_count.' Reviews';
					echo '</p>';

					} else {

					echo '<p>';
					echo '<span class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'woocommerce'), $woofeatured_product_avg_rating).'"><span style="width:'.($woofeatured_product_avg_rating*16).'px"><span itemprop="ratingValue" class="rating">'.$woofeatured_product_avg_rating.'</span> </span></span>'.$woofeatured_product_review_count.' Reviews';
					echo '</p>';

					}

					echo '<p>';
					echo '<a href="'.$woofeatured_product_url.'" class="button add_to_cart_button">View Product</a>';
					echo '</p>';

					echo '</div>';

					$woofeatured_product_number++;

				endwhile;

		echo '<div style="width:100%;height:0px;clear:both;"></div>';

		echo '</div>';

	}

	add_action(  'woocommerce_before_shop_loop', 'woofeatured_display', 0  );

	// Create the admin menu link
	add_action('admin_menu', 'mdb_woofeatured_menu');

	function mdb_woofeatured_menu() {

	    add_submenu_page('woocommerce', __( 'Featured Products', 'mdb_woofeatured_menu' ), __( 'Featured Products', 'mdb_woofeatured_menu' ), 'administrator', 'woofeatured_settings', 'mdb_woofeatured_page');

	}

	// Create the plugin's options / settings page
	function mdb_woofeatured_page() {

?>

		<div class="wrap">

		<style>
			#icon-woocontact { background:url("/wp-content/plugins/woocontact/images/woocontact-icon.gif");background-repeat:no-repeat; }
			.woosasx_bluebar { background:#006699;
				/* Background Gradient */
				background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#0099E5), to(#006699));
				background: -webkit-linear-gradient(top, #0099E5, #006699);
				background: -moz-linear-gradient(top, #0099E5, #006699);
				background: -ms-linear-gradient(top, #0099E5, #006699);
				background: -o-linear-gradient(top, #0099E5, #006699);
			}
		</style>

		<div class="icon32" id="icon-woocontact"></div><h2>WooFeatured: Category Featured Products Display</h2>

		<div style="width:100%;height:1px;background:#ffffff;clear:both;margin-top:20px;margin-bottom:20px;"></div>

		<div style="width:1000px;float:left;margin:0px;">
			<div style="width:600px;float:left;margin:0px;">

				<h3>Getting Started Quick Guide</h3>

				<p>This extension allows you to quickly designate and display any number of featured products at the top of any of your WooCommerce product category pages.  Your featured products will appear above the standard product view grid, highlighting them for your customers and increasing sales of the specific products you want to sell more of!</p>
				<p><img src="http://localhost:81/woocommdev/wp-content/plugins/woofeatured/images/woofeatured-screenshot-01.jpg" alt="#" style="max-width:100%;" /></p>

				<p><strong>How To Set / Change A Category's Featured Products:</strong></p>

				<ol>
					<li>
						Go to the "Edit Product Category" page of the category you wish to add featured products to<br>
						(<em>Products -> Categories in the main left menu</em>)
					</li>
					<li>Find the "Featured Product(s)" option field on the category edit page</li>
					<li>
						Select the products you wish to feature for this category from the dropdown list<br>
						(<em>Hold CTRL and click to select multiple products - choose as many as you wish</em>)
					</li>
					<li>Save your changes &amp; and refresh your cache if you are using a caching plugin</li>
				</ol>

				<p><strong>Other Popular WooCommerce Extensions</strong></p>
				<p>Each of these plugins is designed to make your store more successful and profitable.</p>

				<div style="width:270px;float:left;margin:0px;">
					<h3>Product FAQs System</h3>
					<a href="http://codecanyon.net/item/woofaqs-woocommerce-product-faqs/8548254" target="_blank">
						<img src="https://0.s3.envato.com/files/100857208/WooCommerce-Product-FAQs-Banner.jpg" style="max-width:100%;" />
					</a>
				</div>
				<div style="width:270px;float:right;margin:0px;">
					<h3>Product Contact Tab</h3>
					<a href="http://codecanyon.net/item/woocommerce-product-contact-form-tab/5625449" target="_blank">
						<img src="https://0.s3.envato.com/files/67002840/WooCommerce-Product-Contact-Form-Tab-Banner.jpg" style="max-width:100%;" />
					</a>
				</div>

			</div>
			<div style="width:300px;padding:19px;border:#cccccc 1px solid;float:right;margin:0px;background:#ffffff;">
				<h2 style="margin-bottom:5px;">WooCommerce Category Featured Products <span style="color:#cccccc;">v1.0.0</span></h2>
				
				<h2 style="margin-bottom:5px;margin-top:10px;font-size:18px;line-height:18px;font-weight:bold;padding:0px;">Support &amp; Resources</h2>
				<p style="margin-top:0px;margin-bottom:10px;">Read documentation, FAQs and contact our online support for help getting started.</p>
				<ul style="margin:0px;margin-bottom:20px;">
					<li><a href="http://www.usingwordpressforbusiness.com/woofeatured/" target="_blank">Visit Support &amp; Resource Center</a></li>
					<li><a href="http://www.usingwordpressforbusiness.com/documents/woofeatured-changelog.txt" target="_blank">Read The Changelog</a></li>
				</ul>
				
				<h2 style="margin-bottom:5px;margin-top:10px;font-size:18px;line-height:18px;font-weight:bold;padding:0px;">More Plugins &amp; Themes</h2>
				<p style="margin-top:0px;margin-bottom:10px;">Read documentation, FAQs and contact our online support for help getting started.</p>
				<ul style="margin:0px;margin-bottom:20px;">
					<li><a href="http://www.usingwordpressforbusiness.com/plugins/" target="_blank">View All Plugins</a></li>
				</ul>
				
				<h2 style="margin-bottom:5px;margin-top:10px;font-size:18px;line-height:18px;font-weight:bold;padding:0px;">Like It? Show Some Love</h2>
				<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FWP4Biz&amp;width=300&amp;height=25&amp;colorscheme=light&amp;layout=standard&amp;action=like&amp;show_faces=false&amp;send=false&amp;appId=467671163295082" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:25px;" allowTransparency="true"></iframe>
			</div>
			<div style="width:300px;height:20px;line-height:20px;padding:20px;padding-top:10px;padding-bottom:10px;float:right;margin:0px;clear:right;" class="woosasx_bluebar">
				<div style="width:200px;height:20px;line-height:20px;float:left;margin:0px;color:#ffffff;font-weight:bold;">
					Created By Matthew Brown
				</div>
				<div style="width:90px;height:20px;line-height:20px;float:right;margin:0px;text-align:right;color:#f2f2f2;">
					<a href="http://www.blog.mattbrownsart.com" style="color:#ffffff;text-decoration:none;" target="_blank">Blog</a>
				</div>
			</div>
		</div>
	</div>

<?php

	}

?>