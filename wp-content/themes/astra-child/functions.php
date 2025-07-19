<?php

/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0');


// Include your custom functions file from the "inc" folder
require_once get_stylesheet_directory() . '/inc/model-image.php';
require_once get_stylesheet_directory() . '/inc/film-video.php';
require_once get_stylesheet_directory() . '/inc/photoset-images.php';
require_once get_stylesheet_directory() . '/inc/slider-select-posts.php';
require_once get_stylesheet_directory() . '/inc/bts-video.php';
require_once get_stylesheet_directory() . '/inc/feed-video.php';
require_once get_stylesheet_directory() . '/inc/dj-video.php';
require_once get_stylesheet_directory() . '/inc/closet-video.php';

/**
 * Enqueue styles
 */
function child_enqueue_styles()
{

    wp_enqueue_style('astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all');
    wp_enqueue_style('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/magnific-popup.min.css', false, '', '');
    wp_enqueue_style('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', false, '', '');
    wp_enqueue_style('slick-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css', false, '', '');
    wp_enqueue_style('custom-style', get_stylesheet_directory_uri() . '/assets/css/styles.css', false, '', '');
    wp_enqueue_style('tmp-style', get_stylesheet_directory_uri() . '/assets/css/tmp.css', false, '', '');
    wp_enqueue_style('extra-style', get_stylesheet_directory_uri() . '/assets/css/extra.css', false, '', '');

    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js');
    wp_enqueue_script('slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js');
    wp_enqueue_script('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js');
    wp_enqueue_script('custom-javascript', get_stylesheet_directory_uri() . '/assets/js/script.js');
    wp_enqueue_script('modern-javascript', get_stylesheet_directory_uri() . '/assets/js/modern.js', array(), CHILD_THEME_ASTRA_CHILD_VERSION, true);

    wp_localize_script(
        'custom-javascript',
        'ajax_admin',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('like-nonce')
        )
    );
    wp_localize_script('modern-javascript', 'ajax_admin', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);
function more_post_ajax()
{
    $ppp = isset($_POST["ppp"]) ? intval($_POST["ppp"]) : 4;
    $type = isset($_POST["type"]) ? sanitize_text_field($_POST["type"]) : 'film';
    $page = isset($_POST['pageNumber']) ? intval($_POST['pageNumber']) : 0;

    header("Content-Type: text/html");

    $args = array(
        'post_type' => $type,
        'posts_per_page' => $ppp,
        'paged' => $page,
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()):
        while ($loop->have_posts()):
            $loop->the_post();
            $featured_image_url = esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full'));
            $permalink = esc_url(get_permalink());
            $title = esc_html(get_the_title());
            $terms = wp_get_post_terms(get_the_ID(), 'vendor');
            $film_vendor = '';

            if (!is_wp_error($terms) && !empty($terms)) {
                $term_names = wp_list_pluck($terms, 'name');
                $film_vendor = implode(', ', $term_names);
            }

            if ($type == 'film') {
                $video_url = esc_url(get_post_meta(get_the_ID(), 'model_film_video', true)); ?>
                <div class="film_item">
                    <div class="film_item_content" style="background-image: url(<?php echo $featured_image_url; ?>);">
                        <a href="<?php echo $permalink; ?>">
                            <div class="vendor_details_wrapper">
                                <p class="film_vendor_name">WITH <?php echo $film_vendor; ?></p>
                                <h4 class="vendor_title"><?php echo $title; ?></h4>
                            </div>
                        </a>
                        <?php if ($video_url): ?>
                            <a href="<?php echo $permalink; ?>" class="video-link">
                                <video class="film_video" data-play="hover" muted="muted" autoplay loop>
                                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                                </video>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            } else if ($type == 'photoset') { ?>
                    <div class="photoset_item">
                        <div class="photoset_item_content" style="background-image: url(<?php echo $featured_image_url; ?>);">
                            <a href="<?php echo $permalink; ?>">
                                <p class="overlay-content">WITH <?php echo $film_vendor; ?></p>
                                <h4 class="film_vendor_name"><?php echo $title; ?></h4>
                            </a>
                        </div>
                    </div>
                <?php
            } else if ($type == 'bts') { ?>
                        <div class="film_item">
                            <a href="<?php echo $permalink; ?>">
                                <div class="film_item_content" style="background-image: url(<?php echo $featured_image_url; ?>);">
                                    <div class="vendor_details_wrapper">
                                        <p class="film_vendor_name">WITH <?php echo $film_vendor; ?></p>
                                        <h4 class="vendor_title"><?php echo $title; ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                <?php
            }
        endwhile;
    endif;

    wp_reset_postdata();
    die();
}

