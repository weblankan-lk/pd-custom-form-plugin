<?php

// function cfb_first_name_shortcode() {
//     return '<label for="cfb_first_name">First Name:</label>
//             <input type="text" name="cfb_first_name" id="cfb_first_name" >';
// }
// add_shortcode('cfb_first_name', 'cfb_first_name_shortcode');

// function cfb_last_name_shortcode() {
//     return '<label for="cfb_last_name">Last Name:</label>
//             <input type="text" name="cfb_last_name" id="cfb_last_name" >';
// }
// add_shortcode('cfb_last_name', 'cfb_last_name_shortcode');

// function cfb_email_shortcode() {
//     return '<label for="cfb_email">Email:</label>
//             <input type="email" name="cfb_email" id="cfb_email" >';
// }
// add_shortcode('cfb_email', 'cfb_email_shortcode');

// function cfb_phone_shortcode() {
//     return '<label for="cfb_phone">Phone (with country code):</label>
//             <input type="tel" name="cfb_phone" id="cfb_phone" placeholder="+1 234 567 890" >';
// }
// add_shortcode('cfb_phone', 'cfb_phone_shortcode');

// function cfb_address_shortcode() {
//     return '<label for="cfb_address">Address:</label>
//             <input type="text" name="cfb_address" id="cfb_address" >';
// }
// add_shortcode('cfb_address', 'cfb_address_shortcode');

// function cfb_country_shortcode() {
//     // Example with a few country options
//     return '<label for="cfb_country">Country:</label>
//             <select name="cfb_country" id="cfb_country">
//                 <option value="USA">United States</option>
//                 <option value="CA">Canada</option>
//                 <option value="IN">India</option>
//             </select>';
// }
// add_shortcode('cfb_country', 'cfb_country_shortcode');

// function cfb_cv_upload_shortcode() {
//     return '<label for="cfb_cv">Upload CV (PDF only):</label>
//             <input type="file" name="cfb_cv" id="cfb_cv" accept=".pdf,.doc,.docx">';
// }
// add_shortcode('cfb_cv', 'cfb_cv_upload_shortcode');


function cfb_custom_field_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'type' => 'text',
            'name' => 'custom_field',
            'label' => '',
            'placeholder' => '',
            'required' => 'false',
            'options' => '',
            'accept' => '',
            'value' => '',
        ),
        $atts,
        'cfb_field'
    );

    $required = ($atts['required'] === 'true') ? 'required' : '';
    $html = "<label for='{$atts['name']}'>{$atts['label']}</label>";

    switch ($atts['type']) {
        case 'textarea':
            $html .= "<textarea name='{$atts['name']}' id='{$atts['name']}' placeholder='{$atts['placeholder']}' $required>{$atts['value']}</textarea>";
            break;
        case "select":
            $html .= "<select name='{$atts['name']}' id='{$atts['name']}' $required>";
            foreach (explode(',', $atts['options']) as $option) {
                $html .= "<option value='$option'>$option</option>";
            }
            $html .= "</select>";
            break;
        case 'radio':
        case 'checkbox':
            $html .= "<div class='cfb_checkbox'>";
            foreach (explode(',', $atts['options']) as $option) {
                $html .= "<div><input type='{$atts['type']}' name='{$atts['name']}' value='$option' $required> <span>$option</span></div>";
            }
            $html .= "</div>";
            break;
        case 'file':
            $html .= "<input type='file' name='{$atts['name']}' id='{$atts['name']}' accept='{$atts['accept']}' $required>";
            break;
        case 'submit':
            $html = "<button type='submit' id='{$atts['name']}'>{$atts['label']}</button>";
            break;
        default:
            $html .= "<input type='{$atts['type']}' name='{$atts['name']}' id='{$atts['name']}' placeholder='{$atts['placeholder']}' value='{$atts['value']}' $required>";
    }

    return $html;
}

add_shortcode('cfb_field', 'cfb_custom_field_shortcode');
