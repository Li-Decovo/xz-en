(function () {
    document.querySelectorAll("[data-xz-page-menu-toggle]").forEach(function (toggle) {
        var shell = toggle.closest(".xz-page-header-shell");
        var menu = shell ? shell.querySelector("[data-xz-page-mobile-menu]") : null;
        if (!menu) return;
        function setOpen(open) {
            menu.classList.toggle("is-open", open);
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
        }
        toggle.addEventListener("click", function () { setOpen(!menu.classList.contains("is-open")); });
        menu.addEventListener("click", function (event) { if (event.target.closest("a")) setOpen(false); });
        window.addEventListener("keydown", function (event) { if (event.key === "Escape") setOpen(false); });
    });

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

    document.querySelectorAll("[data-xz-simple-carousel]").forEach(function (carousel) {
        var track = carousel.querySelector("[data-xz-simple-track]");
        var previous = carousel.querySelector("[data-xz-simple-prev]");
        var next = carousel.querySelector("[data-xz-simple-next]");
        var pagination = carousel.querySelector("[data-xz-simple-pagination]");
        if (!track || ((!previous || !next) && !pagination)) return;

        var items = Array.from(track.children);
        var configuredVisible = Math.max(1, parseInt(carousel.dataset.visible || "4", 10));
        var tabletVisible = Math.max(1, parseInt(carousel.dataset.visibleTablet || String(Math.min(2, configuredVisible)), 10));
        var mobileVisible = Math.max(1, parseInt(carousel.dataset.visibleMobile || "1", 10));
        var index = 0;
        var visible = configuredVisible;

        function updatePagination(maxIndex) {
            if (!pagination) return;
            if (pagination.children.length !== maxIndex + 1) {
                pagination.replaceChildren();
                for (var dotIndex = 0; dotIndex <= maxIndex; dotIndex += 1) {
                    (function (targetIndex) {
                        var dot = document.createElement("button");
                        dot.type = "button";
                        dot.setAttribute("aria-label", "Show case slide " + (targetIndex + 1));
                        dot.addEventListener("click", function () {
                            index = targetIndex;
                            update();
                        });
                        pagination.appendChild(dot);
                    }(dotIndex));
                }
            }
            Array.from(pagination.children).forEach(function (dot, dotIndex) {
                var active = dotIndex === index;
                dot.classList.toggle("is-active", active);
                dot.setAttribute("aria-current", active ? "true" : "false");
            });
        }

        function update() {
            visible = window.innerWidth <= 640 ? mobileVisible : (window.innerWidth <= 1360 ? Math.min(tabletVisible, configuredVisible) : configuredVisible);
            visible = Math.min(visible, items.length);
            var gap = parseFloat(window.getComputedStyle(track).columnGap || window.getComputedStyle(track).gap || "0");
            var itemWidth = (track.clientWidth - gap * Math.max(0, visible - 1)) / visible;
            var maxIndex = Math.max(0, items.length - visible);
            index = Math.min(index, maxIndex);
            items.forEach(function (item) { item.style.flex = "0 0 " + itemWidth + "px"; });
            track.style.transform = "translateX(" + (-index * (itemWidth + gap)) + "px)";
            if (previous && next) {
                previous.disabled = items.length <= visible;
                next.disabled = items.length <= visible;
            }
            updatePagination(maxIndex);
        }

        if (previous && next) {
            previous.addEventListener("click", function () {
                var maxIndex = Math.max(0, items.length - visible);
                index = index <= 0 ? maxIndex : index - 1;
                update();
            });
            next.addEventListener("click", function () {
                var maxIndex = Math.max(0, items.length - visible);
                index = index >= maxIndex ? 0 : index + 1;
                update();
            });
        }

        var resizeTimer = null;
        window.addEventListener("resize", function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(update, 160);
        });
        update();
    });

    document.querySelectorAll("[data-xz-product-gallery]").forEach(function (gallery) {
        var mainImage = gallery.querySelector("[data-xz-main-image]");
        gallery.querySelectorAll("[data-xz-gallery-thumb]").forEach(function (button) {
            button.addEventListener("click", function () {
                if (!mainImage) return;
                mainImage.src = button.dataset.fullSrc;
                mainImage.alt = button.dataset.fullAlt || "";
                mainImage.removeAttribute("srcset");
                mainImage.removeAttribute("sizes");
                gallery.querySelectorAll("[data-xz-gallery-thumb]").forEach(function (item) {
                    var active = item === button;
                    item.classList.toggle("is-active", active);
                    item.setAttribute("aria-pressed", active ? "true" : "false");
                });
            });
        });
    });

    function videoEmbedUrl(url) {
        try {
            var parsed = new URL(url, window.location.href);
            var host = parsed.hostname.replace(/^www\./, "");
            var id = "";
            if (host === "youtu.be") id = parsed.pathname.split("/").filter(Boolean)[0] || "";
            if (host.indexOf("youtube.com") !== -1) {
                id = parsed.searchParams.get("v") || "";
                if (!id && parsed.pathname.indexOf("/embed/") === 0) id = parsed.pathname.split("/")[2] || "";
                if (!id && parsed.pathname.indexOf("/shorts/") === 0) id = parsed.pathname.split("/")[2] || "";
            }
            return id ? "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) + "?autoplay=1&rel=0" : "";
        } catch (error) { return ""; }
    }

    document.querySelectorAll("[data-xz-video-open]").forEach(function (opener) {
        var dialog = opener.closest(".xz-home-split")?.nextElementSibling;
        if (!dialog || !dialog.matches("[data-xz-video-dialog]")) return;
        var stage = dialog.querySelector("[data-xz-video-stage]");
        function stopVideo() {
            var video = stage.querySelector("video");
            var iframe = stage.querySelector("iframe");
            if (video) {
                video.pause();
                video.removeAttribute("src");
                video.load();
            }
            if (iframe) iframe.src = "about:blank";
            stage.replaceChildren();
        }
        function closeVideo() {
            stopVideo();
            if (dialog.open) dialog.close();
        }
        opener.addEventListener("click", function (event) {
            event.preventDefault();
            var url = opener.href;
            var embed = videoEmbedUrl(url);
            stage.innerHTML = embed
                ? '<iframe src="' + embed + '" title="Xinzhou video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>'
                : '<video src="' + url.replace(/"/g, "&quot;") + '" controls autoplay playsinline></video>';
            dialog.showModal();
        });
        dialog.querySelector("[data-xz-video-close]").addEventListener("click", closeVideo);
        dialog.addEventListener("click", function (event) { if (event.target === dialog) closeVideo(); });
        dialog.addEventListener("cancel", function (event) { event.preventDefault(); closeVideo(); });
        dialog.addEventListener("close", stopVideo);
    });

    document.querySelectorAll("[data-xz-product-tabs]").forEach(function (tabs) {
        var buttons = Array.from(tabs.querySelectorAll("[data-xz-tab]"));
        var panels = Array.from(tabs.querySelectorAll("[data-xz-tab-panel]"));

        function activate(name) {
            buttons.forEach(function (button) {
                var active = button.dataset.xzTab === name;
                button.classList.toggle("is-active", active);
                button.setAttribute("aria-selected", active ? "true" : "false");
                button.tabIndex = active ? 0 : -1;
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
            button.addEventListener("keydown", function (event) {
                var index = buttons.indexOf(button);
                var nextIndex = index;
                if (event.key === "ArrowRight") nextIndex = (index + 1) % buttons.length;
                if (event.key === "ArrowLeft") nextIndex = (index - 1 + buttons.length) % buttons.length;
                if (event.key === "Home") nextIndex = 0;
                if (event.key === "End") nextIndex = buttons.length - 1;
                if (nextIndex !== index) {
                    event.preventDefault();
                    activate(buttons[nextIndex].dataset.xzTab);
                    buttons[nextIndex].focus();
                }
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

    var inquiryDialog = document.querySelector(".xz-inquiry-dialog");
    document.addEventListener("click", function (event) {
        var opener = event.target.closest("[data-inquiry-open], .xz-el-cta a, .xz-footer-inquiry a");
        if (opener) {
            var popupId = parseInt(window.XinzhouContent && window.XinzhouContent.inquiryPopupId, 10);
            var popupModule = window.elementorProFrontend && window.elementorProFrontend.modules && window.elementorProFrontend.modules.popup;
            event.preventDefault();
            if (popupId && popupModule) {
                popupModule.showPopup({ id: popupId });
            } else if (inquiryDialog && typeof inquiryDialog.showModal === "function" && !inquiryDialog.open) {
                inquiryDialog.showModal();
            }
            return;
        }

        if (inquiryDialog && event.target.closest("[data-inquiry-close]")) {
            inquiryDialog.close();
        }
    });

    if (inquiryDialog && typeof inquiryDialog.showModal === "function") {
        inquiryDialog.addEventListener("click", function (event) {
            if (event.target === inquiryDialog) inquiryDialog.close();
        });
    }
})();
