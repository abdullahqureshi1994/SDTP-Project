<?php
namespace Aepro\Modules\AcfFlexibleContent\Skins;

use Aepro\Aepro;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Aepro\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Grid extends Skin_Base {
	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function _register_controls_actions() {
		parent::_register_controls_actions(); // TODO: Change the autogenerated stub
		add_action( 'elementor/element/ae-acf-flexible-content/content/after_section_end', [ $this, 'get_grid_controls' ] );
	}

	public function get_id() {
		return 'grid';
	}

	public function get_title() {
		return __( 'Grid/List', 'ae-pro' );
	}

	public function get_grid_controls( Widget_Base $widget ) {

		$this->parent = $widget;
		$this->start_controls_section(
			'grid_setting',
			[
				'label' => __( 'Grid', 'ae-pro' ),
			]
		);
		$this->add_control(
			'grid_layout',
			[
				'label'        => __( 'Grid Layout', 'ae-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'default' => __( 'Default', 'ae-pro' ),
					'list'    => __( 'List', 'ae-pro' ),
				],
				'default'      => 'default',
				'prefix_class' => 'ae-grid-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'layout_mode_alert',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'ae_layout_mode_alert',
				//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
				'raw'             => __( Aepro::$_helper->get_widget_admin_note_html( 'Know more about Post Block Carousel', 'https://wpvibes.link/go/feature-post-block-carousel' ), 'ae-pro' ),
				'separator'       => 'none',
				'condition'       => [
					'layout_mode' => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => __( 'Columns', 'ae-pro' ),
				'type'            => Controls_Manager::NUMBER,
				'desktop_default' => '1',
				'tablet_default'  => '1',
				'mobile_default'  => '1',
				'min'             => 1,
				'max'             => 12,
				'condition'       => [
					$this->get_control_id( 'grid_layout' ) => [ 'default', 'checker-board' ],
				],
				'selectors'       => [
					'{{WRAPPER}} .ae-acf-fc-collection' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr)); display:grid',
					'{{WRAPPER}} .ae-acf-fc-masonry-yes .ae-acf-fc-collection' => 'display: block !important;',
					'{{WRAPPER}} .ae-acf-fc-masonry-yes .ae-acf-fc-collection .ae-acf-fc-item' => 'width: calc(100% / {{VALUE}});',
				],
			]
		);

		$this->add_responsive_control(
			'item_col_gap',
			[
				'label'     => __( 'Column Gap', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition' => [
					$this->get_control_id( 'grid_layout' ) => [ 'default' ],
				],
				'selectors' => [
					'{{WRAPPER}} .ae-acf-fc-collection' => 'column-gap: {{SIZE}}{{UNIT}}; grid-column-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-acf-fc-masonry-yes .ae-acf-fc-collection .ae-acf-fc-item' => 'padding-right: calc({{SIZE}}{{UNIT}}/2); padding-left: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .ae-acf-fc-masonry-yes .ae-acf-fc-collection' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_responsive_control(
			'item_row_gap',
			[
				'label'     => __( 'Row Gap', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-acf-fc-collection' => 'row-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.ae-grid-list .ae-acf-fc-collection .ae-acf-fc-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ae-acf-fc-masonry-yes .ae-acf-fc-collection .ae-acf-fc-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'grid_layout' ) => [ 'list', 'default' ],
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {

		$settings       = $this->parent->get_settings_for_display();
		$post           = Aepro::$_helper->get_demo_post_data();
		$flexible_field = $settings['flexible_content'];
		if ( empty( $flexible_field ) ) {
			return;
		}
		$flexible_field_detail = explode( ':', $flexible_field );
		if ( $flexible_field_detail[0] === 'option' ) {
			$data                = 'option';
			$flexible_field_name = $flexible_field_detail[2];
		} else {
			$data                = $post->ID;
			$flexible_field_name = $flexible_field_detail[1];
		}
		$flexible_layouts = $settings[ $flexible_field . '_flexible_layout' ];

		if ( empty( $flexible_layouts ) ) {
			return;
		}

		$masonry = $this->get_instance_value( 'masonry' );
		// Outer Wrapper Attributes
		$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-acf-fc-outer-wrapper' );
		$this->parent->add_render_attribute( 'outer-wrapper', 'data-pid', get_the_ID() );
		$this->parent->add_render_attribute( 'outer-wrapper', 'data-wid', $this->parent->get_id() );
		if ( $masonry === 'yes' ) {
			$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-acf-fc-masonry-yes' );
		}
		$this->parent->add_render_attribute( 'collection', 'class', 'ae-acf-fc-collection' );
		$item_classes       = array_merge( [ 'ae-acf-fc-item', 'ae-acf-fc-item-' . $this->parent->get_id() ] );
		$item_inner_classes = [ 'ae-acf-fc-item-inner' ];
		$template           = [];
		foreach ( $flexible_layouts as $flexible_layout ) {
			$template[ $flexible_layout['flexible_content_layout'] ] = $flexible_layout['flexible_content_template'];
		}
		$with_css = false;
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$with_css = true;
		}
		?>
			<div <?php echo $this->parent->get_render_attribute_string( 'outer-wrapper' ); ?> >
				<div <?php echo $this->parent->get_render_attribute_string( 'collection' ); ?> >
					<?php
					if ( have_rows( $flexible_field_name, $data ) ) :
						Frontend::$_in_flexible_block = true;
						while ( have_rows( $flexible_field_name, $data ) ) :
							the_row();
								$get_row_layout = get_row_layout();
								$this->parent->set_render_attribute( 'item', 'class', $item_classes );
								$this->parent->set_render_attribute( 'item-inner', 'class', $item_inner_classes );
								//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
							if ( $get_row_layout == array_key_exists( $get_row_layout, $template ) ) {
								?>
											<article <?php echo $this->parent->get_render_attribute_string( 'item' ); ?> >
												<div <?php echo $this->parent->get_render_attribute_string( 'item-inner' ); ?>>
													<?php if ( !Plugin::$instance->preview->is_preview() && !Plugin::$instance->editor->is_edit_mode()) { ?>
													<div class="ae_data elementor elementor-<?php echo $template[ $get_row_layout ]; ?>">
													<?php } ?>
														<?php echo Plugin::instance()->frontend->get_builder_content( $template[ $get_row_layout ], $with_css ); ?>
													<?php if ( !Plugin::$instance->preview->is_preview() && !Plugin::$instance->editor->is_edit_mode()) { ?>
													</div>
													<?php } ?>
												</div>
											</article>
									<?php
							}

							endwhile;
						Frontend::$_in_flexible_block = false;
						endif;
					?>
				</div>
			</div>
		<?php
	}
}
