<?php
function custom_form_settings_page_content_1() {
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

function custom_form_register_settings_1() {
    register_setting('custom_form_settings_group', 'api_endpoint');
    register_setting('custom_form_settings_group', 'access_token');
}
add_action('admin_init', 'custom_form_register_settings_1');


// Add a function to add settings to wordpress


?>
