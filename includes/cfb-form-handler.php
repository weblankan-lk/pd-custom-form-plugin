<?php

// Handle form submission
function cfb_handle_form_submission()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cfb_form_id'])) {
        $form_id = intval($_POST['cfb_form_id']);
        $subject = sanitize_text_field($_POST['cfb_subject']);
        $to_email = get_option('admin_email'); // Default recipient

        // SMTP Settings
        $smtp_host = get_option('cfb_smtp_host');
        $smtp_username = get_option('cfb_smtp_username');
        $smtp_password = get_option('cfb_smtp_password');
        $smtp_port = get_option('cfb_smtp_port');
        $smtp_secure = get_option('cfb_smtp_secure');

        // Mailchimp Settings
        $mailchimp_api_key = get_option('cfb_mailchimp_api_key');
        $mailchimp_list_id = get_option('cfb_mailchimp_list_id');
        $mailchimp_server = get_option('cfb_mailchimp_server');

        // Get form fields
        $sender_email = sanitize_email($_POST['cfb_email'] ?? '');
        // $name = sanitize_text_field($_POST['name'] ?? '');
        // $message = sanitize_textarea_field($_POST['message'] ?? '');

        // Prepare Email Content
        // $email_content = "<html><body>";
        // $email_content .= "<h2>New Form Submission</h2>";
        // $email_content .= "<p><strong>Name:</strong> $name</p>";
        // $email_content .= "<p><strong>Email:</strong> $email</p>";
        // $email_content .= "<p><strong>Message:</strong> $message</p>";
        // $email_content .= "</body></html>";

        // Get all submitted fields dynamically
        $email_content = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>New Form Submission</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #ddd;
                }
                .email-header h1 {
                    color: #333;
                }
                .email-content {
                    padding: 20px 0;
                    text-align: left;
                }
                .email-content p {
                    font-size: 16px;
                    color: #555;
                }
                .email-footer {
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                    padding-top: 20px;
                    border-top: 2px solid #ddd;
                }
                .form-data {
                    background: #f9f9f9;
                    padding: 15px;
                    border-radius: 5px;
                    margin-top: 10px;
                }
                .form-data p {
                    margin: 5px 0;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>New Form Submission</h1>
                </div>
                <div class="email-content">
                    <p><strong>Form:</strong> ' . esc_html($subject) . '</p>
                    <div class="form-data">';

        $field_labels = [
            'cfb_first_name' => 'First Name',
            'cfb_last_name' => 'Last Name',
            'cfb_email' => 'Email',
            'cfb_phone' => 'Phone',
            'cfb_address' => 'Address',
            'cfb_country' => 'Country',
            'cfb_cv' => 'CV Attachment'
        ];

        foreach ($_POST as $key => $value) {
            if ($key !== 'cfb_form_id' && $key !== 'cfb_subject') {
                $label = $field_labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $email_content .= "<p><strong>" . esc_html($label) . ":</strong> " . esc_html($value) . "</p>";
            }
        }

        $email_content .= '</div>
                </div>
                <div class="email-footer">
                    <p>&copy; 2025 Your Company. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        // Handle File Upload (CV PDF)
        $attachments = [];
        if (!empty($_FILES['cfb_cv']['name'])) {
            $uploaded_file = $_FILES['cfb_cv'];
            $upload_dir = wp_upload_dir();
            $target_file = $upload_dir['path'] . '/' . basename($uploaded_file['name']);

            if (move_uploaded_file($uploaded_file['tmp_name'], $target_file)) {
                $attachments[] = $target_file; // Attach file to email
            }
        }

        // Set email headers
        $headers = [
            "From: $smtp_username",
            "Reply-To: $sender_email",
            "Content-Type: text/html; charset=UTF-8"
        ];

        // Send Email via SMTP
        if (!empty($smtp_host) && !empty($smtp_username) && !empty($smtp_password)) {
            add_action('phpmailer_init', function ($phpmailer) use ($smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_secure) {
                $phpmailer->isSMTP();
                $phpmailer->Host = $smtp_host;
                $phpmailer->SMTPAuth = true;
                $phpmailer->Username = $smtp_username;
                $phpmailer->Password = $smtp_password;
                $phpmailer->SMTPSecure = $smtp_secure;
                $phpmailer->Port = $smtp_port;
            });
        }

        wp_mail($to_email, $subject, $email_content, $headers, $attachments);

        // Mailchimp Integration (If enabled)
        if (!empty($mailchimp_api_key) && !empty($mailchimp_list_id) && !empty($mailchimp_server)) {
            // Check if Mailchimp is enabled for this form
            $mailchimp_enabled = get_post_meta($form_id, 'cfb_mailchimp_enabled', true);
            if ($mailchimp_enabled) {
                $email = sanitize_email($_POST['cfb_email'] ?? '');
                $name = sanitize_text_field($_POST['cfb_first_name'] ?? '');
                cfb_add_subscriber_to_mailchimp($mailchimp_api_key, $mailchimp_list_id, $mailchimp_server, $email, $name);
            }
        }

        // wp_redirect(add_query_arg('form_submitted'), 'true', wp_get_referer());
        // exit;
    }
}
add_action('init', 'cfb_handle_form_submission');

// Function to add subscriber to Mailchimp
function cfb_add_subscriber_to_mailchimp($api_key, $list_id, $server, $email, $name)
{
    $url = "https://$server.api.mailchimp.com/3.0/lists/$list_id/members/";
    $data = [
        'email_address' => $email,
        'status' => 'subscribed',
        'merge_fields' => [
            'FNAME' => $name
        ]
    ];

    $json_data = json_encode($data);

    $response = wp_remote_post($url, [
        'method' => 'POST',
        'body' => $json_data,
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
            'Content-Type' => 'application/json'
        ]
    ]);

    return wp_remote_retrieve_response_code($response) == 200;
}



