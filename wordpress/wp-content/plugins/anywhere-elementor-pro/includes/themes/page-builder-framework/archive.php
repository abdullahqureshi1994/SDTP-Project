<?php
/**
 * Archive Pages
 *
 * @package Page Builder Framework
 */

// exit if accessed directly
if (! defined('ABSPATH') ) {
    exit;
}

$grid_gap                    = get_theme_mod('sidebar_gap');
$grid_gap ? true : $grid_gap = 'divider';
$archive_title               = get_theme_mod('archive_headline');

get_header(); ?>

<div id="content">

    <div id="inner-content" class="wpbf-container wpbf-container-center">

        <div class="wpbf-grid wpbf-grid-<?php echo esc_attr($grid_gap); ?>">
			<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php do_action('wpbf_sidebar_left'); ?>

            <main id="main" class="wpbf-main wpbf-archive-content wpbf-medium-2-3" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

                <?php if (have_posts() ) : ?>
					<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php echo do_action('aepro_archive_data', ''); ?>

                <?php else : ?>

                    <article id="post-not-found" class="wpbf-post">

                        <header class="article-header">
							<?php //phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>
                            <h1 class="entry-title"><?php esc_html_e("Oops, this article couldn't be found!", 'page-builder-framework'); ?></h1>
                        </header>

                        <section class="article-content">
							<?php //phpcs:ignore WordPress.WP.I18n.TextDomainMismatch ?>
                            <p><?php esc_html_e('Something went wrong.', 'page-builder-framework'); ?></p>
                        </section>

                    </article>

                <?php endif; ?>


            </main>

            <?php do_action('wpbf_sidebar_right'); ?>

        </div>

    </div>

</div>

<?php get_footer(); ?>
