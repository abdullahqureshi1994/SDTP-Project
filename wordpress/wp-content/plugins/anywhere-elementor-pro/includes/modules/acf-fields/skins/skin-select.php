<?php

namespace Aepro\Modules\AcfFields\Skins;

use Aepro\Aepro;
use Aepro\Modules\AcfFields;
use Aepro\Classes\AcfMaster;
use Elementor\Group_Control_Box_Shadow;
use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Plugin as EPlugin;


class Skin_Select extends Skin_Base {

	public function get_id() {
		return 'select';
	}

	public function get_title() {
		return __( 'Select', 'ae-pro' );
	}
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function _register_controls_actions() {

		parent::_register_controls_actions();
		add_action( 'elementor/element/ae-acf/general/after_section_end', [ $this, 'register_style_controls' ] );
		add_action( 'elementor/element/ae-acf/general/after_section_end', [ $this, 'register_fallback' ] );
		add_action( 'elementor/element/ae-acf/select_general-style/after_section_end', [ $this, 'register_fallback_style' ] );
	}

	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		parent::register_select_controls();
	}

	public function register_fallback() {
		if(!$this->load_skin_controls(['select', 'button_group', 'checkbox', 'radio', 'taxonomy' ])){
			return;
		}
		$this->register_fallback_controls();
	}

	public function register_style_controls() {

		if(!$this->load_skin_controls(['select', 'button_group', 'checkbox', 'radio', 'taxonomy' ])){
			return;
		}

		$this->start_controls_section(
			'list_styles',
			[
				'label'     => __( 'List Styles', 'ae-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label'     => __( 'Space Between', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 8,
				],
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ae-icon-list-items:not(.ae-list-horizontal) .ae-icon-list-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2); margin-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .ae-icon-list-items:not(.ae-list-horizontal) .ae-icon-list-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .ae-icon-list-items:not(.ae-list-horizontal) .ae-icon-list-item:after' => 'bottom: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .ae-icon-list-items.ae-list-horizontal .ae-icon-list-item' => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .ae-icon-list-items.ae-list-horizontal' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body.rtl {{WRAPPER}} .ae-icon-list-items.ae-list-horizontal .ae-icon-list-item:after' => 'left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body:not(.rtl) {{WRAPPER}} .ae-icon-list-items.ae-list-horizontal .ae-icon-list-item:after' => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => __( 'Style', 'ae-pro' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'options'   => [
					'solid'  => __( 'Solid', 'ae-pro' ),
					'double' => __( 'Double', 'ae-pro' ),
					'dotted' => __( 'Dotted', 'ae-pro' ),
					'dashed' => __( 'Dashed', 'ae-pro' ),
				],
				'default'   => 'solid',
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ae-icon-list-items:not(.ae-list-horizontal) .ae-icon-list-item:not(:last-child):after' => 'border-top-style: {{VALUE}}',
					'{{WRAPPER}} .ae-icon-list-items.ae-list-horizontal .ae-icon-list-item:not(:last-child):after' => 'border-left-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => __( 'Weight', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ae-icon-list-items:not(.ae-list-horizontal) .ae-icon-list-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-list-horizontal .ae-icon-list-item:not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_width',
			[
				'label'     => __( 'Width', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => '%',
				],
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
					$this->get_control_id( 'layout!' ) => 'horizontal',
				],
				'selectors' => [
					'{{WRAPPER}} .ae-icon-list-item:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_height',
			[
				'label'      => __( 'Height', 'ae-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'default'    => [
					'unit' => '%',
				],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition'  => [
					$this->get_control_id( 'divider' ) => 'yes',
					$this->get_control_id( 'layout' )  => 'horizontal',
				],
				'selectors'  => [
					'{{WRAPPER}} .ae-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ddd',
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'condition' => [
					$this->get_control_id( 'divider' ) => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .ae-icon-list-item:not(:last-child):after' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'general-style',
			[
				'label' => __( 'General', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} span',
			]
		);

		$this->start_controls_tabs( 'style' );

			$this->start_controls_tab(
				'normal_style',
				[
					'label' => __( 'Normal', 'ae-pro' ),
				]
			);

				$this->add_control(
					'item_color',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} span' => 'color:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'item_bg_color',
					[
						'label'     => __( 'Background Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} span' => 'background:{{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'item_border',
						'label'    => __( 'Border', 'ae-pro' ),
						'selector' => '{{WRAPPER}} span',
					]
				);

				$this->add_control(
					'item_border_radius',
					[
						'label'      => __( 'Border Radius', 'ae-pro' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} span   ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'item_box_shadow',
						'label'    => __( 'Item Shadow', 'ae-pro' ),
						'selector' => '{{WRAPPER}} span',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'hover_style',
				[
					'label' => __( 'Hover', 'ae-pro' ),
				]
			);

				$this->add_control(
					'item_color_hover',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} span:hover' => 'color:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'item_bg_color_hover',
					[
						'label'     => __( 'Background Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} span:hover' => 'background:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'item_border_color_hover',
					[
						'label'     => __( 'Border Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} span:hover' => 'border-color:{{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'border_radius_hover',
					[
						'label'     => __( 'Border Radius', 'ae-pro' ),
						'type'      => Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
						],

					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'item_hover_box_shadow',
						'label'    => __( 'Item Shadow', 'ae-pro' ),
						'selector' => '{{WRAPPER}} span:hover',
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'padding',
			[
				'label'     => __( 'Padding', 'ae-pro' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],

			]
		);

		$this->add_responsive_control(
			'margin',
			[
				'label'     => __( 'Margin', 'ae-pro' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],

			]
		);

		$this->add_control(
			'non_selected_heading',
			[
				'label'     => __( 'Not Selected Items', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'show_all_choices' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'color_non_selected',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} li.ae-no-select .ae-icon-list-text' => 'color:{{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'show_all_choices' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'typography_no_select',
				'selector'  => '{{WRAPPER}} li.ae-no-select .ae-icon-list-text',
				'condition' => [
					$this->get_control_id( 'show_all_choices' ) => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	public function register_fallback_style() {

		if(!$this->load_skin_controls(['select', 'button_group', 'checkbox', 'radio', 'taxonomy' ])){
			return;
		}
		$this->fallback_style_controls();
	}

	public function render() {

		$list_items = [];

		$settings = $this->parent->get_settings();

		$field_args = [
			'field_type'   => $settings['field_type'],
			'is_sub_field' => $settings['is_sub_field'],
		];

		$accepted_parent_fields = [ 'repeater', 'group', 'flexible' ];
		//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( in_array( $settings['is_sub_field'], $accepted_parent_fields ) ) {
			$field_args['_skin'] = $settings['_skin'];
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $settings['is_sub_field'] == 'flexible' ) {
				$field_args['field_name']     = $settings['flex_sub_field'];
				$field_args['flexible_field'] = $settings['flexible_field'];
				if ( ! empty( $settings['flexible_field'] ) ) {
					$field_data = explode( ':', $field_args['flexible_field'] );
					if ( $field_data[0] === 'option' ) {
						$parent_field_name = $field_data[2];
						$layout            = $field_data[3];
					} else {
						$parent_field_name = $field_data[1];
						$layout            = $field_data[2];
					}
					$field_args['parent_field'] = $parent_field_name;
					$field_args['layout']       = $layout;
				}
				//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			} elseif ( $settings['is_sub_field'] == 'repeater' ) {
				$field_args['field_name']   = $settings['repeater_sub_field'];
				$field_args['parent_field'] = $settings['repeater_field'];
			} else {
				$field_args['field_name']   = $settings['field_name'];
				$field_args['parent_field'] = $settings['parent_field'];
			}
		} else {
			$field_args['field_name'] = $settings['field_name'];
		}

		if ( $settings['field_type'] === 'term' ) {
			$term         = Aepro::$_helper->get_preview_term_data();
			$field_object = AcfMaster::instance()->get_field_object( $field_args, $term );
		} elseif ( $settings['field_type'] === 'post' ) {
			$post         = Aepro::$_helper->get_demo_post_data();
			$field_object = AcfMaster::instance()->get_field_object( $field_args, $post->ID );
		} elseif ( $settings['field_type'] === 'option' ) {
			$field_object = AcfMaster::instance()->get_field_object( $field_args, 'option' );
		} elseif ( $settings['field_type'] === 'user' ) {
			$author       = Aepro::$_helper->get_preview_author_data();
			$field_object = AcfMaster::instance()->get_field_object( $field_args, 'user_' . $author['prev_author_id'] );
		}
		if ( empty( $field_object ) ) {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'enable_fallback' ) != 'yes' ) {
				return;
			} else {
				$this->render_fallback_content( $settings );
				return;
			}
		}

		$return_format = $field_object['return_format'];
		$selected      = AcfMaster::instance()->get_field_value( $field_args );
		$value         = [];
		//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		if ( $field_object['type'] === 'checkbox' && $field_object['allow_custom'] == 1 && $field_object['save_custom'] == 0 ) {
			if ( ! empty( $selected ) ) {
				switch ( $return_format ) {
					case 'array':
						$is_nested_array = false;
						if ( array_key_exists( 0, $selected ) ) {
							$is_nested_array = true;
						}

						if ( $is_nested_array ) {
							foreach ( $selected as $select ) {
								$value[] = $select['value'];
							}
						} else {
							$value[] = $selected['value'];
						}
						break;
					default:
						foreach ( $selected as $key => $select ) {
									$value[] = $select;
						}
				}
			}
		} else {
			switch ( $return_format ) {
				case 'label':
					foreach ( $field_object['choices'] as $key => $label ) {
						if ( is_array( $selected ) ) {
							if ( in_array( $label, $selected, true ) ) {
								$value[] = $key;
							}
						} else {
							if ( $label === $selected ) {
								$value[] = $key;
							}
						}
					}

					break;
				case 'array':
					if ( empty( $selected ) ) {
						$value = [];
						break;
					}

					$is_nested_array = false;
					if ( array_key_exists( 0, $selected ) ) {
						$is_nested_array = true;
					}

					if ( $is_nested_array ) {
						foreach ( $selected as $select ) {
							$value[] = $select['value'];
						}
					} else {
						$value[] = $selected['value'];
					}
					break;
				default:
					foreach ( $field_object['choices'] as $key => $label ) {
						if ( is_array( $selected ) ) {
							if ( in_array( $key, $selected, true ) ) {
								$value[] = $key;
							}
						} else {
							if ( $key === $selected ) {
								$value[] = $key;
							}
						}
					}
			}
		}

		$data_type        = $this->get_instance_value( 'data_type' );
		$show_all_choices = $this->get_instance_value( 'show_all_choices' );
		$separator        = $this->get_instance_value( 'separator' );
		$divider          = $this->get_instance_value( 'divider' );
		$layout           = $this->get_instance_value( 'layout' );

		$this->parent->add_render_attribute( 'wrapper', 'class', 'ae-acf-wrapper' );
		$this->parent->add_render_attribute( 'wrapper', 'class', 'ae-list-' . $layout );
		$this->parent->add_render_attribute( 'wrapper', 'class', 'ae-icon-list-items' );

		if ( $separator !== '' && $divider === '' ) {
			$this->parent->add_render_attribute( 'wrapper', 'class', 'ae-custom-sep' );
		}

		if ( $layout === 'vertical' ) {
			$separator = '';
		}
		if ( EPlugin::$instance->editor->is_edit_mode() ) {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'preview_fallback' ) == 'yes' ) {
				$this->render_fallback_content( $settings );
			}
		}

		//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		if ( empty( $value ) && $show_all_choices != 'yes' ) {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'enable_fallback' ) != 'yes' ) {
				return;
			} else {
				$this->render_fallback_content( $settings );
				return;
			}
		}

		if ( ! empty( $field_object ) ) {

			?>
		<ul <?php echo $this->parent->get_render_attribute_string( 'wrapper' ); ?>>
			<?php

			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $field_object['type'] === 'checkbox' && $field_object['allow_custom'] == 1 && $field_object['save_custom'] == 0 ) {
					$this->show_custom_options( $value );
			} else {
					// show key
				if ( $data_type === 'key' ) {

					$this->show_key( $value, $show_all_choices, $field_object );
				} else {
					$this->show_label( $value, $show_all_choices, $field_object );
				}
			}

			?>
		</ul>
			<?php
		}
	}

	protected function show_key( $selected, $show_all_choices, $field_object ) {

		$icon           = $this->get_instance_value( 'icon' );
		$icon_unchecked = $this->get_instance_value( 'icon_unchecked' );

		$list_items = [];
		if ( $show_all_choices === 'yes' ) {

			if ( is_array( $selected ) ) {
				// multi items are selected

				foreach ( $field_object['choices'] as $key => $label ) {
					$striked    = false;  // just assuming
					$icon_class = '';
					if ( in_array( $key, $selected, true ) ) {
						// Selected/Checked item
						$icon_class = $icon;
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );
					} else {
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-no-select ae-icon-list-item' );
						$icon_class = $icon_unchecked;
					}

					?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
								<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
								<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $key; ?>
						</span>
						</div>
					</li>

					<?php

				}
			} else {

				foreach ( $field_object['choices'] as $key => $label ) {

					if ( $key === $selected ) {
						// Selected/Checked item
						$icon_class = $icon;
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );
					} else {
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-no-select ae-icon-list-item' );
						$icon_class = $icon_unchecked;
					}

					?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
							<span class="ae-icon-list-icon">
									<i class="<?php echo ( $icon_class ); ?>"></i>
								</span>
							<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $key; ?>
						</span>
						</div>
					</li>

					<?php
					if ( $key === $selected ) {
						$list_items[] = '<span>' . $key . '</span>';
					} else {
						$list_items[] = '<span class="ae-no-select">' . $key . '</span>';
					}
				}
			}
		} else {

			if ( is_array( $selected ) ) {
				// multi items are selected

				$icon_class = $icon;
				$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );

				foreach ( $field_object['choices'] as $key => $label ) {
					if ( in_array( $key, $selected, true ) ) {

						?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
							<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
							<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $key; ?>
						</span>
						</div>
					</li>

						<?php
					}
				}
			} else {

				$icon_class = $icon;
				$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );

				?>

				<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
					<div class="ae-icon-list-item-inner">
					<?php
					if ( $icon_class !== '' ) {
						?>
						<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
						<?php
					}
					?>

					<span class="ae-icon-list-text">
							<?php echo $selected; ?>
					</span>
					</div>
				</li>

					<?php

			}
		}

		return $list_items;
	}

	protected function show_label( $selected, $show_all_choices, $field_object ) {
		$icon           = $this->get_instance_value( 'icon' );
		$icon_unchecked = $this->get_instance_value( 'icon_unchecked' );

		$list_items = [];

		if ( $show_all_choices === 'yes' ) {

			if ( is_array( $selected ) ) {
				// multi items are selected

				foreach ( $field_object['choices'] as $key => $label ) {

					$striked    = false;  // just assuming
					$icon_class = '';
					if ( in_array( $key, $selected, true ) ) {
						// Selected/Checked item
						$icon_class = $icon;
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );
					} else {
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-no-select ae-icon-list-item' );
						$icon_class = $icon_unchecked;
					}

					?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
							<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
							<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $label; ?>
						</span>
						</div>
					</li>

					<?php

				}
			} else {

				foreach ( $field_object['choices'] as $key => $label ) {

					$icon_class = '';
					if ( $key === $selected ) {
						// Selected/Checked item
						$icon_class = $icon;
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );
					} else {
						$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-no-select ae-icon-list-item' );
						$icon_class = $icon_unchecked;
					}

					?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
							<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
							<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $label; ?>
						</span>
						</div>
					</li>

					<?php

				}
			}
		} else {

			if ( is_array( $selected ) ) {
				// multi items are selected

				$icon_class = $icon;
				$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );

				foreach ( $field_object['choices'] as $key => $label ) {
					if ( in_array( $key, $selected, true ) ) {
						?>

					<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
						<div class="ae-icon-list-item-inner">
						<?php
						if ( $icon_class !== '' ) {
							?>
							<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
							<?php
						}
						?>

						<span class="ae-icon-list-text">
							<?php echo $label; ?>
						</span>
						</div>
					</li>

						<?php
					}
				}
			} else {

				$icon_class = $icon;
				$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );

				?>

				<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
					<div class="ae-icon-list-item-inner">
					<?php
					if ( $icon_class !== '' ) {
						?>
						<span class="ae-icon-list-icon">
									<i class="<?php echo $icon_class; ?>"></i>
								</span>
						<?php
					}
					?>

					<span class="ae-icon-list-text">
						<?php
						foreach ( $field_object['choices'] as $key => $label ) {
							if ( $key === $selected ) {
								echo $label;
							}
						}
						?>	
					</span>
					</div>
				</li>

					<?php

			}
		}

		return $list_items;
	}

	protected function show_custom_options( $value ) {
		$list_items = [];
		$icon       = $this->get_instance_value( 'icon' );
		$icon_class = $icon;
		$this->parent->set_render_attribute( 'item_wrapper', 'class', 'ae-icon-list-item' );
		foreach ( $value as $val ) {
			?>
			<li <?php echo $this->parent->get_render_attribute_string( 'item_wrapper' ); ?>>
				<div class="ae-icon-list-item-inner">
				<?php
				if ( $icon_class !== '' ) {
					?>
					<span class="ae-icon-list-icon">
								<i class="<?php echo $icon_class; ?>"></i>
							</span>
					<?php
				}
				?>
				<span class="ae-icon-list-text">
						<?php echo $val; ?>
				</span>
				</div>
			</li>
			<?php
		}
		return $list_items;
	}
}
