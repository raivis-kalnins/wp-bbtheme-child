const MOTION_SELECTOR = '[data-motion], .motion-fade-up, .motion-fade-left, .motion-fade-right, .motion-scale-in, .motion-reveal, .wp-theme-motion-item';

function show(node) {
  node.classList.remove('motion-pending');
  node.classList.add('motion-in');
  node.classList.add('is-visible');
}

export function initMotion(root = document) {
  const reducedMotion = window.matchMedia
    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const nodes = [...root.querySelectorAll(MOTION_SELECTOR)];

  if (reducedMotion || !('IntersectionObserver' in window)) {
    nodes.forEach(show);
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      show(entry.target);
      observer.unobserve(entry.target);
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -48px 0px',
  });

  nodes.forEach((node, index) => {
    if (node.dataset.motionReady) return;
    node.dataset.motionReady = '1';
    node.classList.add('motion-pending');
    if (!node.style.getPropertyValue('--motion-delay')) {
      node.style.setProperty('--motion-delay', `${Math.min(index % 6, 5) * 70}ms`);
    }
    observer.observe(node);
  });
}

export function observeMotion() {
  initMotion(document);
  if (!window.MutationObserver || !document.body) return;

  let timer = 0;
  const mutationObserver = new MutationObserver((records) => {
    if (!records.some((record) => record.addedNodes.length > 0)) return;
    window.clearTimeout(timer);
    timer = window.setTimeout(() => initMotion(document), 90);
  });

  mutationObserver.observe(document.body, { childList: true, subtree: true });
}
