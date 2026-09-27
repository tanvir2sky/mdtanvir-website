// Post reactions: toggle instantly, then sync with the server (rolls back on failure).
const bar = document.querySelector("[data-reactions]");

if (bar) {
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  const apply = (counts, mine) => {
    bar.querySelectorAll("[data-reaction]").forEach((button) => {
      const type = button.dataset.reaction;
      button.setAttribute("aria-pressed", mine.includes(type) ? "true" : "false");
      button.querySelector("[data-reaction-count]").textContent = counts[type] ?? 0;
    });
  };

  const snapshot = () => {
    const counts = {};
    const mine = [];
    bar.querySelectorAll("[data-reaction]").forEach((button) => {
      counts[button.dataset.reaction] = Number(button.querySelector("[data-reaction-count]").textContent);
      if (button.getAttribute("aria-pressed") === "true") mine.push(button.dataset.reaction);
    });
    return { counts, mine };
  };

  bar.addEventListener("click", async (event) => {
    const button = event.target.closest("[data-reaction]");
    if (!button || button.disabled) return;

    const type = button.dataset.reaction;
    const before = snapshot();
    const active = before.mine.includes(type);

    // Optimistic update.
    apply(
      { ...before.counts, [type]: Math.max(0, before.counts[type] + (active ? -1 : 1)) },
      active ? before.mine.filter((t) => t !== type) : [...before.mine, type]
    );
    button.disabled = true;

    try {
      const response = await fetch(bar.dataset.reactions, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json", "X-CSRF-TOKEN": token },
        body: JSON.stringify({ type }),
      });
      if (!response.ok) throw new Error(response.statusText);
      const data = await response.json();
      apply(data.counts, data.mine);
    } catch {
      apply(before.counts, before.mine);
    } finally {
      button.disabled = false;
    }
  });
}
