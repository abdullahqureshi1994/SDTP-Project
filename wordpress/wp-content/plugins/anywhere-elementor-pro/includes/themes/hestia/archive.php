<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package  Hestia
 * @since    Hestia 1.0
 * @modified 1.1.30
 */

get_header();

$sidebar_layout  = get_theme_mod('hestia_blog_sidebar_layout', 'sidebar-right');
$wrapper_classes = apply_filters('hestia_filter_archive_content_classes', 'col-md-8 archive-post-wrap');



?>
<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<div class="<?php echo hestia_layout(); ?>">
    <div class="hestia-blogs">
        <div class="container">
            <div class="row">
                <?php
                if ($sidebar_layout === 'sidebar-left' ) {
                    get_sidebar();
                }
                ?>
                <div class="<?php echo esc_attr($wrapper_classes); ?>">
                    <?php
                    if (have_posts() ) :
                     //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo do_action('aepro_archive_data', '');
                    else :
                        get_template_part('template-parts/content', 'none');
                    endif;
                    ?>
                </div>
                <?php

                if ($sidebar_layout === 'sidebar-right' ) {
                    get_sidebar();
                }
                ?>
            </div>
        </div>
    </div>
<?php
get_footer(); ?>
