<?php

namespace Aepro\Base;

use Aepro\Helper;

abstract class ModuleBase {


	/**
	 * Reflection
	 *
	 * @var reflection
	 */
	private $reflection;

	/**
	 * Reflection
	 *
	 * @var instances
	 */
	protected static $instances = [];

	/**
	 * Class name to Call
	 *
	 * @since 0.0.1
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * Class instance
	 *
	 * @since 0.0.1
	 *
	 * @return static
	 */
	public static function instance() {
		if ( empty( static::$instances[ static::class_name() ] ) ) {
			static::$instances[ static::class_name() ] = new static();
		}

		return static::$instances[ static::class_name() ];
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->reflection = new \ReflectionClass( $this );
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	/**
	 * Init Widgets
	 *
	 * @since 0.0.1
	 */
	public function init_widgets() {
		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {

			if ( Helper::is_widget_active( $widget ) ) {

				$widget_class = str_replace( '-', ' ', $widget );
				$widget_class = str_replace( ' ', '', ucwords( $widget_class ) );

				$class_name = $this->reflection->getNamespaceName() . '\Widgets\\' . $widget_class;

				$widget_obj = new $class_name();
				if ( $widget_obj->is_enabled() ) {
					$widget_manager->register( $widget_obj );
				}
			}
		}
	}

	/**
	 * Get Widgets
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_widgets() {
		return [];
	}

	public function register_ajax_actions( $ajax_manager ) {
	}
}
