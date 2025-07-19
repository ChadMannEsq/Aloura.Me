<?php
/*
Template Name: Performer Application
*/

/*
 * This template displays the "Performer Application" form created
 * with Contact Form 7. Update the form ID to match the actual
 * form created in the WordPress admin. The plugin will handle
 * emailing submissions to site administrators and storing them
 * safely in the database.
 */

get_header(); ?>

<main id="main" class="site-main performer-application">
    <div class="performer_application_form">
        <?php echo do_shortcode('[contact-form-7 id="1234" title="Performer Application"]'); ?>
    </div>
</main>

<?php get_footer(); ?>
