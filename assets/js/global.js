(function () {
    const currentName = window.location.pathname.split("/").pop() || "index.html";
    const sectionName = currentName === "product-detail.html"
        ? "products.html"
        : currentName === "news-detail.html"
            ? "news.html"
            : currentName;

    document.querySelectorAll(".xz-header__nav a, .xz-mobile-drawer a").forEach((link) => {
        const linkName = new URL(link.href, window.location.href).pathname.split("/").pop() || "index.html";
        const isActive = linkName === sectionName;
        link.classList.toggle("is-active", isActive);
        if (isActive) {
            link.setAttribute("aria-current", "page");
        } else {
            link.removeAttribute("aria-current");
        }
    });
})();

(function () {
    const productsLink = document.querySelector('.xz-header__nav > a[href$="products.html"]');
    if (!productsLink) return;

    const categories = [
        ["ibc-tank", "IBC Tank Production Lines", "./assets/images/about/product-ibc-production-line.webp"],
        ["steel-grating", "Steel Grating Welding Lines", "./assets/images/about/product-steel-grating.webp"],
        ["reinforcing-mesh", "Reinforcing Mesh Welding Lines", "./assets/images/about/product-reinforcing-mesh.webp"],
        ["lattice-girder", "Lattice Girder Welding Lines", "./assets/images/about/product-lattice-girder.webp"],
        ["cable-tray", "Cable Tray Mesh Welding Lines", "./assets/images/about/product-cable-tray.webp"],
        ["fence-panel", "3D Fence Panel Lines", "./assets/images/about/product-fence-panel.webp"],
        ["resistance-welding", "Resistance Welding Machines", "./assets/images/about/product-resistance-spot-welder.webp"]
    ];

    const item = document.createElement("div");
    item.className = "xz-header__products";
    productsLink.parentNode.insertBefore(item, productsLink);
    item.appendChild(productsLink);

    productsLink.setAttribute("aria-haspopup", "true");
    productsLink.setAttribute("aria-expanded", "false");

    const mega = document.createElement("div");
    mega.className = "xz-header__mega";
    mega.setAttribute("aria-label", "Product categories");
    mega.innerHTML = categories.map(([slug, label, image]) => `
        <a class="xz-header__mega-card" href="./products.html?category=${slug}">
            <img src="${image}" alt="" loading="lazy">
            <span>${label}</span>
        </a>
    `).join("") + `
        <a class="xz-header__mega-card xz-header__mega-card--contact" href="./contact.html#inquiry">
            <span>Discuss Your Project</span>
            <small>Share your product, output and factory requirements with Xinzhou.</small>
        </a>
    `;
    item.appendChild(mega);

    const setExpanded = (expanded) => productsLink.setAttribute("aria-expanded", String(expanded));
    item.addEventListener("mouseenter", () => setExpanded(true));
    item.addEventListener("mouseleave", () => setExpanded(false));
    item.addEventListener("focusin", () => setExpanded(true));
    item.addEventListener("focusout", (event) => {
        if (!item.contains(event.relatedTarget)) setExpanded(false);
    });
})();

(function () {
    const toggle = document.querySelector("[data-mobile-menu-toggle]");
    const drawer = document.querySelector("[data-mobile-menu]");

    if (!toggle || !drawer) return;

    function setOpen(open) {
        drawer.classList.toggle("is-open", open);
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
    }

    toggle.addEventListener("click", () => {
        setOpen(!drawer.classList.contains("is-open"));
    });

    drawer.addEventListener("click", (event) => {
        const target = event.target;
        if (target instanceof Element && target.closest("a")) {
            setOpen(false);
        }
    });

    window.addEventListener("keydown", (event) => {
        if (event.key === "Escape") setOpen(false);
    });
})();

