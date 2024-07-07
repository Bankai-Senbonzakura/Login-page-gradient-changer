<?php
/*
* Plugin Name: Login Page Customizer
* Description: A plugin to customize the WordPress login page with gradient backgrounds and other styling options.
* Version: 1.3
* Author: Scorching Mustang
*/

// Enqueue necessary scripts and styles
add_action('admin_enqueue_scripts', 'lpc_enqueue_admin_assets');
add_action('login_enqueue_scripts', 'lpc_enqueue_login_styles');
add_action('login_head', 'lpc_apply_custom_styles');

// Register the admin menu
add_action('admin_menu', 'lpc_add_admin_menu');

// Enqueue admin scripts and styles
function lpc_enqueue_admin_assets() {
    // Enqueue jQuery (if not already loaded)
    wp_enqueue_script('jquery');

    // Enqueue jscolor script
    wp_enqueue_script('jscolor', 'https://jscolor.com/release/2.4.6/jscolor.min.js', [], '2.4.6', true);

    // Enqueue custom JavaScript
    wp_enqueue_script('lpc-customizer-script', plugin_dir_url(__FILE__) . 'lpc-customizer.js', ['jquery'], '1.0', true);

    // Enqueue custom CSS
    wp_enqueue_style('lpc-custom-css', plugin_dir_url(__FILE__) . 'lpc-custom.css');
}

// Enqueue styles for the login page
function lpc_enqueue_login_styles() {
    wp_enqueue_style('lpc-login-style', plugin_dir_url(__FILE__) . 'lpc-login-style.css');
}

// Apply custom styles to the login page
function lpc_apply_custom_styles() {
    $color1 = get_option('lpc_color1', 'ffffff');
    $color2 = get_option('lpc_color2', '000000');
    $gradientType = get_option('lpc_gradient_type', 'linear');
    $gradientDirection = get_option('lpc_gradient_direction', 'to right');

    $gradientStyle = $gradientType === 'radial' ? "radial-gradient(circle, #$color1, #$color2)" : "linear-gradient($gradientDirection, #$color1, #$color2)";

    echo "<style>
        body.login {
            background: $gradientStyle; /* Apply background gradient to body */
        }
        .login #login {
            display: none; /* Hide the login form */
        }
    </style>";
}

// Add custom admin menu
function lpc_add_admin_menu() {
    add_menu_page(
        'Login Page Customizer',
        'Login Customizer',
        'manage_options',
        'lpc-login-customizer',
        'lpc_render_admin_page',
        'dashicons-admin-generic',
        6
    );
}

// Render the admin page for the plugin
function lpc_render_admin_page() {
    ?>
    <div class="lpc-customizer-container">
        <div class="lpc-form-container">
            <h1>Customize Your Login Page</h1>
            <form id="lpc-login-customizer-form">
                <?php wp_nonce_field('lpc_login_customizer_action', 'lpc_login_customizer_nonce'); ?>

                <label for="lpc-color1">Color 1:</label>
                <input type="text" id="lpc-color1" class="jscolor" name="color1" data-tip="Choose the first gradient color" />

                <label for="lpc-color2">Color 2:</label>
                <input type="text" id="lpc-color2" class="jscolor" name="color2" data-tip="Choose the second gradient color" />

                <label for="lpc-gradient-type">Gradient Type:</label>
                <select id="lpc-gradient-type" name="gradient-type" data-tip="Select the type of gradient">
                    <option value="linear">Linear</option>
                    <option value="radial">Radial</option>
                </select>

                <label for="lpc-gradient-direction">Gradient Direction:</label>
                <select id="lpc-gradient-direction" name="gradient-direction" data-tip="Select the direction of the gradient">
                    <option value="to right">To Right</option>
                    <option value="to left">To Left</option>
                    <option value="to bottom">To Bottom</option>
                    <option value="to top">To Top</option>
                    <option value="to bottom right">To Bottom Right</option>
                    <option value="to bottom left">To Bottom Left</option>
                    <option value="to top right">To Top Right</option>
                    <option value="to top left">To Top Left</option>
                </select>

                <input type="submit" value="Save Changes" class="button button-primary">
            </form>
        </div>
        <div id="lpc-login-preview" class="lpc-preview-container">
            <h2>Live Preview</h2>
            <div class="lpc-login-form-container">
                <div id="lpc-preview-background" class="lpc-preview-background"></div>
            </div>
        </div>
    </div>
    <?php
}

?>
