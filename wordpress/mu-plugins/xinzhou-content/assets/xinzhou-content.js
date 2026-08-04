(function () {
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
