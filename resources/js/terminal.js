// Interactive "php artisan" terminal in the hero section.
const terminal = document.getElementById("hero-terminal");

if (terminal) {
  const output = terminal.querySelector("[data-terminal-output]");
  const form = terminal.querySelector("[data-terminal-form]");
  const input = terminal.querySelector("[data-terminal-input]");
  const data = JSON.parse(terminal.querySelector("[data-terminal-data]").textContent);

  const history = [];
  let historyIndex = 0;

  const escapeHtml = (value) =>
    String(value).replace(/[&<>"']/g, (char) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    })[char]);

  const row = (label, value) =>
    `<div><span class="text-green-400">${escapeHtml(label.padEnd(12, " ")).replace(/ /g, "&nbsp;")}</span>${value}</div>`;

  const scrollToSection = (id) => {
    const target = document.getElementById(id);
    if (target) target.scrollIntoView({ behavior: "smooth" });
  };

  const commands = {
    help: () =>
      [
        '<div class="text-yellow-300">Available commands:</div>',
        row("about", "Who I am"),
        row("skills", "Tech stack by area"),
        row("experience", "Career timeline"),
        row("projects", "Selected work"),
        row("contact", "How to reach me"),
        data.cv_url ? row("cv", "Download my CV") : "",
        row("clear", "Clear the screen"),
      ].join(""),

    about: () =>
      [
        `<div class="text-cyan-300 font-bold">${escapeHtml(data.name)}</div>`,
        row("Role", escapeHtml(data.role)),
        row("Location", escapeHtml(data.location)),
        `<div class="mt-1 text-gray-300">${escapeHtml(data.summary)}</div>`,
      ].join(""),

    skills: () =>
      Object.entries(data.skills)
        .map(([area, items]) => row(area, escapeHtml(items.join(", "))))
        .join(""),

    experience: () =>
      data.experience
        .map((job) => row(job.period, `${escapeHtml(job.role)} <span class="text-gray-500">@</span> ${escapeHtml(job.company)}`))
        .join(""),

    projects: () =>
      data.projects
        .map((project) => `<div><span class="text-cyan-300">${escapeHtml(project.name)}</span> <span class="text-gray-500">[${escapeHtml(project.stack)}]</span></div>`)
        .join(""),

    contact: () => {
      setTimeout(() => scrollToSection("contact"), 400);
      return [
        row("Email", `<a class="underline" href="mailto:${escapeHtml(data.email)}">${escapeHtml(data.email)}</a>`),
        row("LinkedIn", `<a class="underline" href="${escapeHtml(data.linkedin)}" target="_blank" rel="noopener noreferrer">tanvir-cs</a>`),
        row("GitHub", `<a class="underline" href="${escapeHtml(data.github)}" target="_blank" rel="noopener noreferrer">tanvir-cs</a>`),
        '<div class="text-gray-400">Scrolling to the contact form…</div>',
      ].join("");
    },

    cv: () => {
      if (!data.cv_url) return '<div class="text-red-400">CV is not available yet.</div>';
      window.open(data.cv_url, "_blank", "noopener");
      return '<div class="text-gray-400">Opening CV…</div>';
    },
  };

  const aliases = { list: "help", "--help": "help", whoami: "about", inspire: "about" };

  const print = (html) => {
    output.insertAdjacentHTML("beforeend", html);
    output.scrollTop = output.scrollHeight;
  };

  const run = (raw) => {
    const command = raw.trim().replace(/^php\s+artisan\s+/, "").toLowerCase();
    if (!command) return;

    print(`<div class="mt-2"><span class="text-cyan-400">$ php artisan</span> ${escapeHtml(command)}</div>`);

    if (command === "clear") {
      output.innerHTML = "";
      return;
    }

    const handler = commands[aliases[command] ?? command];
    print(
      handler
        ? handler()
        : `<div class="text-red-400">Command "${escapeHtml(command)}" is not defined.</div><div class="text-gray-400">Try <span class="text-yellow-300">help</span>.</div>`
    );
  };

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const value = input.value;
    if (value.trim()) {
      history.push(value);
      historyIndex = history.length;
    }
    input.value = "";
    run(value);
  });

  input.addEventListener("keydown", (event) => {
    if (event.key === "ArrowUp" && historyIndex > 0) {
      historyIndex -= 1;
      input.value = history[historyIndex];
      event.preventDefault();
    } else if (event.key === "ArrowDown") {
      historyIndex = Math.min(history.length, historyIndex + 1);
      input.value = history[historyIndex] ?? "";
      event.preventDefault();
    }
  });

  // Clicking anywhere in the terminal focuses the prompt (without stealing focus on load).
  terminal.addEventListener("click", (event) => {
    if (event.target.tagName !== "A") input.focus({ preventScroll: true });
  });

  // Intro: "type" the about command once, then show the help hint.
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const intro = "about";

  if (reduceMotion) {
    run(intro);
    print('<div class="mt-2 text-gray-500">Type <span class="text-yellow-300">help</span> to see what else you can run.</div>');
  } else {
    let i = 0;
    const typer = setInterval(() => {
      input.value = intro.slice(0, ++i);
      if (i >= intro.length) {
        clearInterval(typer);
        setTimeout(() => {
          input.value = "";
          run(intro);
          print('<div class="mt-2 text-gray-500">Type <span class="text-yellow-300">help</span> to see what else you can run.</div>');
        }, 350);
      }
    }, 120);
  }
}
