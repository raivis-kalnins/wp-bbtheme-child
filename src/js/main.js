import { initHeader } from './components/header.js';
import { observeMotion } from './components/motion.js';
import { initBlog } from './components/blog.js';


function syncSwiperNavigationCenter(block) {
  const swiper = Array.from(block.children).find((child) => child.classList && child.classList.contains('swiper')) || block.querySelector('.swiper');
  if (!swiper) return;

  const slide = swiper.querySelector('.swiper-slide-active.wpbb-swiper-slide, .swiper-slide-visible.wpbb-swiper-slide, .wpbb-swiper-slide');
  let y = 0;
  if (slide) {
    const blockRect = block.getBoundingClientRect();
    const slideRect = slide.getBoundingClientRect();
    if (slideRect.height > 0) y = (slideRect.top - blockRect.top) + (slideRect.height / 2);
  }
  if (!(Number.isFinite(y) && y > 0)) y = swiper.offsetTop + (swiper.clientHeight / 2);
  if (Number.isFinite(y) && y > 0) block.style.setProperty('--wpbb-swiper-nav-y', `${Math.round(y)}px`);
}


function normalizePartnerSwipers() {
  document.querySelectorAll('.wp-theme-partners-section .swiper').forEach((swiperElement) => {
    const swiper = swiperElement.swiper;
    if (!swiper || !swiper.params) return;

    const normalize = (params) => {
      if (!params) return;
      params.centeredSlides = false;
      params.centeredSlidesBounds = false;
      params.centerInsufficientSlides = false;
      params.slidesOffsetBefore = 0;
      params.slidesOffsetAfter = 0;
    };
    normalize(swiper.params);
    normalize(swiper.originalParams);
    swiper.update();
  });
}

function syncAllSwiperNavigationCenters() {
  document.querySelectorAll('.wpbb-swiper-block--nav-gutter').forEach(syncSwiperNavigationCenter);
}

function moveTestimonialNavigationToOuterGutter() {
  document.querySelectorAll('.wpbb-swiper-block').forEach((block) => {
    const cards = block.querySelector('.wpbb-swiper--cards');
    const explicitTestimonials = block.closest('.business-testimonials, .wp-theme-testimonials-section');
    if (!cards && !explicitTestimonials) return;
    if (block.querySelector('.wpbb-swiper--hero, .wpbb-swiper--gallery, .wpbb-swiper--logos')) return;

    const prev = block.querySelector('.swiper-button-prev');
    const next = block.querySelector('.swiper-button-next');
    if (!prev && !next) return;

    block.classList.add('wpbb-swiper-block--nav-gutter');
    if (prev && prev.parentElement !== block) block.appendChild(prev);
    if (next && next.parentElement !== block) block.appendChild(next);
    syncSwiperNavigationCenter(block);
  });
}

function initPresentationGeometry() {
  let resizeTimer = 0;
  const run = () => {
    moveTestimonialNavigationToOuterGutter();
    normalizePartnerSwipers();
    window.setTimeout(() => {
      moveTestimonialNavigationToOuterGutter();
      normalizePartnerSwipers();
      syncAllSwiperNavigationCenters();
    }, 600);
  };
  window.addEventListener('resize', () => {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(() => {
      normalizePartnerSwipers();
      syncAllSwiperNavigationCenters();
    }, 100);
  }, { passive: true });
  if (document.readyState === 'complete') run();
  else window.addEventListener('load', run, { once: true });
}


