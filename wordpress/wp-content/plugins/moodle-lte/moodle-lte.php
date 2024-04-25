<?php
/*
Plugin Name: Moodle-LTE
Description: This plugin provides a feature to connect your Moodle LMS to your WordPress website.
Version: 1.2
Author: Abdullah & Talal
*/

// Include settings.php
require_once(plugin_dir_path(__FILE__) . 'settings.php');
require_once(plugin_dir_path(__FILE__) . 'generateInvoice.php');
require_once(plugin_dir_path(__FILE__).'vendor/autoload.php');

if (!function_exists('dd')) {
  /**
   * @return never
   */
  function dd(...$vars)
  {
      if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
          header('HTTP/1.1 500 Internal Server Error');
      }

      foreach ($vars as $v) {
        VarDumper::dump($v);
      }

      exit(1);
  }
}
// Shortcode function to render the form
function moodle_lte_shortcode_($atts) {
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
            cursor: pointer;
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

function moodle_lte_shortcode($atts) {
    $atts = shortcode_atts(array(
        'options' => '[]', // Default to empty array
    ), $atts);
    $options = json_decode($atts['options'], true); // Decode JSON into array

    if (!is_array($options)) {
        // If decoding fails, fallback to empty array
        $options = [];
    }

    // Query courses post type
    $courses_query = new WP_Query(array(
        'post_type' => 'course',
        'posts_per_page' => -1, // Retrieve all courses
    ));
    $pdf_path = false;
    if (isset($_GET['pdf_path'])) {
        $pdf_path = $_GET['pdf_path'];
    }

    // while ($courses_query->have_posts()) : dump($courses_query->the_post(), the_ID(), get_post_meta(84,'_moodle_course_id', true) );
    // endwhile;

    ob_start(); ?>
    <style>
        .alert.alert-success {
            color: #1d291c;
            background-color: #a3e79f;
            width: 100%;
            padding: 15px 30px;
            border-radius: 10px;
            margin: 30px 0px;
            font-weight: 600;
            font-size: 18px;
        }
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
            cursor: pointer;
        }
        .text-center{
            text-align: center;
        }
        .d-flex.justify-content-center{
            display: flex;
            justify-content: space-between;
        }
        #custom-form .btn-secondary {
            color: #fff;
            background-color: #28284D;
            border-color: #28284D;
            cursor: pointer;
        }
    </style>
    <?php if($pdf_path){ ?>
        <div class="d-flex justify-content-center">
            <div class="alert alert-success">Form Submitted successfully! Please download Invoice</div>
        </div>
    <?php } ?>
    <form id="custom-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="application/x-www-form-urlencoded" >
        <input type="hidden" name="action" value="handle_moodle_lte_form_submission">
    
        <label for="full_name" class="control-label">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required class="form-control"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required class="form-control" ><br>

        <label for="course">Course:</label>
        <select id="course" name="course" class="form-control" >
            <?php 
            // Loop through each course and add as an option in the dropdown
            if ($courses_query->have_posts()) :
                while ($courses_query->have_posts()) : $courses_query->the_post();?>
                    <option value="<?= get_the_ID(); ?>"><?php the_title(); ?></option>
                <?php endwhile;
                wp_reset_postdata(); // Reset post data
            endif; ?>
        </select><br/>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" required class="form-control" ><br>
        
        <?php if($pdf_path){ ?>
            <div class="d-flex justify-content-center">
                <a target="_blank" class="btn btn-secondary" href="<?= $pdf_path; ?>">Download Invoice</a></p>
                <button type="submit" class="btn btn-secondary" name="submit" role="button" value="enroll">Submit</button>
            </div>
        <?php } else { ?>
            <div class="text-center">
                <button type="submit" class="btn btn-secondary" name="submit" role="button" value="enroll">Submit</button>
            </div>
        <?php } ?>
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

        $moodle_course_id = get_post_meta($course,'_moodle_course_id', true);
        $moodle_enrol_instance_id = get_post_meta($course,'_moodle_enrol_instance_id', true);
        $price = get_post_meta($course,'_course_price', true);

        $invoice_id = generateInvoice($customer = $full_name, $amount = $price, $course_id = $moodle_course_id, $email = $email);
        // dump($invoice_id);
        $pdf_path = generatePDFInvoice($invoice_id);

        //Make entry to Moodle API endpoint

        $query = [
            "wstoken" => $access_token,
            "moodlewsrestformat" => 'json',
            "wsfunction" => 'enrol_wpmoodlelte_enrol_user',
            "courseid" => $moodle_course_id,
            "invoice_id" => $invoice_id['number'],
            "date" => $invoice_id['date'],
            "status" => "Paid",//$invoice_id['status'],
            "amount" => $invoice_id['amount'],
            "instanceid" => $moodle_enrol_instance_id,
            "name" => rawurlencode($full_name),
            "email" => $email,
            "download_invoice" => $pdf_path,
          ];
        
        $api_endpoint = $api_endpoint."/webservice/rest/server.php?".http_build_query($query);


        // Call the API
        $response = wp_remote_post($api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json'
            )
        ));

        // Handle API response
        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);
            // Display link based on API response
            if ($data && $data->status) {
                echo '<p>Thank you for submitting the form. Here is your link: <a href="' . $data->data->url_for_download_wpmoodlelte . '">Download Link</a></p>';
            } else {
                echo '<p>There was an error processing your request. Please try again later.</p>';
            }
        } else {
            echo '<p>There was an error processing your request. Please try again later.</p>';
        }

        if($invoice_id && $pdf_path){
            // Redirect back to the page with a success message
            wp_redirect(add_query_arg('pdf_path', urlencode($pdf_path), wp_get_referer()));
            exit();
        } else {
            // Redirect back to the page with an error message
            wp_redirect(add_query_arg('error', '1', wp_get_referer()));
            exit();
        }
    }
}
// add_action('init', 'handle_moodle_lte_form_submission');
add_action('admin_post_handle_moodle_lte_form_submission', 'handle_moodle_lte_form_submission');


