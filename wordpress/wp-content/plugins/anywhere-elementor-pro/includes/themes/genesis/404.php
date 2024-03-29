<?php

get_header();
remove_action('genesis_loop', 'genesis_do_loop');

add_action('genesis_loop', 'genesis_404');
do_action('genesis_before_content_sidebar_wrap');


do_action('genesis_before_content');
genesis_markup(
    [
        'open'    => '<article class="entry">',
        'context' => 'entry-404',
    ]
);

do_action('genesis_before_loop');
//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo do_action('aepro_404');

do_action('genesis_after_loop');
genesis_markup(
    [
        'close'   => '</article>',
        'context' => 'entry-404',
    ]
);
do_action('genesis_after_content');


do_action('genesis_after_content_sidebar_wrap');

get_footer();
