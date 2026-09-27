// Project brief builder: turns a few picks into a ready-to-send contact message.
import { t } from "./i18n";

const brief = document.getElementById("project-brief");
const contactForm = document.getElementById("contact-form");

if (brief && contactForm) {
  const featuresByType = t("brief.features");
  const typeLabels = t("brief.types");

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

    const lines = [t("brief.greeting"), "", t("brief.intro", { type: typeLabels[type] })];
    if (idea) lines.push("", t("brief.idea"), idea);
    if (features.length) lines.push("", t("brief.include"), ...features.map((feature) => `- ${feature}`));
    lines.push("", `${t("brief.budget")}: ${budget}`, `${t("brief.timeline")}: ${timeline}`, "", t("brief.closing"));

    const subject = contactForm.querySelector("#subject");
    const message = contactForm.querySelector("#message");
    subject.value = t("brief.subject", { type: typeLabels[type] });
    message.value = lines.join("\n");

    brief.open = false;
    contactForm.scrollIntoView({ behavior: "smooth", block: "start" });
    contactForm.querySelector("#name").focus({ preventScroll: true });
  });
}
