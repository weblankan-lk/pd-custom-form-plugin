<?php

function cfb_add_form_meta_box()
{
    add_meta_box(
        'cfb_form_fields',
        'Form Configuration',
        'cfb_form_fields_callback',
        'cfb_form',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'cfb_add_form_meta_box');

function cfb_form_fields_callback($post)
{
    $fields = get_post_meta($post->ID, 'cfb_fields', true);
    $subject = get_post_meta($post->ID, 'cfb_subject', true);
    $custom_code = get_post_meta($post->ID, 'cfb_custom_code', true);  // Store the custom code

?>
    <label for="cfb_subject">Subject:</label>
    <input type="text" name="cfb_subject" value="<?php echo esc_attr($subject); ?>" required>

    <h3>Custom Form Layout (Shortcodes)</h3>
    <p>Use the following shortcodes in the editor below to create a custom layout:</p>
    <ul>
        <li>[cfb_first_name]</li>
        <li>[cfb_last_name]</li>
        <li>[cfb_email]</li>
        <li>[cfb_phone]</li>
        <li>[cfb_address]</li>
        <li>[cfb_country]</li>
    </ul>

    <label for="cfb_custom_code">Custom HTML/Shortcode Layout:</label>
    <?php
    // Use the wp_editor function to display a rich text editor for custom HTML/Shortcodes
    wp_editor($custom_code, 'cfb_custom_code', array(
        'textarea_name' => 'cfb_custom_code',
        'media_buttons' => false,
        'teeny' => true,
        'textarea_rows' => 10,
        'editor_class' => 'cfb-custom-editor'
    ));
    ?>
<?php
}

function cfb_save_form_fields($post_id)
{
    if (isset($_POST['cfb_fields'])) {
        update_post_meta($post_id, 'cfb_fields', $_POST['cfb_fields']);
    }
    if (isset($_POST['cfb_subject'])) {
        update_post_meta($post_id, 'cfb_subject', sanitize_text_field($_POST['cfb_subject']));
    }
    if (isset($_POST['cfb_custom_code'])) {
        update_post_meta($post_id, 'cfb_custom_code', wp_kses_post($_POST['cfb_custom_code']));
    }
}
add_action('save_post', 'cfb_save_form_fields');
