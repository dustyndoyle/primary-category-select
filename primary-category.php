<?php
/*
 * Plugin Name: Dustyn Doyle Primary Category
 * Version: 1.0
 * Description: This plugin adds the ability to select a Primary Category
 * Author: Dustyn Doyle
 * Author URI: http://www.dustyndoyle.com
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'DldPrimaryCategory' ) ) {

	class DldPrimaryCategory {

		public function __construct() {

			add_action( 'add_meta_boxes', array( $this, 'add_primary_category_dropdown' ) );
			add_action( 'save_post', array( $this, 'save_primary_category_value' ), 10, 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
			add_filter( 'post_link_category', array( $this, 'change_post_category_permalink' ), 10, 3 );
			// Reorder the terms for anything displaying the first term as the category
			add_filter( 'get_the_terms', array( $this, 'reorder_terms' ), 10, 3 );
		}

		public function add_primary_category_dropdown() {

			add_meta_box( 'dld_primary_category', 'Primary Category', array(
				$this,
				'primary_category_dropdown_content'
			), array( 'post' ), 'side', 'high' );
		}

		public function primary_category_dropdown_content( $post ) {

			wp_nonce_field( basename( __FILE__ ), 'meta-box-nonce' );

			$taxonomy = 'category';

			// Get the Categories selected for this post
			$post_categories = $this->get_post_categories( $post->ID, $taxonomy );

			// Get an existing value if it exists
			$primary_category_value = $this::get_primary_category( $post->ID );

			?>
			<div>
				<label for="dld_primary_category_select">Select this Post's Primary Category</label>
				<select name="dld_primary_category_select" id="dld_primary_category_select">
					<?php
					if( !empty( $post_categories ) ) {

						foreach ( $post_categories as $post_category ) {

							// Add an option to the select if categories are selected
							echo '<option value="' . $post_category->term_id . '" ' . ( $post_category->term_id == $primary_category_value ? " selected" : "" ) . '>' . $post_category->name . '</option>';
						}
					}
					?>
				</select>
			</div>
			<?php
		}

		public function save_primary_category_value( $post_id, $post, $update ) {

			// If the nonce value is not set, bail
			if (
				! isset( $_POST["meta-box-nonce"] )
				|| ! wp_verify_nonce( $_POST["meta-box-nonce"], basename( __FILE__ ) )
			) {

				return $post_id;
			}

			// If we are not on a Post this metabox should not show up so it should not save
			if ( 'post' !== $post->post_type ) {

				return $post_id;
			}

			$primary_category_value = '';

			// There is a Primary Category Value Selected
			// Get the value and assign it the variable
			if ( isset( $_POST['dld_primary_category_select'] ) ) {

				$primary_category_value = $_POST['dld_primary_category_select'];
			}

			// Update the field in the database so we can get it
			$this->update_primary_category( $post_id, $primary_category_value );
		}

		public function enqueue_js( $hook ) {
			global $post;

			if( 'post.php' !== $hook && 'post-new.php' !== $hook ) {

				return;
			}

			// Make sure we only enqueue the script on Posts because that is the only place we want it
			if( 'post' !== $post->post_type ) {

				return;
			}

			wp_enqueue_script( 'dld_primary_category_script', plugin_dir_url( __FILE__ ) . 'assets/js/primary-category.js', array( 'jquery' ), '1.0.0', true );
		}

		public function change_post_category_permalink( $cat, $cats, $post ) {

			$primary_category = $this::get_primary_category( $post->ID );

			// If there is a Primary Category selected and it is not the current category permalink
			// change the permalink to the Primary Category selected
			if ( $this::has_primary_category( $post->ID ) && $primary_category !== $cat->cat_id ) {

				$cat = get_category( $primary_category );
			}

			return $cat;
		}

		public function reorder_terms( $terms, $post_id, $taxonomy ) {

			$primary_category = $this::get_primary_category( $post_id );

			// If this post has a primary category we can sort them
			if ( $this::has_primary_category( $post_id ) ) {

				foreach ( $terms as $key => $term ) {

					// This term is the Primary Category term
					if ( $primary_category == $term->term_id ) {

						// Remove the term from the array
						unset( $terms[ $key ] );

						// Place the term at the beginning of the array
						array_unshift( $terms, $term );
					}
				}
			}

			return $terms;
		}

		protected function get_post_categories( $post_id, $taxonomy ) {

			return get_the_terms( $post_id, $taxonomy );
		}

		public static function get_primary_category( $post_id ) {

			return get_post_meta( $post_id, 'dld_primary_category_select', true );
		}

		protected function update_primary_category( $post_id, $primary_category ) {

			return update_post_meta( $post_id, 'dld_primary_category_select', $primary_category );;
		}

		public static function has_primary_category( $post_id ) {

			$primary_category = self::get_primary_category( $post_id );

			if( !empty( $primary_category ) ) {

				return true;
			}

			return false;
		}
	}
}

new DldPrimaryCategory();