function initQuoteDrawer() {
  const trigger = document.querySelector('.wp-theme-quote-float');
  if (!trigger || document.body.classList.contains('wpbb-request-quote-disabled')) return;
  const directLink = trigger.matches('a') ? trigger : trigger.querySelector('a');
  const href = (directLink && directLink.getAttribute('href')) || '/request-a-quote/';
  const countNode = trigger.querySelector('.wp-theme-quote-count, .count, [data-quote-count]');
  const count = countNode ? countNode.textContent.trim() : (trigger.textContent.match(/\d+/) || ['0'])[0];

  const backdrop = document.createElement('div');
  backdrop.className = 'wp-theme-quote-drawer-backdrop';
  backdrop.hidden = true;
  const drawer = document.createElement('aside');
  drawer.className = 'wp-theme-quote-drawer';
  drawer.setAttribute('aria-hidden', 'true');
  drawer.innerHTML = `
    <div class="wp-theme-quote-drawer__head">
      <div><span class="wp-theme-sector-eyebrow">Quote</span><h2>Your quote</h2></div>
      <button type="button" class="wp-theme-quote-drawer__close" aria-label="Close quote">×</button>
    </div>
    <div class="wp-theme-quote-drawer__body">
      <p class="wp-theme-quote-drawer__count"><strong>${count}</strong> item${count === '1' ? '' : 's'} currently saved for quotation.</p>
      <p>Review the selected items, add your contact details and send one quotation request when you are ready.</p>
    </div>
    <div class="wp-theme-quote-drawer__actions"><a class="wp-theme-btn wp-theme-btn--primary" href="${href}">Review quote</a></div>`;
  document.body.append(backdrop, drawer);
  const close = () => { drawer.classList.remove('is-open'); drawer.setAttribute('aria-hidden', 'true'); backdrop.hidden = true; document.body.classList.remove('wp-theme-quote-drawer-open'); };
  const open = (event) => { if (event) event.preventDefault(); backdrop.hidden = false; requestAnimationFrame(() => drawer.classList.add('is-open')); drawer.setAttribute('aria-hidden', 'false'); document.body.classList.add('wp-theme-quote-drawer-open'); drawer.querySelector('.wp-theme-quote-drawer__close').focus(); };
  trigger.addEventListener('click', open);
  backdrop.addEventListener('click', close);
  drawer.querySelector('.wp-theme-quote-drawer__close').addEventListener('click', close);
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && drawer.classList.contains('is-open')) close(); });
}



function applyChildThemeTablerIcons() {
  const cfg = window.wpbbChildVisuals;
  if (!cfg || !cfg.base || !Array.isArray(cfg.icons) || !cfg.icons.length) return;
  const slots = Array.from(document.querySelectorAll('#wp-theme-main .wp-theme-card-icon, #wp-theme-main .wp-theme-sector-card__icon, #wp-theme-main .wp-theme-icon-box'))
    .filter((node) => !node.closest('.wp-theme-contact-detail'));
  slots.forEach((slot, index) => {
    if (slot.querySelector('.wp-theme-tabler-icon')) return;
    const icon = cfg.icons[index % cfg.icons.length];
    if (!icon) return;
    slot.querySelectorAll('svg, img').forEach((legacy) => { legacy.setAttribute('aria-hidden', 'true'); legacy.style.display = 'none'; });
    const span = document.createElement('span');
    span.className = 'wp-theme-tabler-icon';
    span.setAttribute('aria-hidden', 'true');
    const safeUrl = `${cfg.base.replace(/\/$/, '')}/assets/icons/tabler/${icon}.svg`;
    span.style.setProperty('--wp-theme-tabler-icon', `url("${safeUrl}")`);
    slot.appendChild(span);
  });
}

