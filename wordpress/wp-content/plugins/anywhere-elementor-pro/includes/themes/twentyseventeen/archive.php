<?php

get_header(); ?>

    <div class="wrap">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
                <?php if (have_posts() ) : ?>
					<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php echo do_action('aepro_archive_data', ''); ?>

                    <?php
                else :

                    get_template_part('content', 'none');

                endif;
                ?>


            </main><!-- #main -->
        </div><!-- #primary -->
    </div><!-- .wrap -->

<?php
get_footer();
