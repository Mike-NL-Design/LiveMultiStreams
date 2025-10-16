<?php
/**
 * Plugin Name: Live Multi Streams
 * Description: Display multiple live streams with admin control and editable stream URLs (Twitch/YouTube/MP4/HLS).
 * Version: 1.3
 * Author and (C): Michael Rooze
 */

if (!defined('ABSPATH')) exit;

// Enqueue scripts & styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('lms-style', plugins_url('assets/css/lms.css', __FILE__));
    wp_enqueue_script('lms-script', plugins_url('assets/js/lms.js', __FILE__), ['jquery'], filemtime(plugin_dir_path(__FILE__).'assets/js/lms.js'), true);
});

// Shortcode
add_shortcode('live_multi_streams', 'lms_display_videowall');

// Admin setup
add_action('admin_menu', 'lms_add_admin_menu');
add_action('admin_init', 'lms_register_settings');

function lms_add_admin_menu() {
    add_menu_page(
        'Live Multi Streams',
        'Live Multi Streams',
        'manage_options',
        'lms-settings',
        'lms_settings_page',
        'dashicons-video-alt3',
        80
    );
}

function lms_register_settings() {
    register_setting('lms_settings_group', 'lms_stream_count', [
        'default' => 12,
        'type' => 'integer'
    ]);
    register_setting('lms_settings_group', 'lms_streams', [
        'default' => [],
        'type' => 'array'
    ]);
    register_setting('lms_settings_group', 'lms_default_mode', [
        'default' => 'dark',
        'type' => 'string'
    ]);
}

function lms_settings_page() {
    $count = intval(get_option('lms_stream_count', 12));
    $streams = get_option('lms_streams', []);
    $default_mode = get_option('lms_default_mode', 'dark');
    ?>
    <div class="wrap">
        <h1>🎥 Live Multi Streams Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('lms_settings_group'); ?>
            <?php do_settings_sections('lms_settings_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Number of Streams</th>
                    <td>
                        <select name="lms_stream_count" onchange="this.form.submit()">
                            <?php
                            $options = [4,6,8,10,12];
                            foreach ($options as $opt) {
                                echo '<option value="'.$opt.'" '.selected($opt, $count, false).'>'.$opt.'</option>';
                            }
                            ?>
                        </select>
                        <p class="description">Change count and click Save Settings to update the fields below.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Default Theme</th>
                    <td>
                        <select name="lms_default_mode">
                            <option value="dark" <?php selected($default_mode, 'dark'); ?>>Dark</option>
                            <option value="light" <?php selected($default_mode, 'light'); ?>>Light</option>
                        </select>
                        <p class="description">Which theme should the videowall use by default?</p>
                    </td>
                </tr>
            </table>

            <h2>Stream URLs & Titles</h2>
            <table class="form-table">
                <?php
                for ($i = 1; $i <= $count; $i++) {
                    $url = isset($streams[$i]['url']) ? esc_attr($streams[$i]['url']) : '';
                    $title = isset($streams[$i]['title']) ? esc_attr($streams[$i]['title']) : '';
                    ?>
                    <tr>
                        <th scope="row" style="width: 160px;">Stream <?php echo $i; ?></th>
                        <td>
                            <input type="text" name="lms_streams[<?php echo $i; ?>][url]" value="<?php echo $url; ?>" style="width: 70%;" placeholder="Video/stream URL (mp4, HLS, or embed URL)" />
                            <br>
                            <input type="text" name="lms_streams[<?php echo $i; ?>][title]" value="<?php echo $title; ?>" style="width: 70%; margin-top:6px;" placeholder="Title (optional)" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <hr>
        <p>Place the shortcode <code>[live_multi_streams]</code> on any page to show the videowall.</p>
    </div>
    <?php
}

function lms_display_videowall() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/videowall.php';
    return ob_get_clean();
}
