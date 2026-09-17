export function initBlog() {
  document.querySelectorAll('[data-blog-app]').forEach((app) => {
    const form = app.querySelector('[data-blog-search-form]');
    const input = form?.querySelector('input[type="search"]');
    const filters = app.querySelector('[data-blog-filters]');
    const list = app.querySelector('[data-blog-list]');
    const results = app.querySelector('[data-blog-results]');
    const count = app.querySelector('[data-blog-count]');
    const empty = app.querySelector('[data-blog-empty]');
    const loadWrap = app.querySelector('[data-blog-load-wrap]');
    const loadButton = app.querySelector('[data-blog-load-more]');
    const resetButton = app.querySelector('[data-blog-reset]');
    if (!list || !results) return;

    const state = {
      search: input?.value.trim() || '',
      category: filters?.querySelector('.is-active')?.getAttribute('data-blog-category') || '',
      page: 1,
      loading: false,
      controller: null,
    };
    let searchTimer = 0;

    const setBusy = (busy) => {
      state.loading = busy;
      results.classList.toggle('is-loading', busy);
      results.setAttribute('aria-busy', busy ? 'true' : 'false');
      if (loadButton) {
        loadButton.disabled = busy;
        loadButton.textContent = busy ? 'Loading…' : 'Load more';
      }
    };

    const syncReset = () => {
      if (resetButton) resetButton.hidden = !(state.search || state.category);
    };

    const updateUrl = () => {
      const url = new URL(window.location.href);
      if (state.search) url.searchParams.set('blog_search', state.search); else url.searchParams.delete('blog_search');
      if (state.category) url.searchParams.set('blog_category', state.category); else url.searchParams.delete('blog_category');
      url.searchParams.delete('paged');
      window.history.replaceState({}, '', url.toString());
      syncReset();
    };

    const request = async (page, append = false) => {
      if (state.controller && !append) state.controller.abort();
      state.controller = new AbortController();
      setBusy(true);
      const data = new FormData();
      data.set('action', 'wp_theme_blog_filter');
      data.set('nonce', app.dataset.nonce || '');
      data.set('search', state.search);
      data.set('category', state.category);
      data.set('page', String(page));
      data.set('per_page', app.dataset.perPage || '6');
      try {
        const response = await fetch(app.dataset.ajaxUrl || '/wp-admin/admin-ajax.php', {
          method: 'POST', credentials: 'same-origin', body: data,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: state.controller.signal,
        });
        if (!response.ok) throw new Error('Request failed');
        const json = await response.json();
        if (!json?.success || !json.data) throw new Error('Invalid response');
        if (append) list.insertAdjacentHTML('beforeend', json.data.html || ''); else list.innerHTML = json.data.html || '';
        state.page = page;
        if (count) count.textContent = json.data.countLabel || '';
        if (empty) empty.hidden = append || Boolean((json.data.html || '').trim());
        if (loadWrap) loadWrap.hidden = !json.data.hasMore;
        if (loadButton) loadButton.dataset.page = String(json.data.nextPage || page + 1);
        updateUrl();
        document.dispatchEvent(new CustomEvent('wpTheme:blogUpdated', { detail: { app, page, append } }));
      } catch (error) {
        if (error?.name === 'AbortError') return;
        if (!append) {
          const url = new URL(window.location.href);
          if (state.search) url.searchParams.set('blog_search', state.search);
          if (state.category) url.searchParams.set('blog_category', state.category);
          window.location.assign(url.toString());
        }
      } finally {
        setBusy(false);
      }
    };

    form?.addEventListener('submit', (event) => {
      event.preventDefault();
      window.clearTimeout(searchTimer);
      state.search = input?.value.trim() || '';
      state.page = 1;
      request(1, false);
    });

    input?.addEventListener('input', () => {
      window.clearTimeout(searchTimer);
      const value = input.value.trim();
      if (value.length === 1) return;
      searchTimer = window.setTimeout(() => {
        state.search = value;
        state.page = 1;
        request(1, false);
      }, 360);
    });

    filters?.addEventListener('click', (event) => {
      const button = event.target.closest('[data-blog-category]');
      if (!button) return;
      filters.querySelectorAll('[data-blog-category]').forEach((item) => {
        const active = item === button;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
      state.category = button.getAttribute('data-blog-category') || '';
      state.page = 1;
      request(1, false);
    });

    resetButton?.addEventListener('click', () => {
      state.search = '';
      state.category = '';
      state.page = 1;
      if (input) input.value = '';
      filters?.querySelectorAll('[data-blog-category]').forEach((item) => {
        const active = !item.getAttribute('data-blog-category');
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
      request(1, false);
    });

    loadButton?.addEventListener('click', () => {
      const next = Number.parseInt(loadButton.dataset.page || String(state.page + 1), 10);
      request(Number.isFinite(next) ? next : state.page + 1, true);
    });
    syncReset();
  });

  document.querySelectorAll('[data-copy-article-link]').forEach((button) => {
    button.addEventListener('click', async () => {
      const url = button.getAttribute('data-url') || window.location.href;
      const originalLabel = button.getAttribute('aria-label') || button.getAttribute('title') || 'Copy link';
      let copied = false;
      try {
        if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') throw new Error('Clipboard API unavailable');
        await navigator.clipboard.writeText(url);
        copied = true;
      } catch (error) {
        window.prompt('Copy this link', url);
      }
      if (!copied) return;
      button.classList.add('is-copied');
      button.setAttribute('aria-label', 'Link copied');
      button.setAttribute('title', 'Link copied');
      window.setTimeout(() => {
        button.classList.remove('is-copied');
        button.setAttribute('aria-label', originalLabel);
        button.setAttribute('title', originalLabel);
      }, 1600);
    });
  });

  document.querySelectorAll('[data-blog-single]').forEach((article) => {
    const content = article.querySelector('[data-blog-article-content]');
    const toc = article.querySelector('[data-blog-toc]');
    const tocList = article.querySelector('[data-blog-toc-list]');
    const progress = article.querySelector('[data-blog-progress]');
    if (progress) {
      const updateProgress = () => {
        const start = article.getBoundingClientRect().top + window.scrollY;
        const end = start + article.offsetHeight - window.innerHeight;
        const value = end <= start ? 100 : Math.max(0, Math.min(100, ((window.scrollY - start) / (end - start)) * 100));
        progress.style.width = `${value}%`;
      };
      updateProgress();
      window.addEventListener('scroll', updateProgress, { passive: true });
      window.addEventListener('resize', updateProgress);
    }
    if (!content || !toc || !tocList) return;
    const headings = Array.from(content.querySelectorAll('h2, h3'));
    if (headings.length < 2) { toc.hidden = true; return; }
    const used = new Set();
    headings.forEach((heading, index) => {
      let id = heading.id || heading.textContent.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || `section-${index + 1}`;
      const base = id; let suffix = 2; let existing = document.getElementById(id);
      while (used.has(id) || (existing && existing !== heading)) { id = `${base}-${suffix++}`; existing = document.getElementById(id); }
      used.add(id); heading.id = id;
      const link = document.createElement('a'); link.href = `#${id}`; link.textContent = heading.textContent;
      if (heading.tagName === 'H3') link.classList.add('is-subheading');
      tocList.appendChild(link);
    });
    if ('IntersectionObserver' in window) {
      const links = Array.from(tocList.querySelectorAll('a'));
      const observer = new IntersectionObserver((entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];
        if (!visible) return;
        links.forEach((link) => link.classList.toggle('is-active', link.getAttribute('href') === `#${visible.target.id}`));
      }, { rootMargin: '-18% 0px -68% 0px', threshold: 0 });
      headings.forEach((heading) => observer.observe(heading));
    }
  });
}
