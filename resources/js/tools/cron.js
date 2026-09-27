// Cron expression explainer: plain-language description, next run times and the Laravel scheduler method.
import { t, locale } from "../i18n";

const FIELDS = [
  { key: "minute", min: 0, max: 59 },
  { key: "hour", min: 0, max: 23 },
  { key: "dom", min: 1, max: 31 },
  { key: "month", min: 1, max: 12, names: ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"], offset: 1 },
  { key: "dow", min: 0, max: 7, names: ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"], offset: 0 },
];

class CronError extends Error {}

function parseValue(raw, field) {
  const upper = raw.toUpperCase();
  if (field.names?.includes(upper)) return field.names.indexOf(upper) + field.offset;
  if (!/^\d+$/.test(raw)) throw new CronError(t("tools.cron.invalid", { value: raw, field: t(`tools.cron.fields.${field.key}`) }));
  const value = Number(raw);
  if (value < field.min || value > field.max) {
    throw new CronError(t("tools.cron.outOfRange", { value: raw, field: t(`tools.cron.fields.${field.key}`), min: field.min, max: field.max }));
  }
  return value;
}

/** Parse one field into a sorted array of allowed values. */
function parseField(text, field) {
  const values = new Set();

  for (const part of text.split(",")) {
    if (part === "") throw new CronError(t("tools.cron.invalid", { value: text, field: t(`tools.cron.fields.${field.key}`) }));
    const [range, stepText] = part.split("/");
    const step = stepText === undefined ? 1 : Number(stepText);
    if (stepText !== undefined && (!/^\d+$/.test(stepText) || step < 1)) {
      throw new CronError(t("tools.cron.invalid", { value: part, field: t(`tools.cron.fields.${field.key}`) }));
    }

    let start;
    let end;
    if (range === "*") {
      start = field.min;
      end = field.key === "dow" ? 6 : field.max;
    } else if (range.includes("-")) {
      [start, end] = range.split("-").map((value) => parseValue(value, field));
      if (start > end) throw new CronError(t("tools.cron.invalid", { value: part, field: t(`tools.cron.fields.${field.key}`) }));
    } else {
      start = parseValue(range, field);
      end = stepText === undefined ? start : field.max;
    }

    for (let value = start; value <= end; value += step) {
      values.add(field.key === "dow" && value === 7 ? 0 : value);
    }
  }

  return [...values].sort((a, b) => a - b);
}

export function parseCron(expression) {
  const parts = expression.trim().split(/\s+/).filter(Boolean);
  if (parts.length !== 5) throw new CronError(t("tools.cron.fieldCount"));

  const parsed = {};
  FIELDS.forEach((field, index) => {
    parsed[field.key] = parseField(parts[index], field);
  });
  parsed.raw = parts;
  // Standard cron: if both day fields are restricted, a day matches when EITHER matches.
  parsed.domRestricted = !parts[2].startsWith("*");
  parsed.dowRestricted = !parts[4].startsWith("*");
  return parsed;
}

function dayMatches(cron, date) {
  const dom = cron.dom.includes(date.getDate());
  const dow = cron.dow.includes(date.getDay());
  if (cron.domRestricted && cron.dowRestricted) return dom || dow;
  if (cron.domRestricted) return dom;
  if (cron.dowRestricted) return dow;
  return true;
}

export function nextRuns(cron, count = 5, from = new Date()) {
  const runs = [];
  const date = new Date(from);
  date.setSeconds(0, 0);
  date.setMinutes(date.getMinutes() + 1);
  const limit = new Date(from);
  limit.setFullYear(limit.getFullYear() + 2);

  while (runs.length < count && date < limit) {
    if (!cron.month.includes(date.getMonth() + 1)) {
      date.setMonth(date.getMonth() + 1, 1);
      date.setHours(0, 0, 0, 0);
    } else if (!dayMatches(cron, date)) {
      date.setDate(date.getDate() + 1);
      date.setHours(0, 0, 0, 0);
    } else if (!cron.hour.includes(date.getHours())) {
      date.setHours(date.getHours() + 1, 0, 0, 0);
    } else if (!cron.minute.includes(date.getMinutes())) {
      date.setMinutes(date.getMinutes() + 1, 0, 0);
    } else {
      runs.push(new Date(date));
      date.setMinutes(date.getMinutes() + 1, 0, 0);
    }
  }
  return runs;
}

// ---------- Description ----------

const pad = (value) => String(value).padStart(2, "0");
const weekdayName = (day) => new Intl.DateTimeFormat(locale, { weekday: "long" }).format(new Date(2023, 0, 1 + day));
const monthName = (month) => new Intl.DateTimeFormat(locale, { month: "long" }).format(new Date(2023, month - 1, 1));

function isFull(values, field) {
  const max = field.key === "dow" ? 6 : field.max;
  return values.length === max - field.min + 1;
}

/** Step of an evenly spaced set starting at the field minimum, e.g. [0, 15, 30, 45] → 15. */
function evenStep(values, field) {
  if (values.length < 2 || values[0] !== field.min) return null;
  const step = values[1] - values[0];
  const max = field.key === "dow" ? 6 : field.max;
  const expected = Math.floor((max - field.min) / step) + 1;
  return values.every((value, index) => value === field.min + index * step) && values.length === expected ? step : null;
}

function isContiguous(values, wrapWeek = false) {
  const next = (value) => (wrapWeek ? (value + 1) % 7 : value + 1);
  return values.length > 2 && values.every((value, index) => index === 0 || value === next(values[index - 1]));
}

function describeList(values, format, wrapWeek = false) {
  if (isContiguous(values, wrapWeek)) return t("tools.cron.through", { from: format(values[0]), to: format(values[values.length - 1]) });
  const names = values.map(format);
  if (names.length === 1) return names[0];
  return `${names.slice(0, -1).join(", ")} ${t("tools.cron.and")} ${names[names.length - 1]}`;
}

export function describe(cron) {
  const [minuteField, hourField] = FIELDS;
  const { minute, hour } = cron;
  const allMinutes = isFull(minute, minuteField);
  const allHours = isFull(hour, hourField);
  const minuteStep = evenStep(minute, minuteField);
  const parts = [];

  if (minute.length === 1 && hour.length === 1) {
    parts.push(t("tools.cron.atTime", { time: `${pad(hour[0])}:${pad(minute[0])}` }));
  } else if (minute.length === 1 && hour.length <= 4 && !allHours) {
    parts.push(t("tools.cron.atTime", { time: describeList(hour, (h) => `${pad(h)}:${pad(minute[0])}`) }));
  } else if (minute.length === 1 && allHours) {
    parts.push(minute[0] === 0 ? t("tools.cron.everyHour") : t("tools.cron.atMinutePast", { minute: minute[0] }));
  } else if (minute.length === 1 && evenStep(hour, hourField)) {
    const n = evenStep(hour, hourField);
    parts.push(minute[0] === 0 ? t("tools.cron.everyNHoursStart", { n }) : t("tools.cron.everyNHoursAt", { n, minute: minute[0] }));
  } else {
    if (allMinutes) parts.push(t("tools.cron.everyMinute"));
    else if (minuteStep) parts.push(t("tools.cron.everyNMinutes", { n: minuteStep }));
    else if (minute.length === 1) parts.push(t("tools.cron.atMinutePast", { minute: minute[0] }));
    else parts.push(t("tools.cron.atMinutes", { list: describeList(minute, String) }));

    if (!allHours) {
      const hourStep = evenStep(hour, hourField);
      if (hourStep) parts.push(t("tools.cron.everyNHours", { n: hourStep }));
      else if (isContiguous(hour)) parts.push(t("tools.cron.between", { from: `${pad(hour[0])}:00`, to: `${pad(hour[hour.length - 1])}:59` }));
      else parts.push(t("tools.cron.duringHours", { list: describeList(hour, (h) => `${pad(h)}:00`) }));
    }
  }

  const dayParts = [];
  if (cron.domRestricted) dayParts.push(t("tools.cron.onDayOfMonth", { list: describeList(cron.dom, String) }));
  // List weekdays Monday-first so "6,0" reads "Saturday and Sunday".
  const weekdays = [...cron.dow].sort((a, b) => ((a + 6) % 7) - ((b + 6) % 7));
  if (cron.dowRestricted) dayParts.push(t("tools.cron.onDays", { list: describeList(weekdays, weekdayName, true) }));
  if (dayParts.length) parts.push(dayParts.join(` ${t("tools.cron.or")} `));

  if (!isFull(cron.month, FIELDS[3])) parts.push(t("tools.cron.inMonths", { list: describeList(cron.month, monthName) }));

  return `${parts.join(", ")}.`;
}

// ---------- Laravel scheduler mapping ----------

const MINUTE_METHODS = { 1: "everyMinute", 2: "everyTwoMinutes", 3: "everyThreeMinutes", 4: "everyFourMinutes", 5: "everyFiveMinutes", 10: "everyTenMinutes", 15: "everyFifteenMinutes", 30: "everyThirtyMinutes" };
const HOUR_METHODS = { 2: "everyTwoHours", 3: "everyThreeHours", 4: "everyFourHours", 6: "everySixHours" };

export function laravelMethod(cron, expression) {
  const [m, h, dom, mon, dow] = cron.raw;
  const time = () => `'${pad(cron.hour[0])}:${pad(cron.minute[0])}'`;
  const single = (values) => values.length === 1;
  const everyDay = dom === "*" && mon === "*" && dow === "*";

  if (h === "*" && everyDay) {
    const step = m === "*" ? 1 : m.startsWith("*/") ? Number(m.slice(2)) : null;
    if (step && MINUTE_METHODS[step]) return `${MINUTE_METHODS[step]}()`;
    if (m === "0") return "hourly()";
    if (single(cron.minute)) return `hourlyAt(${cron.minute[0]})`;
  }

  if (m === "0" && h.startsWith("*/") && everyDay && HOUR_METHODS[Number(h.slice(2))]) {
    return `${HOUR_METHODS[Number(h.slice(2))]}()`;
  }

  if (single(cron.minute) && single(cron.hour) && mon === "*") {
    const midnight = cron.minute[0] === 0 && cron.hour[0] === 0;

    if (dom === "*" && dow === "*") return midnight ? "daily()" : `dailyAt(${time()})`;
    if (dom === "*" && cron.dowRestricted) {
      const days = cron.dow.join(",");
      if (days === "1,2,3,4,5") return `weekdays()->at(${time()})`;
      if (days === "0,6") return `weekends()->at(${time()})`;
      if (single(cron.dow)) return midnight && cron.dow[0] === 0 ? "weekly()" : `weeklyOn(${cron.dow[0]}, ${time()})`;
    }
    if (dow === "*" && single(cron.dom)) return midnight && cron.dom[0] === 1 ? "monthly()" : `monthlyOn(${cron.dom[0]}, ${time()})`;
  }

  if (expression.trim().replace(/\s+/g, " ") === "0 0 1 1 *") return "yearly()";
  if (expression.trim().replace(/\s+/g, " ") === "0 0 1 */3 *") return "quarterly()";

  return `cron('${cron.raw.join(" ")}')`;
}

// ---------- UI ----------

const tool = document.getElementById("cron-tool");

if (tool) {
  const input = tool.querySelector("[data-cron-input]");
  const description = tool.querySelector("[data-cron-description]");
  const error = tool.querySelector("[data-cron-error]");
  const laravel = tool.querySelector("[data-cron-laravel]");
  const runsList = tool.querySelector("[data-cron-runs]");
  const timezone = tool.querySelector("[data-cron-timezone]");
  const formatter = new Intl.DateTimeFormat(locale, { weekday: "short", year: "numeric", month: "short", day: "numeric", hour: "2-digit", minute: "2-digit" });

  timezone.textContent = t("tools.cron.timezone", { zone: Intl.DateTimeFormat().resolvedOptions().timeZone });

  const render = () => {
    try {
      const cron = parseCron(input.value);
      description.textContent = describe(cron);
      description.classList.remove("hidden");
      error.classList.add("hidden");
      input.setAttribute("aria-invalid", "false");
      laravel.textContent = `Schedule::command('app:your-command')->${laravelMethod(cron, input.value)};`;

      const runs = nextRuns(cron);
      runsList.innerHTML = runs.length
        ? runs
            .map((run, index) => `<li class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-white/10 px-3 py-2"><span class="text-xs text-gray-400">${index + 1}</span>${formatter.format(run)}</li>`)
            .join("")
        : `<li class="text-gray-500">${t("tools.cron.noRuns")}</li>`;
    } catch (err) {
      if (!(err instanceof CronError)) throw err;
      error.textContent = err.message;
      error.classList.remove("hidden");
      description.classList.add("hidden");
      input.setAttribute("aria-invalid", "true");
      laravel.textContent = "";
      runsList.innerHTML = "";
    }
  };

  input.addEventListener("input", render);
  tool.querySelectorAll("[data-cron-preset]").forEach((button) =>
    button.addEventListener("click", () => {
      input.value = button.dataset.cronPreset;
      render();
      input.focus();
    })
  );

  tool.querySelector("[data-cron-copy]").addEventListener("click", async (event) => {
    const button = event.currentTarget;
    try {
      await navigator.clipboard.writeText(laravel.textContent);
      button.textContent = t("common.copied");
    } catch {
      button.textContent = t("common.pressCtrlC");
    }
    setTimeout(() => (button.textContent = t("tools.copy")), 1500);
  });

  render();
}
