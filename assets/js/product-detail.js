(function () {
    const gallery = document.querySelector("[data-product-gallery]");

    if (gallery) {
        const mainImage = gallery.querySelector("[data-main-product-image]");
        const thumbnails = Array.from(gallery.querySelectorAll("[data-gallery-thumb]"));

        thumbnails.forEach((thumbnail) => {
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

    tabButtons.forEach((button, index) => {
        button.addEventListener("click", () => activateTab(button.dataset.tab, false));

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
