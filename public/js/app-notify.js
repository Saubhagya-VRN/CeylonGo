/**
 * Ceylon Go — toast notifications + window.alert shim.
 * Loads CSS from the same directory as this script.
 */
(function () {
  'use strict';
  if (window.__ceylonAppNotify) {
    return;
  }
  window.__ceylonAppNotify = true;

  var SVG = {
    info:
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    success:
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    error:
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/></svg>',
    warning:
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
  };

  function scriptBase() {
    var scripts = document.getElementsByTagName('script');
    var i = scripts.length;
    while (i--) {
      var s = scripts[i].src || '';
      if (s.indexOf('app-notify.js') !== -1) {
        return s.replace(/\/js\/app-notify\.js(\?.*)?$/i, '');
      }
    }
    return '/CeylonGo/public';
  }

  function ensureCss() {
    var base = scriptBase();
    var href = base + '/css/app-notify.css';
    if (document.querySelector('link[data-app-notify-css="1"]')) {
      return;
    }
    var l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = href;
    l.setAttribute('data-app-notify-css', '1');
    (document.head || document.documentElement).appendChild(l);
  }

  function inferType(message) {
    var t = String(message).toLowerCase();
    if (t.indexOf('✅') !== -1 || /\bsuccess\b|\bsaved\b|\bcomplete\b/.test(t)) {
      return 'success';
    }
    if (t.indexOf('❌') !== -1 || /\bfail|\berror\b|\binvalid\b|\bcannot\b|\bunable\b/.test(t)) {
      return 'error';
    }
    if (/\bwarning\b|\bcaution\b/.test(t)) {
      return 'warning';
    }
    if (
      /\bsubmit\s+your\s+trip\s+first\b/.test(t) ||
      /\bplease\s+submit\s+your\s+trip\b/.test(t) ||
      /\bsubmit\s+trip\s+button\b/.test(t)
    ) {
      return 'warning';
    }
    return 'info';
  }

  function ensureHost() {
    var host = document.getElementById('app-notify-host');
    if (!host) {
      host = document.createElement('div');
      host.id = 'app-notify-host';
      host.className = 'app-notify-host';
      host.setAttribute('aria-live', 'polite');
      (document.body || document.documentElement).appendChild(host);
    }
    return host;
  }

  function dismiss(el) {
    if (!el || !el.parentNode) {
      return;
    }
    el.classList.remove('app-notify--visible');
    setTimeout(function () {
      if (el.parentNode) {
        el.parentNode.removeChild(el);
      }
    }, 320);
  }

  /**
   * @param {string} message
   * @param {{ type?: string, duration?: number }} [opts]
   */
  function notify(message, opts) {
    ensureCss();
    opts = opts || {};
    var type = opts.type || inferType(message);
    if (['info', 'success', 'error', 'warning'].indexOf(type) === -1) {
      type = 'info';
    }
    var duration = typeof opts.duration === 'number' ? opts.duration : 4800;

    var host = ensureHost();
    var el = document.createElement('div');
    el.className = 'app-notify app-notify--' + type;
    el.setAttribute('role', 'alert');

    var iconWrap = document.createElement('span');
    iconWrap.className = 'app-notify__icon';
    iconWrap.innerHTML = SVG[type] || SVG.info;

    var text = document.createElement('span');
    text.className = 'app-notify__text';
    text.textContent = String(message);

    el.appendChild(iconWrap);
    el.appendChild(text);

    var tid;
    function arm() {
      tid = setTimeout(function () {
        dismiss(el);
      }, duration);
    }
    function disarm() {
      if (tid) {
        clearTimeout(tid);
        tid = null;
      }
    }

    el.addEventListener('click', function () {
      disarm();
      dismiss(el);
    });
    el.addEventListener('mouseenter', disarm);
    el.addEventListener('mouseleave', arm);

    host.appendChild(el);
    requestAnimationFrame(function () {
      el.classList.add('app-notify--visible');
    });
    arm();
  }

  window.notify = notify;

  var _alert = window.alert;
  window.alert = function (message) {
    try {
      notify(String(message), { type: inferType(message) });
    } catch (e) {
      if (typeof _alert === 'function') {
        _alert.call(window, message);
      }
    }
  };
})();
