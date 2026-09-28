// Blog article page: reading progress bar, table-of-contents highlighting, copy link.
import { t } from "./i18n";

const article = document.querySelector(".article-content");

if (article) {
  // Reading progress
  const progressBar = document.querySelector("[data-reading-progress]");

  if (progressBar) {
    let ticking = false;

    const updateProgress = () => {
      const rect = article.getBoundingClientRect();
      const total = rect.height - window.innerHeight * 0.6;
      const progress = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : 1;
      progressBar.style.transform = `scaleX(${progress})`;
      ticking = false;
    };

    window.addEventListener(
      "scroll",
      () => {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(updateProgress);
        }
      },
      { passive: true }
    );
    updateProgress();
  }

  // Highlight the section currently being read
  const tocLinks = [...document.querySelectorAll("[data-toc-link]")];

  if (tocLinks.length) {
    const headings = tocLinks
      .map((link) => document.getElementById(link.dataset.tocLink))
      .filter(Boolean);

    const setActive = (id) => {
      tocLinks.forEach((link) => link.classList.toggle("is-active", link.dataset.tocLink === id));
    };

    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries.filter((entry) => entry.isIntersecting);
        if (visible.length) {
          visible.sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
          setActive(visible[0].target.id);
        }
      },
      { rootMargin: "-90px 0px -65% 0px" }
    );

    headings.forEach((heading) => observer.observe(heading));
    if (headings[0]) setActive(headings[0].id);
  }
}

// Copy link button
document.querySelectorAll("[data-copy-link]").forEach((button) => {
  button.addEventListener("click", async () => {
    const label = button.querySelector("[data-copy-label]");
    if (label) label.dataset.original ??= label.textContent;
    try {
      await navigator.clipboard.writeText(button.dataset.copyLink);
      if (label) label.textContent = t("common.copied");
    } catch {
      if (label) label.textContent = t("common.pressCtrlC");
      window.prompt(t("common.copyThisLink"), button.dataset.copyLink);
    }
    setTimeout(() => {
      if (label) label.textContent = label.dataset.original;
    }, 2000);
  });
});
