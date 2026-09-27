// Project brief builder: turns a few picks into a ready-to-send contact message.
const brief = document.getElementById("project-brief");
const contactForm = document.getElementById("contact-form");

if (brief && contactForm) {
  const featuresByType = {
    laravel: ["Admin dashboard", "REST API", "Authentication & roles", "Payments", "Third-party integrations", "Performance optimisation"],
    shopify: ["Custom theme", "Custom Shopify app", "Store migration", "Checkout & payments", "ERP / CRM integration", "Speed optimisation"],
    ai: ["Chat assistant", "Content generation", "Document summarisation", "Smart search", "Workflow automation", "Add AI to an existing app"],
    other: ["Code review / audit", "Bug fixing", "Ongoing maintenance", "Technical consulting"],
  };

  const typeLabels = {
    laravel: "Laravel web application",
    shopify: "Shopify store / app",
    ai: "AI-powered feature",
    other: "Engineering support",
  };

  const featureContainer = brief.querySelector("[data-brief-features]");
  const generateButton = brief.querySelector("[data-brief-generate]");

  const selectedType = () => brief.querySelector('input[name="brief_type"]:checked')?.value ?? "laravel";

  const renderFeatures = () => {
    featureContainer.innerHTML = "";
    featuresByType[selectedType()].forEach((feature) => {
      const label = document.createElement("label");
      label.className =
        "inline-flex items-center gap-2 rounded-full border border-gray-300 dark:border-gray-700 px-3 py-1.5 text-sm cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/30";
      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = feature;
      checkbox.className = "accent-primary-600";
      label.append(checkbox, feature);
      featureContainer.append(label);
    });
  };

  brief.querySelectorAll('input[name="brief_type"]').forEach((radio) => radio.addEventListener("change", renderFeatures));
  renderFeatures();

  generateButton.addEventListener("click", () => {
    const type = selectedType();
    const features = [...featureContainer.querySelectorAll("input:checked")].map((input) => input.value);
    const budget = brief.querySelector("[data-brief-budget]").value;
    const timeline = brief.querySelector("[data-brief-timeline]").value;
    const idea = brief.querySelector("[data-brief-idea]").value.trim();

    const lines = [
      "Hi Tanvir,",
      "",
      `I'd like to discuss a ${typeLabels[type].toLowerCase()}.`,
    ];
    if (idea) lines.push("", "The idea:", idea);
    if (features.length) lines.push("", "What it should include:", ...features.map((feature) => `- ${feature}`));
    lines.push("", `Budget: ${budget}`, `Timeline: ${timeline}`, "", "Looking forward to hearing from you!");

    const subject = contactForm.querySelector("#subject");
    const message = contactForm.querySelector("#message");
    subject.value = `Project enquiry: ${typeLabels[type]}`;
    message.value = lines.join("\n");

    brief.open = false;
    contactForm.scrollIntoView({ behavior: "smooth", block: "start" });
    contactForm.querySelector("#name").focus({ preventScroll: true });
  });
}
