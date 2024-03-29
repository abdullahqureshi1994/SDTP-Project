<?php

namespace Aepro\Modules\Portfolio\Widgets;

use Aepro\Aepro;
use Aepro\Frontend;
use Aepro\Modules\PostBlocks\Widgets\AePostBlocks;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AePortfolio extends AePostBlocks {

	protected $_access_level = 1;

	public function get_name() {

		return 'ae-portfolio';
	}

	public function get_title() {

		return __( 'AE - Portfolio', 'ae-pro' );
	}

	public function get_keywords() {
		return [
			'posts',
			'cpt',
			'item',
			'blocks',
			'loop',
			'query',
			'portfolio',
			'custom post type',
		];
	}
	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {

		parent::register_controls();

		$post_types = Aepro::$_helper->get_rule_post_types();

		$this->remove_control( 'show_infinite_scroll' );

		$this->remove_control( 'disable_ajax' );

		$this->update_control(
			'layout_mode',
			[
				'options' => [
					'list' => __( 'List', 'ae-pro' ),
					'grid' => __( 'Grid', 'ae-pro' ),
				],
			]
		);

		$this->update_control(
			'ae_post_type',
			[
				'options' => $post_types,
			]
		);

		$this->update_control(
			'show_pagination',
			[
				'condition' => [
					'layout_mode' => [ 'grid', 'list' ],
				],
			]
		);

		$this->update_control(
			'pagination_contols',
			[
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->update_control(
			'pagination_style',
			[
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->update_control(
			'enable_overlay',
			[
				'condition' => [],
			]
		);

		$this->start_controls_section(
			'filter_bar',
			[
				'label' => __( 'Filter Bar', 'ae-pro' ),
			]
		);

		$this->add_control(
			'show_filters',
			[
				'label'        => __( 'Show Filter Bar', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'ae-pro' ),
				'label_off'    => __( 'Hide', 'ae-pro' ),
				'return_value' => 'yes',
				'condition'    => [
					'ae_post_type!' => 'current_loop',
				],
			]
		);

		$this->add_control(
			'filter_taxonomy',
			[
				'label'     => __( 'Taxonomy', 'ae-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => Aepro::$_helper->get_rules_taxonomies(),
				'condition' => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->add_control(
			'only_parent_term',
			[
				'label'        => __( 'Only Parent', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '0',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => '0',
				'condition'    => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_all',
			[
				'label'        => __( "Show 'All' ", 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'ae-pro' ),
				'label_off'    => __( 'Hide', 'ae-pro' ),
				'return_value' => 'yes',
				'condition'    => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->add_control(
			'tab_all_text',
			[
				'label'     => __( "Tab 'All' Text", 'ae-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'All',
				'condition' => [
					'show_filters' => 'yes',
					'show_all'     => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_label',
			[
				'label'     => __( 'Label', 'ae-pro' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'filter_align',
			[
				'label'     => __( 'Align', 'ae-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left' => [
						'title' => __( 'Left', 'ae-pro' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'ae-pro' ),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'ae-pro' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .aep-filter-bar' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar_style',
			[
				'label'     => __( 'Filter Bar', 'ae-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filters' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'hor_gap',
			[
				'label'     => __( 'Horizontal Gap', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .filter-items' => 'margin-left: calc({{SIZE}}{{UNIT}}/2); margin-right: calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_responsive_control(
			'ver_gap',
			[
				'label'     => __( 'Vertical Gap', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-post-list-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [ '5' ],
				'selectors'  => [
					'{{WRAPPER}} .filter-items, {{WRAPPER}} .filter-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'filter_item_border',
				'label'    => __( 'Border', 'ae-pro' ),
				'selector' => '{{WRAPPER}} .filter-items',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => __( 'Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .filter-items',
			]
		);

		$this->add_control(
			'label_heading',
			[
				'label'     => __( 'Label', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'label'    => __( 'Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .filter-label',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .filter-label' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->start_controls_tabs( 'filter_styles' );

			$this->start_controls_tab( 'filter_style_normal', [ 'label' => __( 'Normal', 'ae-pro' ) ] );

				$this->add_control(
					'filter-color',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .filter-items a' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'filter_item_bg',
					[
						'label'     => __( 'Background', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .filter-items' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'filter_item_border_radius',
					[
						'label'      => __( 'Border Radius', 'ae-pro' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .filter-items' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filter_style_hover', [ 'label' => __( 'Hover/Active', 'ae-pro' ) ] );

				$this->add_control(
					'filter-hover-color',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
						'selectors' => [
							'{{WRAPPER}} .filter-items:hover a, {{WRAPPER}} .filter-items.active a' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'filter_item_hover_bg',
					[
						'label'     => __( 'Background', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .filter-items:hover, {{WRAPPER}} .filter-items.active' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'filter-hover-border-color',
					[
						'label'     => __( 'Border Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
						'selectors' => [
							'{{WRAPPER}} .filter-items:hover, {{WRAPPER}} .filter-items.active' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'filter_item_hover_border_radius',
					[
						'label'      => __( 'Border Radius', 'ae-pro' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .filter-items:hover, {{WRAPPER}} .filter-items.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
						],
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function generate_output( $settings, $with_wrapper = true ) {
		$post_type = $settings['ae_post_type'];

		list($post_items, $query_args) = $this->get_posts( $settings );

		if ( ! isset( $query_args ) && $post_type !== 'current_loop' ) {
			return;
		}

		$masonry = $settings['masonry_grid'];
		$this->add_render_attribute( 'post-list-wrapper', 'class', 'ae-post-list-wrapper' );

		$this->add_render_attribute( 'post-widget-wrapper', 'data-pid', get_the_ID() );
		$this->add_render_attribute( 'post-widget-wrapper', 'data-wid', $this->get_id() );
		$this->add_render_attribute( 'post-widget-wrapper', 'data-source', $settings['ae_post_type'] );
		$this->add_render_attribute( 'post-widget-wrapper', 'class', 'ae-post-widget-wrapper' );
		$this->add_render_attribute( 'post-widget-wrapper', 'class', 'ae-masonry-' . $masonry );

		if ( $settings['show_all'] !== 'yes' ) {
			$this->add_render_attribute( 'post-widget-wrapper', 'class', 'ae-tab-all-disabled' );
		}

		$this->add_render_attribute( 'post-list-item', 'class', 'ae-post-list-item' );

		$with_css = false;
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$with_css = true;
		}
		?>
		<?php if ( $settings['enable_overlay'] ) { ?>
		<div class="ae-post-overlay">
			<div class="ae-post-overlay-wrapper">
				<div class="ae-post-overlay-icon">
					<?php Icons_Manager::render_icon( $settings['overlay_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</div>
				<div class="ae-post-overlay-text">
					<?php echo $settings['overlay_text']; ?>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if ( $with_wrapper ) { ?>
			<?php $this->get_widget_title_html(); ?>
			<div <?php echo $this->get_render_attribute_string( 'post-widget-wrapper' ); ?>>
		<?php } ?>

		<?php
		if ( $settings['show_filters'] === 'yes' ) {

			if ( $settings['filter_taxonomy'] === '' ) {
				printf( '<div class"message"><p class="%1$s">%2$s</p></div>', esc_attr( 'elementor-alert elementor-alert-warning' ), esc_html( __( "Please select taxonomy from 'Content > Filter Bar > Taxonomy'", 'ae-pro' ) ) );
			}
				$filter_terms = $this->get_filters( $settings );
			if ( is_array( $filter_terms ) && count( $filter_terms ) ) {
				?>
					<div class="aep-filter-bar">
					<?php
					if ( $settings['filter_label'] !== '' ) {
						?>
								<div class="filter-label"><?php echo $settings['filter_label']; ?></div>
						<?php
					}
						$first_term_element = 0;
					if ( $settings['show_all'] === 'yes' ) {
						?>
						<?php //phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.PHP.StrictComparisons.LooseComparison, WordPress.WP.I18n.NonSingularStringLiteralText ?>
								<div class="filter-items <?php echo ( ( isset( $_POST['term_id'] ) && $_POST['term_id'] == 0 ) || ! count( $_POST ) ) ? 'active' : ''; ?>"><a  href="#" data-term-id="0"><?php echo esc_html__( $settings['tab_all_text'], 'ae-pro' ); ?></a></div>
							<?php
					} else {
						$first_term_element = $filter_terms[0]->term_id;
					}
					foreach ( $filter_terms as $term ) {
						?>
						<?php //phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.PHP.StrictComparisons.LooseComparison ?>
								<div class="filter-items <?php echo ( ( isset( $_POST['term_id'] ) && $_POST['term_id'] == $term->term_id ) || ( $term->term_id === $first_term_element && ! isset( $_POST['term_id'] ) ) ) ? 'active' : ''; ?>"><a href="#" data-term-id="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></a></div>
							<?php
					}
					?>
					</div>
					<?php
			}
			?>

		<?php } ?>

		<div <?php echo $this->get_render_attribute_string( 'post-list-wrapper' ); ?>>
			<?php
			if ( $post_items->have_posts() ) {
				while ( $post_items->have_posts() ) {
					$post_items->the_post();

					Frontend::$_ae_post_block = get_the_ID();
					?>
					<article <?php echo $this->get_render_attribute_string( 'post-list-item' ); ?>>
						<div class="ae-article-inner">
							<?php if ( !Plugin::$instance->preview->is_preview() && !Plugin::$instance->editor->is_edit_mode()) { ?>
							<div class="ae_data elementor elementor-<?php echo $settings['template']; ?>">
							<?php } ?>
								<?php echo Plugin::instance()->frontend->get_builder_content( $settings['template'], $with_css ); ?>
							<?php if ( !Plugin::$instance->preview->is_preview() && !Plugin::$instance->editor->is_edit_mode()) { ?>
							</div>
							<?php } ?>
						</div>
					</article>
					<?php
				}
				Frontend::$_ae_post_block = 0;
			} else {
				?>
				<div class="ae-no-posts">
					<?php echo do_shortcode( $settings['no_posts_message'] ); ?>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>


		<?php
		if ( $settings['show_pagination'] === 'yes' ) {
			$this->add_render_attribute( 'pagination-wrapper', 'class', 'ae-pagination-wrapper' );
			?>
			<div <?php echo $this->get_render_attribute_string( 'pagination-wrapper' ); ?>>
				<?php
				$current = 1;
				//phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['page_num'] ) ) {
					//phpcs:ignore WordPress.Security.NonceVerification.Missing
					$current = $_POST['page_num'];
				}
				//phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['pid'] ) ) {
					//phpcs:ignore WordPress.Security.NonceVerification.Missing
					$current_post_id = $_POST['pid'];
				} else {
					$current_post_id = get_the_ID();
				}

				$base_url = get_permalink( $current_post_id );

				$page_limit = $post_items->max_num_pages;

				if ( '' !== $settings['pagination_page_limit'] ) {
					$page_limit = min( $settings['pagination_page_limit'], $page_limit );
				}

				if ( 2 > $page_limit ) {
					return;
				}

				$paginate_args = [
					'base'    => $base_url . '%_%',
					'total'   => $page_limit,
					'current' => $current,
				];

				if ( $post_type === 'current_loop' ) {
					unset( $paginate_args['base'] );
					$current = get_query_var( 'paged' );
					//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( $current == 0 ) {
						$paginate_args['current'] = 1;
					} else {
						$paginate_args['current'] = $current;
					}
				}

				if ( $settings['show_prev_next'] === 'yes' ) {
					$paginate_args['prev_next'] = true;
					$paginate_args['prev_text'] = $settings['prev_text'];
					$paginate_args['next_text'] = $settings['next_text'];
				} else {
					$paginate_args['prev_next'] = false;
				}

				echo Aepro::$_helper->paginate_links( $paginate_args );
				?>
			</div>
		<?php } ?>

		<?php if ( $with_wrapper ) { ?>
			</div>
		<?php } ?>

		<?php
	}

	public function get_filters( $settings ) {

		$filter_taxonomy = $settings['filter_taxonomy'];

		// check if post have terms selected for this taxonomy

		if ( isset( $settings[ $filter_taxonomy . '_ae_ids' ] ) && is_array( $settings[ $filter_taxonomy . '_ae_ids' ] ) && count( $settings[ $filter_taxonomy . '_ae_ids' ] ) ) {
			// just return the list of these terms
			$terms = get_terms(
				$filter_taxonomy,
				[
					'hide_empty'       => true,
					'term_taxonomy_id' => $settings[ $filter_taxonomy . '_ae_ids' ],
				]
			);
		} else {
			$terms = get_terms(
				$filter_taxonomy,
				[
					'hide_empty' => true,
					'parent'     => $settings['only_parent_term'],
				]
			);
		}

		return $terms;
	}
}
