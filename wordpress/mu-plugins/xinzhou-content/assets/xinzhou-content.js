(function () {
    document.querySelectorAll("[data-xz-carousel]").forEach(function (carousel) {
        var track = carousel.querySelector("[data-xz-carousel-track]");
        var previous = carousel.querySelector("[data-xz-carousel-prev]");
        var next = carousel.querySelector("[data-xz-carousel-next]");
        if (!track || !previous || !next) return;

        var originals = Array.from(track.children);
        if (originals.length < 2) return;
        var originalCount = originals.length;
        var visibleCount = 4;
        var index = 0;
        var timer = null;
        var animating = false;
        var queuedDirection = 0;

        function getVisibleCount() {
            if (window.innerWidth <= 640) return 1;
            if (window.innerWidth <= 900) return 2;
            return Math.min(4, originalCount);
        }

        function getStepSize() {
            var card = track.querySelector(".xz-home-carousel-card");
            var styles = window.getComputedStyle(track);
            return card.getBoundingClientRect().width + parseFloat(styles.columnGap || styles.gap || 0);
        }

        function setPosition(animate) {
            track.style.transition = animate ? "transform 0.55s ease" : "none";
            track.style.transform = "translateX(" + (-index * getStepSize()) + "px)";
            if (!animate) {
                track.offsetHeight;
                window.requestAnimationFrame(function () {
                    track.style.transition = "transform 0.55s ease";
                });
            }
        }

        function rebuild() {
            track.querySelectorAll("[data-xz-carousel-clone]").forEach(function (node) { node.remove(); });
            visibleCount = getVisibleCount();
            animating = false;
            queuedDirection = 0;

            var before = document.createDocumentFragment();
            originals.slice(-visibleCount).forEach(function (card) {
                var clone = card.cloneNode(true);
                clone.dataset.xzCarouselClone = "true";
                before.appendChild(clone);
            });
            track.prepend(before);

            var after = document.createDocumentFragment();
            originals.slice(0, visibleCount).forEach(function (card) {
                var clone = card.cloneNode(true);
                clone.dataset.xzCarouselClone = "true";
                after.appendChild(clone);
            });
            track.appendChild(after);
            index = visibleCount;
            setPosition(false);
        }

        function move(direction) {
            if (animating) {
                queuedDirection = direction;
                return;
            }
            animating = true;
            index += direction;
            setPosition(true);
        }

        function normalize() {
            if (index >= originalCount + visibleCount) {
                index = visibleCount;
                setPosition(false);
            } else if (index < visibleCount) {
                index = originalCount + visibleCount - 1;
                setPosition(false);
            }
        }

        function stop() {
            if (timer) window.clearInterval(timer);
            timer = null;
        }

        function start() {
            stop();
            if (carousel.dataset.autoplay !== "true") return;
            timer = window.setInterval(function () { move(1); }, parseInt(carousel.dataset.speed || "4200", 10));
        }

        previous.addEventListener("click", function () { move(-1); start(); });
        next.addEventListener("click", function () { move(1); start(); });
        track.addEventListener("transitionend", function (event) {
            if (event.target !== track || event.propertyName !== "transform") return;
            normalize();
            animating = false;
            if (queuedDirection) {
                var direction = queuedDirection;
                queuedDirection = 0;
                window.requestAnimationFrame(function () { move(direction); });
            }
        });
        carousel.addEventListener("mouseenter", stop);
        carousel.addEventListener("mouseleave", start);

        var resizeTimer = null;
        window.addEventListener("resize", function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(rebuild, 180);
        });

        rebuild();
        start();
    });

    document.querySelectorAll("[data-xz-product-gallery]").forEach(function (gallery) {
        var mainImage = gallery.querySelector("[data-xz-main-image]");
        gallery.querySelectorAll("[data-xz-gallery-thumb]").forEach(function (button) {
            button.addEventListener("click", function () {
                if (!mainImage) return;
                mainImage.src = button.dataset.fullSrc;
                gallery.querySelectorAll("[data-xz-gallery-thumb]").forEach(function (item) {
                    var active = item === button;
                    item.classList.toggle("is-active", active);
                    item.setAttribute("aria-pressed", active ? "true" : "false");
                });
            });
        });
    });

    document.querySelectorAll("[data-xz-product-tabs]").forEach(function (tabs) {
        var buttons = Array.from(tabs.querySelectorAll("[data-xz-tab]"));
        var panels = Array.from(tabs.querySelectorAll("[data-xz-tab-panel]"));

        function activate(name) {
            buttons.forEach(function (button) {
                var active = button.dataset.xzTab === name;
                button.classList.toggle("is-active", active);
                button.setAttribute("aria-selected", active ? "true" : "false");
            });
            panels.forEach(function (panel) {
                var active = panel.dataset.xzTabPanel === name;
                panel.classList.toggle("is-active", active);
                panel.hidden = !active;
            });
        }

        buttons.forEach(function (button) {
            button.addEventListener("click", function () {
                activate(button.dataset.xzTab);
            });
        });

        document.querySelectorAll("[data-xz-open-tab]").forEach(function (link) {
            link.addEventListener("click", function (event) {
                event.preventDefault();
                activate(link.dataset.xzOpenTab);
                document.getElementById("product-information").scrollIntoView({ behavior: "smooth" });
            });
        });
    });
})();
