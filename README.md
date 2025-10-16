# LiveMultiStreams
LiveMultiStreams v1.3

Currently, your Live Multi Streams plugin supports embedding and playback of streams that are direct video sources (like .mp4, .m3u8, or HLS/HTTP Live Streams) and some embeddable platforms.
Here’s what works and what doesn’t:

✅ WORKS (natively supported):

Direct MP4 URLs — e.g.
https://www.example.com/videos/sample.mp4

HLS/M3U8 streams — e.g.
https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8

YouTube embed URLs —
https://www.youtube.com/embed/VIDEO_ID

Vimeo embed URLs —
https://player.vimeo.com/video/VIDEO_ID

PeerTube / Custom HLS servers — as long as they provide .m3u8 or .mp4

⚠️ LIMITED or DOESN’T WORK:

Twitch links like https://www.twitch.tv/pi6zdmlive
→ These aren’t direct video URLs. Twitch blocks embeds unless you use their official player iframe with a valid parent parameter and HTTPS site.

Facebook / Instagram Live — blocked by permissions / CORS.

TikTok / other app-based streams — not embeddable without their SDKs.
