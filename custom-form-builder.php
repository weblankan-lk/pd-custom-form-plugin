<?php

/**
 * Plugin Name: Custom Form Builder
 * Description: A custom form builder with shortcode support.
 * Version: 1.0
 * Author: Insaf Inhaam
 */

// Register Custom Post Type
function cfb_register_form_post_type()
{
    register_post_type('cfb_form', [
        'label' => 'Forms',
        'public' => true,
        'show_ui' => true,
        'supports' => ['title'],
        'menu_position' => 20,
    ]);
}
add_action('init', 'cfb_register_form_post_type');

// Add SMTP and Mailchimp Settings Page to the Admin Menu
function cfb_add_smtp_mailchimp_settings_page()
{
    add_submenu_page(
        'edit.php?post_type=cfb_form', // Parent menu (Forms)
        'SMTP & Mailchimp Settings',   // Page title
        'SMTP & Mailchimp Settings',   // Menu title
        'manage_options',              // Required capability
        'cfb_smtp_mailchimp_settings', // Menu slug
        'cfb_render_smtp_mailchimp_settings_page' // Callback function
    );
}
add_action('admin_menu', 'cfb_add_smtp_mailchimp_settings_page');

// Css file import.
function cfb_enqueue_styles()
{
    wp_enqueue_style('cfb_style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css', [], '1.0.0', 'all');
    wp_enqueue_script('cfb-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', [], '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'cfb_enqueue_styles');

// Meta Box for Form Settings
function cfb_add_form_meta_box()
{
    add_meta_box('cfb_form_fields', 'Form Configuration', 'cfb_form_fields_callback', 'cfb_form', 'normal', 'high');
}
add_action('add_meta_boxes', 'cfb_add_form_meta_box');

function cfb_form_fields_callback($post)
{
    $subject = get_post_meta($post->ID, 'cfb_subject', true);
    $custom_code = get_post_meta($post->ID, 'cfb_custom_code', true);
    $mailchimp_enabled = get_post_meta($post->ID, 'cfb_mailchimp_enabled', true);
    ?>
    <label for="cfb_subject">Subject:</label>
    <input type="text" name="cfb_subject" value="<?php echo esc_attr($subject); ?>" required>

    <h3>Custom Form Layout (Shortcodes)</h3>
    <p>Use shortcodes below:</p>
    <ul>
        <li><strong>[cfb_first_name]</strong></li>
        <li><strong>[cfb_last_name]</strong></li>
        <li><strong>[cfb_email]</strong></li>
        <li><strong>[cfb_phone]</strong></li>
        <li><strong>[cfb_address]</strong></li>
        <li><strong>[cfb_country]</strong></li>
        <li><strong>[cfb_cv]</strong></li>
    </ul>

    <label for="cfb_custom_code">Custom HTML/Shortcode Layout:</label>
    <?php wp_editor($custom_code, 'cfb_custom_code', ['textarea_name' => 'cfb_custom_code', 'media_buttons' => false, 'teeny' => true, 'textarea_rows' => 10]); ?>
    <br>
    <label for="cfb_mailchimp_enabled">
        <input type="checkbox" name="cfb_mailchimp_enabled" value="1" <?php checked($mailchimp_enabled, 1); ?>>
        Enable Mailchimp Integration
    </label>

    <p>Use shortcode: <strong>[cfb_form id="<?php echo $post->ID; ?>"]</strong></p>
    <?php
}

function cfb_save_form_fields($post_id)
{
    if (isset($_POST['cfb_subject'])) {
        update_post_meta($post_id, 'cfb_subject', sanitize_text_field($_POST['cfb_subject']));
    }
    if (isset($_POST['cfb_custom_code'])) {
        update_post_meta($post_id, 'cfb_custom_code', wp_kses_post($_POST['cfb_custom_code']));
    }
    if (isset($_POST['cfb_mailchimp_enabled'])) {
        update_post_meta($post_id, 'cfb_mailchimp_enabled', isset($_POST['cfb_mailchimp_enabled']) ? 1 : 0);
    }
}
add_action('save_post', 'cfb_save_form_fields');

// Form Shortcode Rendering
function cfb_form_shortcode($atts)
{
    $atts = shortcode_atts(['id' => ''], $atts);
    $form_id = intval($atts['id']);
    if (!$form_id)
        return 'Invalid Form ID';

    $subject = get_post_meta($form_id, 'cfb_subject', true);
    $custom_code = get_post_meta($form_id, 'cfb_custom_code', true);

    return '<form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cfb_form_id" value="' . esc_attr($form_id) . '">
                <input type="hidden" name="cfb_subject" value="' . esc_attr($subject) . '">
                ' . do_shortcode($custom_code) . '
                <button type="submit">Submit</button>
            </form>';
}
add_shortcode('cfb_form', 'cfb_form_shortcode');




include_once plugin_dir_path(__FILE__) . 'includes/form-render.php';
include_once plugin_dir_path(__FILE__) . 'includes/cfb-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
?>