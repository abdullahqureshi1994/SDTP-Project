<?php
/*
Plugin Name: Moodle-LTE
Description: This plugin provides a feature to connect your Moodle LMS to your WordPress website.
Version: 1.0
Author: Abdullah & Talal
*/

global $eb_plugin_data;
$eb_plugin_data = array(
	'name'           => 'Moodle-LTE',
	'slug'           => 'moodle-lte',
	'version'        => '3.0.4'
);


require_once(plugin_dir_path(__FILE__) . 'settings.php');

// Shortcode function to render the form

function moodle_lte_shortcode($atts) {
    $atts = shortcode_atts(array(
        'options' => '[]', // Default to empty array
    ), $atts);
    $options = json_decode($atts['options'], true); // Decode JSON into array

    if (!is_array($options)) {
        // If decoding fails, fallback to empty array
        $options = [];
    }
    ob_start(); ?>
    <style>
        #custom-form .form-control {
            display: block;
            width: 100%;
            height: calc(1.5em + .75rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
        #custom-form .btn {
            display: inline-block;
            font-weight: 400;
            color: #212529;
            text-align: center;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
        #custom-form .btn-primary {
            color: #fff;
            background-color: #000000;
            border-color: #000000;
        }
        .text-center{
            text-align: center;
        }
    </style>
    <form id="custom-form" method="post">
        <label for="full_name" class="control-label">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required class="form-control"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required class="form-control" ><br>

        <label for="course">Course:</label>
        <select id="course" name="course" class="form-control" >
            <?php foreach ($options as $key => $value) : ?>
                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
            <?php endforeach; ?>
        </select><br/>
        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required class="form-control" ><br>
        <div class="text-center">
            <input type="submit" class="btn btn-primary" name="submit" value="Enroll Now">
        </div>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('moodle_lte_form', 'moodle_lte_shortcode');

// Function to handle form submission
function handle_moodle_lte_form_submission() {
    if (isset($_POST['submit'])) {
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $course = sanitize_text_field($_POST['course']);
        $mobile = sanitize_text_field($_POST['mobile']);

        // Get API endpoint URL and access token from plugin settings
        $api_endpoint = get_option('api_endpoint');
        $access_token = get_option('access_token');

        // Call the API
        $response = wp_remote_post($api_endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array(
                'full_name' => $full_name,
                'email' => $email,
                'course' => $course,
                'mobile' => $mobile
            ))
        ));

        // Handle API response
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);

            // Display link based on API response
            if ($data->success) {
                echo '<p>Thank you for submitting the form. Here is your link: <a href="' . $data->link . '">Download Link</a></p>';
            } else {
                echo '<p>There was an error processing your request. Please try again later.</p>';
            }
        } else {
            echo '<p>There was an error processing your request. Please try again later.</p>';
        }
    }
}
add_action('init', 'handle_moodle_lte_form_submission');
/*
// Plugin settings page
function moodle_lte_settings_page() {
    add_options_page(
        'Custom Form Settings',
        'Custom Form Settings',
        'manage_options',
        'custom-form-settings',
        'moodle_lte_settings_page_content'
    );
}
// Function to display settings page content
function moodle_lte_settings_page_content() {
    ?>
    <div class="wrap">
        <h2>Custom Form Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_form_settings_group'); ?>
            <?php do_settings_sections('custom-form-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
add_action('admin_menu', 'moodle_lte_settings_page');
*/

// // Register settings
// function moodle_lte_register_settings() {
//     register_setting('custom_form_settings_group', 'api_endpoint');
//     register_setting('custom_form_settings_group', 'access_token');
// }
// add_action('admin_init', 'moodle_lte_register_settings');



function moodle_lte_course_post_type() {
    $labels = array(
        'name'               => _x('Courses', 'post type general name', 'textdomain'),
        'singular_name'      => _x('Course', 'post type singular name', 'textdomain'),
        'menu_name'          => _x('Courses', 'admin menu', 'textdomain'),
        'name_admin_bar'     => _x('Course', 'add new on admin bar', 'textdomain'),
        'add_new'            => _x('Add New', 'course', 'textdomain'),
        'add_new_item'       => __('Add New Course', 'textdomain'),
        'new_item'           => __('New Course', 'textdomain'),
        'edit_item'          => __('Edit Course', 'textdomain'),
        'view_item'          => __('View Course', 'textdomain'),
        'all_items'          => __('All Courses', 'textdomain'),
        'search_items'       => __('Search Courses', 'textdomain'),
        'parent_item_colon'  => __('Parent Courses:', 'textdomain'),
        'not_found'          => __('No courses found.', 'textdomain'),
        'not_found_in_trash' => __('No courses found in Trash.', 'textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.', 'textdomain'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'course'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('course', $args);
}
add_action('init', 'moodle_lte_course_post_type');

function moodle_lte_course_taxonomy() {
    $labels = array(
        'name'              => _x( 'Course Categories', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Course Category', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Course Categories', 'textdomain' ),
        'all_items'         => __( 'All Course Categories', 'textdomain' ),
        'parent_item'       => __( 'Parent Course Category', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Course Category:', 'textdomain' ),
        'edit_item'         => __( 'Edit Course Category', 'textdomain' ),
        'update_item'       => __( 'Update Course Category', 'textdomain' ),
        'add_new_item'      => __( 'Add New Course Category', 'textdomain' ),
        'new_item_name'     => __( 'New Course Category Name', 'textdomain' ),
        'menu_name'         => __( 'Course Category', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'course-category' ),
    );

    register_taxonomy( 'course_category', array( 'course' ), $args );
}
add_action( 'init', 'moodle_lte_course_taxonomy' );



function moodle_lte() {
    add_menu_page(
        'Moodle LTE Page',
        'Moodle LTE',
        'manage_options',
        'moodle-lte',
        'moodle_lte_page'
    );
}

add_action('admin_menu', 'moodle_lte');

function moodle_lte_page() {
    // Your plugin page content goes here
    echo '<h1>Welcome to Moodle LTE</h1>';
}

// Step 1: Add Submenu
function moodle_lte_submenu_page() {
    add_submenu_page(
        'my-plugin-slug', // Parent slug
        'My Plugin Settings', // Page title
        'Settings', // Menu title
        'manage_options', // Capability
        'my-plugin-settings', // Menu slug
        'my_plugin_settings_page_content' // Callback function
    );
}
add_action('admin_menu', 'moodle_lte_submenu_page');

// Step 2: Settings Page Content
function moodle_lte_settings_page_content() {
    ?>
    <div class="wrap">
        <h2>My Plugin Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('my_plugin_settings_group'); ?>
            <?php do_settings_sections('my-plugin-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Endpoint URL</th>
                    <td><input type="text" name="api_endpoint" value="<?php echo esc_attr(get_option('api_endpoint')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Token</th>
                    <td><input type="text" name="access_token" value="<?php echo esc_attr(get_option('access_token')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Service Name</th>
                    <td><input type="text" name="service_name" value="<?php echo esc_attr(get_option('service_name')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


// Step 3: Register Settings
function moodle_lte_register_settings() {
    register_setting('moodle_lte_settings_group', 'api_endpoint');
    register_setting('moodle_lte_settings_group', 'access_token');
    register_setting('moodle_lte_settings_group', 'service_name');
    // Add more settings fields as needed
}
add_action('admin_init', 'my_plugin_register_settings');



