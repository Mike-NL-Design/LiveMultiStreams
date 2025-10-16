<?php
/**
 * Video Wall Template with chat under videos (works for Twitch & YouTube + native video)
  */
if (!defined('ABSPATH')) exit;

$stream_count = intval(get_option('lms_stream_count', 12));
$streams = get_option('lms_streams', []);
$default_mode = get_option('lms_default_mode', 'dark');
$container_class = ($default_mode === 'light') ? 'lms-light' : 'lms-dark';

// helpers
function lms_extract_youtube_id($url) {
    if (preg_match('~(?:v=|\/v\/|youtu\.be\/|\/embed\/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
        return $m[1];
    }
    return false;
}
function lms_extract_twitch_channel($url) {
    if (preg_match('~twitch\.tv\/(channels\/)?([A-Za-z0-9_]+)~i', $url, $m)) {
        return $m[2];
    }
    return false;
}

$host = esc_attr($_SERVER['HTTP_HOST']);
?>

<div id="lms-container" class="<?php echo esc_attr($container_class); ?>">

  <!-- Controls -->
  <div class="lms-controls-wrapper">
    <div class="lms-controls">
      <button id="lms-toggle-grid">Toggle Grid</button>
      <button id="lms-mute-all">Mute All</button>
      <button id="lms-toggle-theme">Toggle Theme</button>
      <button id="lms-toggle-chat-global">Toggle Chat</button>
      <button id="lms-hide-footer">Hide Footer</button>
    </div>
  </div>

  <!-- Video wall -->
  <div id="lms-videowall">
    <?php for ($i = 1; $i <= $stream_count; $i++):
        $raw = $streams[$i]['url'] ?? '';
        $title = $streams[$i]['title'] ?? 'Demo Stream '.$i;
        $url = !empty($raw) ? $raw : 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4';
        $yt_id = lms_extract_youtube_id($url);
        $tw_channel = lms_extract_twitch_channel($url);
        if ($tw_channel) $provider = 'twitch';
        elseif ($yt_id) $provider = 'youtube';
        else $provider = 'video';

        // data attributes for chat
        $data = 'data-provider="'.esc_attr($provider).'" data-url="'.esc_attr($url).'"';
        if ($provider === 'twitch') $data .= ' data-channel="'.esc_attr($tw_channel).'"';
        if ($provider === 'youtube') $data .= ' data-videoid="'.esc_attr($yt_id).'"';
    ?>
      <div class="lms-video">
        <div class="lms-player-wrap">
        <?php
        if ($provider === 'twitch') {
            // Twitch player
            echo '<iframe class="lms-player-iframe" src="https://player.twitch.tv/?channel='.esc_attr($tw_channel).'&parent='.$host.'" frameborder="0" allowfullscreen="true" scrolling="no"></iframe>';
        } elseif ($provider === 'youtube') {
            echo '<iframe class="lms-player-iframe" src="https://www.youtube.com/embed/'.esc_attr($yt_id).'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
        } else {
            echo '<video class="lms-html-video" src="'.esc_url($url).'" controls playsinline preload="metadata"></video>';
        }
        ?>
        </div>

        <div class="lms-caption-row">
          <div class="lms-caption"><?php echo esc_html($title); ?></div>
          <div class="lms-per-controls">
            <button class="lms-chat-toggle" <?php echo $data; ?>>Chat</button>
          </div>
        </div>

        <div class="lms-chat" <?php echo $data; ?> style="display:none" aria-hidden="true"></div>
      </div>
    <?php endfor; ?>
  </div>

  <div class="lms-footer">
    <a class="lms-support-link" href="https://buymeacoffee.com/nlbiertjenl" target="_blank" rel="noopener noreferrer">☕ Support the developer – Buy me a coffee</a>
  </div>

</div>
