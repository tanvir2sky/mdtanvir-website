// Access to the UI strings rendered by resources/views/partials/js-i18n.blade.php.
const strings = window.__i18n ?? {};

/** Look up "section.key" and replace :placeholders. Falls back to the key. */
export function t(path, replacements = {}) {
  const value = path.split(".").reduce((node, key) => (node == null ? undefined : node[key]), strings);
  if (typeof value !== "string") return value ?? path;

  return Object.entries(replacements).reduce(
    (text, [key, replacement]) => text.replaceAll(`:${key}`, replacement),
    value
  );
}

export const locale = strings.locale ?? "en";