add_action('wp_ajax_nopriv_more_post_ajax', 'more_post_ajax');
add_action('wp_ajax_more_post_ajax', 'more_post_ajax');


// Hook the function to user registration

// user add Model
function add_custom_user_role()
{
    add_role(
        'model',
        __('Model'),
        array(
            'read' => true,  // True allows this capability
            'edit_posts' => false,
            'delete_posts' => false,
            // Add other capabilities as needed
        )
    );
}
add_action('init', 'add_custom_user_role');

/**
 * Check if a user has an active subscription.
 */
function am_is_user_subscribed($user_id = null) {
    $user_id = $user_id ? $user_id : get_current_user_id();
    return get_user_meta($user_id, 'subscription_active', true) === '1';
}

/**
 * Set default subscription meta when a user registers.
 */
function am_set_default_subscription($user_id) {
    add_user_meta($user_id, 'subscription_active', '0', true);
}
add_action('user_register', 'am_set_default_subscription');

// Rest Api in login
// function custom_rest_api_init() {
//     register_rest_route('custom/v1', '/login', array(
//         'methods' => 'POST',
//         'callback' => 'custom_login_handler',
//         'permission_callback' => '__return_true',
//     ));
// }
// add_action('rest_api_init', 'custom_rest_api_init');

// function custom_login_handler($request) {
//     $creds = array();
//     $user_email = $request->get_param('user_email');    
//     $user_password = $request->get_param('password');

//     if (!$user_email || !$user_password) {
//         return new WP_Error('missing_parameters', __('Email and password are required.'), array('status' => 400));
//     }

//     $user = get_user_by('email', $user_email);

//     if (!$user) {
//         return new WP_Error('invalid_email', __('Invalid email address.'), array('status' => 403));
//     }

//     $creds['user_login'] = $user->user_login;
//     $creds['user_password'] = $user_password;
//     $creds['remember'] = true;

//     $user = wp_signon($creds, false);

//     if (is_wp_error($user)) {
//         return new WP_Error('invalid_login', __('Invalid email or password.'), array('status' => 403));
//     }

//     // Get all user meta keys and values
//     $user_type = get_user_meta($user->ID, 'user_registration_user_type', true);
//     $user_type = strtolower($user_type);

//     return rest_ensure_response(array(
//         'user_id' => $user->ID,
//         'user_nicename' => $user->user_nicename,
//         'user_email' => $user->user_email,
//         'user_display_name' => $user->display_name,
//         'user_type' => $user_type,
//     ));
// }

add_filter('jwt_auth_token_before_dispatch', 'add_user_details_to_jwt_response', 10, 2);

function add_user_details_to_jwt_response($response, $user)
{
    // Get user meta
    $user_type = get_user_meta($user->ID, 'user_registration_user_type', true);
    $user_type = strtolower($user_type);

    // Add user type and username to the response
    $response['user_type'] = $user_type;
    $response['user_email'] = $user->user_email;

    return $response;
}

add_action('rest_api_init', function () {
    register_rest_route('jwt-auth/v1', '/token/validate', array(
        'methods' => 'POST',
        'callback' => 'custom_jwt_auth_validate_token',
    ));
});

function custom_jwt_auth_validate_token(WP_REST_Request $request)
{
    $token = $request->get_header('Authorization');
    if (empty($token)) {
        return new WP_Error('jwt_auth_no_token', 'No token provided', array('status' => 403));
    }

    // Remove 'Bearer ' from the token string if present
    $token = str_replace('Bearer ', '', $token);

    try {
        $decoded = JWT::decode($token, new Key(JWT_AUTH_SECRET_KEY, 'HS256'));
        return array(
            'code' => 'jwt_auth_valid_token',
            'message' => 'Token is valid',
            'data' => array(
                'status' => 200,
                'user' => $decoded->data->user
            ),
        );
    } catch (Exception $e) {
        return new WP_Error('jwt_auth_invalid_token', $e->getMessage(), array('status' => 403));
    }
}

