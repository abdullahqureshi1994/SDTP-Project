<?php

namespace Aepro\Modules\Searchform\Widgets;

use Aepro\Aepro;
use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class AeSearchform extends Widget_Base {

	protected $_access_level = 1;

	public function get_name() {
		return 'ae-searchform';
	}

	public function get_title() {
		return __( 'AE - Search Form', 'ae-pro' );
	}

	public function get_icon() {
		return 'ae-pro-icon eicon-search';
	}

	public function get_categories() {
		return [ 'ae-template-elements' ];
	}

	public function get_keywords() {
		return [
			'search',
			'form',
		];
	}

	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {

		$this->start_controls_section(
			'section_General_title',
			[
				'label' => __( 'General', 'ae-pro' ),
			]
		);
		$this->add_control(
			'button_text',
			[
				'label'   => __( 'Button Text', 'ae-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Search', 'ae-pro' ),
			]
		);

		$this->add_control(
			'input_placeholder_text',
			[
				'label'   => __( 'Input Placeholder Text', 'ae-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Search...', 'ae-pro' ),
			]
		);
		$this->add_responsive_control(
			'form_align',
			[
				'label'     => __( 'Alignment', 'ae-pro' ),
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
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'General', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'button_style_heading',
			[
				'label'     => __( 'Button Style', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_text_typography',
				'label'    => __( 'Button Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} button',
			]
		);
		$this->start_controls_tabs( 'button_style' );
		$this->start_controls_tab( 'button_normal', [ 'label' => __( 'Normal', 'ae-pro' ) ] );
			$this->button_normal_style_options();
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover', [ 'label' => __( 'Hover', 'ae-pro' ) ] );
			$this->button_hover_style_options();
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'input_style_heading',
			[
				'label'     => __( 'Input Style', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'input_text_typography',
				'label'    => __( 'Input Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} input',
			]
		);

		$this->start_controls_tabs( 'input_style' );
		$this->start_controls_tab( 'input_normal', [ 'label' => __( 'Normal', 'ae-pro' ) ] );
			$this->input_normal_style_options();
		$this->end_controls_tab();

		$this->start_controls_tab( 'input_hover', [ 'label' => __( 'Hover', 'ae-pro' ) ] );
			$this->input_hover_style_options();
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function button_normal_style_options() {
		$this->add_control(
			'button_background_normal_color',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} button' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'button_text_normal_color',
			[
				'label'     => __( 'Text Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} button' => 'color: {{VALUE}};',
				],
			]
		);
		Aepro::$_helper->box_model_controls(
			$this,
			[
				'name'          => 'button_normal_advance_style',
				'label'         => __( 'Button', 'ae-pro' ),
				'border'        => true,
				'border-radius' => true,
				'margin'        => true,
				'padding'       => true,
				'box-shadow'    => true,
				'selector'      => '{{WRAPPER}} button',
			]
		);
	}
	public function button_hover_style_options() {
		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'button_text_hover_color',
			[
				'label'     => __( 'Text Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} button:hover' => 'color: {{VALUE}};',
				],
			]
		);
		Aepro::$_helper->box_model_controls(
			$this,
			[
				'name'          => 'button_hover_advance_style',
				'label'         => __( 'Button', 'ae-pro' ),
				'border'        => true,
				'border-radius' => true,
				'margin'        => true,
				'padding'       => true,
				'box-shadow'    => true,
				'selector'      => '{{WRAPPER}} button:hover',
			]
		);
	}
	public function input_normal_style_options() {
		$this->add_control(
			'input_normal_bg_color',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} input' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'input_normal_text_color',
			[
				'label'     => __( 'Text Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} input' => 'color: {{VALUE}};',
				],
			]
		);

		Aepro::$_helper->box_model_controls(
			$this,
			[
				'name'          => 'input_normal_advance_style',
				'label'         => __( 'Input', 'ae-pro' ),
				'border'        => true,
				'border-radius' => true,
				'margin'        => true,
				'padding'       => true,
				'box-shadow'    => true,
				'selector'      => '{{WRAPPER}} input',
			]
		);
	}
	public function input_hover_style_options() {
		$this->add_control(
			'input_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} input:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'input_hover_text_color',
			[
				'label'     => __( 'Text Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} input:hover' => 'color: {{VALUE}};',
				],
			]
		);

		Aepro::$_helper->box_model_controls(
			$this,
			[
				'name'          => 'input_hover_advance_style',
				'label'         => __( 'Input', 'ae-pro' ),
				'border'        => true,
				'border-radius' => true,
				'margin'        => true,
				'padding'       => true,
				'box-shadow'    => true,
				'selector'      => '{{WRAPPER}} input:hover',
			]
		);
	}

	protected function render() {

		if ( $this->is_debug_on() ) {
			return;
		}

		$settings = $this->get_settings();
		$text     = '';
		$text    .= "<form method='get' class='search-form' action='" . esc_url( home_url( '/' ) ) . "'>";
		$text    .= "<input type='search' class='search-field' placeholder='" . $settings['input_placeholder_text'] . "' value='" . esc_attr( get_search_query() ) . "' name='s' title='Search for:' />";
		$text    .= "<button type='submit' class='search-submit'>" . $settings['button_text'] . '</button>';
		if ( function_exists( 'wpml_the_language_input_field' ) ) {
			$text .= wpml_get_language_input_field();
		}
		$text .= '</form>';

		echo $text;
	}

}
