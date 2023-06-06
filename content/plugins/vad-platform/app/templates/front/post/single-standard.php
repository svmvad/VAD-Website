<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

namespace YOOtheme;

get_header();

$config = app(Config::class);

if (have_posts()) :

    while (have_posts()) : the_post();

        ?>
        <article id="post-<?php the_ID() ?>" <?php post_class('uk-article') ?> typeof="Article" vocab="https://schema.org/">

            <meta property="name" content="<?= esc_html(get_the_title()) ?>">
            <meta property="author" typeof="Person" content="<?= esc_html(get_the_author()) ?>">
            <meta property="dateModified" content="<?= get_the_modified_date('c') ?>">
            <meta class="uk-margin-remove-adjacent" property="datePublished" content="<?= get_the_date('c') ?>">

            <?php the_content(); ?>


            <?php include ( 'author-section.php' ); ?>

        </article>


    <?php

        echo do_shortcode('[articles_section]' );

    endwhile;

endif;

get_footer();
