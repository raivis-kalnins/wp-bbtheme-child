(function (W, D) {
  'use strict';

  var cfg = W.wpbbSuiteV128 || {};
  var slug = String(cfg.themeSlug || '').toLowerCase();
  var heroUrls = Array.isArray(cfg.heroUrls) ? cfg.heroUrls.filter(Boolean) : [];
  var raf = W.requestAnimationFrame || function (fn) { return W.setTimeout(fn, 16); };

  function qa(sel, root) {
    try { return Array.prototype.slice.call((root || D).querySelectorAll(sel)); }
    catch (e) { return []; }
  }

  function q(sel, root) {
    try { return (root || D).querySelector(sel); }
    catch (e) { return null; }
  }

  function text(el) {
    return String(el && el.textContent || '').replace(/\s+/g, ' ').trim();
  }

  function norm(s) {
    return String(s || '').toLowerCase().replace(/[\u2013\u2014]/g, '-').replace(/[^a-z0-9]+/g, ' ').replace(/\s+/g, ' ').trim();
  }

  function directChildren(el) {
    return el ? Array.prototype.slice.call(el.children || []) : [];
  }

  function directCells(row) {
    return directChildren(row).filter(function (el) {
      return el.nodeType === 1 && (
        /(^|\s)col(?:-|\s|$)/.test(el.className || '') ||
        el.matches('.wpbb-col,.wpbb-column,.wpbb-grid__item,.wpbb-card-cell,.wp-theme-grid__item')
      );
    });
  }

  function commonParent(items) {
    if (!items.length) return null;
    var parent = items[0].parentElement;
    if (!parent) return null;
    return items.every(function (el) { return el.parentElement === parent; }) ? parent : null;
  }

  function themeClass() {
    if (!D.body) return;
    if (slug.indexOf('automotive') !== -1) D.body.classList.add('wpbb-v128-theme-automotive');
    if (slug.indexOf('business') !== -1 && slug.indexOf('building') === -1) D.body.classList.add('wpbb-v128-theme-business');
    if (slug.indexOf('building') !== -1) D.body.classList.add('wpbb-v128-theme-building');
  }

  /* ---------------------------------------------------------------------- */
  /* Process / delivery model recovery                                      */
  /* ---------------------------------------------------------------------- */
  function findCard(cell) {
    if (!cell) return null;
    return q('.wp-theme-process-card,.wp-theme-sector-card,.wpbb-icon-card,.wpbb-card,.card', cell) || cell.firstElementChild || cell;
  }

  function processTitle(cell) {
    var h = q('.wpbb-icon-card__title,.card-title,h2,h3,h4,h5,strong', cell);
    return norm(text(h || cell));
  }

  function looksLikeProcessRow(row, cells) {
    if (!row || cells.length !== 3) return false;
    if (row.closest('.wp-theme-process-section,.wp-theme-sector-process-grid')) return true;

    var titles = cells.map(processTitle);
    if (titles[0].indexOf('discover') !== -1 && titles[1].indexOf('design') !== -1 && titles[2].indexOf('deliver') !== -1) return true;

    var stepTexts = cells.map(function (c) { return norm(text(c)).slice(0, 90); });
    var sequential = /(^| )0?1( |$)/.test(stepTexts[0]) && /(^| )0?2( |$)/.test(stepTexts[1]) && /(^| )0?3( |$)/.test(stepTexts[2]);
    if (sequential) return true;

    var section = row.closest('section,.wpbb-bootstrap-div,.wpbb-section,.wp-theme-section-shell,.wpbb-v67-section-shell') || row.parentElement;
    var sectionText = norm(text(section)).slice(0, 500);
    return sectionText.indexOf('delivery model') !== -1 || sectionText.indexOf('simple process from first question') !== -1;
  }

  function markProcessRow(row, cells) {
    row.classList.add('wpbb-v128-process-grid');
    var section = row.closest('section,.wpbb-bootstrap-div,.wpbb-section,.wp-theme-section-shell,.wpbb-v67-section-shell') || row.parentElement;
    if (section) section.classList.add('wpbb-v128-process-section');

    cells.forEach(function (cell) {
      cell.classList.add('wpbb-v128-process-cell');
      var card = findCard(cell);
      if (card) card.classList.add('wpbb-v128-process-card');
    });
  }

  function processRepair() {
    var candidates = qa('.wp-theme-sector-process-grid,.wp-theme-process-section .row,.wp-theme-process-section .wpbb-row,.row,.wpbb-row');
    candidates.forEach(function (row) {
      var cells = directCells(row);
      if (looksLikeProcessRow(row, cells)) markProcessRow(row, cells);
    });

    // Some saved demo markup has the three cards directly inside the process
    // container with no Bootstrap row wrapper.
    qa('.wp-theme-sector-process-grid').forEach(function (grid) {
      var cells = directChildren(grid).filter(function (el) { return el.nodeType === 1; });
      if (cells.length === 3) markProcessRow(grid, cells);
    });
  }

  /* ---------------------------------------------------------------------- */
  /* Automotive stale proof/demo card recovery                              */
  /* ---------------------------------------------------------------------- */
  var AUTO_PROOF = [
    ['Vehicle catalogue', 'New, used and rental stock share one fast finder with practical filters.'],
    ['WooCommerce parts', 'Parts use the same cart, checkout, order and account journey as the rest of the site.'],
    ['Service-ready content', 'Workshop services and contact journeys stay clear, useful and easy to maintain.']
  ];

  function replaceCardCopy(card, idx) {
    if (!card || !AUTO_PROOF[idx]) return;
    var title = q('.wpbb-icon-card__title,.card-title,h2,h3,h4,h5,strong', card);
    var body = q('.wpbb-icon-card__text,.card-text,p', card);
    var wasPlaceholder = !!(title && norm(text(title)) === 'card title');
    if (wasPlaceholder) title.textContent = AUTO_PROOF[idx][0];
    if (body && wasPlaceholder) body.textContent = AUTO_PROOF[idx][1];
  }

  function automotiveProofRepair() {
    if (!D.body || !D.body.classList.contains('wpbb-v128-theme-automotive')) return;

    var placeholders = qa('h1,h2,h3,h4,h5,h6,.card-title,.wpbb-icon-card__title,strong').filter(function (el) {
      return norm(text(el)) === 'card title';
    });
    var cards = [];
    placeholders.forEach(function (title) {
      var card = title.closest('.wpbb-icon-card,.wp-theme-sector-card,.wpbb-card,.card,[class*="card"]') || title.parentElement;
      if (card && cards.indexOf(card) === -1) cards.push(card);
    });

    if (cards.length >= 3) {
      cards.slice(0, 3).forEach(function (card, idx) {
        card.classList.add('wpbb-v128-auto-proof-card');
        replaceCardCopy(card, idx);
        var cell = card.closest('[class*="col-"],.wpbb-col,.wpbb-column,.wpbb-grid__item') || card.parentElement;
        if (cell) cell.classList.add('wpbb-v128-auto-proof-cell');
      });
      var cells = cards.slice(0, 3).map(function (card) {
        return card.closest('[class*="col-"],.wpbb-col,.wpbb-column,.wpbb-grid__item') || card.parentElement;
      }).filter(Boolean);
      var row = commonParent(cells);
      if (row) row.classList.add('wpbb-v128-auto-proof-grid');
    }

    qa('.wpbb-sector-proof-band,.wp-theme-sector-proof,.wp-theme-proof-section').forEach(function (band) {
      var localCards = qa('.wpbb-sector-proof-card,.wp-theme-sector-card,.wpbb-icon-card,.card', band).slice(0, 3);
      if (localCards.length === 3) {
        var localCells = localCards.map(function (card) {
          card.classList.add('wpbb-v128-auto-proof-card');
          return card.closest('[class*="col-"],.wpbb-col,.wpbb-column,.wpbb-grid__item') || card.parentElement;
        }).filter(Boolean);
        localCells.forEach(function (cell) { cell.classList.add('wpbb-v128-auto-proof-cell'); });
        var localRow = commonParent(localCells);
        if (localRow) localRow.classList.add('wpbb-v128-auto-proof-grid');
      }
    });

    qa('h1,h2,h3,h4,h5,h6').forEach(function (el) {
      if (norm(text(el)).indexOf('new cars used stock rentals parts and service without separate websites') === 0) {
        el.classList.add('wpbb-v128-auto-dark-heading');
      }
    });
  }

  /* ---------------------------------------------------------------------- */
  /* Automotive home catalogue: mark only the true product-cell parent row  */
  /* ---------------------------------------------------------------------- */
  function automotiveHomeCatalogue() {
    if (!D.body || !D.body.classList.contains('wpbb-v128-theme-automotive')) return;
    qa('.wp-theme-home-product-catalogue').forEach(function (section) {
      var cards = qa('.wpbb-catalogue-card,.product.type-product,.product-card', section);
      var groups = new Map();
      cards.forEach(function (card) {
        var cell = card.closest('[class*="col-"],.wpbb-col,.wpbb-column,.wpbb-grid__item') || card.parentElement;
        if (!cell || !cell.parentElement) return;
        var row = cell.parentElement;
        if (!groups.has(row)) groups.set(row, []);
        groups.get(row).push({ cell: cell, card: card });
      });
      groups.forEach(function (items, row) {
        if (items.length < 2) return;
        row.classList.add('wpbb-v128-home-catalogue-grid');
        items.forEach(function (item) {
          item.cell.classList.add('wpbb-v128-home-catalogue-cell');
          item.card.classList.add('wpbb-v128-home-catalogue-card');
        });
      });
    });
  }

  /* ---------------------------------------------------------------------- */
  /* WooCommerce archive / product / cart / checkout ownership              */
  /* ---------------------------------------------------------------------- */
  function markShopGrid() {
    var roots = qa('.wp-theme-woo-legacy--catalog,.woocommerce-shop,.post-type-archive-product');
    roots.forEach(function (root) {
      qa('ul.products,.products,.iws-products-grid,.iws-filter-products,.iws-product-filter-results').forEach(function (candidate) {
        var products = directChildren(candidate).filter(function (el) {
          return el.matches('li.product,.product,.type-product,.iws-product-card,[class*="product-card"]');
        });
        if (products.length >= 2) {
          candidate.classList.add('wpbb-v128-shop-grid');
          products.forEach(function (product) { product.classList.add('wpbb-v128-shop-product'); });
        }
      });
    });
  }

  function markProductPage() {
    qa('.wp-theme-woo-legacy--product .wpbb-complete-product,.single-product .wpbb-complete-product').forEach(function (product) {
      product.classList.add('wpbb-v128-product-page');
      var main = q('.wpbb-complete-product__main', product);
      if (main) main.classList.add('wpbb-v128-product-main');
    });
  }

  function markCart() {
    qa('.wp-theme-woo-legacy--cart .woocommerce,.woocommerce-cart main .woocommerce,#wp-theme-main .woocommerce').forEach(function (wrap) {
      var form = q('.woocommerce-cart-form', wrap);
      var totals = q('.cart-collaterals', wrap);
      if (!form || !totals) return;
      wrap.classList.add('wpbb-v128-cart-grid');
      form.classList.add('wpbb-v128-cart-form');
      totals.classList.add('wpbb-v128-cart-totals');
    });
  }

  function replaceNewsletterLabel(form) {
    qa('label', form).forEach(function (label) {
      var n = norm(text(label));
      if (n.indexOf('wordpress newsletter updates') === -1 && n.indexOf('newsletter updates by email') === -1) return;
      label.classList.add('wpbb-v128-newsletter-consent');
      var walker = D.createTreeWalker(label, W.NodeFilter ? W.NodeFilter.SHOW_TEXT : 4, null);
      var nodes = [], node;
      while ((node = walker.nextNode())) nodes.push(node);
      nodes.forEach(function (txt) {
        if (norm(txt.nodeValue).indexOf('newsletter') !== -1 || norm(txt.nodeValue).indexOf('wordpress') !== -1) {
          txt.nodeValue = ' I agree to receive occasional email updates and can unsubscribe at any time.';
        }
      });
    });
  }

  function markCheckout() {
    qa('form.woocommerce-checkout,form.checkout.woocommerce-checkout').forEach(function (form) {
      form.classList.add('wpbb-v128-checkout-grid');
      replaceNewsletterLabel(form);
    });
  }

  function commerceRepair() {
    markShopGrid();
    markProductPage();
    markCart();
    markCheckout();
  }

  /* ---------------------------------------------------------------------- */
  /* Hero source, fade and stable 3-dot pager                               */
  /* ---------------------------------------------------------------------- */
  function forceImage(img, url) {
    if (!img || !url) return;
    if (img.getAttribute('src') !== url) img.setAttribute('src', url);
    img.removeAttribute('srcset');
    img.removeAttribute('sizes');
    img.setAttribute('loading', 'eager');
    img.setAttribute('fetchpriority', 'high');
  }

  function heroBlocks() {
    var blocks = qa('.wpbb-swiper--hero,.wp-theme-hero .swiper,.wp-theme-hero-slider,.wp-theme-sector-hero .swiper');
    return blocks.filter(function (block, idx) { return blocks.indexOf(block) === idx; });
  }

  function retireOldPagers(scope) {
    qa('.wpbb-v123-hero-pagination,.wpbb-v124-hero-pagination,.wpbb-v126-hero-pagination,.wpbb-v127-hero-pagination,.swiper-pagination', scope).forEach(function (el) {
      if (!el.classList.contains('wpbb-v128-hero-pagination')) el.classList.add('wpbb-v128-retired-pager');
    });
    var parent = scope && scope.parentElement;
    if (parent) {
      qa(':scope > .wpbb-v123-hero-pagination,:scope > .wpbb-v124-hero-pagination,:scope > .wpbb-v126-hero-pagination,:scope > .wpbb-v127-hero-pagination,:scope > .swiper-pagination', parent).forEach(function (el) {
        el.classList.add('wpbb-v128-retired-pager');
      });
    }
  }

  function physicalSlides(block) {
    var slides = qa('.swiper-slide', block).filter(function (slide) {
      return !slide.classList.contains('swiper-slide-duplicate');
    });
    return slides;
  }

  function forceHeroSources(block) {
    if (!heroUrls.length) return;
    var slides = physicalSlides(block);
    slides.forEach(function (slide, idx) {
      var url = heroUrls[idx % heroUrls.length];
      var img = q('img', slide);
      if (img) forceImage(img, url);
      var media = q('.wpbb-swiper-slide__media,.wp-theme-hero__media,.wpbb-hero-media', slide);
      if (media) media.classList.add('wpbb-v128-hero-media');
      slide.classList.add('wpbb-v128-hero-slide');
    });
  }

  function activeIndex(block, count) {
    var sw = block && block.swiper;
    if (sw && typeof sw.realIndex === 'number') return ((sw.realIndex % count) + count) % count;
    var active = q('.swiper-slide-active', block);
    var slides = physicalSlides(block);
    var idx = active ? slides.indexOf(active) : 0;
    return idx < 0 ? 0 : (idx % count);
  }

  function buildHeroPager(block) {
    var count = Math.max(heroUrls.length, physicalSlides(block).length);
    if (count < 2) return;
    if (heroUrls.length >= 3) count = 3;

    retireOldPagers(block);
    var existing = q('.wpbb-v128-hero-pagination', block.parentElement || block);
    if (existing && Number(existing.getAttribute('data-count')) === count) return;
    if (existing) existing.remove();

    var pager = D.createElement('div');
    pager.className = 'wpbb-v128-hero-pagination';
    pager.setAttribute('data-count', String(count));
    pager.setAttribute('role', 'tablist');
    pager.setAttribute('aria-label', 'Hero slides');

    function paint() {
      var idx = activeIndex(block, count);
      qa('button', pager).forEach(function (btn, i) {
        var on = i === idx;
        btn.classList.toggle('is-active', on);
        btn.setAttribute('aria-selected', on ? 'true' : 'false');
      });
    }

    for (var i = 0; i < count; i++) {
      (function (index) {
        var btn = D.createElement('button');
        btn.type = 'button';
        btn.className = 'wpbb-v128-hero-bullet';
        btn.setAttribute('role', 'tab');
        btn.setAttribute('aria-label', 'Go to slide ' + (index + 1));
        btn.addEventListener('click', function () {
          var sw = block.swiper;
          if (sw) {
            if (typeof sw.slideToLoop === 'function') sw.slideToLoop(index);
            else if (typeof sw.slideTo === 'function') sw.slideTo(index);
          } else {
            var slides = physicalSlides(block);
            slides.forEach(function (slide, j) { slide.classList.toggle('swiper-slide-active', j === index); });
          }
          W.setTimeout(paint, 30);
        });
        pager.appendChild(btn);
      })(i);
    }

    var host = block.parentElement || block;
    host.classList.add('wpbb-v128-hero-host');
    host.appendChild(pager);
    paint();

    var sw = block.swiper;
    if (sw && typeof sw.on === 'function' && !block.__wpbbV128Bound) {
      block.__wpbbV128Bound = true;
      sw.on('slideChange', paint);
      sw.on('transitionEnd', paint);
    }
  }

  function heroRepair() {
    heroBlocks().forEach(function (block) {
      block.classList.add('wpbb-v128-hero');
      forceHeroSources(block);
      buildHeroPager(block);
    });
  }

  function run() {
    themeClass();
    processRepair();
    automotiveProofRepair();
    automotiveHomeCatalogue();
    commerceRepair();
    heroRepair();
  }

  var queued = false;
  function schedule() {
    if (queued) return;
    queued = true;
    raf(function () {
      queued = false;
      run();
    });
  }

  if (D.readyState === 'loading') D.addEventListener('DOMContentLoaded', schedule, { once: true });
  else schedule();

  W.addEventListener('load', function () {
    schedule();
    W.setTimeout(schedule, 250);
    W.setTimeout(schedule, 900);
  });

  if (W.MutationObserver) {
    var observer = new MutationObserver(function (mutations) {
      var relevant = mutations.some(function (m) { return m.addedNodes && m.addedNodes.length; });
      if (relevant) schedule();
    });
    observer.observe(D.documentElement, { childList: true, subtree: true });
  }
})(window, document);
