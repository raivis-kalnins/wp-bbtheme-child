(function () {
  'use strict';

  function ready(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback);
      return;
    }
    callback();
  }

  ready(function () {
    var config = window.WPThemeCacheToolbar || {};
    var labels = config.labels || {};
    var toolbarItem = document.getElementById('wp-admin-bar-wp-theme-cache');
    var toolbarLink = toolbarItem ? toolbarItem.querySelector('.ab-item') : null;
    var toolbarLabel = toolbarItem ? toolbarItem.querySelector('.wp-theme-cache-toolbar-label') : null;
    var toolbarIcon = toolbarItem ? toolbarItem.querySelector('.wp-theme-cache-toolbar-icon') : null;
    var pageButtons = Array.prototype.slice.call(document.querySelectorAll('[data-wp-theme-clean-cache]'));
    var inFlight = false;
    var resetTimer = null;

    if (!toolbarLink && !pageButtons.length) {
      return;
    }

    if (!window.fetch || !window.URLSearchParams) {
      return;
    }

    function setIcon(icon, state) {
      if (!icon) return;
      icon.classList.remove('dashicons-update', 'dashicons-yes-alt', 'dashicons-warning');
      if (state === 'success') icon.classList.add('dashicons-yes-alt');
      else if (state === 'error') icon.classList.add('dashicons-warning');
      else icon.classList.add('dashicons-update');
    }

    function setState(state, message) {
      if (toolbarItem) {
        toolbarItem.classList.remove('is-cleaning', 'is-clean', 'is-error');
        if (state === 'loading') toolbarItem.classList.add('is-cleaning');
        if (state === 'success') toolbarItem.classList.add('is-clean');
        if (state === 'error') toolbarItem.classList.add('is-error');
      }

      if (toolbarLink) {
        toolbarLink.setAttribute('aria-busy', state === 'loading' ? 'true' : 'false');
        toolbarLink.setAttribute('aria-label', message || labels.toolbarDefault || 'Theme Cache');
      }
      if (toolbarLabel) {
        toolbarLabel.textContent = message || labels.toolbarDefault || 'Theme Cache';
      }
      setIcon(toolbarIcon, state);

      pageButtons.forEach(function (button) {
        var buttonLabel = button.querySelector('.wp-theme-cache-button-label');
        var buttonIcon = button.querySelector('.wp-theme-cache-button-icon');
        button.classList.remove('is-cleaning', 'is-clean', 'is-error');
        if (state === 'loading') button.classList.add('is-cleaning');
        if (state === 'success') button.classList.add('is-clean');
        if (state === 'error') button.classList.add('is-error');
        button.setAttribute('aria-busy', state === 'loading' ? 'true' : 'false');
        if (buttonLabel) {
          buttonLabel.textContent = message || labels.buttonDefault || 'Clean Cache';
        }
        setIcon(buttonIcon, state);
      });
    }

    function resetState() {
      if (toolbarItem) toolbarItem.classList.remove('is-cleaning', 'is-clean', 'is-error');
      if (toolbarLink) {
        toolbarLink.setAttribute('aria-busy', 'false');
        toolbarLink.setAttribute('aria-label', labels.toolbarDefault || 'Theme Cache');
      }
      if (toolbarLabel) toolbarLabel.textContent = labels.toolbarDefault || 'Theme Cache';
      setIcon(toolbarIcon, 'default');

      pageButtons.forEach(function (button) {
        var buttonLabel = button.querySelector('.wp-theme-cache-button-label');
        var buttonIcon = button.querySelector('.wp-theme-cache-button-icon');
        button.classList.remove('is-cleaning', 'is-clean', 'is-error');
        button.setAttribute('aria-busy', 'false');
        if (buttonLabel) buttonLabel.textContent = labels.buttonDefault || 'Clean Cache';
        setIcon(buttonIcon, 'default');
      });
    }

    function cleanCache(event) {
      if (event) event.preventDefault();
      if (inFlight || !config.ajaxUrl || !config.nonce) return;

      if (resetTimer) {
        window.clearTimeout(resetTimer);
        resetTimer = null;
      }

      inFlight = true;
      setState('loading', labels.cleaning || 'Cleaning Theme Cache…');

      var body = new URLSearchParams();
      body.append('action', 'wp_theme_purge_cache_ajax');
      body.append('nonce', config.nonce);

      fetch(config.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body.toString()
      })
        .then(function (response) {
          return response.json().then(function (payload) {
            if (!response.ok || !payload.success) {
              var errorMessage = payload && payload.data && payload.data.message;
              throw new Error(errorMessage || labels.failed || 'Cache Clean Failed');
            }
            return payload;
          });
        })
        .then(function (payload) {
          var successMessage = payload && payload.data && payload.data.message;
          setState('success', successMessage || labels.clean || 'Theme Cache Clean');
        })
        .catch(function (error) {
          setState('error', error.message || labels.failed || 'Cache Clean Failed');
          resetTimer = window.setTimeout(function () {
            resetState();
            resetTimer = null;
          }, 3000);
        })
        .finally(function () {
          inFlight = false;
        });
    }

    if (toolbarLink) toolbarLink.addEventListener('click', cleanCache);
    pageButtons.forEach(function (button) {
      button.addEventListener('click', cleanCache);
    });
  });
}());