add_action('rest_api_init', function () {
    register_rest_route('jwt-auth/v1', '/token/refresh', [
        'methods' => 'POST',
        'callback' => 'custom_refresh_jwt_token',
        'permission_callback' => '__return_true',
    ]);
});

function custom_refresh_jwt_token(WP_REST_Request $request)
{
    $token = $request->get_param('token');

    if (!$token) {
        return new WP_Error('no_token', 'No token provided', array('status' => 400));
    }

    $decoded_token = JWT::decode($token, JWT_AUTH_SECRET_KEY, array('HS256'));

    if (!$decoded_token) {
        return new WP_Error('invalid_token', 'Invalid token provided', array('status' => 401));
    }

    $user_id = $decoded_token->data->user->id;

    if (!get_user_by('id', $user_id)) {
        return new WP_Error('user_not_found', 'User not found', array('status' => 404));
    }

    $new_token = array(
        'iss' => get_bloginfo('url'),
        'iat' => time(),
        'exp' => time() + (60 * 60), // Set token expiration time
        'data' => array(
            'user' => array(
                'id' => $user_id,
            ),
        ),
    );

    $new_jwt = JWT::encode($new_token, JWT_AUTH_SECRET_KEY);

    return array(
        'token' => $new_jwt,
    );
}


// invalid password 
function custom_wp_authenticate_email_password($user, $email, $password)
{
    if ($user instanceof WP_User) {
        return $user;
    }

    if (empty($email) || empty($password)) {
        if (is_wp_error($user)) {
            return $user;
        }

        $error = new WP_Error();

        if (empty($email)) {
            // Uses 'empty_username' for back-compat with wp_signon().
            $error->add('empty_username', __('<strong>Error:</strong> The email field is empty.'));
        }

        if (empty($password)) {
            $error->add('empty_password', __('<strong>Error:</strong> The password field is empty.'));
        }

        return $error;
    }

    if (!is_email($email)) {
        return $user;
    }

    $user = get_user_by('email', $email);

    if (!$user) {
        return new WP_Error(
            'invalid_email',
            __('Unknown email address. Check again or try your username.')
        );
    }

    /** This filter is documented in wp-includes/user.php */
    $user = apply_filters('wp_authenticate_user', $user, $password);

    if (is_wp_error($user)) {
        return $user;
    }

    if (!wp_check_password($password, $user->user_pass, $user->ID)) {
        return new WP_Error(
            'incorrect_password',
            __('Icorrect password. Please try again')
        );
    }

    return $user;
}
remove_filter('authenticate', 'wp_authenticate_email_password', 20, 3);
add_filter('authenticate', 'custom_wp_authenticate_email_password', 20, 3);

function add_sticky_chat_icon()
{
    $icon_url = get_stylesheet_directory_uri() . '/assets/images/chat.svg'; // Update the path if necessary
    ?>
    <a href=<?php echo home_url('/signup/') ?> class="sticky-chat-icon">
        <img src="<?php echo esc_url($icon_url); ?>" alt="Chat Icon">
    </a>
    <?php
}
add_action('wp_footer', 'add_sticky_chat_icon');

function enqueue_sticky_chat_icon_styles()
{
    ?>
    <style>
        .sticky-chat-icon {
            position: fixed;
            bottom: 25px;
            left: 26px;
            z-index: 1000;
        }

        .sticky-chat-icon img {
            width: 60px;
            /* Adjust the size as needed */
            height: auto;
            display: none;
        }
    </style>
    <?php
}
add_action('wp_head', 'enqueue_sticky_chat_icon_styles');

// Model user will add as model taxonomy on WordPress

function assign_model_taxonomy_on_registration($user_id)
{
    if (isset($_POST['form_data'])) {
        $form_data = json_decode(stripslashes($_POST['form_data']), true);

        $user_type = '';
        foreach ($form_data as $field) {
            if ($field['field_name'] === 'user_type') {
                $user_type = $field['value'];
                break;
            }
        }

        if ($user_type === 'model') {
            $user = get_userdata($user_id);
            $user_login = $user->user_login;

            $result = wp_insert_term(
                $user_login,   // The term to add
                'model'        // The taxonomy name
            );

            if (!is_wp_error($result)) {
                wp_set_object_terms($user_id, $result['term_id'], 'model', false);
            }
        }
    }
}
add_action('user_register', 'assign_model_taxonomy_on_registration');