(function () {
    document.querySelectorAll(".inquiry-strip-form").forEach((form) => {
        form.addEventListener("submit", (event) => {
            event.preventDefault();
            const email = new FormData(form).get("email");
            const subject = "Subscribe to Xinzhou updates";
            const body = `Please add this email address to Xinzhou updates: ${email}`;
            window.location.href = `mailto:xinzhou@weldercn.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        });
    });
})();

(function () {
    const triggers = Array.from(document.querySelectorAll(".xz-header__cta, [data-inquiry-open]"));
    if (!triggers.length) return;

    const dialog = document.createElement("dialog");
    dialog.className = "xz-inquiry-dialog";
    dialog.setAttribute("aria-labelledby", "xz-inquiry-title");
    dialog.innerHTML = `
        <div class="xz-inquiry-dialog__head">
            <p class="xz-inquiry-dialog__label">Equipment Inquiry</p>
            <h2 id="xz-inquiry-title">Get Your Line Proposal</h2>
            <button class="xz-inquiry-dialog__close" type="button" aria-label="Close inquiry form" title="Close">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path></svg>
            </button>
        </div>
        <form class="xz-inquiry-dialog__form" data-global-inquiry-form>
            <div class="xz-inquiry-dialog__grid">
                <label><span>Name *</span><input type="text" name="name" autocomplete="name" required placeholder="Your full name"></label>
                <label><span>Business Email *</span><input type="email" name="email" autocomplete="email" required placeholder="name@company.com"></label>
                <label><span>Company</span><input type="text" name="company" autocomplete="organization" placeholder="Your company name"></label>
                <label><span>Country / Region *</span><input type="text" name="country" autocomplete="country-name" required placeholder="Your country or market"></label>
                <label><span>Product Category *</span><select name="category" required><option value="">Select a category</option><option>IBC Tank Production Line</option><option>Steel Grating Welding Machine</option><option>Reinforcing Mesh Welding Machine</option><option>Lattice Girder Welding Line</option><option>Cable Tray Mesh Welding Machine</option><option>3D Fence Panel Production Line</option><option>Resistance Spot Welding Machine</option><option>Other / Customized Equipment</option></select></label>
                <label><span>Target Capacity</span><input type="text" name="capacity" placeholder="Required output per hour or shift"></label>
            </div>
            <label><span>Production Requirement *</span><textarea name="requirement" required placeholder="Share the finished product, dimensions, material and factory conditions."></textarea></label>
            <div class="xz-inquiry-dialog__footer">
                <p class="xz-inquiry-dialog__status" aria-live="polite" data-global-inquiry-status></p>
                <button class="xz-button xz-button--primary xz-inquiry-dialog__submit" type="submit">Prepare Inquiry<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M22 2 11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></button>
            </div>
        </form>
    `;
    document.body.appendChild(dialog);

    const closeButton = dialog.querySelector(".xz-inquiry-dialog__close");
    const form = dialog.querySelector("[data-global-inquiry-form]");
    const status = dialog.querySelector("[data-global-inquiry-status]");
    const firstInput = dialog.querySelector("input");

    function openDialog(event) {
        event.preventDefault();
        if (typeof dialog.showModal === "function") {
            dialog.showModal();
        } else {
            dialog.setAttribute("open", "");
        }
        document.body.classList.add("xz-inquiry-open");
        window.setTimeout(() => firstInput.focus(), 0);
    }

    function closeDialog() {
        if (typeof dialog.close === "function") {
            dialog.close();
        } else {
            dialog.removeAttribute("open");
        }
        document.body.classList.remove("xz-inquiry-open");
    }

    document.addEventListener("click", (event) => {
        if (!(event.target instanceof Element)) return;
        const trigger = event.target.closest(".xz-header__cta, [data-inquiry-open]");
        if (trigger) openDialog(event);
    });
    closeButton.addEventListener("click", closeDialog);

    dialog.addEventListener("click", (event) => {
        if (event.target === dialog) closeDialog();
    });

    dialog.addEventListener("close", () => {
        document.body.classList.remove("xz-inquiry-open");
    });

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const data = new FormData(form);
        const subject = `Equipment inquiry from ${data.get("company") || data.get("name")}`;
        const message = [
            `Name: ${data.get("name")}`,
            `Business Email: ${data.get("email")}`,
            `Company: ${data.get("company") || "Not provided"}`,
            `Country / Region: ${data.get("country")}`,
            `Product Category: ${data.get("category")}`,
            `Target Capacity: ${data.get("capacity") || "Not provided"}`,
            `Source Page: ${window.location.href}`,
            "",
            "Production Requirement:",
            data.get("requirement")
        ].join("\n");

        status.textContent = "Your email application is opening with the inquiry details prepared.";
        window.location.href = `mailto:xinzhou@weldercn.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message)}`;
    });
})();

(function () {
    if (document.querySelector(".xz-global-footer")) return;

    const footer = document.createElement("footer");
    footer.className = "xz-global-footer";
    footer.innerHTML = `
        <div class="xz-global-footer__inner">
            <div class="xz-global-footer__brand">
                <img class="xz-global-footer__brand-logo" src="./src/media/xinzhou-logo.webp" alt="Xinzhou Welding Equipment">
                <p class="xz-global-footer__brand-copy">Xinzhou provides automated resistance welding equipment and complete production line solutions, supported by engineering, manufacturing and global technical service.</p>
                <button class="xz-button xz-button--primary xz-global-footer__inquiry" type="button" data-inquiry-open>Send an Inquiry<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></button>
            </div>
            <nav aria-label="Footer main menu">
                <h2>Main Menu</h2>
                <ul>
                    <li><a href="./index.html">Home</a></li>
                    <li><a href="./about.html">About</a></li>
                    <li><a href="./services.html">Services</a></li>
                    <li><a href="./products.html">Products</a></li>
                    <li><a href="./news.html">News</a></li>
                    <li><a href="./contact.html">Contact</a></li>
                </ul>
            </nav>
            <nav aria-label="Footer product categories">
                <h2>Product Categories</h2>
                <ul>
                    <li><a href="./products.html?category=ibc-tank">IBC Tank Production Lines</a></li>
                    <li><a href="./products.html?category=steel-grating">Steel Grating Welding Lines</a></li>
                    <li><a href="./products.html?category=reinforcing-mesh">Reinforcing Mesh Welding Lines</a></li>
                    <li><a href="./products.html?category=lattice-girder">Lattice Girder Welding Lines</a></li>
                    <li><a href="./products.html?category=cable-tray">Cable Tray Mesh Welding Lines</a></li>
                    <li><a href="./products.html?category=fence-panel">3D Fence Panel Lines</a></li>
                    <li><a href="./products.html?category=resistance-welding">Resistance Welding Machines</a></li>
                </ul>
            </nav>
            <div class="xz-global-footer__contact">
                <h2>Contact Xinzhou</h2>
                <ul class="xz-global-footer__contact-list">
                    <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2" stroke="currentColor" stroke-width="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg><a href="mailto:xinzhou@weldercn.com">xinzhou@weldercn.com</a></li>
                    <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.62a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.28-1.28a2 2 0 0 1 2.11-.45c.84.29 1.72.5 2.62.62A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg><a href="tel:+8618067231686">+86 180 6723 1686</a></li>
                    <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"></circle></svg><span>Ningbo, Zhejiang, China</span></li>
                    <li><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><rect width="12" height="8" x="6" y="14" stroke="currentColor" stroke-width="2"></rect></svg><span>+86 574 88256693</span></li>
                </ul>
                <div class="xz-global-footer__socials" aria-label="Xinzhou social media">
                    <a href="https://www.linkedin.com/company/xinzhouwelding" target="_blank" rel="noreferrer" aria-label="Xinzhou on LinkedIn"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.48 6S0 4.88 0 3.5 1.12 1 2.48 1s2.5 1.12 2.5 2.5ZM.5 8h4V24h-4V8Zm7 0h3.83v2.19h.05c.53-1.01 1.84-2.08 3.79-2.08 4.05 0 4.8 2.67 4.8 6.14V24h-4v-6.91c0-1.65-.03-3.76-2.29-3.76-2.29 0-2.64 1.79-2.64 3.64V24h-4V8Z"></path></svg></a>
                    <a href="https://www.facebook.com/xinzhouwelder" target="_blank" rel="noreferrer" aria-label="Xinzhou on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12Z"></path></svg></a>
                    <a href="https://www.tiktok.com/@xinzhouwelder" target="_blank" rel="noreferrer" aria-label="Xinzhou on TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69A4.83 4.83 0 0 1 16 5.13V16.3a5.3 5.3 0 1 1-5.3-5.3c.4 0 .79.04 1.17.13v2.73a2.73 2.73 0 1 0 1.56 2.47V0h2.61a4.83 4.83 0 0 0 4.84 4.84v1.85c-.45 0-.88-.04-1.29-.1Z"></path></svg></a>
                </div>
            </div>
        </div>
        <div class="xz-global-footer__bottom"><p>&copy; 2026 Ningbo Xinzhou Welding Equipment Co., Ltd. All rights reserved.</p></div>
    `;

    const currentScript = document.currentScript;
    if (currentScript && currentScript.parentNode) {
        currentScript.parentNode.insertBefore(footer, currentScript);
    } else {
        document.body.appendChild(footer);
    }
})();
