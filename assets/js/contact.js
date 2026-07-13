(function () {
    const form = document.querySelector("[data-contact-form]");
    const status = document.querySelector("[data-contact-status]");

    if (!form) return;

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
            `Target Production Capacity: ${data.get("capacity") || "Not provided"}`,
            "",
            "Production Requirement:",
            data.get("requirement")
        ].join("\n");

        if (status) status.textContent = "Your email application is opening with the inquiry details prepared.";
        window.location.href = `mailto:xinzhou@weldercn.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message)}`;
    });
})();
