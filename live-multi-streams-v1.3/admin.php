<div class="wrap">
    <h1 style="display:flex; justify-content:space-between; align-items:center;">
        <span>Live Multi Streams Settings</span>
        <a href="https://www.buymeacoffee.com/mikerooze"
           target="_blank"
           style="background:#ffdd00; color:#23282d; padding:6px 12px; border-radius:6px; font-weight:600; text-decoration:none; margin-left:20px;">
           ☕ Support the Developer
        </a>
    </h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('lms_settings_group');
        do_settings_sections('lms_settings_group');

        $num_streams = get_option('lms_num_streams', 4);
        $theme = get_option('lms_theme', 'dark');
        ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Number of Streams</th>
                <td>
                    <select name="lms_num_streams">
                        <?php foreach ([4, 6, 8, 10, 12] as $n): ?>
                            <option value="<?php echo esc_attr($n); ?>" <?php selected($num_streams, $n); ?>>
                                <?php echo esc_html($n); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Change count and click Save Settings to update the fields below.</p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Default Theme</th>
                <td>
                    <select name="lms_theme">
                        <option value="dark" <?php selected($theme, 'dark'); ?>>Dark</option>
                        <option value="light" <?php selected($theme, 'light'); ?>>Light</option>
                    </select>
                    <p class="description">Which theme should the videowall use by default?</p>
                </td>
            </tr>
        </table>

        <h2>Stream URLs & Titles</h2>
        <?php
        for ($i = 1; $i <= $num_streams; $i++):
            $url = get_option("lms_stream_url_$i", '');
            $title = get_option("lms_stream_title_$i", '');
        ?>
            <p><strong>Stream <?php echo $i; ?></strong></p>
            <input type="text" name="lms_stream_url_<?php echo $i; ?>" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="Video/stream URL (mp4, HLS, or embed URL)" />
            <br>
            <input type="text" name="lms_stream_title_<?php echo $i; ?>" value="<?php echo esc_attr($title); ?>" class="regular-text" placeholder="Title (optional)" />
            <br><br>
        <?php endfor; ?>

        <?php submit_button('Save Settings'); ?>
    </form>

    <p style="margin-top:20px;">
        Place the shortcode <code>[live_multi_streams]</code> on any page to show the videowall.
    </p>
</div>
