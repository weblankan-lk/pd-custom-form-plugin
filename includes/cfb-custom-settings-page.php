<?php

function cfb_render_custom_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Custom Form Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cfb_settings_group');
            do_settings_sections('cfb_custom_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
