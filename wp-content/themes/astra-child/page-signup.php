<?php
/*
Template Name: Signup
*/
get_header(); ?>


<main id="main" class="site-main">
    <div class="signup_content_wrap">
        <div class="signup_content_left">
            <div class="singup_left_wrap">
                <a href="<?php echo home_url(); ?>"><img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/singup-putri-logo.png"
                    alt="Putri logo"></a>
                <p>Sign up to support your favorite creators </p>
            </div>
            <div class="signup_rounding"></div>
            <!-- <div class="ellipse_shape"></div> -->
        </div>
        <div class="signup_content_right">
            <div class="signup_form">
                <p>Create your account</p>
                <?php echo do_shortcode('[user_registration_form id="300"]'); ?>
                <div class="signup_terms">  
                    <p>By signing up you agree to our <a href="#">Terms of Service </a>and <a href="#">Privacy Policy,</a></p>
                </div>
                <div class="signup_accout">
                    <p>Already have an account? <a href="https://putri-chat.hupp.in/signin"> Log in</a></p>
                </div>
                <a href="#" class="signup_withx"><img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/svg/twitter_svgrepo.png"
                alt="Signup with X"><span>SIGN IN WITH X</span></a>
                <a href="#" class="signup_with_google"><img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/google.png"
                alt="Signup with Google"><span>SIGN IN WITH GOOGLE</span></a>
            </div>
        </div>
    </div>
</main>
<?php

get_footer();
