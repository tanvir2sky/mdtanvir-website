// Booking page: fetch open slots (UTC) and show them by day in the visitor's own time zone.
import { t, locale } from "./i18n";

const form = document.getElementById("booking");

if (form) {
  const $ = (selector) => form.querySelector(selector);
  const zoneSelect = $("[data-booking-timezone]");
  const zoneInput = $("[data-booking-timezone-input]");
  const startInput = $("[data-booking-start]");
  const daysEl = $("[data-booking-days]");
  const timesEl = $("[data-booking-times]");
  const summary = $("[data-booking-summary]");
  const submit = $("[data-booking-submit]");

  const browserZone = Intl.DateTimeFormat().resolvedOptions().timeZone || "UTC";
  let zone = zoneInput.value || browserZone;
  let slots = [];
  let selectedDay = null;

  const zones = typeof Intl.supportedValuesOf === "function" ? Intl.supportedValuesOf("timeZone") : [browserZone, "UTC"];
  if (!zones.includes(zone)) zones.unshift(zone);
  zoneSelect.innerHTML = zones.map((z) => `<option value="${z}">${z.replaceAll("_", " ")}</option>`).join("");
  zoneSelect.value = zone;

  const dayKey = (date) => new Intl.DateTimeFormat("en-CA", { timeZone: zone, year: "numeric", month: "2-digit", day: "2-digit" }).format(date);
  const fmt = (options) => new Intl.DateTimeFormat(locale, { timeZone: zone, ...options });

  const byDay = () => {
    const groups = new Map();
    for (const iso of slots) {
      const date = new Date(iso);
      const key = dayKey(date);
      if (!groups.has(key)) groups.set(key, []);
      groups.get(key).push(date);
    }
    return groups;
  };

  const updateSummary = () => {
    if (!startInput.value) {
      summary.textContent = t("booking.pickFirst");
      submit.disabled = true;
      return;
    }
    const date = new Date(startInput.value);
    summary.textContent = `${fmt({ weekday: "long", day: "numeric", month: "long" }).format(date)} · ${fmt({ hour: "2-digit", minute: "2-digit" }).format(date)} (${zone.replaceAll("_", " ")})`;
    submit.disabled = false;
  };

  const render = () => {
    const groups = byDay();
    if (!selectedDay || !groups.has(selectedDay)) {
      const preselected = startInput.value && slots.includes(startInput.value) ? dayKey(new Date(startInput.value)) : null;
      selectedDay = preselected ?? groups.keys().next().value;
    }

    daysEl.innerHTML = [...groups.entries()]
      .map(([key, dates]) => {
        const active = key === selectedDay;
        return `<button type="button" role="tab" aria-selected="${active}" data-day="${key}"
          class="shrink-0 rounded-2xl border px-4 py-3 text-center transition ${active ? "border-primary-500 bg-primary-600 text-white" : "border-gray-200 dark:border-white/10 hover:border-primary-400"}">
          <span class="block text-xs font-semibold uppercase">${fmt({ weekday: "short" }).format(dates[0])}</span>
          <span class="block text-xl font-black">${fmt({ day: "numeric" }).format(dates[0])}</span>
          <span class="block text-xs opacity-80">${fmt({ month: "short" }).format(dates[0])}</span>
        </button>`;
      })
      .join("");

    timesEl.innerHTML = (groups.get(selectedDay) || [])
      .map((date) => {
        const iso = date.toISOString().replace(".000Z", "Z");
        const active = iso === startInput.value;
        return `<button type="button" data-slot="${iso}" aria-pressed="${active}"
          class="rounded-xl border px-3 py-2.5 font-mono text-sm font-semibold transition ${active ? "border-primary-500 bg-primary-600 text-white" : "border-gray-200 dark:border-white/10 hover:border-primary-400"}">
          ${fmt({ hour: "2-digit", minute: "2-digit" }).format(date)}</button>`;
      })
      .join("");

    updateSummary();
  };

  daysEl.addEventListener("click", (event) => {
    const button = event.target.closest("[data-day]");
    if (!button) return;
    selectedDay = button.dataset.day;
    render();
  });

  timesEl.addEventListener("click", (event) => {
    const button = event.target.closest("[data-slot]");
    if (!button) return;
    startInput.value = button.dataset.slot;
    render();
    $("#bk-name")?.focus({ preventScroll: true });
  });

  zoneSelect.addEventListener("change", () => {
    zone = zoneSelect.value;
    zoneInput.value = zone;
    selectedDay = null;
    render();
  });

  zoneInput.value = zone;

  fetch(form.dataset.slotsUrl, { headers: { Accept: "application/json" } })
    .then((response) => response.json())
    .then((data) => {
      slots = (data.slots || []).map((iso) => iso.replace(".000Z", "Z"));
      if (startInput.value && !slots.includes(startInput.value)) startInput.value = "";
      $("[data-booking-loading]").classList.add("hidden");
      if (!slots.length) {
        $("[data-booking-empty]").classList.remove("hidden");
        return;
      }
      $("[data-booking-picker]").classList.remove("hidden");
      render();
    })
    .catch(() => {
      $("[data-booking-loading]").classList.add("hidden");
      $("[data-booking-empty]").classList.remove("hidden");
    });
}

// Generic helpers used by several forms.
document.querySelectorAll("[data-char-count]").forEach((field) => {
  const counter = document.getElementById(field.dataset.charCount);
  const update = () => (counter.textContent = `${field.value.length}/${field.maxLength}`);
  field.addEventListener("input", update);
  update();
});

document.querySelectorAll("button[data-loading-label]").forEach((button) => {
  button.form?.addEventListener("submit", () => {
    button.disabled = true;
    button.textContent = button.dataset.loadingLabel;
  });
});
