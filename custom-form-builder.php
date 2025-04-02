<?php

/**
 * Plugin Name: Custom Form Builder
 * Description: A custom form builder with shortcode support.
 * Version: 1.0
 * Author: Insaf Inhaam
 */

if (!class_exists('Custom_Form_Builder_Updater')) {
    class Custom_Form_Builder_Updater
    {
        private $plugin_slug;
        private $github_url;
        private $current_version;

        public function __construct()
        {
            $this->plugin_slug = plugin_basename(__FILE__);
            $this->github_url = 'https://api.github.com/repos/weblankan-lk/pd-custom-form-plugin/releases/latest';
            $this->current_version = '1.0'; // ✅ Keep this version updated

            add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
            add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
        }

        public function check_for_update($transient)
        {
            if (empty($transient->checked)) {
                return $transient;
            }

            // Fetch latest release from GitHub
            $response = wp_remote_get($this->github_url);
            if (is_wp_error($response)) {
                return $transient;
            }

            $release = json_decode(wp_remote_retrieve_body($response));
            if (!isset($release->tag_name)) {
                return $transient;
            }

            $latest_version = $release->tag_name;

            // Check if update is needed
            if (version_compare($this->current_version, $latest_version, '<')) {
                $transient->response[$this->plugin_slug] = (object) [
                    'slug' => $this->plugin_slug,
                    'new_version' => $latest_version,
                    'url' => $release->html_url,
                    'package' => $release->assets[0]->browser_download_url ?? '',
                ];
            }

            return $transient;
        }

        public function plugin_info($false, $action, $args)
        {
            if ($args->slug !== $this->plugin_slug) {
                return $false;
            }

            $response = wp_remote_get($this->github_url);
            if (is_wp_error($response)) {
                return $false;
            }

            $release = json_decode(wp_remote_retrieve_body($response));
            if (!isset($release->tag_name)) {
                return $false;
            }

            return (object) [
                'name' => 'Custom Form Builder',
                'slug' => $this->plugin_slug,
                'version' => $release->tag_name,
                'author' => '<a href="https://github.com/yourusername">Insaf Inhaam</a>',
                'homepage' => $release->html_url,
                'download_link' => $release->assets[0]->browser_download_url ?? '',
                'sections' => [
                    'description' => 'A custom form builder plugin with automatic GitHub updates.',
                ],
            ];
        }
    }

    new Custom_Form_Builder_Updater();
}

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
        'edit.php?post_type=cfb_form',
        'SMTP & Mailchimp Settings',
        'SMTP & Mailchimp Settings',
        'manage_options',
        'cfb_smtp_mailchimp_settings',
        'cfb_render_smtp_mailchimp_settings_page'
    );
}
add_action('admin_menu', 'cfb_add_smtp_mailchimp_settings_page');

function cfb_add_custom_submenu_page()
{
    add_submenu_page(
        'edit.php?post_type=cfb_form', // Parent menu (Forms)
        'Custom Form Settings',        // Page title
        'Custom Settings',             // Menu title
        'manage_options',              // Capability
        'cfb_custom_settings',         // Menu slug
        'cfb_render_custom_settings_page' // Callback function
    );
}

add_action('admin_menu', 'cfb_add_custom_submenu_page');

// Css file import.
function cfb_enqueue_styles($hook)
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
        <li><strong>[cfb_field type="text" name="patient_name" label="Patient's Name" placeholder="Type Here"
                required="true"]</strong></li>
        <li><strong>[cfb_field type="email" name="payer_email" label="Email Address" placeholder="Enter your email"
                required="true"]</strong></li>
        <li><strong>[cfb_field type="url" name="website" label="Website" placeholder="https://yourwebsite.com"]</strong>
        </li>
        <li><strong>[cfb_field type="tel" name="phone" label="Phone Number" placeholder="+1 234 567 890"]</strong></li>
        <li><strong>[cfb_field type="number" name="age" label="Age"]</strong></li>
        <li><strong>[cfb_field type="date" name="dob" label="Date of Birth"]</strong></li>
        <li><strong>[cfb_field type="textarea" name="address" label="Address" placeholder="Enter your full
                address"]</strong></li>
        <li><strong>[cfb_field type="select" name="country" label="Select Country" options="USA,Canada,India,UK"]</strong>
        </li>
        <li><strong>[cfb_field type="radio" name="gender" label="Gender" options="Male,Female,Other"]</strong></li>
        <li><strong>[cfb_field type="checkbox" name="terms" label="Accept Terms and Conditions" options="I Agree"]</strong>
        </li>
        <li><strong>[cfb_field type="file" name="cv" label="Upload CV" accept=".pdf,.doc,.docx"]</strong></li>
        <li><strong>[cfb_field type="submit" name="submit_btn" label="Submit"]</strong></li>
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

    return '<form id="cfb-form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="cfb_form_id" value="' . esc_attr($form_id) . '">
                <input type="hidden" name="cfb_subject" value="' . esc_attr($subject) . '">
                ' . do_shortcode($custom_code) . '

                <p id="cfb-error-message" style="color: red; display: none;"></p>
            </form>';
}
add_shortcode('cfb_form', 'cfb_form_shortcode');




include_once plugin_dir_path(__FILE__) . 'includes/cfb-form-render.php';
include_once plugin_dir_path(__FILE__) . 'includes/cfb-settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/cfb-form-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/cfb-custom-settings-page.php';
?>