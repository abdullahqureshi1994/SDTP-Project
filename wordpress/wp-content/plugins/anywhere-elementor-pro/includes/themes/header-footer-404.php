<?php

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

get_header();
/**
 * Before Header-Footer page template content.
 *
 * Fires before the content of Elementor Header-Footer page template.
 *
 * @since 2.0.0
 */
do_action('elementor/page_templates/header-footer/before_content');
//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo do_action('aepro_404');

/**
 * After Header-Footer page template content.
 *
 * Fires after the content of Elementor Header-Footer page template.
 *
 * @since 2.0.0
 */
do_action('elementor/page_templates/header-footer/after_content');

get_footer();
