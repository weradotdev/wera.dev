// Wera Chat Embed Script

// To configure, set window.WERA_CHAT_EMBED_CONFIG before loading the script, e.g.:
// <script>window.WERA_CHAT_EMBED_CONFIG = { iframeUrl: 'https://yourdomain.com/pages/⚡ticket', width: 420, height: 640, position: 'bottom-left' };</script>
// <script src="/embed-chat.js"></script>

(function () {
  // Configurable defaults
  var defaults = {
    iframeUrl: '/pages/ticket',
    position: 'bottom-right', // 'bottom-right', 'bottom-left', 'top-right', 'top-left'
    width: 400,
    height: 600,
    buttonSize: 28,
    zIndex: 9999,
    buttonBackground: 'transparent',
    borderRadius: '16px',
    boxShadow: '0 4px 24px rgba(0,0,0,0.18)',
    iconColor: '#2d89ef',
  };
  
  var config = Object.assign({}, defaults, window.WERA_CHAT_EMBED_CONFIG || {});

  // Position mapping
  var pos = {
    'bottom-right': { bottom: '10px', right: '10px' },
    'bottom-left': { bottom: '10px', left: '10px' },
    'top-right': { top: '10px', right: '10px' },
    'top-left': { top: '10px', left: '10px' },
  };

  // Create button
  var btn = document.createElement('div');
  btn.style.cssText = [
    'position:fixed',
    'cursor:pointer',
    'width:' + config.buttonSize + 'px',
    'height:' + config.buttonSize + 'px',
    'border-radius:50%',
    'box-shadow:' + config.boxShadow,
    'display:flex',
    'align-items:center',
    'justify-content:center',
    'z-index:' + config.zIndex,
    'transition:transform 0.15s ease,box-shadow 0.15s ease',
  ].join(';');
  
  Object.assign(btn.style, pos[config.position] || pos['bottom-right']);
  btn.onmouseenter = function() { btn.style.transform = 'scale(1.08)'; };
  btn.onmouseleave = function() { btn.style.transform = 'scale(1)'; };

  // Lifebuoy SVG icon
  btn.innerHTML = '<svg width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\" stroke=\"' + config.iconColor + '\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288\"/></svg>';

  // Create iframe overlay
  var overlay = document.createElement('div');
  overlay.style.position = 'fixed';
  overlay.style.zIndex = config.zIndex + 1;
  overlay.style.borderRadius = config.borderRadius;
  overlay.style.boxShadow = config.boxShadow;
  overlay.style.overflow = 'hidden';
  overlay.style.display = 'none';
  overlay.style.width = config.width + 'px';
  overlay.style.height = config.height + 'px';
  Object.assign(overlay.style, pos[config.position] || pos['bottom-right']);

  // Close button
  var closeBtn = document.createElement('div');
  closeBtn.style.position = 'absolute';
  closeBtn.style.top = '8px';
  closeBtn.style.right = '8px';
  closeBtn.style.width = closeBtn.style.height = '32px';
  closeBtn.style.display = 'flex';
  closeBtn.style.alignItems = 'center';
  closeBtn.style.justifyContent = 'center';
  closeBtn.style.cursor = 'pointer';
  closeBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6L14 14M14 6L6 14" stroke="#888" stroke-width="2" stroke-linecap="round"/></svg>';

  // Iframe
  var iframe = document.createElement('iframe');
  iframe.src = config.iframeUrl;
  iframe.style.width = '100%';
  iframe.style.height = '100%';
  iframe.style.border = 'none';
  iframe.style.background = 'transparent';

  overlay.appendChild(closeBtn);
  overlay.appendChild(iframe);

  // Show overlay
  btn.onclick = function () {
    overlay.style.display = 'block';
    btn.style.display = 'none';
  };
  // Hide overlay
  closeBtn.onclick = function () {
    overlay.style.display = 'none';
    btn.style.display = 'flex';
  };

  // Add to DOM
  document.body.appendChild(btn);
  document.body.appendChild(overlay);
})();
