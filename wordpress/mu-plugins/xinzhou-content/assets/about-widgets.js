(function () {
    const carousel = document.querySelector("[data-history-carousel]");
    if (!carousel) return;

    const track = carousel.querySelector("[data-history-track]");
    const viewport = carousel.querySelector(".about-timeline-viewport");
    if (!track || !viewport) return;

    const originalCards = Array.from(track.children);
    const cardCount = originalCards.length;
    const slideDelay = 3000;
    const returnDelay = 5000;
    let visibleCount = 3;
    let index = 0;
    let timer = null;
    let isDragging = false;
    let isFocused = false;
    let isReturning = false;
    let dragStartX = 0;
    let dragDeltaX = 0;
    let dragStartTranslate = 0;

    function getVisibleCount() {
        if (window.innerWidth <= 640) return 1;
        return 3;
    }

    function getMaxIndex() {
        return Math.max(0, cardCount - visibleCount);
    }

    function clampNumber(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    function getStepSize() {
        const firstCard = track.querySelector(".about-timeline-card");
        if (!firstCard) return 0;

        const styles = window.getComputedStyle(track);
        const gap = parseFloat(styles.columnGap || styles.gap || "0");
        return firstCard.getBoundingClientRect().width + gap;
    }

    function getCurrentTranslate() {
        return -index * getStepSize();
    }

    function syncCardHeights() {
        carousel.style.removeProperty("--timeline-card-body-height");

        window.requestAnimationFrame(() => {
            const bodies = Array.from(track.querySelectorAll(".about-timeline-card__body"));
            if (!bodies.length) return;

            const maxHeight = Math.ceil(Math.max(...bodies.map((body) => body.getBoundingClientRect().height)));
            carousel.style.setProperty("--timeline-card-body-height", `${maxHeight}px`);
        });
    }

    function setPosition(animate) {
        track.style.transition = animate ? "transform 0.58s ease" : "none";
        track.style.transform = `translateX(${getCurrentTranslate()}px)`;

        if (!animate) {
            track.offsetHeight;
            window.requestAnimationFrame(() => {
                track.style.transition = "transform 0.58s ease";
            });
        }
    }

    function stop() {
        if (timer) {
            window.clearTimeout(timer);
            timer = null;
        }
    }

    function scheduleNext() {
        stop();
        if (isDragging || isFocused || isReturning || cardCount <= visibleCount) return;

        timer = window.setTimeout(() => {
            if (index >= getMaxIndex()) {
                isReturning = true;
                index = cardCount;
                setPosition(true);
                return;
            } else {
                index += 1;
            }

            setPosition(true);
            scheduleNext();
        }, index >= getMaxIndex() ? returnDelay : slideDelay);
    }

    function buildReturnClones() {
        track.querySelectorAll("[data-history-return-clone]").forEach((node) => node.remove());

        const fragment = document.createDocumentFragment();
        originalCards.slice(0, visibleCount).forEach((card) => {
            const clone = card.cloneNode(true);
            clone.dataset.historyReturnClone = "true";
            clone.setAttribute("aria-hidden", "true");
            clone.querySelectorAll("img").forEach((image) => {
                image.draggable = false;
            });
            fragment.appendChild(clone);
        });

        track.appendChild(fragment);
    }

    function rebuild() {
        visibleCount = getVisibleCount();
        buildReturnClones();
        index = clampNumber(index, 0, getMaxIndex());
        isReturning = false;
        setPosition(false);
        syncCardHeights();
        scheduleNext();
    }

    function dragTo(deltaX) {
        const stepSize = getStepSize();
        const minTranslate = -getMaxIndex() * stepSize;
        const maxTranslate = 0;
        const nextTranslate = clampNumber(dragStartTranslate + deltaX, minTranslate, maxTranslate);

        dragDeltaX = nextTranslate - dragStartTranslate;
        track.style.transition = "none";
        track.style.transform = `translateX(${nextTranslate}px)`;
    }

    function onPointerDown(event) {
        if (event.button !== undefined && event.button !== 0) return;
        if (isReturning) return;

        isDragging = true;
        dragStartX = event.clientX;
        dragDeltaX = 0;
        dragStartTranslate = getCurrentTranslate();
        carousel.classList.add("is-dragging");
        stop();
        viewport.setPointerCapture(event.pointerId);
    }

    function onPointerMove(event) {
        if (!isDragging) return;

        event.preventDefault();
        dragTo(event.clientX - dragStartX);
    }

    function onPointerUp(event) {
        if (!isDragging) return;

        try {
            viewport.releasePointerCapture(event.pointerId);
        } catch (error) {
            /* Pointer capture may already be released by the browser. */
        }

        const stepSize = getStepSize();
        const threshold = Math.min(120, Math.max(48, stepSize * 0.18));
        let movedCards = Math.abs(dragDeltaX) > threshold ? Math.round(-dragDeltaX / stepSize) : 0;

        movedCards = clampNumber(movedCards, -visibleCount, visibleCount);
        index = clampNumber(index + movedCards, 0, getMaxIndex());

        isDragging = false;
        carousel.classList.remove("is-dragging");
        setPosition(true);
        scheduleNext();
    }

    track.addEventListener("transitionend", (event) => {
        if (event.target !== track || event.propertyName !== "transform" || !isReturning) return;

        isReturning = false;
        index = 0;
        setPosition(false);
        scheduleNext();
    });

    let resizeTimer = null;
    window.addEventListener("resize", () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(rebuild, 180);
    });

    viewport.addEventListener("pointerdown", onPointerDown);
    viewport.addEventListener("pointermove", onPointerMove);
    viewport.addEventListener("pointerup", onPointerUp);
    viewport.addEventListener("pointercancel", onPointerUp);
    viewport.addEventListener("lostpointercapture", onPointerUp);

    carousel.addEventListener("focusin", () => {
        isFocused = true;
        stop();
    });

    carousel.addEventListener("focusout", () => {
        isFocused = false;
        scheduleNext();
    });

    track.querySelectorAll("img").forEach((image) => {
        image.addEventListener("load", syncCardHeights, { once: true });
        image.draggable = false;
    });

    rebuild();
})();
