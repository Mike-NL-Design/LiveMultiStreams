/* lms.js — unified controls, chat, mute, grid, theme, per-stream + global chat */
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("lms-container");
  const wall = document.getElementById("lms-videowall");
  const toggleGridBtn = document.getElementById("lms-toggle-grid");
  const muteAllBtn = document.getElementById("lms-mute-all");
  const toggleThemeBtn = document.getElementById("lms-toggle-theme");
  const toggleChatGlobalBtn = document.getElementById("lms-toggle-chat-global");
  const hideFooterBtn = document.getElementById("lms-hide-footer");
  const supportLink = document.querySelector(".lms-support-link");

  // initial theme from container class (server-side)
  // persist user override in localStorage
  const savedTheme = localStorage.getItem('lms-theme');
  if (savedTheme) {
    container.classList.remove('lms-dark','lms-light');
    container.classList.add(savedTheme === 'light' ? 'lms-light' : 'lms-dark');
  }

  // ===== Toggle Theme =====
  toggleThemeBtn?.addEventListener('click', () => {
    if (container.classList.contains('lms-dark')) {
      container.classList.remove('lms-dark'); container.classList.add('lms-light');
      localStorage.setItem('lms-theme','light');
    } else {
      container.classList.remove('lms-light'); container.classList.add('lms-dark');
      localStorage.setItem('lms-theme','dark');
    }
    // ensure support link visible contrast
    adjustSupportContrast();
  });

  function adjustSupportContrast() {
    if (!supportLink) return;
    if (container.classList.contains('lms-dark')) {
      supportLink.style.color = '#fff';
    } else {
      supportLink.style.color = '#111';
    }
  }
  adjustSupportContrast();

  // ===== Toggle Grid =====
  toggleGridBtn?.addEventListener('click', () => {
    wall.classList.toggle('grid-compact');
  });

  // ===== Mute / Unmute All =====
  let muted = true; // default to muted for autoplay safety
  muteAllBtn?.addEventListener('click', () => {
    const vids = wall.querySelectorAll('video');
    muted = !muted;
    vids.forEach(v => { v.muted = muted; });
    // Try to command iframe players if possible (YouTube/Twitch). best-effort.
    const iframes = wall.querySelectorAll('iframe');
    iframes.forEach(ifr => {
      try {
        const src = ifr.src || '';
        if (src.includes('youtube.com')) {
          // YouTube postMessage API call
          ifr.contentWindow.postMessage('{"event":"command","func":"'+(muted ? 'mute' : 'unMute')+'","args":""}', '*');
        } else if (src.includes('twitch.tv')) {
          // Twitch embed supports postMessage for chat/player commands in some modes — best-effort
          // mute/unmute via player API is not standardized for iframe embeds - skip if not supported
        }
      } catch(e) {
        // ignore cross-origin issues
      }
    });
    muteAllBtn.textContent = muted ? 'Unmute All' : 'Mute All';
  });

  // ===== Hide Footer =====
  hideFooterBtn?.addEventListener('click', () => {
    const f = document.querySelector('.lms-footer');
    if (!f) return;
    f.style.display = (f.style.display === 'none') ? '' : 'none';
  });

  // ===== Per-stream Chat toggles (delegated) =====
  wall.addEventListener('click', function(e) {
    const btn = e.target.closest('.lms-chat-toggle');
    if (!btn) return;
    const videoTile = btn.closest('.lms-video');
    if (!videoTile) return;
    const chatDiv = videoTile.querySelector('.lms-chat');
    if (!chatDiv) return;
    const provider = chatDiv.getAttribute('data-provider');
    const channel = chatDiv.getAttribute('data-channel');
    const videoid = chatDiv.getAttribute('data-videoid');
    const host = window.location.hostname;

    if (chatDiv.style.display === 'block') {
      chatDiv.style.display = 'none';
      chatDiv.setAttribute('aria-hidden','true');
      return;
    }

    // build chat iframe only if not present
    if (!chatDiv.querySelector('iframe')) {
      if (provider === 'twitch' && channel) {
        const src = 'https://www.twitch.tv/embed/' + encodeURIComponent(channel) + '/chat?parent=' + encodeURIComponent(host);
        const ifr = document.createElement('iframe');
        ifr.src = src; ifr.width = '100%'; ifr.height = '320'; ifr.frameBorder = '0'; ifr.className = 'lms-chat-iframe'; ifr.loading = 'lazy';
        chatDiv.innerHTML = ''; chatDiv.appendChild(ifr);
      } else if (provider === 'youtube' && videoid) {
        const src = 'https://www.youtube.com/live_chat?v=' + encodeURIComponent(videoid) + '&embed_domain=' + encodeURIComponent(host);
        const ifr = document.createElement('iframe');
        ifr.src = src; ifr.width = '100%'; ifr.height = '320'; ifr.frameBorder = '0'; ifr.className = 'lms-chat-iframe'; ifr.loading = 'lazy';
        chatDiv.innerHTML = ''; chatDiv.appendChild(ifr);
      } else {
        chatDiv.innerHTML = '<div class="lms-error">No chat available for this stream</div>';
      }
    }

    chatDiv.style.display = 'block';
    chatDiv.setAttribute('aria-hidden','false');
  });

  // ===== Global Chat Toggle =====
  toggleChatGlobalBtn?.addEventListener('click', function() {
    const chats = wall.querySelectorAll('.lms-chat');
    let anyOpen = false;
    chats.forEach(c => { if (c.style.display === 'block') anyOpen = true; });

    chats.forEach(c => {
      if (anyOpen) {
        c.style.display = 'none';
        c.setAttribute('aria-hidden','true');
      } else {
        // if no iframe yet, build minimal; but to avoid too many loads we let per-stream build on show
        if (!c.querySelector('iframe')) {
          // if attributes indicate provider, build now
          const provider = c.getAttribute('data-provider');
          const channel = c.getAttribute('data-channel');
          const videoid = c.getAttribute('data-videoid');
          const host = window.location.hostname;
          if (provider === 'twitch' && channel) {
            const src = 'https://www.twitch.tv/embed/' + encodeURIComponent(channel) + '/chat?parent=' + encodeURIComponent(host);
            const ifr = document.createElement('iframe');
            ifr.src = src; ifr.width = '100%'; ifr.height = '320'; ifr.frameBorder = '0'; ifr.className = 'lms-chat-iframe'; ifr.loading = 'lazy';
            c.innerHTML = ''; c.appendChild(ifr);
          } else if (provider === 'youtube' && videoid) {
            const src = 'https://www.youtube.com/live_chat?v=' + encodeURIComponent(videoid) + '&embed_domain=' + encodeURIComponent(host);
            const ifr = document.createElement('iframe');
            ifr.src = src; ifr.width = '100%'; ifr.height = '320'; ifr.frameBorder = '0'; ifr.className = 'lms-chat-iframe'; ifr.loading = 'lazy';
            c.innerHTML = ''; c.appendChild(ifr);
          } else {
            c.innerHTML = '<div class="lms-error">No chat available for this stream</div>';
          }
        }
        c.style.display = 'block';
        c.setAttribute('aria-hidden','false');
      }
    });
  });

  // Responsive: adjust chat iframe heights on resize
  function adjustChatHeights() {
    const iframes = wall.querySelectorAll('.lms-chat-iframe');
    iframes.forEach(ifr => {
      if (window.innerWidth < 600) ifr.style.height = '220px';
      else ifr.style.height = '320px';
    });
  }
  window.addEventListener('resize', adjustChatHeights);
  adjustChatHeights();

});
