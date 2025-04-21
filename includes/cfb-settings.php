<?php



// Render the Settings Page
function cfb_render_smtp_mailchimp_settings_page()
{
    ?>
    <div class="wrap">
        <h1>SMTP & Mailchimp Settings</h1>
        <form method="POST" action="options.php">
            <?php
            settings_fields('cfb_smtp_mailchimp_options_group'); // Settings group
            do_settings_sections('cfb_smtp_mailchimp_settings');   // Settings section
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


// Render the settings page
function cfb_register_smtp_mailchimp_settings()
{
    // SMTP Settings
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_smtp_host');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_smtp_username');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_smtp_password');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_smtp_port');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_smtp_secure');

    // Mailchimp Settings
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_mailchimp_api_key');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_mailchimp_list_id');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_mailchimp_server');

    // reCAPTCHA
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_recaptcha_site_key');
    register_setting('cfb_smtp_mailchimp_options_group', 'cfb_recaptcha_secret_key');
}

add_action('admin_init', 'cfb_register_smtp_mailchimp_settings');

function cfb_add_smtp_mailchimp_settings_fields()
{
    // SMTP Settings Section
    add_settings_section(
        'cfb_smtp_settings_section',
        'SMTP Settings',
        null,
        'cfb_smtp_mailchimp_settings'
    );

    add_settings_field(
        'cfb_smtp_host',
        'SMTP Host',
        'cfb_smtp_host_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_smtp_settings_section'
    );

    add_settings_field(
        'cfb_smtp_username',
        'SMTP Username',
        'cfb_smtp_username_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_smtp_settings_section'
    );

    add_settings_field(
        'cfb_smtp_password',
        'SMTP Password',
        'cfb_smtp_password_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_smtp_settings_section'
    );

    add_settings_field(
        'cfb_smtp_port',
        'SMTP Port',
        'cfb_smtp_port_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_smtp_settings_section'
    );

    add_settings_field(
        'cfb_smtp_secure',
        'SMTP Secure (tls/ssl)',
        'cfb_smtp_secure_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_smtp_settings_section'
    );

    // Mailchimp Settings Section
    add_settings_section(
        'cfb_mailchimp_settings_section',
        'Mailchimp Settings',
        null,
        'cfb_smtp_mailchimp_settings'
    );

    add_settings_field(
        'cfb_mailchimp_api_key',
        'Mailchimp API Key',
        'cfb_mailchimp_api_key_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_mailchimp_settings_section'
    );

    add_settings_field(
        'cfb_mailchimp_list_id',
        'Mailchimp List ID',
        'cfb_mailchimp_list_id_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_mailchimp_settings_section'
    );

    add_settings_field(
        'cfb_mailchimp_server',
        'Mailchimp Server Prefix (us2, us3, etc.)',
        'cfb_mailchimp_server_field',
        'cfb_smtp_mailchimp_settings',
        'cfb_mailchimp_settings_section'
    );

    // reCAPTCHA
    add_settings_section(
        'cfb_recaptcha_settings_section',
        'reCAPTCHA Settings',
        null,
        'cfb_smtp_mailchimp_settings'
    );

    add_settings_field(
        'cfb_recaptcha_site_key',
        'reCAPTCHA Site Key',
        function () {
            echo "<input type='text' name='cfb_recaptcha_site_key' value='" . esc_attr(get_option('cfb_recaptcha_site_key')) . "' />";
        },
        'cfb_smtp_mailchimp_settings',
        'cfb_recaptcha_settings_section'
    );

    add_settings_field(
        'cfb_recaptcha_secret_key',
        'reCAPTCHA Secret Key',
        function () {
            echo "<input type='text' name='cfb_recaptcha_secret_key' value='" . esc_attr(get_option('cfb_recaptcha_secret_key')) . "' />";
        },
        'cfb_smtp_mailchimp_settings',
        'cfb_recaptcha_settings_section'
    );
}

add_action('admin_init', 'cfb_add_smtp_mailchimp_settings_fields');

// Callback functions for each field
function cfb_smtp_host_field()
{
    $smtp_host = get_option('cfb_smtp_host');
    echo "<input type='text' name='cfb_smtp_host' value='" . esc_attr($smtp_host) . "' />";
}

function cfb_smtp_username_field()
{
    $smtp_username = get_option('cfb_smtp_username');
    echo "<input type='text' name='cfb_smtp_username' value='" . esc_attr($smtp_username) . "' />";
}

function cfb_smtp_password_field()
{
    $smtp_password = get_option('cfb_smtp_password');
    echo "<input type='password' name='cfb_smtp_password' value='" . esc_attr($smtp_password) . "' />";
}

function cfb_smtp_port_field()
{
    $smtp_port = get_option('cfb_smtp_port');
    echo "<input type='text' name='cfb_smtp_port' value='" . esc_attr($smtp_port) . "' />";
}

function cfb_smtp_secure_field()
{
    $smtp_secure = get_option('cfb_smtp_secure');
    echo "<input type='text' name='cfb_smtp_secure' value='" . esc_attr($smtp_secure) . "' />";
}

function cfb_mailchimp_api_key_field()
{
    $mailchimp_api_key = get_option('cfb_mailchimp_api_key');
    echo "<input type='text' name='cfb_mailchimp_api_key' value='" . esc_attr($mailchimp_api_key) . "' />";
}

function cfb_mailchimp_list_id_field()
{
    $mailchimp_list_id = get_option('cfb_mailchimp_list_id');
    echo "<input type='text' name='cfb_mailchimp_list_id' value='" . esc_attr($mailchimp_list_id) . "' />";
}

function cfb_mailchimp_server_field()
{
    $mailchimp_server = get_option('cfb_mailchimp_server');
    echo "<input type='text' name='cfb_mailchimp_server' value='" . esc_attr($mailchimp_server) . "' />";
}


// Enqueue reCAPTCHA Script

function cfb_enqueue_recaptcha_script()
{
    $site_key = get_option('cfb_recaptcha_site_key');
    if (!empty($site_key)) {
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
    }
}
add_action('wp_enqueue_scripts', 'cfb_enqueue_recaptcha_script');