function add_model_taxonomy_capabilities()
{
    $role = get_role('subscriber'); // Replace 'subscriber' with your role name

    if ($role) {
        $role->add_cap('manage_categories'); // Allow managing categories
        $role->add_cap('edit_posts');        // Allow editing posts
    }
}
add_action('init', 'add_model_taxonomy_capabilities');

// Call user to add API for adding user in MongoDB

function custom_call_external_api_on_user_registration($user_id)
{
    // Get the user data
    $user_info = get_userdata($user_id);
    if (!$user_info) {
        return; // User data retrieval failed, no need to proceed
    }

    $email = $user_info->user_email;
    $name = $user_info->display_name;

    $user_type = '';

    // Check if form data is available
    if (isset($_POST['form_data'])) {
        $form_data = json_decode(stripslashes($_POST['form_data']), true);

        // Loop through form data to find user_type
        foreach ($form_data as $field) {
            if ($field['field_name'] === 'user_type') {
                $user_type = $field['value'];
                break;
            }
        }
    }

    // If no user_type found in form data, use default value
    if (empty($user_type)) {
        $user_type = 'default'; // Set a default value or handle accordingly
    }

    // Prepare the data for the API request
    $body = array(
        'name' => $name,
        'email' => $email,
        'type' => $user_type, // Use dynamic user type
    );

    error_log('Body Data: ' . $body);

    $response = wp_remote_post('https://aloura.me/api/auth/signup', array(
        'method' => 'POST',
        'body' => json_encode($body),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'timeout' => 30,
    ));

    // Handle the response
    if (is_wp_error($response)) {
        // Get and log the error message
        $error_message = $response->get_error_message();
        error_log('API Error: ' . $error_message);
        return;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    error_log('Response Body Data: ' . $response_body);

    // Decode and handle the response
    $decoded_response = json_decode($response_body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Optionally handle JSON decoding errors');
        return;
    }

    if (isset($decoded_response['error'])) {
        error_log('Optionally handle API errors');
    } else {
        error_log('Process successful response if needed');
    }
}
add_action('user_register', 'custom_call_external_api_on_user_registration'); // Ensure this hook is correct

function custom_rest_change_password()
{
    register_rest_route('custom/v1', '/change-password', array(
        'methods' => 'POST',
        'callback' => 'handle_password_change',
        'permission_callback' => '__return_true', // Allow all requests
    ));
}
add_action('rest_api_init', 'custom_rest_change_password');

function handle_password_change(WP_REST_Request $request)
{
    $email = sanitize_email($request->get_param('email'));
    $password = sanitize_text_field($request->get_param('password'));
    $new_password = sanitize_text_field($request->get_param('new_password'));

    if (empty($email) || empty($password) || empty($new_password)) {
        return new WP_Error('empty_fields', 'Please provide email, current password, and new password.', array('status' => 400));
    }

    // Get the user by email
    $user = get_user_by('email', $email);
    if (!$user) {
        return new WP_Error('invalid_email', 'No user found with this email.', array('status' => 404));
    }

    // Verify the password
    if (!wp_check_password($password, $user->data->user_pass, $user->ID)) {
        return new WP_Error('incorrect_password', 'The current password is incorrect.', array('status' => 401));
    }

    // Change the password
    wp_set_password($new_password, $user->ID);

    return new WP_REST_Response('Password successfully changed.', 200);
}

function custom_login_endpoint()
{
    register_rest_route('custom/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'custom_login_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'custom_login_endpoint');
function custom_login_callback(WP_REST_Request $request) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'am_login_' . md5($ip);
    $attempts = (int) get_transient($key);
    if ($attempts >= 5) {
        return new WP_Error('too_many_attempts', 'Too many login attempts. Please wait.', array('status' => 429));
    }

    $email = sanitize_email($request->get_param('email'));
    $password = sanitize_text_field($request->get_param('password'));
    
    if (empty($email) || empty($password)) {
        return new WP_Error('missing', 'Email and password are required.', array('status' => 400));
    }

    // Check if the email is registered
    if (!email_exists($email)) {
        return new WP_Error('invalid_email', 'The email address is not registered.', array('status' => 400));
    }

    // Try to authenticate the user
    $creds = array(
        'user_login'    => $email,
        'user_password' => $password,
        'remember'      => true,
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error_code = $user->get_error_code();
        set_transient($key, $attempts + 1, 5 * 60);

        // Check for specific WP error codes
        if ($error_code === 'invalid_username') {
            return new WP_Error('invalid_email', 'The email address is not registered.', array('status' => 400));
        } elseif ($error_code === 'incorrect_password') {
            return new WP_Error('incorrect_password', 'The password is incorrect.', array('status' => 400));
        } else {
            return new WP_Error('authentication_failed', 'Authentication failed.', array('status' => 400));
        }
    }

    // Authentication successful
    delete_transient($key);
    $user_type = get_user_meta($user->ID, 'user_registration_user_type', true);
    $user_type = strtolower($user_type);

    // Set cookies for authenticated user
    $cookie_name = 'wordpress_logged_in_' . COOKIEHASH;
    $cookie_value = wp_generate_auth_cookie($user->ID, 'logged_in', 'auth_cookie');
    $cookie_domain = parse_url(home_url(), PHP_URL_HOST);

    // Delete the old cookie
    setcookie($cookie_name, '', time() - 3600, '/', $cookie_domain, true, true);

    // Set the new cookie with SameSite=None and Secure attributes
    setcookie($cookie_name, $cookie_value, [
        'expires' => time() + 3600, // 1 hour expiry
        'path' => '/',
        'domain' => $cookie_domain,
        'secure' => true,    // Ensure the cookie is sent over HTTPS
        'httponly' => true,  // Helps mitigate XSS attacks
        'samesite' => 'None' // Allows cross-site requests
    ]);

    // Prepare response data
    $response_data = array(
        'user_id' => $user->ID,
        'user_nicename' => $user->user_nicename,
        'user_email' => $user->user_email,
        'user_display_name' => $user->display_name,
        'user_type' => $user_type,
    );

    return rest_ensure_response($response_data);
}

function set_custom_cookie_flags() {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        // Ensure Secure and SameSite=None are set for REST API cookies
        add_action('wp_loaded', function() {
            if (isset($_COOKIE['wordpress_logged_in_']) || isset($_COOKIE['wordpress_sec_'])) {
                $cookie_name = isset($_COOKIE['wordpress_logged_in_']) ? 'wordpress_logged_in_' : 'wordpress_sec_';
                $cookie_value = $_COOKIE[$cookie_name];
                $cookie_domain = parse_url(home_url(), PHP_URL_HOST);
                
                setcookie($cookie_name, '', time() - 3600, '/', $cookie_domain, true, true);
                
                setcookie($cookie_name, $cookie_value, [
                    'expires' => time() + 3600, 
                    'path' => '/',
                    'domain' => $cookie_domain,
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None'
                ]);
            }
        });
    }
}
add_action('init', 'set_custom_cookie_flags');

