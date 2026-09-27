// ⌘K / Ctrl+K command palette: jump to sections, articles and case studies, or run quick actions.
import { t } from "./i18n";

const dialog = document.getElementById("command-palette");

if (dialog && typeof dialog.showModal === "function") {
  const input = dialog.querySelector("[data-palette-input]");
  const list = dialog.querySelector("[data-palette-results]");
  const status = dialog.querySelector("[data-palette-status]");
  const commands = JSON.parse(dialog.querySelector("[data-palette-commands]").textContent);
  const isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);
  const RECENT_KEY = "palette.recent";
  const GROUP_ORDER = ["recent", "navigation", "project", "post", "action"];
  const ICONS = { post: "fas fa-newspaper", project: "fas fa-diagram-project" };

  let content = null; // posts + case studies, fetched the first time the palette opens
  let results = [];
  let active = 0;

  document.querySelectorAll("[data-shortcut-label]").forEach((el) => {
    el.textContent = isMac ? "⌘K" : "Ctrl K";
  });

  const escapeHtml = (value) =>
    String(value ?? "").replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);

  const normalize = (value) =>
    String(value ?? "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "");

  const readRecent = () => {
    try {
      return JSON.parse(localStorage.getItem(RECENT_KEY) || "[]");
    } catch {
      return [];
    }
  };

  const remember = (item) => {
    try {
      const recent = readRecent().filter((entry) => entry.id !== item.id);
      recent.unshift({ id: item.id, title: item.title, url: item.url, icon: item.icon, type: item.type ?? item.group });
      localStorage.setItem(RECENT_KEY, JSON.stringify(recent.slice(0, 5)));
    } catch {
      // Storage unavailable (private mode); recents are optional.
    }
  };

  const loadContent = async () => {
    if (content) return;
    content = [];
    status.textContent = t("palette.loading");
    try {
      const response = await fetch(dialog.dataset.searchUrl, { headers: { Accept: "application/json" } });
      const data = await response.json();
      content = (data.items || []).map((item) => ({
        ...item,
        id: `${item.type}:${item.url}`,
        group: item.type,
        icon: ICONS[item.type],
        subtitle: item.category,
      }));
    } catch {
      content = [];
    }
    status.textContent = "";
    if (dialog.open) render();
  };

  /** Higher is better: title prefix, then word start, then anywhere in title or keywords. */
  const score = (item, query) => {
    const title = normalize(item.title);
    const haystack = `${title} ${normalize(item.subtitle)} ${normalize(item.keywords)}`;
    if (title.startsWith(query)) return 3;
    if (title.split(/[\s\-–:]+/).some((word) => word.startsWith(query))) return 2;
    return query.split(/\s+/).every((part) => haystack.includes(part)) ? 1 : 0;
  };

  const search = (rawQuery) => {
    const query = normalize(rawQuery.trim());
    const all = [...commands, ...(content || [])];

    if (!query) {
      const recent = readRecent().map((item) => ({ ...item, group: "recent", icon: item.icon || "fas fa-clock-rotate-left" }));
      return [...recent, ...commands];
    }

    return all
      .map((item) => ({ item, rank: score(item, query) }))
      .filter(({ rank }) => rank > 0)
      .sort((a, b) => b.rank - a.rank)
      .map(({ item }) => item)
      .slice(0, 30);
  };

  const render = () => {
    results = search(input.value);
    active = Math.min(active, Math.max(results.length - 1, 0));

    if (!results.length) {
      list.innerHTML = `<li class="px-4 py-10 text-center text-sm text-gray-500">${escapeHtml(t("palette.noResults", { query: input.value }))}</li>`;
      input.removeAttribute("aria-activedescendant");
      return;
    }

    // Group while keeping each result's global index for keyboard navigation.
    const groups = {};
    results.forEach((item, index) => (groups[item.group] ??= []).push({ item, index }));

    list.innerHTML = GROUP_ORDER.filter((group) => groups[group])
      .map(
        (group) => `
        <li role="presentation" class="px-3 pb-1 pt-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">${escapeHtml(t(`palette.groups.${group}`))}</li>
        ${groups[group]
          .map(
            ({ item, index }) => `
          <li id="palette-option-${index}" role="option" data-index="${index}" aria-selected="${index === active}"
              class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-sm ${index === active ? "bg-primary-50 text-primary-900 dark:bg-primary-500/15 dark:text-white" : "text-gray-700 dark:text-gray-300"}">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400"><i class="${escapeHtml(item.icon || "fas fa-arrow-right")} text-xs"></i></span>
            <span class="min-w-0 flex-1">
              <span class="block truncate font-medium">${escapeHtml(item.title)}</span>
              ${item.subtitle ? `<span class="block truncate text-xs text-gray-500">${escapeHtml(item.subtitle)}</span>` : ""}
            </span>
            ${item.external ? '<i class="fas fa-arrow-up-right-from-square text-[10px] text-gray-400"></i>' : ""}
            ${index === active ? '<i class="fas fa-turn-down fa-rotate-90 text-xs text-gray-400"></i>' : ""}
          </li>`
          )
          .join("")}`
      )
      .join("");

    input.setAttribute("aria-activedescendant", `palette-option-${active}`);
    list.querySelector(`[data-index="${active}"]`)?.scrollIntoView({ block: "nearest" });
  };

  const run = async (item) => {
    if (!item) return;
    remember(item);

    if (item.action === "copy") {
      try {
        await navigator.clipboard.writeText(item.value);
        status.textContent = t("palette.emailCopied");
      } catch {
        window.prompt(t("common.copyThisLink"), item.value);
      }
      setTimeout(close, 700);
      return;
    }

    if (item.action === "theme") {
      document.getElementById("theme-toggle")?.click();
      close();
      return;
    }

    close();
    if (item.external) {
      window.open(item.url, "_blank", "noopener");
    } else if (item.url) {
      window.location.href = item.url;
    }
  };

  const open = () => {
    if (dialog.open) return;
    input.value = "";
    active = 0;
    status.textContent = "";
    render();
    dialog.showModal();
    input.focus();
    loadContent();
  };

  const close = () => {
    if (dialog.open) dialog.close();
  };

  document.addEventListener("keydown", (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === "k") {
      event.preventDefault();
      dialog.open ? close() : open();
    }
  });

  document.querySelectorAll("[data-command-palette-open]").forEach((button) => button.addEventListener("click", open));

  input.addEventListener("input", () => {
    active = 0;
    render();
  });

  input.addEventListener("keydown", (event) => {
    if (event.key === "ArrowDown") {
      event.preventDefault();
      active = (active + 1) % Math.max(results.length, 1);
      render();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      active = (active - 1 + results.length) % Math.max(results.length, 1);
      render();
    } else if (event.key === "Enter") {
      event.preventDefault();
      run(results[active]);
    }
  });

  list.addEventListener("mousemove", (event) => {
    const option = event.target.closest("[data-index]");
    if (option && Number(option.dataset.index) !== active) {
      active = Number(option.dataset.index);
      render();
    }
  });

  list.addEventListener("click", (event) => {
    const option = event.target.closest("[data-index]");
    if (option) run(results[Number(option.dataset.index)]);
  });

  // Close when clicking the backdrop (outside the panel).
  dialog.addEventListener("click", (event) => {
    if (event.target === dialog) close();
  });
}
