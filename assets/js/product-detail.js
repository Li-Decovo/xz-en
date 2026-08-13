(function () {
    const gallery = document.querySelector("[data-product-gallery]");

    if (gallery) {
        const mainImage = gallery.querySelector("[data-main-product-image]");
        const thumbnails = Array.from(gallery.querySelectorAll("[data-gallery-thumb]"));

        thumbnails.forEach((thumbnail) => {
            if (thumbnail.dataset.fullSrc) {
                const preload = new Image();
                preload.src = thumbnail.dataset.fullSrc;
            }

            thumbnail.addEventListener("click", () => {
                mainImage.src = thumbnail.dataset.fullSrc;
                mainImage.alt = thumbnail.dataset.alt;

                thumbnails.forEach((item) => {
                    const active = item === thumbnail;
                    item.classList.toggle("is-active", active);
                    item.setAttribute("aria-pressed", active ? "true" : "false");
                });
            });
        });
    }

    const tabs = document.querySelector("[data-product-tabs]");
    if (!tabs) return;

    const tabButtons = Array.from(tabs.querySelectorAll("[data-tab]"));
    const tabPanels = Array.from(tabs.querySelectorAll("[data-tab-panel]"));
    const tabList = tabs.querySelector(".product-tabs__list");
    let suppressClick = false;

    function activateTab(name, focusButton) {
        tabButtons.forEach((button) => {
            const active = button.dataset.tab === name;
            button.classList.toggle("is-active", active);
            button.setAttribute("aria-selected", active ? "true" : "false");
            button.tabIndex = active ? 0 : -1;
            if (active && focusButton) button.focus();
        });

        tabPanels.forEach((panel) => {
            const active = panel.dataset.tabPanel === name;
            panel.classList.toggle("is-active", active);
            panel.hidden = !active;
        });
    }

    if (tabList) {
        let dragging = false;
        let hasDragged = false;
        let startX = 0;
        let startScrollLeft = 0;
        let suppressTimer = null;

        tabList.addEventListener("pointerdown", (event) => {
            if (event.pointerType === "mouse" && event.button !== 0) return;
            dragging = true;
            hasDragged = false;
            startX = event.clientX;
            startScrollLeft = tabList.scrollLeft;
            tabList.classList.add("is-dragging");
            tabList.setPointerCapture(event.pointerId);
        });

        tabList.addEventListener("pointermove", (event) => {
            if (!dragging) return;
            const delta = event.clientX - startX;
            if (Math.abs(delta) > 6) {
                hasDragged = true;
                event.preventDefault();
            }
            tabList.scrollLeft = startScrollLeft - delta;
        });

        function endDrag(event) {
            if (!dragging) return;
            dragging = false;
            tabList.classList.remove("is-dragging");
            if (tabList.hasPointerCapture(event.pointerId)) tabList.releasePointerCapture(event.pointerId);
            if (hasDragged) {
                suppressClick = true;
                window.clearTimeout(suppressTimer);
                suppressTimer = window.setTimeout(() => { suppressClick = false; }, 180);
            }
        }

        tabList.addEventListener("pointerup", endDrag);
        tabList.addEventListener("pointercancel", endDrag);
        tabList.addEventListener("mouseleave", () => {
            dragging = false;
            hasDragged = false;
            tabList.classList.remove("is-dragging");
        });
    }

    tabButtons.forEach((button, index) => {
        button.addEventListener("click", (event) => {
            if (suppressClick) {
                event.preventDefault();
                return;
            }
            activateTab(button.dataset.tab, false);
        });

        button.addEventListener("keydown", (event) => {
            let nextIndex = index;

            if (event.key === "ArrowRight") nextIndex = (index + 1) % tabButtons.length;
            if (event.key === "ArrowLeft") nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
            if (event.key === "Home") nextIndex = 0;
            if (event.key === "End") nextIndex = tabButtons.length - 1;

            if (nextIndex !== index) {
                event.preventDefault();
                activateTab(tabButtons[nextIndex].dataset.tab, true);
            }
        });
    });

    document.querySelectorAll("[data-open-product-tab]").forEach((link) => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            activateTab(link.dataset.openProductTab, false);
            document.getElementById("product-information").scrollIntoView({ behavior: "smooth", block: "start" });
        });
    });

    activateTab("overview", false);
})();