function add_cors_http_header() {
    
    header("Access-Control-Allow-Origin: https://aloura.me");
    header("Access-Control-Allow-Credentials: true");
}
add_action('init', 'add_cors_http_header');

function restrict_wp_rest_api_access($result)
{
    // Get the current request route
    $current_route = !empty($GLOBALS['wp']->query_vars['rest_route']) ? $GLOBALS['wp']->query_vars['rest_route'] : '';

    // List of allowed routes for unauthenticated users
    $allowed_routes = array(
        '/custom/v1/change-password',
        '/jwt-auth/v1/token',
        '/jwt-auth/v1/token/refresh',
        '/jwt-auth/v1/token/validate',
        '/custom/v1/login',
        // Add other routes you want to exempt here
    );

    // Check if the current route is in the allowed routes list
    if (in_array($current_route, $allowed_routes)) {
        return $result; // Allow access to this route
    }

    // Restrict access if the user is not logged in
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', esc_html__('You are not allowed to access this REST API.'), array('status' => 401));
    }

    return $result;
}
add_filter('rest_authentication_errors', 'restrict_wp_rest_api_access');


function modify_menu_items_based_on_login($items, $args) {
    // Check if this is the correct menu
    if ($args->theme_location == 'primary') {
        // Check if user is logged in
        if (is_user_logged_in()) {
            // Remove Sign Up menu item if logged in
            $items = preg_replace('/<li[^>]*class="[^"]*signup[^"]*"[^>]*>.*?<\/li>/is', '', $items);
        } else {
            // Remove Login menu item if not logged in
            $items = preg_replace('/<li[^>]*class="[^"]*login[^"]*"[^>]*>.*?<\/li>/is', '', $items);
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'modify_menu_items_based_on_login', 10, 2);

/**
 * Register custom post type for private messages.
 */
function am_register_message_cpt() {
    register_post_type('private_message', array(
        'public' => false,
        'show_ui' => true,
        'label' => 'Private Messages',
        'supports' => array('title', 'editor', 'author', 'custom-fields'),
        'capability_type' => 'post',
    ));
}
add_action('init', 'am_register_message_cpt');

/**
 * REST endpoint to send a private message.
 */
function am_rest_send_message(WP_REST_Request $request) {
    if (!is_user_logged_in()) {
        return new WP_Error('forbidden', 'Authentication required', array('status' => 401));
    }
    $recipient = intval($request->get_param('recipient'));
    $content   = sanitize_text_field($request->get_param('message'));
    if (!$recipient || empty($content)) {
        return new WP_Error('missing', 'Recipient and message required', array('status' => 400));
    }
    $post_id = wp_insert_post(array(
        'post_type' => 'private_message',
        'post_title' => 'Message to ' . $recipient,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'meta_input' => array('recipient' => $recipient)
    ));
    return array('id' => $post_id);
}

/**
 * REST endpoint to list current user's messages.
 */
function am_rest_get_messages() {
    if (!is_user_logged_in()) {
        return new WP_Error('forbidden', 'Authentication required', array('status' => 401));
    }
    $args = array(
        'post_type' => 'private_message',
        'meta_key'   => 'recipient',
        'meta_value' => get_current_user_id(),
        'posts_per_page' => -1,
    );
    $query = get_posts($args);
    $messages = array();
    foreach ($query as $msg) {
        $messages[] = array(
            'id' => $msg->ID,
            'content' => $msg->post_content,
            'from' => $msg->post_author,
            'date' => $msg->post_date,
        );
    }
    return $messages;
}

/**
 * REST endpoint to send a tip to another user.
 */
function am_rest_send_tip(WP_REST_Request $request) {
    if (!is_user_logged_in()) {
        return new WP_Error('forbidden', 'Authentication required', array('status' => 401));
    }
    $user_id = intval($request->get_param('user'));
    $amount  = floatval($request->get_param('amount'));
    if (!$user_id || $amount <= 0) {
        return new WP_Error('invalid', 'Invalid parameters', array('status' => 400));
    }
    $current = (float) get_user_meta($user_id, 'tips_total', true);
    update_user_meta($user_id, 'tips_total', $current + $amount);
    return array('success' => true);
}

/**
 * REST endpoint to fetch analytics for the current user.
 */
function am_rest_get_analytics() {
    if (!is_user_logged_in()) {
        return new WP_Error('forbidden', 'Authentication required', array('status' => 401));
    }
    $posts = get_posts(array('author' => get_current_user_id(), 'posts_per_page' => -1));
    $views = 0;
    foreach ($posts as $p) {
        $views += (int) get_post_meta($p->ID, 'view_count', true);
    }
    return array('total_views' => $views);
}

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/message', array(
        'methods' => 'POST',
        'callback' => 'am_rest_send_message',
    ));
    register_rest_route('custom/v1', '/messages', array(
        'methods' => 'GET',
        'callback' => 'am_rest_get_messages',
    ));
    register_rest_route('custom/v1', '/tip', array(
        'methods' => 'POST',
        'callback' => 'am_rest_send_tip',
    ));
    register_rest_route('custom/v1', '/analytics', array(
        'methods' => 'GET',
        'callback' => 'am_rest_get_analytics',
    ));
});

/**
 * Increment post view count.
 */
function am_record_view() {
    if (is_singular()) {
        $id = get_the_ID();
        $views = (int) get_post_meta($id, 'view_count', true);
        update_post_meta($id, 'view_count', $views + 1);
    }
}
add_action('wp_head', 'am_record_view');

