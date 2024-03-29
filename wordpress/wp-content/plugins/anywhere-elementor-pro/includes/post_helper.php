<?php

namespace Aepro;

use Aepro\Aepro_Post_List;
use Elementor\Plugin;
use Aepro\Modules\PostBlocks\Widgets\AePostBlocks;
use Aepro\Modules\Portfolio\Widgets\AePortfolio;

class Post_Helper {
	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_ae_post_data', [ $this, 'ajax_post_data' ] );
		add_action( 'wp_ajax_nopriv_ae_post_data', [ $this, 'ajax_post_data' ] );

		add_action( 'wp_ajax_ae_portfolio_data', [ $this, 'ajax_portfolio_data' ] );
		add_action( 'wp_ajax_nopriv_ae_portfolio_data', [ $this, 'ajax_portfolio_data' ] );

		add_action( 'wp_ajax_ae_term_data', [ $this, 'ajax_term_data' ] );
		add_action( 'wp_ajax_nopriv_ae_term_data', [ $this, 'ajax_term_data' ] );

		add_action( 'wp_ajax_ae_repeater_data', [ $this, 'ajax_repeater_data' ] );
		add_action( 'wp_ajax_nopriv_ae_repeater_data', [ $this, 'ajax_repeater_data' ] );
	}

	public function ajax_term_data() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$fetch_mode = $_REQUEST['fetch_mode'];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_type = $_REQUEST['post_type'];
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search = $_REQUEST['q'];

		$results = [];
		switch ( $fetch_mode ) {

			case 'terms':
				$args = [
					'object_type' => $post_type,
					'public'      => true,
					'_builtin'    => true,
				];

				$results = $this->get_all_terms( $args, 'object', $search );

				break;

			case 'selected_terms':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! empty( ( $_REQUEST['selected_terms'] ) ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$selected_terms = $_REQUEST['selected_terms'];
					$results        = $this->get_selected_terms_by_term_id( $selected_terms );
				}
				break;
		}

		wp_send_json_success( $results );
	}

	public function get_selected_terms_by_term_id( $selected_terms ) {
		$all_terms = [];
		$terms     = get_terms( [ 'include' => $selected_terms ] );
		foreach ( $terms as $term ) {
			$term_tax    = get_taxonomy( $term->taxonomy );
			$all_terms[] = [
				'id'   => $term_tax->name . ':' . $term->term_id,
				'text' => $term_tax->labels->name . ': ' . $term->name,
			];
		}
		return $all_terms;
	}

	public function get_all_terms( $args, $output, $search ) {
		$taxonomies = get_object_taxonomies( $args, $output );
		$all_terms  = [];
		foreach ( $taxonomies as $taxonomy => $object ) {
			$term_args = [
				'taxonomy'   => $object->name,
				'hide_empty' => false,
				'name__like' => $search,
			];
			$terms     = get_terms( $term_args );
			foreach ( $terms as $term ) {
				$all_terms[] = [
					'id'   => $object->name . ':' . $term->term_id,
					'text' => $object->label . ': ' . $term->name,
				];
			}
		}
		return $all_terms;
	}

	public function ajax_post_data() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'aep_ajax_nonce' ) ) {
			wp_die();
		}
		$fetch_mode = $_REQUEST['fetch_mode'];

		$results = [];
		switch ( $fetch_mode ) {
			case 'posts':
				$params = [
					's' => $_REQUEST['q'],
				];
				$query  = new \WP_Query( $params );

				foreach ( $query->posts as $post ) {
						$results[] = [
							'id'   => $post->ID,
							'text' => $post->post_title,
						];
				}
				break;

			case 'paged':
				ob_start();
				$this->get_widget_output( $_POST['pid'], $_POST['wid'] );
				$results = ob_get_contents();
				ob_end_clean();
				break;

			case 'selected_posts':
				$args  = [
					'post__in'  => $_POST['selected_posts'],
					'post_type' => 'any',
					'orderby'   => 'post__in',
				];
				$posts = get_posts( $args );
				if ( count( $posts ) ) {
					foreach ( $posts as $p ) {
						$results[] = [
							'id'   => $p->ID,
							'text' => $p->post_title,
						];
					}
				}
				break;
		}

		wp_send_json_success( $results );
	}


	public function get_widget_output( $post_id, $widget_id ) {
		$elementor = Plugin::$instance;

		$meta = $elementor->documents->get( $post_id )->get_elements_data();

		$widget = $this->find_element_recursive( $meta, $widget_id );

		$widget_instance    = $elementor->elements_manager->create_element_instance( $widget );
		$widget['settings'] = $widget_instance->get_active_settings();

		if ( isset( $widget['settings'] ) ) {

			if ( $widget['widgetType'] === 'ae-post-blocks' ) {
				$post_list = new AePostBlocks();
			} elseif ( $widget['widgetType'] === 'ae-portfolio' ) {
				$post_list = new AePortfolio();
			}

			$post_list->generate_output( $widget['settings'], false );
		}
	}

	private function find_element_recursive( $elements, $widget_id ) {
		foreach ( $elements as $element ) {
			if ( $widget_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $widget_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	public function get_authors() {
		$args = [
			'has_published_posts' => true,
			'fields'              => [
				'ID',
				'display_name',
			],
			'capability'          => [ 'edit_posts' ],
		];

		// Capability queries were only introduced in WP 5.9.
		if ( version_compare( $GLOBALS['wp_version'], '5.9-alpha', '<' ) ) {
			$args['who'] = 'authors';
			unset( $args['capability'] );
		}

		$user_query = new \WP_User_Query( $args );

		$authors = [];

		foreach ( $user_query->get_results() as $result ) {
			$authors[ $result->ID ] = $result->display_name;
		}

		return $authors;
	}

	public function get_taxonomy_terms( $taxonomy ) {

		$tax_array = [];
		$terms     = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			]
		);

		if ( count( $terms ) ) {
			foreach ( $terms as $term ) {
				$tax_array[ $term->term_id ] = $term->name;
			}
		}

		return $tax_array;
	}
	public function get_all_taxonomies() {
		$ae_taxonomy_filter_args = [
			'show_in_nav_menus' => true,
		];

		return get_taxonomies( $ae_taxonomy_filter_args, 'objects' );
	}

	public function get_taxonomies_by_post_type( $post_type ) {
		$tax_array  = [];
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		if ( isset( $taxonomies ) && count( $taxonomies ) ) {
			foreach ( $taxonomies as $tax ) {
				$tax_array[ $tax->name ] = $tax->label;
			}
		}
		return $tax_array;
	}

	public function get_aepro_the_archive_title() {
		if ( is_category() ) {
			/* translators: Category archive title. 1: Category name */
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			/* translators: Tag archive title. 1: Tag name */
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			/* translators: Author archive title. 1: Author name */
			$title = get_the_author();
		} elseif ( is_year() ) {
			/* translators: Yearly archive title. 1: Year */
			$title = get_the_date( _x( 'Y', 'yearly archives date format', 'ae-pro' ) );
		} elseif ( is_month() ) {
			/* translators: Monthly archive title. 1: Month name and year */
			$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'ae-pro' ) );
		} elseif ( is_day() ) {
			/* translators: Daily archive title. 1: Date */
			$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'ae-pro' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title', 'ae-pro' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title', 'ae-pro' );
			}
		} elseif ( is_post_type_archive() ) {
			/* translators: Post type archive title. 1: Post type name */
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$tax = get_taxonomy( get_queried_object()->taxonomy );
			/* translators: Taxonomy term archive title. 1: Taxonomy singular name, 2: Current taxonomy term */
			$title = single_term_title( '', false );
		} else {
			$title = __( 'Archives', 'ae-pro' );
		}

		/**
		 * Filters the archive title.
		 *
		 * @since 4.1.0
		 *
		 * @param string $title Archive title to be displayed.
		 */
		return apply_filters( 'ae_get_the_archive_title', $title );
	}
	public function get_aepro_the_archive_description( $term = 0, $taxonomy = 'post_tag' ) {
		return term_description( $term, $taxonomy );
	}

	public function ajax_repeater_data() {

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'aep_ajax_nonce' ) ) {
			wp_die();
		}

		$field_name = $_REQUEST['fieldName'];
		$post_id    = $_REQUEST['post_id'];
		$field_type = $_REQUEST['fieldType'];

		if ( get_post_type( $post_id ) === 'ae_global_templates' ) {
			$mode = get_post_meta( $post_id, 'ae_render_mode', true );
			if ( $mode === 'acf_repeater_layout' ) {

				$field_loc      = get_post_meta( $post_id, 'ae_repeater_loc', true );
				$acf_field_type = get_post_meta( $post_id, 'ae_acf_field_type', true );
				if ( $field_loc === 'option' ) {
					$data_id = 'option';

				} else {
					$data_id = get_post_meta( $post_id, 'ae_preview_post_ID', true );
				}

				$image_size = $_REQUEST['image_size'];
				if ( empty( $image_size ) ) {
					$image_size = 'full';
				}

				if ( $acf_field_type === 'repeater' ) {
					$repeaters = get_post_meta( $post_id, 'ae_acf_repeater_name', true );

					$repeater_fields_arr = explode( ':', $repeaters );
					$main_field          = get_field( $repeater_fields_arr[0], $data_id );

					array_shift( $repeater_fields_arr );

					$leaf  = $main_field;
					$value = $this->get_repeater_leaf_value( $main_field, $repeater_fields_arr, $field_name );
					if ( $field_type === 'image' ) {
						$field['value'] = wp_get_attachment_image_src( $value, $image_size );

					} elseif ( $field_type === 'color' ) {
						$field['value'] = $value;
					}
					$field['field_type'] = $field_type;

					wp_send_json_success( $field );

				} elseif ( $acf_field_type === 'flexible_content' ) {

					$flexible_field = $_REQUEST['flexible_field'];

					$main_field = explode( ':', $flexible_field );

					if ( $main_field[0] === 'option' ) {
						$parent_field_name = $main_field[2];
						$layout            = $main_field[3];
						$data_id           = 'option';
					} else {
						$parent_field_name = $main_field[1];
						$layout            = $main_field[2];
					}
					$flexible_content = get_field( $parent_field_name, $data_id );

					foreach ( $flexible_content as $key => $fc ) {
						if ( ! is_array( $fc ) || ( ! array_key_exists( 'acf_fc_layout', $fc ) ) ) {
							return;
						}
						//phpcs:ingore WordPress.PHP.StrictComparisons.LooseComparison
						if ( $fc['acf_fc_layout'] == $layout ) {
							$index = $key;
							break;
						}
					}
					$value = $flexible_content[ $index ][ $field_name ];
					if ( $field_type === 'image' ) {
						$field['value'] = wp_get_attachment_image_src( $value, $image_size );

					} elseif ( $field_type === 'color' ) {
						$field['value'] = $value;
					}
					$field['field_type'] = $field_type;
					wp_send_json_success( $field );
				}
			}
		}
	}

	public function get_repeater_leaf_value( $data, $field_arr, $field_name ) {
		if ( count( $field_arr ) === 0 ) {
			return $data[0][ $field_name ];
		}

		$data = $data[0][ $field_arr[0] ];
		array_shift( $field_arr );
		return $this->get_repeater_leaf_value( $data, $field_arr, $field_name );
	}

}

Post_Helper::instance();
