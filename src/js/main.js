(function () {
    const tabsRoot = document.querySelector('[data-product-tabs]');
    if (!tabsRoot) return;

    const tabs = Array.from(tabsRoot.querySelectorAll('[data-tab-target]'));
    const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));

    function activateTab(target) {
      tabs.forEach((tab) => {
        const isActive = tab.dataset.tabTarget === target;
        tab.classList.toggle('is-active', isActive);
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      panels.forEach((panel) => {
        panel.hidden = panel.dataset.tabPanel !== target;
      });
    }

    tabs.forEach((tab) => {
      tab.setAttribute('aria-selected', tab.classList.contains('is-active') ? 'true' : 'false');
      tab.addEventListener('click', () => activateTab(tab.dataset.tabTarget));
    });

    activateTab('all');
  })();

  (function () {
    const carousel = document.querySelector('[data-news-carousel]');
    if (!carousel) return;

    const track = carousel.querySelector('[data-news-track]');
    const prevButton = carousel.querySelector('[data-news-prev]');
    const nextButton = carousel.querySelector('[data-news-next]');
    const originals = Array.from(track.children);
    const originalCount = originals.length;
    let visibleCount = 4;
    let index = 4;
    let timer = null;
    let isAnimating = false;
    let queuedDirection = 0;

    function getVisibleCount() {
      if (window.innerWidth <= 640) return 1;
      if (window.innerWidth <= 900) return 2;
      return 4;
    }

    function getStepSize() {
      const card = track.querySelector('.news-card');
      const styles = window.getComputedStyle(track);
      const gap = parseFloat(styles.columnGap || styles.gap || 0);
      return card.getBoundingClientRect().width + gap;
    }

    function setPosition(animate) {
      track.style.transition = animate ? 'transform 0.55s ease' : 'none';
      track.style.transform = `translateX(${-index * getStepSize()}px)`;

      if (!animate) {
        track.offsetHeight;
        window.requestAnimationFrame(() => {
          track.style.transition = 'transform 0.55s ease';
        });
      }
    }

    function normalizePosition() {
      if (index >= originalCount + visibleCount) {
        index = visibleCount;
        setPosition(false);
      }

      if (index < visibleCount) {
        index = originalCount + visibleCount - 1;
        setPosition(false);
      }
    }

    function rebuildClones() {
      track.querySelectorAll('[data-news-clone]').forEach((node) => node.remove());
      visibleCount = getVisibleCount();
      isAnimating = false;
      queuedDirection = 0;

      const before = document.createDocumentFragment();
      originals.slice(-visibleCount).forEach((card) => {
        const clone = card.cloneNode(true);
        clone.dataset.newsClone = 'true';
        before.appendChild(clone);
      });
      track.prepend(before);

      const after = document.createDocumentFragment();
      originals.slice(0, visibleCount).forEach((card) => {
        const clone = card.cloneNode(true);
        clone.dataset.newsClone = 'true';
        after.appendChild(clone);
      });
      track.appendChild(after);

      index = visibleCount;
      setPosition(false);
    }

    function move(direction) {
      if (isAnimating) {
        queuedDirection = direction;
        return;
      }

      isAnimating = true;
      index += direction;
      setPosition(true);
    }

    function startAutoPlay() {
      stopAutoPlay();
      timer = window.setInterval(() => {
        if (!isAnimating) move(1);
      }, 4200);
    }

    function stopAutoPlay() {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    }

    nextButton.addEventListener('click', () => {
      move(1);
      startAutoPlay();
    });

    prevButton.addEventListener('click', () => {
      move(-1);
      startAutoPlay();
    });

    track.addEventListener('transitionend', (event) => {
      if (event.target !== track) return;
      if (event.propertyName !== 'transform') return;

      normalizePosition();
      isAnimating = false;

      if (queuedDirection) {
        const direction = queuedDirection;
        queuedDirection = 0;
        window.requestAnimationFrame(() => move(direction));
      }
    });

    let resizeTimer = null;
    window.addEventListener('resize', () => {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(rebuildClones, 180);
    });

    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);

    rebuildClones();
    startAutoPlay();
  })();
