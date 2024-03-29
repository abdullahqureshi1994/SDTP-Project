<?php

namespace Aepro\Modules\Woo\Widgets;

use Aepro\Aepro;
use Elementor;
use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class AeWooDescription extends Widget_Base {

	protected $_access_level = 2;

	public function get_name() {
		return 'ae-woo-description';
	}

	public function is_enabled() {

		if ( AE_WOO ) {
			return true;
		}

		return false;
	}

	public function get_title() {
		return __( 'AE - Woo Description', 'ae-pro' );
	}

	public function get_icon() {
		return 'ae-pro-icon eicon-woocommerce';
	}

	public function get_categories() {
		return [ 'ae-template-elements' ];
	}

	public function get_keywords() {
		return [
			'woocommerce',
			'shop',
			'store',
			'content',
			'text',
			'description',
			'short description',
			'product',
		];
	}

	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout_settings',
			[
				'label' => __( 'Layout Settings', 'ae-pro' ),
			]
		);

		$this->add_control(
			'description_type',
			[
				'label'   => __( 'Source', 'ae-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'short' => __( 'Short Description', 'ae-pro' ),
					'full'  => __( 'Full Description', 'ae-pro' ),
				],
				'default' => 'full',
			]
		);

		$this->add_control(
			'description_size',
			[
				'label'     => __( 'Description Size', 'ae-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '10',
				'condition' => [
					'description_type' => 'short',
				],

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'Description', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-element-woo-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
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
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .ae-element-woo-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => __( 'Description Typography', 'ae-pro' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ae-element-woo-content',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		if ( $this->is_debug_on() ) {
			return;
		}
		$settings = $this->get_settings();
		$product  = Aepro::$_helper->get_ae_woo_product_data();
		if ( ! $product ) {
			return '';
		}
		$this->add_render_attribute( 'woo-content-class', 'class', 'ae-element-woo-content' );
		if ( $settings['description_type'] === 'short' ) :
			$product_short_description = wpautop( $product->get_short_description() );
			if ( $product_short_description !== '' ) {
				if ( $settings['description_size'] > 0 ) {
					$product_description = wp_trim_words( $product_short_description, $settings['description_size'], '...' );
				} else {
					$product_short_description = wpautop( $product_short_description );
					if ( isset( $GLOBALS['wp_embed'] ) ) {
						$product_short_description = $GLOBALS['wp_embed']->autoembed( $product_short_description );
					}
					$product_description = $product_short_description;
				}
			}
			if ( empty( $product_description ) ) {
				return;
			}
			?>			
			<div <?php echo $this->get_render_attribute_string( 'woo-content-class' ); ?>>
			<?php echo do_shortcode( $product_description ); ?>
			</div>
			<?php
		else :
			$edit_mode = get_post_meta( $product->get_id(), '_elementor_edit_mode', '' );
			if ( isset( $edit_mode[0] ) && $edit_mode[0] === 'builder' ) {
				$product_description  = '<div class="ae_data elementor elementor-<?php echo $product_id; ?>">';
				$product_description .= Elementor\Plugin::instance()->frontend->get_builder_content( $product->get_id() );
				$product_description .= '</div>';
			} else {
				$product_description = wpautop( $product->get_description() );
				if ( isset( $GLOBALS['wp_embed'] ) ) {
					$product_description = $GLOBALS['wp_embed']->autoembed( $product_description );
				}
			}
			if ( empty( $product_description ) ) {
				return;
			}
			?>
			<div <?php echo $this->get_render_attribute_string( 'woo-content-class' ); ?>>
				<?php echo do_shortcode( $product_description ); ?>
			</div>
		<?php endif; ?>
		<?php
	}
}
