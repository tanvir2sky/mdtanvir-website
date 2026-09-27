// Shopify webhook HMAC verifier. Runs entirely in the browser using Web Crypto.
import { t } from "../i18n";

const tool = document.getElementById("hmac-tool");

if (tool) {
  const form = tool.querySelector("[data-hmac-form]");
  const secretInput = tool.querySelector("[data-hmac-secret]");
  const headerInput = tool.querySelector("[data-hmac-header]");
  const bodyInput = tool.querySelector("[data-hmac-body]");
  const result = tool.querySelector("[data-hmac-result]");

  const escapeHtml = (value) =>
    String(value).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);

  const toBase64 = (buffer) => btoa(String.fromCharCode(...new Uint8Array(buffer)));

  async function computeHmac(secret, body) {
    const encoder = new TextEncoder();
    const key = await crypto.subtle.importKey("raw", encoder.encode(secret), { name: "HMAC", hash: "SHA-256" }, false, ["sign"]);
    return toBase64(await crypto.subtle.sign("HMAC", key, encoder.encode(body)));
  }

  /** Length-independent comparison, like PHP's hash_equals. */
  function safeEqual(a, b) {
    let diff = a.length ^ b.length;
    for (let i = 0; i < Math.max(a.length, b.length); i++) {
      diff |= (a.charCodeAt(i) || 0) ^ (b.charCodeAt(i) || 0);
    }
    return diff === 0;
  }

  function show(state, title, detail, computed) {
    const styles = {
      match: "border-emerald-500/40 bg-emerald-500/10 text-emerald-800 dark:text-emerald-200",
      mismatch: "border-rose-500/40 bg-rose-500/10 text-rose-800 dark:text-rose-200",
      info: "border-sky-500/40 bg-sky-500/10 text-sky-800 dark:text-sky-200",
    };
    const icons = { match: "fa-circle-check", mismatch: "fa-circle-xmark", info: "fa-circle-info" };

    result.className = `rounded-3xl border p-6 ${styles[state]}`;
    result.innerHTML = `
      <p class="flex items-center gap-2 text-lg font-bold"><i class="fas ${icons[state]}"></i>${escapeHtml(title)}</p>
      <p class="mt-2 text-sm">${escapeHtml(detail)}</p>
      ${computed ? `<p class="mt-4 text-xs font-semibold uppercase tracking-wider opacity-70">${escapeHtml(t("tools.hmac.computed"))}</p>
      <code class="mt-1 block break-all rounded-lg bg-white/60 dark:bg-black/30 p-2 font-mono text-xs">${escapeHtml(computed)}</code>` : ""}`;
  }

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const secret = secretInput.value;
    const header = headerInput.value.trim();
    const body = bodyInput.value;

    if (!secret || !body) {
      show("info", t("tools.hmac.missingTitle"), t("tools.hmac.missingDetail"));
      return;
    }

    if (!window.crypto?.subtle) {
      show("info", t("tools.hmac.unsupportedTitle"), t("tools.hmac.unsupportedDetail"));
      return;
    }

    const computed = await computeHmac(secret, body);

    if (!header) {
      show("info", t("tools.hmac.computedTitle"), t("tools.hmac.computedDetail"), computed);
    } else if (safeEqual(computed, header)) {
      show("match", t("tools.hmac.matchTitle"), t("tools.hmac.matchDetail"), computed);
    } else {
      show("mismatch", t("tools.hmac.mismatchTitle"), t("tools.hmac.mismatchDetail"), computed);
    }
  });

  tool.querySelector("[data-hmac-reveal]")?.addEventListener("click", (event) => {
    const visible = secretInput.type === "text";
    secretInput.type = visible ? "password" : "text";
    event.currentTarget.querySelector("i").className = visible ? "fas fa-eye" : "fas fa-eye-slash";
  });

  tool.querySelector("[data-copy-snippet]")?.addEventListener("click", async (event) => {
    const button = event.currentTarget;
    try {
      await navigator.clipboard.writeText(tool.querySelector("[data-snippet]").textContent);
      button.textContent = t("common.copied");
    } catch {
      button.textContent = t("common.pressCtrlC");
    }
    setTimeout(() => (button.textContent = t("tools.copy")), 1500);
  });
}
