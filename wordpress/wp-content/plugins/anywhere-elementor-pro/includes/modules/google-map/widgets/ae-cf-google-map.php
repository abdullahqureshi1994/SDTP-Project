<?php
namespace Aepro\Modules\GoogleMap\Widgets;

use Aepro\Aepro;
use Elementor\Plugin;
use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class AeCfGoogleMap extends Widget_Base {

	protected $_has_template_content = false;

	protected $_access_level = 1;

	public function get_name() {
		return 'ae-cf-google-map';
	}

	public function get_title() {
		return __( 'AE - Custom Field Map', 'ae-pro' );
	}

	public function get_icon() {
		return 'ae-pro-icon eicon-google-maps';
	}

	public function get_script_depends() {
		return [ 'ae-gmap' ];
	}

	public function get_categories() {
		return [ 'ae-template-elements' ];
	}

	public function get_keywords() {
		return [
			'google',
			'map',
			'embed',
			'location',
			'marker',
		];
	}

    //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$map_key = get_option( 'ae_pro_gmap_api' );
		if ( ! isset( $map_key ) || $map_key === '' ) {
			$this->add_control(
				'notice',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => '<div class="ae-pro-notice">
                                <a target="_blank" href="' . admin_url( 'edit.php?post_type=ae_global_templates&page=aepro-settings' ) . '">Click Here</a> to add google map api key.
                            </div>',
				]
			);
		}

		if ( \Aepro\Plugin::show_acf() || \Aepro\Plugin::show_acf( true ) ) {
			$map_source['acf_google_map'] = __( 'ACF Google Map', 'ae-pro' );
		}
		$map_source['custom_fields'] = __( 'Custom Fields', 'ae-pro' );

		$this->add_control(
			'field_type',
			[
				'label'   => __( 'Source', 'ae-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $map_source,
				'default' => 'acf_google_map',
			]
		);

		$this->add_control(
			'acf-google-map-field',
			[
				'label'       => __( 'Field Key', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your ACF Google Map key', 'ae-pro' ),
				'condition'   => [
					'field_type' => 'acf_google_map',
				],
			]
		);

		$this->add_control(
			'custom-field-lat',
			[
				'label'       => __( 'Latitude', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your custom field key', 'ae-pro' ),
				'condition'   => [
					'field_type' => 'custom_fields',
				],
			]
		);

		$this->add_control(
			'custom-field-lng',
			[
				'label'       => __( 'Longitude', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your custom field key', 'ae-pro' ),
				'condition'   => [
					'field_type' => 'custom_fields',
				],
			]
		);

		$this->add_control(
			'custom-field-address',
			[
				'label'       => __( 'Address', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your custom field key', 'ae-pro' ),
				'condition'   => [
					'field_type' => 'custom_fields',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'     => __( 'Height', 'ae-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 200,
				'selectors' => [
					'{{WRAPPER}} .ae-cf-gmap' => 'height:{{VALUE}}px',
				],
			]
		);
		$this->add_control(
			'zoom',
			[
				'label'   => __( 'Zoom', 'ae-pro' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min' => 6,
						'max' => 20,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
			]
		);

		$this->add_control(
			'custom-field-map-styles',
			[
				'label'       => __( 'Snazzy Style', 'ae-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'Add style from Snazzy Maps. Copy and Paste style array from here -> <a href="https://snazzymaps.com/explore" target="_blank">Snazzy Maps</a>', 'ae-pro' ),
			]
		);
		$this->end_controls_section();
	}

	protected function render() {

		if ( $this->is_debug_on() ) {
			return;
		}

		$post_data = Aepro::$_helper->get_demo_post_data();
		$settings  = $this->get_settings();
		$location  = [];
		$styles    = $settings['custom-field-map-styles'];
		if ( $settings['field_type'] === 'acf_google_map' ) {
			if ( $settings['acf-google-map-field'] ) {
				$repeater = Aepro::$_helper->is_repeater_block_layout();
				if ( $repeater['is_repeater'] ) {
					if ( isset( $repeater['field'] ) ) {
						$repeater_field = get_field( $repeater['field'], $post_data->ID );

						$location = $repeater_field[0][ $settings['acf-google-map-field'] ];

					} else {
						$location = get_sub_field( $settings['acf-google-map-field'] );
					}
				} else {
					$location = get_post_meta( $post_data->ID, $settings['acf-google-map-field'], true );
				}
			}
		} else {
			if ( $settings['custom-field-address'] && $settings['custom-field-lat'] && $settings['custom-field-lng'] ) {
				if ( get_post_meta( $post_data->ID, $settings['custom-field-address'], true ) || get_post_meta( $post_data->ID, $settings['custom-field-lat'], true ) || get_post_meta( $post_data->ID, $settings['custom-field-lng'], true ) ) {
					$location = [
						'address' => get_post_meta( $post_data->ID, $settings['custom-field-address'], true ),
						'lat'     => get_post_meta( $post_data->ID, $settings['custom-field-lat'], true ),
						'lng'     => get_post_meta( $post_data->ID, $settings['custom-field-lng'], true ),
					];
				}
			}
		}

		if ( ! empty( $location ) ) :
			$lat     = $location['lat'];
			$lng     = $location['lng'];
			$address = $location['address']; ?>
			<div class="ae-cf-gmap-wrapper">
				<div class="ae-cf-gmap" data-zoom="<?php echo esc_html( $settings['zoom']['size'] ); ?>" data-styles='<?php echo esc_html( $styles ); ?>'>
					<div class="marker" data-lat="<?php echo esc_html( $lat ); ?>" data-lng="<?php echo esc_html( $lng ); ?>">
						<address><?php echo $address; ?></address>
					</div>
				</div>
			</div>
			<?php
		endif;
	}

}