function boot() {
  initHeader();
  applyChildThemeTablerIcons();
  observeMotion();
  initBlog();
  initPresentationGeometry();
  initQuoteDrawer();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
/* v3.8.10.43: make initially visible demo media deterministic in sliders/grids. */
function wpbbStabilizeDemoImagesV381043(root = document) {
  const selector = [
    '#wp-theme-main .wpbb-swiper--hero img',
    '#wp-theme-main .wpbb-swiper--gallery img',
    '#wp-theme-main .wp-theme-gallery-card img',
    '#wp-theme-main .wp-theme-blog-card img',
    '#wp-theme-main .wp-theme-blog-list-card img',
    '#wp-theme-main .wpbb-sector-card img',
    '#wp-theme-main .woocommerce ul.products li.product img'
  ].join(',');
  root.querySelectorAll(selector).forEach((image, index) => {
    const deferred = image.getAttribute('data-src') || image.getAttribute('data-lazy-src') || image.getAttribute('data-original');
    if ((!image.getAttribute('src') || image.getAttribute('src') === 'about:blank') && deferred) image.setAttribute('src', deferred);
    if (index < 12 || image.closest('.wpbb-swiper--hero, .wpbb-swiper--gallery')) image.loading = 'eager';
    image.decoding = 'async';
    const reveal = () => {
      image.style.opacity = '1';
      image.style.visibility = 'visible';
    };
    if (image.complete) reveal();
    else image.addEventListener('load', reveal, { once: true });
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => wpbbStabilizeDemoImagesV381043(), { once: true });
} else {
  wpbbStabilizeDemoImagesV381043();
}


/* v3.8.10.45: sector-card thumbnails and an accessible body-level gallery modal. */
function wpbbChildGalleryNormalizePath(value) {
  try {
    const path = new URL(value, window.location.href).pathname.replace(/\/+$/, '');
    return path || '/';
  } catch (error) {
    const path = String(value || '').split(/[?#]/)[0].replace(/\/+$/, '');
    return path || '/';
  }
}

function wpbbChildGalleryPayload(value) {
  const galleries = window.wpbbChildSectorGalleries;
  if (!galleries || typeof galleries !== 'object') return null;
  const path = wpbbChildGalleryNormalizePath(value);
  if (galleries[path]) return galleries[path];

  const segment = path.split('/').filter(Boolean).pop();
  if (!segment) return null;
  const match = Object.keys(galleries).find((key) => {
    const keySegment = wpbbChildGalleryNormalizePath(key).split('/').filter(Boolean).pop();
    return keySegment === segment;
  });
  return match ? galleries[match] : null;
}

function wpbbChildGalleryViewer() {
  if (window.wpbbChildGalleryViewerApi) return window.wpbbChildGalleryViewerApi;

  const lightbox = document.createElement('div');
  lightbox.className = 'wp-theme-item-lightbox';
  lightbox.hidden = true;
  lightbox.dataset.itemGalleryDynamic = 'true';
  lightbox.dataset.wpbbChildGallery = 'true';
  lightbox.setAttribute('role', 'dialog');
  lightbox.setAttribute('aria-modal', 'true');
  lightbox.setAttribute('aria-hidden', 'true');
  lightbox.setAttribute('aria-label', 'Image gallery');
  lightbox.innerHTML = `
    <button class="wp-theme-item-lightbox__backdrop" type="button" aria-label="Close image gallery"></button>
    <div class="wp-theme-item-lightbox__dialog" role="document">
      <div class="wp-theme-item-lightbox__topbar">
        <span class="wp-theme-item-lightbox__counter" aria-live="polite"></span>
        <button class="wp-theme-item-lightbox__close" type="button" aria-label="Close image gallery">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
      </div>
      <div class="wp-theme-item-lightbox__viewer">
        <button class="wp-theme-item-lightbox__nav wp-theme-item-lightbox__nav--prev" type="button" aria-label="Previous image">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <figure class="wp-theme-item-lightbox__figure">
          <img src="" alt="">
          <figcaption></figcaption>
        </figure>
        <button class="wp-theme-item-lightbox__nav wp-theme-item-lightbox__nav--next" type="button" aria-label="Next image">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
      <div class="wp-theme-item-lightbox__thumbs" aria-label="Gallery thumbnails"></div>
    </div>`;
  document.body.appendChild(lightbox);

  const counter = lightbox.querySelector('.wp-theme-item-lightbox__counter');
  const image = lightbox.querySelector('.wp-theme-item-lightbox__figure img');
  const caption = lightbox.querySelector('.wp-theme-item-lightbox__figure figcaption');
  const thumbs = lightbox.querySelector('.wp-theme-item-lightbox__thumbs');
  const closeButton = lightbox.querySelector('.wp-theme-item-lightbox__close');
  const previousButton = lightbox.querySelector('.wp-theme-item-lightbox__nav--prev');
  const nextButton = lightbox.querySelector('.wp-theme-item-lightbox__nav--next');
  const backdrop = lightbox.querySelector('.wp-theme-item-lightbox__backdrop');
  const state = { images: [], index: 0, title: '', trigger: null, touchX: null };

  const imageUrl = (item) => item.full || item.display || item.url || item.src || item.thumb || '';
  const thumbUrl = (item) => item.thumb || item.display || item.full || item.url || item.src || '';
  const imageAlt = (item) => item.alt || item.title || state.title || 'Gallery image';

  const render = () => {
    if (!state.images.length) return;
    state.index = (state.index + state.images.length) % state.images.length;
    const item = state.images[state.index];
    image.classList.add('is-loading');
    image.onload = () => image.classList.remove('is-loading');
    image.onerror = () => image.classList.remove('is-loading');
    image.src = imageUrl(item);
    image.alt = imageAlt(item);
    caption.textContent = imageAlt(item);
    counter.textContent = `${state.index + 1} / ${state.images.length}${state.title ? ` — ${state.title}` : ''}`;
    previousButton.hidden = state.images.length < 2;
    nextButton.hidden = state.images.length < 2;
    thumbs.querySelectorAll('.wp-theme-item-lightbox__thumb').forEach((button, index) => {
      const active = index === state.index;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-current', active ? 'true' : 'false');
      if (active) button.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    });
  };

  const buildThumbs = () => {
    thumbs.replaceChildren();
    state.images.forEach((item, index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'wp-theme-item-lightbox__thumb';
      button.setAttribute('aria-label', `Show image ${index + 1}`);
      const thumb = document.createElement('img');
      thumb.src = thumbUrl(item);
      thumb.alt = '';
      thumb.loading = 'eager';
      thumb.decoding = 'async';
      button.appendChild(thumb);
      button.addEventListener('click', () => {
        state.index = index;
        render();
      });
      thumbs.appendChild(button);
    });
  };

  const close = () => {
    if (lightbox.hidden) return;
    lightbox.hidden = true;
    lightbox.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('wp-theme-gallery-open');
    document.body.classList.remove('wp-theme-gallery-open');
    const trigger = state.trigger;
    state.trigger = null;
    if (trigger && typeof trigger.focus === 'function') trigger.focus({ preventScroll: true });
  };

  const open = (payload, index = 0, trigger = null) => {
    const items = payload && Array.isArray(payload.images) ? payload.images.filter((item) => imageUrl(item)) : [];
    if (!items.length) return;
    state.images = items;
    state.index = Number.isFinite(Number(index)) ? Number(index) : 0;
    state.title = String(payload.title || '');
    state.trigger = trigger instanceof HTMLElement ? trigger : document.activeElement;
    buildThumbs();
    render();
    lightbox.hidden = false;
    lightbox.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('wp-theme-gallery-open');
    document.body.classList.add('wp-theme-gallery-open');
    window.requestAnimationFrame(() => closeButton.focus({ preventScroll: true }));
  };

  const previous = () => {
    if (state.images.length < 2) return;
    state.index -= 1;
    render();
  };
  const next = () => {
    if (state.images.length < 2) return;
    state.index += 1;
    render();
  };

  closeButton.addEventListener('click', close);
  backdrop.addEventListener('click', close);
  previousButton.addEventListener('click', previous);
  nextButton.addEventListener('click', next);
  lightbox.querySelector('.wp-theme-item-lightbox__viewer').addEventListener('touchstart', (event) => {
    state.touchX = event.changedTouches[0] ? event.changedTouches[0].clientX : null;
  }, { passive: true });
  lightbox.querySelector('.wp-theme-item-lightbox__viewer').addEventListener('touchend', (event) => {
    if (state.touchX === null || !event.changedTouches[0]) return;
    const distance = event.changedTouches[0].clientX - state.touchX;
    state.touchX = null;
    if (Math.abs(distance) < 45) return;
    if (distance > 0) previous();
    else next();
  }, { passive: true });

  document.addEventListener('keydown', (event) => {
    if (lightbox.hidden) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      close();
    } else if (event.key === 'ArrowLeft') {
      event.preventDefault();
      previous();
    } else if (event.key === 'ArrowRight') {
      event.preventDefault();
      next();
    } else if (event.key === 'Tab') {
      const focusable = Array.from(lightbox.querySelectorAll('button:not([hidden])')).filter((button) => !button.disabled);
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  const api = { open, close };
  window.wpbbChildGalleryViewerApi = api;
  return api;
}

function wpbbChildGalleryActivate(control, callback) {
  control.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();
    callback(event);
  });
  control.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    event.preventDefault();
    event.stopPropagation();
    callback(event);
  });
}

function wpbbChildGalleryInitSingles(root = document) {
  root.querySelectorAll('[data-wpbb-child-gallery-key]').forEach((gallery) => {
    if (gallery.dataset.wpbbChildGalleryReady === 'true') return;
    const payload = wpbbChildGalleryPayload(gallery.dataset.wpbbChildGalleryKey || window.location.pathname);
    if (!payload || !Array.isArray(payload.images) || payload.images.length < 2) return;

    gallery.dataset.wpbbChildGalleryReady = 'true';
    const stageButton = gallery.querySelector('[data-wpbb-gallery-open]');
    const stageImage = gallery.querySelector('.wp-theme-item-gallery__stage img');
    const thumbButtons = Array.from(gallery.querySelectorAll('[data-wpbb-gallery-index]'));
    let currentIndex = 0;

    const select = (index) => {
      const next = Number(index);
      if (!Number.isFinite(next) || !payload.images[next]) return;
      currentIndex = next;
      const item = payload.images[currentIndex];
      if (stageImage) {
        stageImage.src = item.display || item.full || item.url || item.thumb || stageImage.src;
        stageImage.alt = item.alt || payload.title || stageImage.alt;
      }
      thumbButtons.forEach((button, buttonIndex) => {
        const active = buttonIndex === currentIndex;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-current', active ? 'true' : 'false');
      });
    };

    thumbButtons.forEach((button) => {
      button.addEventListener('click', () => select(button.dataset.wpbbGalleryIndex));
    });
    if (stageButton) {
      stageButton.addEventListener('click', () => wpbbChildGalleryViewer().open(payload, currentIndex, stageButton));
    }
    select(0);
  });
}

function wpbbChildGalleryCardLink(card) {
  const direct = card.querySelector('a.wpbb-sector-card__media[href], a.wp-theme-item-gallery-link[href]');
  if (direct && direct.querySelector('img')) return direct;
  return Array.from(card.querySelectorAll('a[href]')).find((link) => link.querySelector('img')) || null;
}

function wpbbChildGalleryInitCards(root = document) {
  root.querySelectorAll('.wpbb-sector-card, .wp-theme-property-card, .medicine-doctor-card').forEach((card) => {
    if (card.dataset.wpbbChildCardGalleryReady === 'true') return;
    const link = wpbbChildGalleryCardLink(card);
    if (!link) return;
    const payload = wpbbChildGalleryPayload(link.href);
    if (!payload || !Array.isArray(payload.images) || payload.images.length < 2) return;
    const mainImage = link.querySelector('img');
    if (!mainImage) return;

    // Parent releases may already provide card gallery controls. Do not duplicate them.
    if (link.querySelector('.wp-theme-item-gallery-card__open, .wp-theme-item-gallery-card__thumbs')) {
      card.dataset.wpbbChildCardGalleryReady = 'true';
      return;
    }

    card.dataset.wpbbChildCardGalleryReady = 'true';
    link.classList.add('wp-theme-item-gallery-card');
    mainImage.classList.add('wp-theme-item-gallery-card__main');
    let currentIndex = 0;

    const openControl = document.createElement('span');
    openControl.className = 'wp-theme-item-gallery-card__open';
    openControl.setAttribute('role', 'button');
    openControl.setAttribute('tabindex', '0');
    openControl.setAttribute('aria-label', `Open ${payload.title || 'item'} image gallery`);
    openControl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M4 8a2 2 0 0 1 2-2h2l1.2-2h5.6L16 6h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><circle cx="12" cy="12.5" r="3.2"/></svg><span></span>';
    openControl.querySelector('span').textContent = String(payload.images.length);

    const thumbRail = document.createElement('span');
    thumbRail.className = 'wp-theme-item-gallery-card__thumbs';
    thumbRail.setAttribute('aria-label', 'Gallery thumbnails');
    const visibleCount = Math.min(3, payload.images.length);
    const thumbControls = [];

    const select = (index) => {
      if (!payload.images[index]) return;
      currentIndex = index;
      const item = payload.images[currentIndex];
      mainImage.classList.add('is-changing');
      mainImage.removeAttribute('srcset');
      mainImage.removeAttribute('sizes');
      mainImage.removeAttribute('data-srcset');
      mainImage.onload = () => mainImage.classList.remove('is-changing');
      mainImage.onerror = () => mainImage.classList.remove('is-changing');
      mainImage.src = item.display || item.full || item.url || item.thumb || mainImage.src;
      mainImage.alt = item.alt || payload.title || mainImage.alt;
      thumbControls.forEach((control, controlIndex) => {
        const active = controlIndex === currentIndex;
        control.classList.toggle('is-active', active);
        control.setAttribute('aria-current', active ? 'true' : 'false');
      });
    };

    for (let index = 0; index < visibleCount; index += 1) {
      const item = payload.images[index];
      const control = document.createElement('span');
      control.className = 'wp-theme-item-gallery-card__thumb';
      control.setAttribute('role', 'button');
      control.setAttribute('tabindex', '0');
      control.setAttribute('aria-label', `Show image ${index + 1}`);
      const thumb = document.createElement('img');
      thumb.src = item.thumb || item.display || item.full || item.url || '';
      thumb.alt = '';
      thumb.loading = 'lazy';
      thumb.decoding = 'async';
      control.appendChild(thumb);
      wpbbChildGalleryActivate(control, () => select(index));
      thumbControls.push(control);
      thumbRail.appendChild(control);
    }

    if (payload.images.length > visibleCount) {
      const more = document.createElement('span');
      more.className = 'wp-theme-item-gallery-card__more';
      more.setAttribute('role', 'button');
      more.setAttribute('tabindex', '0');
      more.setAttribute('aria-label', `Open all ${payload.images.length} images`);
      more.textContent = `+${payload.images.length - visibleCount}`;
      wpbbChildGalleryActivate(more, () => wpbbChildGalleryViewer().open(payload, currentIndex, more));
      thumbRail.appendChild(more);
    }

    wpbbChildGalleryActivate(openControl, () => wpbbChildGalleryViewer().open(payload, currentIndex, openControl));
    link.append(openControl, thumbRail);
    select(0);
  });
}

function wpbbChildGalleryInit(root = document) {
  const scope = root instanceof Element || root instanceof Document ? root : document;
  wpbbChildGalleryInitSingles(scope);
  wpbbChildGalleryInitCards(scope);
}

function wpbbChildGalleryBoot() {
  wpbbChildGalleryInit(document);
  const main = document.querySelector('#wp-theme-main') || document.body;
  let queued = false;
  const observer = new MutationObserver((records) => {
    if (queued) return;
    if (!records.some((record) => record.addedNodes.length)) return;
    queued = true;
    window.requestAnimationFrame(() => {
      queued = false;
      wpbbChildGalleryInit(main);
    });
  });
  observer.observe(main, { childList: true, subtree: true });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', wpbbChildGalleryBoot, { once: true });
} else {
  wpbbChildGalleryBoot();
}

/* v3.8.10.45 frontend/editor content and media-row normalisation. */
(function wpbbChildLayoutHardening381045() {
  const scopes = () => Array.from(document.querySelectorAll('#wp-theme-main, .editor-styles-wrapper'));

  function normalise(scope) {
    if (!scope || !scope.querySelectorAll) return;
    scope.querySelectorAll('.wpbb-row, .row').forEach((row) => {
      const media = row.querySelector('.wp-theme-sector-media-text__media, .wp-theme-sector-page-image');
      if (!media || media.closest('.wpbb-row, .row') !== row) return;
      row.classList.remove('align-items-center', 'align-items-end', 'align-items-start');
      row.classList.add('align-items-stretch', 'wpbb-quality-media-row');
    });

    if (typeof document.createTreeWalker !== 'function' || typeof NodeFilter === 'undefined') return;
    const walker = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT, {
      acceptNode(node) {
        const parent = node.parentElement;
        if (!parent || parent.closest('script,style,textarea,pre,code')) return NodeFilter.FILTER_REJECT;
        return /\\u0026|\bu0026\b/i.test(node.nodeValue || '') ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
      }
    });
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach((node) => {
      node.nodeValue = (node.nodeValue || '').replace(/\\u0026|\bu0026\b/gi, '&');
    });
  }

  function boot() {
    scopes().forEach(normalise);
    if (typeof MutationObserver !== 'function') return;
    scopes().forEach((scope) => {
      if (scope.dataset.wpbbLayoutHardeningReady === 'true') return;
      scope.dataset.wpbbLayoutHardeningReady = 'true';
      let queued = false;
      const observer = new MutationObserver((records) => {
        if (queued || !records.some((record) => record.addedNodes.length || record.type === 'characterData')) return;
        queued = true;
        window.requestAnimationFrame(() => {
          queued = false;
          normalise(scope);
        });
      });
      observer.observe(scope, { childList:true, subtree:true, characterData:true });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once:true });
  else boot();
})();
