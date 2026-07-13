(function () {
    const grid = document.querySelector("[data-products-grid]");
    const pagination = document.querySelector("[data-product-pagination]");

    if (!grid || !pagination) return;

    const cards = Array.from(grid.querySelectorAll("[data-product-card]"));
    const pageSize = 9;
    const totalPages = Math.ceil(cards.length / pageSize);
    const params = new URLSearchParams(window.location.search);
    let currentPage = Math.min(Math.max(Number(params.get("page")) || 1, 1), totalPages);

    const icons = {
        previous: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
        next: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>'
    };

    function createButton(label, page, options) {
        const button = document.createElement("button");
        button.type = "button";
        button.dataset.page = String(page);
        button.setAttribute("aria-label", options.ariaLabel);
        button.title = options.ariaLabel;
        button.disabled = options.disabled;

        if (options.active) {
            button.classList.add("is-active");
            button.setAttribute("aria-current", "page");
        }

        if (options.icon) {
            button.innerHTML = icons[options.icon];
        } else {
            button.textContent = label;
        }

        return button;
    }

    function renderPagination() {
        pagination.replaceChildren();

        pagination.appendChild(createButton("", currentPage - 1, {
            ariaLabel: "Previous product page",
            disabled: currentPage === 1,
            active: false,
            icon: "previous"
        }));

        for (let page = 1; page <= totalPages; page += 1) {
            pagination.appendChild(createButton(String(page), page, {
                ariaLabel: `Product page ${page}`,
                disabled: false,
                active: page === currentPage
            }));
        }

        pagination.appendChild(createButton("", currentPage + 1, {
            ariaLabel: "Next product page",
            disabled: currentPage === totalPages,
            active: false,
            icon: "next"
        }));
    }

    function renderPage(page, moveFocus) {
        currentPage = page;
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;

        cards.forEach((card, index) => {
            const visible = index >= start && index < end;
            card.hidden = !visible;
            card.setAttribute("aria-hidden", visible ? "false" : "true");
        });

        renderPagination();

        const url = new URL(window.location.href);
        if (currentPage === 1) {
            url.searchParams.delete("page");
        } else {
            url.searchParams.set("page", String(currentPage));
        }
        window.history.replaceState({}, "", url);

        if (moveFocus) {
            document.querySelector(".product-archive__head").scrollIntoView({ behavior: "smooth", block: "start" });
        }
    }

    pagination.addEventListener("click", (event) => {
        const button = event.target.closest("button[data-page]");
        if (!button || button.disabled) return;

        const targetPage = Number(button.dataset.page);
        if (targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
            renderPage(targetPage, true);
        }
    });

    renderPage(currentPage, false);
})();