// Plugin settings page
function moodle_lte_settings_page() {
    add_options_page(
        'Moodle-LTE Settings',
        'Moodle-LTE Settings',
        'manage_options',
        'moodle-lte-settings',
        'moodle_lte_settings_page_content'
    );
}
add_action('admin_menu', 'moodle_lte_settings_page');

// Function to display settings page content
function moodle_lte_settings_page_content() {
    
    /* $invoices = generateInvoices(5); // Generate 5 dummy invoices

    foreach ($invoices as $invoice) {
        $pdfFilePath = generatePDFInvoice($invoice);
        echo 'Invoice ' . $invoice['number'] . ': <a href="' . $pdfFilePath . '">Download PDF</a><br>';
    } */
    add_settings_error('moodle_lte_messages', 'sync_success', 'Courses synchronized successfully.', 'updated');

    ?>
    <div class="wrap">
        <h2>Moodle-LTE Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('moodle_lte_settings_group'); ?>
            <?php do_settings_sections('moodle-lte-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Endpoint URL</th>
                    <td><input type="text" name="api_endpoint" value="<?php echo esc_attr(get_option('api_endpoint')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Token</th>
                    <td><input type="text" name="access_token" value="<?php echo esc_attr(get_option('access_token')); ?>" /></td>
                </tr>
                <!-- <tr valign="top">
                    <th scope="row">Service Name</th>
                    <td><input type="text" name="service_name" value="<?php echo esc_attr(get_option('service_name')); ?>" /></td>
                </tr> -->
            </table>
            <?php submit_button(); ?>
        </form>
        <!-- Sync courses -->
        <h3>Synchronize Courses</h3>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php settings_fields('moodle_lte_settings_group'); ?>
            <?php do_settings_sections('moodle-lte-settings'); ?>
            <table class="form-table">
                <!-- Your settings fields here -->
                <input type="hidden" name="action" value="sync_courses_button">
            </table>
            <?php submit_button('Sync Courses', 'primary', 'sync_courses_button'); ?>
        </form>

        <h3>ShortCode</h3>
        <p>Use this shortcode to embed course registration form <strong>[moodle_lte_form]</strong></p>
    </div>
    <?php
}

function handle_sync_courses_button() {
    
    if (isset($_POST['sync_courses_button'])) {
        // Perform the synchronization process
        $result = synchronize_courses_from_moodle_to_wordpress() ;

        // Redirect back to settings page with success or error message
        if ($result === true) {
            $redirect_url = add_query_arg('settings-updated', 'true', $_POST['_wp_http_referer']);
        } else {
            $redirect_url = add_query_arg('sync_error', urlencode('Error synchronizing courses: ' . $result), $_POST['_wp_http_referer']);
        }
        // dump($redirect_url);
        wp_redirect($redirect_url);
        exit;

    }
}
add_action('admin_post_sync_courses_button', 'handle_sync_courses_button', 10);

// Register settings
function moodle_lte_register_settings() {
    register_setting('moodle_lte_settings_group', 'api_endpoint');
    register_setting('moodle_lte_settings_group', 'access_token');
    register_setting('moodle_lte_settings_group', 'service_name');
    // Add more settings fields as needed
}
add_action('admin_init', 'moodle_lte_register_settings');

//Helper function to Sync Courses
function synchronize_courses_from_moodle_to_wordpress() {
    // Your synchronization logic here
    // Fetch courses from Moodle using the API
    // Add/update courses in WordPress
    // Return true if synchronization is successful, otherwise return an error message

    // Moodle API endpoint
    $moodle_endpoint = get_option('api_endpoint').'/webservice/rest/server.php';

    // Moodle Web Service parameters
    $params = array(
        'wstoken' => get_option('access_token'),
        'wsfunction' => 'core_course_get_courses',
        'moodlewsrestformat' => 'json'
    );

    // Make a request to Moodle API
    $response = wp_remote_post($moodle_endpoint, array(
        'method' => 'POST',
        'body' => http_build_query($params)
    ));
    
    // Check if request was successful
    if (!is_wp_error($response) && $response['response']['code'] === 200) {
        // Parse JSON response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        // dd($data);
        // Check if courses were returned
        if (isset($data) && is_array($data)) {
            $courses = $data;
            // dump($courses);
            foreach ($courses as $course) {
                // Create a new draft post
                $post_id = wp_insert_post(array(
                    'post_title' => $course['fullname'], // Course title from Moodle
                    'post_content' => $course['summary'], // Course description from Moodle
                    'post_type' => 'course', // Your custom post type for courses
                    'post_status' => 'draft' // Set the post status to draft
                ));
                // dump(is_wp_error($post_id));
                // Check if post was created successfully
                if (!is_wp_error($post_id)) {
                    // Save additional course data as post meta
                    update_post_meta($post_id, '_moodle_course_id', $course['id']); // Moodle course ID
                    // Add more meta data as needed
                }
            }
            // Process the courses data...
            return true;
        } else {
            echo 'No courses found.';
        }
    } else {
        echo 'Error fetching courses from Moodle.';
    }
}
// Function to add plugin menu
function moodle_lte_menu() {
    add_menu_page(
        'Moodle LTE',
        'Moodle LTE',
        'manage_options',
        'moodle-lte',
        'moodle_lte_page'
    );
}
add_action('admin_menu', 'moodle_lte_menu');

// Function to display plugin menu page
function moodle_lte_page() {
    echo '<h1 class="moodle-lte-welcome" >Welcome to Moodle LTE</h1>';
    ?>
    <div class="wrap">
        <style>
            .shepherd-content .shepherd-text{
                overflow: auto !important;
            }
        </style>
        <h2 class="moodle-lte-config" >Moodle-LTE Configuration</h2>
        <form method="post" action="options.php">
            <?php settings_fields('moodle_lte_settings_group'); ?>
            <?php do_settings_sections('moodle-lte-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Endpoint URL</th>
                    <td><input class="moodle-lte-lms-url" type="text" name="api_endpoint" value="<?php echo esc_attr(get_option('api_endpoint')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Token</th>
                    <td><input class="moodle-lte-lms-token type="text" name="access_token" value="<?php echo esc_attr(get_option('access_token')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Service Name</th>
                    <td><input class="moodle-lte-lms-service-name" type="text" name="service_name" value="<?php echo esc_attr(get_option('service_name')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


//Courses CPT


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
        'show_in_menu'       => 'moodle-lte',
        'query_var'          => true,
        'rewrite'            => array('slug' => 'course'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author','thumbnail', 'excerpt', 'comments','page-attributes')
    );

    register_post_type('course', $args);
}
add_action('init', 'moodle_lte_course_post_type');
//course type meta


// Function to add meta box for course details
function moodle_lte_add_course_meta_box() {
    add_meta_box(
        'moodle_lte_course_details', // Meta box ID
        'Course Details', // Title of the meta box
        'moodle_lte_render_course_meta_box', // Callback function to render the content of the meta box
        'course', // Post type to which the meta box should be added
        'normal', // Context (normal, side, advanced)
        'default' // Priority (default, high, low)
    );
}
add_action('add_meta_boxes', 'moodle_lte_add_course_meta_box');

// Function to render the content of the course meta box
function moodle_lte_render_course_meta_box($post) {
    // Retrieve existing values from the database, if any
    $course_instructor = get_post_meta($post->ID, '_course_instructor', true);
    $course_duration = get_post_meta($post->ID, '_course_duration', true);
    $moodle_course_id = get_post_meta($post->ID, '_moodle_course_id', true);
    $moodle_enrol_instance_id = get_post_meta($post->ID, '_moodle_enrol_instance_id', true);
    $course_price = get_post_meta($post->ID, '_course_price', true);

    wp_nonce_field(basename(__FILE__), 'moodle_lte_course_meta_box_nonce');
    ?>
    <p>
        <label for="course_instructor">Instructor:</label><br>
        <input type="text" id="course_instructor" name="course_instructor" value="<?php echo esc_attr($course_instructor); ?>">
    </p>
    <p>
        <label for="course_duration">Duration:</label><br>
        <input type="text" id="course_duration" name="course_duration" value="<?php echo esc_attr($course_duration); ?>">
    </p>
    <p>
        <label for="moodle_course_id">Moodle Course Id:</label><br>
        <input type="text" id="moodle_course_id" name="moodle_course_id" value="<?php echo esc_attr($moodle_course_id); ?>">
    </p>
    <p>
        <label for="moodle_enrol_instance_id">Moodle Enrol Instance Id:</label><br>
        <input type="text" id="moodle_enrol_instance_id" name="moodle_enrol_instance_id" value="<?php echo esc_attr($moodle_enrol_instance_id); ?>">
    </p>
    <p>
        <label for="course_price">Course Price:</label><br>
        <input type="text" id="course_price" name="course_price" value="<?php echo esc_attr($course_price); ?>">
    </p>
    <?php
}

// Function to save meta box data when the course is saved
function moodle_lte_save_course_meta_data($post_id) {
    // Check if nonce is set and valid
    if (isset($_POST['moodle_lte_course_meta_box_nonce']) && wp_verify_nonce($_POST['moodle_lte_course_meta_box_nonce'], basename(__FILE__))) {
        // Save/update course instructor meta data
        if (isset($_POST['course_instructor'])) {
            update_post_meta($post_id, '_course_instructor', sanitize_text_field($_POST['course_instructor']));
        }
        // Save/update course duration meta data
        if (isset($_POST['course_duration'])) {
            update_post_meta($post_id, '_course_duration', sanitize_text_field($_POST['course_duration']));
        }
        // Save/update course moodle id meta data
        if (isset($_POST['moodle_course_id'])) {
            update_post_meta($post_id, '_moodle_course_id', sanitize_text_field($_POST['moodle_course_id']));
        }
        if (isset($_POST['moodle_enrol_instance_id'])) {
            update_post_meta($post_id, '_moodle_enrol_instance_id', sanitize_text_field($_POST['moodle_enrol_instance_id']));
        }
        // Save/update course price meta data
        if (isset($_POST['course_price'])) {
            update_post_meta($post_id, '_course_price', sanitize_text_field($_POST['course_price']));
        }
    }
    else {
        // Nonce verification failed
        echo "Nonce verification failed.";
    }
}
add_action('save_post', 'moodle_lte_save_course_meta_data');


///-----------

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
        'show_in_menu'      => 'moodle-lte',
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'course-category' ),
    );

    register_taxonomy( 'course_category', array( 'course' ), $args );
}
add_action( 'init', 'moodle_lte_course_taxonomy' );

