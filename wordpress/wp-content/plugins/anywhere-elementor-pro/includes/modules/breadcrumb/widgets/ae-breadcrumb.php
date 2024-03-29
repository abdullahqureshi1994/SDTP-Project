<?php

namespace Aepro\Modules\Breadcrumb\Widgets;

use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class AeBreadcrumb extends Widget_Base {

	protected $_access_level = 1;

	public function get_name() {
		return 'ae-breadcrumb';
	}

	public function is_enabled() {

		if ( AE_YOAST_SEO ) {
			return true;
		}

		if ( AE_RANK_MATH ) {
			return true;
		}

		return false;
	}

	public function get_title() {
		return __( 'AE - Breadcrumb', 'ae-pro' );
	}

	public function get_icon() {
		return 'ae-pro-icon eicon fa fa-angle-double-right';
	}

	public function get_categories() {
		return [ 'ae-template-elements' ];
	}

	public function get_keywords() {
		return [
			'yoast',
			'rankmath',
			'seo',
			'breadcrumbs',
			'internal links',
		];
	}
    //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {
		$this->start_controls_section(
			'section_General_title',
			[
				'label' => __( 'General Style', 'ae-pro' ),
			]
		);
		$this->add_responsive_control(
			'anchor_align',
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
		$this->add_control(
			'separator_color',
			[
				'label'     => __( 'Separator Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} span span, {{WRAPPER}} span.separator' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'anchor_style',
			[
				'label'     => __( 'Anchor Style', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'button_style' );
		$this->start_controls_tab( 'anchor_normal', [ 'label' => __( 'Normal', 'ae-pro' ) ] );
		$this->add_control(
			'anchor_normal_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'anchor_normal_typography',
				'label'    => __( 'Anchor Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} a',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'anchor_hover', [ 'label' => __( 'Hover', 'ae-pro' ) ] );
		$this->add_control(
			'anchor_hover_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'anchor_hover_typography',
				'label'    => __( 'Anchor Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} a:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'current_page_style',
			[
				'label'     => __( 'Current Page Style', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'current_page_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} span .breadcrumb_last, {{WRAPPER}} span.last' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'current_page_typography',
				'label'     => __( 'Current Page Typography', 'ae-pro' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .breadcrumb_last, {{WRAPPER}} .last',
				],
			]
		);

		if ( AE_YOAST_SEO ) {
			$info_message = 'Additional settings are available in the Yoast SEO <a href="' . admin_url( 'admin.php?page=wpseo_titles#top#breadcrumbs' ) . '" target="_blank">Breadcrumbs Panel</a>';
		}

		if ( AE_RANK_MATH ) {
			$info_message = 'Additional settings are available in the Rank Math SEO <a href="' . admin_url( 'admin.php?page=rank-math-options-general#setting-panel-breadcrumbs' ) . '" target="_blank">Breadcrumbs Panel</a>';
		}
		$this->add_control(
			'ae_breadcrumb_raw_html',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
				'raw'             => __( $info_message, 'ae-pro' ),
				'separator'       => 'after',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		if ( $this->is_debug_on() ) {
			return;
		}

		$breadcrumbs = '';
		if ( AE_YOAST_SEO ) {
			$breadcrumbs = yoast_breadcrumb( '', '', false );
		}
		if ( AE_RANK_MATH ) {
			$breadcrumbs = rank_math_the_breadcrumbs();
		}

		echo $breadcrumbs;
	}

}
