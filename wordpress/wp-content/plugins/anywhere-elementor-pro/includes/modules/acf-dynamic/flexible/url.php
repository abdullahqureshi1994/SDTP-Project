<?php

namespace Aepro\Modules\AcfDynamic\Flexible;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Aepro\Aepro;

class Url extends Data_Tag {

	public function get_name() {
		return 'ae-acf-flexible-url';
	}

	public function get_title() {
		return __( '(AE) ACF Flexible URL', 'ae-pro' );
	}

	public function get_group() {
		return 'ae-dynamic';
	}

	public function get_categories() {
		return [
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
		];
	}

	public function get_panel_template_setting_key() {
		return 'key';
	}
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function register_controls() {

		$this->add_control(
			'key',
			[
				'label'   => __( 'Select Layout', 'ae-pro' ),
				'type'    => Controls_Manager::SELECT,
				'groups'  => Aepro::$_helper->ae_get_flexible_content_fields(),
				'default' => '',
			]
		);

		$this->add_control(
			'flex_sub_field',
			[
				'label'            => __( 'Sub Field', 'ae-pro' ),
				'type'             => 'aep-query',
				'parent_field'     => 'key',
				'supported_fields' => implode( ' ', $this->get_supported_fields() ),
				'query_type'       => 'flex-sub-fields',
				'placeholder'      => 'Select',
				'condition'        => [
					'key!' => '',
				],
				'render_type'      => 'template',
			]
		);

		$this->add_control(
			'fallback',
			[
				'label'   => __( 'Fallback', 'ae-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
			]
		);
	}

	protected function get_supported_fields() {
		return [
			'url',
			'image',
			'file',
			'text',
			'email',
			'relationship',
			'link',
			'page_link',
			'post_object',
			'taxonomy',
		];
	}

	public function get_value( array $options = [] ) {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['key'] ) ) {
			return;
		}
		list($field, $value) = AcfFlexibleDynamicHelper::instance()->get_acf_field_value( $this );

		if ( $field ) {
			if ( is_array( $value ) && isset( $value[0] ) ) {
				$value = $value[0];
			}

			if ( $value ) {
				if ( ! isset( $field['return_format'] ) ) {
					$field['return_format'] = isset( $field['save_format'] ) ? $field['save_format'] : '';
				}

				switch ( $field['type'] ) {
					case 'email':
						if ( $value ) {
							$value = 'mailto:' . $value;
						}
						break;
					case 'image':
					case 'file':
						switch ( $field['return_format'] ) {
							case 'array':
							case 'object':
								$value = $value['url'];
								break;
							case 'id':
								if ( 'image' === $field['type'] ) {
									$src   = wp_get_attachment_image_src( $value, 'full' );
									$value = $src[0];
								} else {
									$value = wp_get_attachment_url( $value );
								}
								break;
						}
						break;
					case 'post_object':
					case 'relationship':
						if ( $field['type'] === 'post_object' ) {
							if ( $field['return_format'] === 'object' ) {
								$value = $value->ID;
							}
						}
						$value = get_permalink( $value );
						break;
					case 'taxonomy':
						$value = get_term_link( $value, $field['taxonomy'] );
						break;
					case 'link':
						if ( is_array( $value ) ) {
							$value = $value['url'];
						} else {
							$value;
						}
				}
			}
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		return wp_kses_post( $value );
	}
}