// Enqueue scripts for the admin area
function moodle_lte_enqueue_admin_scripts($hook_suffix) {
    // Enqueue scripts only on the plugin settings page
    if ($hook_suffix === 'toplevel_page_moodle-lte') {
        // Enqueue Shepherd.js
        wp_enqueue_script('shepherd-js', 'https://cdnjs.cloudflare.com/ajax/libs/shepherd.js/8.0.0/js/shepherd.js', array(), '8.0.0', true);

        wp_enqueue_style('shepherd-css', 'https://cdnjs.cloudflare.com/ajax/libs/shepherd.js/8.0.0/css/shepherd.css', array(), '8.0.0');
        
        // Enqueue custom JavaScript file for the tour guide
        wp_enqueue_script('moodle-lte-tour-guide', plugin_dir_url(__FILE__) . 'js/tour-guide.js', array('shepherd-js'), '1.0.0', true);
        wp_enqueue_style('moodle-lte-tour-guide-css', plugin_dir_url(__FILE__) . 'css/style.css', array(), '1.0.0', true);

    }
}
add_action('admin_enqueue_scripts', 'moodle_lte_enqueue_admin_scripts');


add_filter( 'elementor/dynamic_tags/get_tags', function( $tags ) {
    // Define your custom meta fields here
    $custom_meta_fields = array(
        'custom_meta_field_1' => array(
            'title' => 'Custom Meta Field 1',
            'group' => 'Custom Meta Fields', // Optional: Group your tags under a specific category
        ),
        'custom_meta_field_2' => array(
            'title' => 'Custom Meta Field 2',
            'group' => 'Custom Meta Fields',
        ),
    );

    // Merge your custom meta fields with the existing tags
    $tags = array_merge( $tags, $custom_meta_fields );

    return $tags;
} );