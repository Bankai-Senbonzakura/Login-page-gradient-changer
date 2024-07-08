<?php
/*
Plugin Name: Login Page Customizer
Description: Customize the WordPress login page with gradient backgrounds and other styling options.
Version: 1.3
Author: Scorching Mustang
*/

// Enqueue necessary scripts and styles
add_action('admin_enqueue_scripts', 'lpc_enqueue_admin_assets');

// Register the admin menu
add_action('admin_menu', 'lpc_add_admin_menu');

// Enqueue admin scripts and styles
function lpc_enqueue_admin_assets() {
    wp_enqueue_script('jquery'); // Enqueue jQuery as a dependency

    // Enqueue jscolor script for color picker
    wp_enqueue_script('jscolor', 'https://jscolor.com/release/2.4.6/jscolor.min.js', [], '2.4.6', true);

    // Enqueue custom JavaScript
    wp_enqueue_script('lpc-custom-script', plugin_dir_url(__FILE__) . 'lpc-custom.js', ['jquery'], '1.0', true);

    // Enqueue custom CSS
    wp_enqueue_style('lpc-custom-css', plugin_dir_url(__FILE__) . 'lpc-custom.css');
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
    $current_color1 = get_option('lpc_color1', '');
    $current_color2 = get_option('lpc_color2', '');
    $current_gradient_type = get_option('lpc_gradient_type', '');
    $current_gradient_direction = get_option('lpc_gradient_direction', '');

    lpc_apply_styles_on_preview();
    ?>
    <div class="lpc-customizer-container">
        <div class="lpc-form-container">
            <h1>Customize Your Login Page</h1>
            <form id="lpc-login-customizer-form" method="post" action="">
                <?php wp_nonce_field('lpc_login_customizer_action', 'lpc_login_customizer_nonce'); ?>

                <label for="lpc-color1">Color 1:</label><br>
                <input type="text" id="lpc-color1" class="jscolor" name="color1" value="<?php echo esc_attr($current_color1); ?>" data-tip="Choose the first gradient color"><br>

                <label for="lpc-color2">Color 2:</label><br>
                <input type="text" id="lpc-color2" class="jscolor" name="color2" value="<?php echo esc_attr($current_color2); ?>" data-tip="Choose the second gradient color"><br>

                <label for="lpc-gradient-type">Gradient Type:</label><br>
                <select id="lpc-gradient-type" name="gradient-type" data-tip="Select the type of gradient">
                    <option value="linear" <?php selected($current_gradient_type, 'linear'); ?>>Linear</option>
                    <option value="radial" <?php selected($current_gradient_type, 'radial'); ?>>Radial</option>
                </select><br>

                <label for="lpc-gradient-direction">Gradient Direction:</label><br>
                <select id="lpc-gradient-direction" name="gradient-direction" data-tip="Select the direction of the gradient">
                    <option value="to right" <?php selected($current_gradient_direction, 'to right'); ?>>To Right</option>
                    <option value="to left" <?php selected($current_gradient_direction, 'to left'); ?>>To Left</option>
                    <option value="to bottom" <?php selected($current_gradient_direction, 'to bottom'); ?>>To Bottom</option>
                    <option value="to top" <?php selected($current_gradient_direction, 'to top'); ?>>To Top</option>
                    <option value="to bottom right" <?php selected($current_gradient_direction, 'to bottom right'); ?>>To Bottom Right</option>
                    <option value="to bottom left" <?php selected($current_gradient_direction, 'to bottom left'); ?>>To Bottom Left</option>
                    <option value="to top right" <?php selected($current_gradient_direction, 'to top right'); ?>>To Top Right</option>
                    <option value="to top left" <?php selected($current_gradient_direction, 'to top left'); ?>>To Top Left</option>
                </select><br><br>

                <input type="submit" name="lpc-submit" value="Save Changes" class="button button-primary">
            </form>
        </div>
        <div id="lpc-login-preview" class="lpc-preview-container">
            <h2>Live Preview</h2>
            <div id="lpc-preview-background" class="lpc-preview-background"></div>
        </div>
    </div>
    <?php
}

// Handle form submission
if (isset($_POST['lpc-submit'])) {
    $posted_color1 = sanitize_hex_color($_POST['color1']);
    $posted_color2 = sanitize_hex_color($_POST['color2']);
    $posted_gradient_type = sanitize_text_field($_POST['gradient-type']);
    $posted_gradient_direction = sanitize_text_field($_POST['gradient-direction']);

    if ($posted_color1 !== get_option('lpc_color1')) {
        update_option('lpc_color1', $posted_color1);
    }
    if ($posted_color2 !== get_option('lpc_color2')) {
        update_option('lpc_color2', $posted_color2);
    }
    if ($posted_gradient_type !== get_option('lpc_gradient_type')) {
        update_option('lpc_gradient_type', $posted_gradient_type);
    }
    if ($posted_gradient_direction !== get_option('lpc_gradient_direction')) {
        update_option('lpc_gradient_direction', $posted_gradient_direction);
    }

    lpc_apply_custom_login_styles(); // Apply styles immediately after saving
}

// Enqueue custom styles for login page
add_action('login_enqueue_scripts', 'lpc_apply_custom_login_styles');

function lpc_apply_custom_login_styles() {
    $color1 = get_option('lpc_color1', '#ffffff');
    $color2 = get_option('lpc_color2', '#000000');
    $gradientType = get_option('lpc_gradient_type', 'linear');
    $gradientDirection = get_option('lpc_gradient_direction', 'to right');

    $gradientStyle = $gradientType === 'radial' ? "radial-gradient(circle, $color1, $color2)" : "linear-gradient($gradientDirection, $color1, $color2)";

    echo "<style>body.login { background: $gradientStyle !important; }</style>";
}

// Update live preview with selected styles
function lpc_apply_styles_on_preview() {
    $color1 = get_option('lpc_color1', '#ffffff');
    $color2 = get_option('lpc_color2', '#000000');
    $gradientType = get_option('lpc_gradient_type', 'linear');
    $gradientDirection = get_option('lpc_gradient_direction', 'to right');

    $gradientStyle = $gradientType === 'radial' ?
        "radial-gradient(circle, $color1, $color2)" :
        "linear-gradient($gradientDirection, $color1, $color2)";

    $sanitized_gradientStyle = esc_attr($gradientStyle);

    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('lpc-preview-background').style.background = '$sanitized_gradientStyle';
    });
    </script>";
}
?>
