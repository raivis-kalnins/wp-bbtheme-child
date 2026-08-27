(function(){
  function applyTheme(mode){
    var dark = mode === 'dark';
    document.documentElement.classList.toggle('is-dark-theme', dark);
    document.body && document.body.classList.toggle('is-dark-theme', dark);
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    document.querySelectorAll('[data-wp-theme-toggle]').forEach(function(btn){
      btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
    });
  }

  function getSavedTheme(){
    try { return localStorage.getItem('wpThemeMode'); } catch(e) { return null; }
  }

  function saveTheme(mode){
    try { localStorage.setItem('wpThemeMode', mode); } catch(e) {}
  }

  function bindThemeToggle(){
    document.querySelectorAll('[data-wp-theme-toggle]').forEach(function(btn){
      btn.addEventListener('click', function(){
        var next = document.documentElement.classList.contains('is-dark-theme') ? 'light' : 'dark';
        saveTheme(next);
        applyTheme(next);
        window.dispatchEvent(new CustomEvent('wp-theme:mode-change', {detail:{mode:next}}));
      });
    });
  }

  function bindHeaderMenus(){
    var header = document.querySelector('.wp-theme-site-header');
    var nav = document.getElementById('wp-theme-primary-navigation');
    var button = document.querySelector('.wp-theme-demo-hamburger');
    if (!header || !nav || !button) return;

    function setOpen(open){
      header.classList.toggle('is-menu-open', open);
      document.body.classList.toggle('menu-open', open);
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    button.addEventListener('click', function(){
      setOpen(button.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape') {
        setOpen(false);
        button.focus();
      }
    });

    document.addEventListener('click', function(e){
      if (window.innerWidth <= 991 && !header.contains(e.target)) setOpen(false);
    });

    nav.querySelectorAll('.menu-item-has-children').forEach(function(item){
      var link = item.querySelector(':scope > a');
      if (!link) return;
      link.setAttribute('aria-haspopup', 'true');
      link.addEventListener('click', function(e){
        if (window.innerWidth > 991 || item.classList.contains('is-open')) return;
        e.preventDefault();
        item.classList.add('is-open');
        link.setAttribute('aria-expanded', 'true');
      });
    });

    window.addEventListener('resize', function(){
      if (window.innerWidth > 991) setOpen(false);
    }, {passive:true});
  }

  function bindStickyHeader(){
    var header = document.querySelector('.wp-theme-site-header');
    if (!header) return;
    var update = function(){
      header.classList.toggle('is-scrolled', window.scrollY > 12);
    };
    update();
    window.addEventListener('scroll', update, {passive:true});
  }

  function init(){
    var saved = getSavedTheme();
    if (saved) applyTheme(saved);
    bindThemeToggle();
    bindHeaderMenus();
    bindStickyHeader();
    document.querySelectorAll('.btn-back-home').forEach(function (el) {
      el.setAttribute('href', window.wpThemeHome || '/');
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
