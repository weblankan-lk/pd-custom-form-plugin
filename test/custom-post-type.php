<?php

function cfb_register_form_post_type()
{
    $args = array(
        'public' => true,
        'label' => 'Custom Forms',
        'menu_icon' => 'dashicons-feedback',
        'supports' => array('title'),
    );
    register_post_type('cfb_form', $args);
}

add_action('init', 'cfb_register_form_post_type');
