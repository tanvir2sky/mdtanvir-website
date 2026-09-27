<?php

namespace App\Support;

use Illuminate\Support\Str;

class HtmlToc
{
    /**
     * Adds ids to every h2/h3 in the HTML and returns a table of contents built from them.
     *
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    public static function build(string $html): array
    {
        $toc = [];
        $used = [];

        $result = preg_replace_callback(
            '/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
            function (array $match) use (&$toc, &$used) {
                $full = $match[0];
                $level = (int) $match[1];
                $attributes = $match[2] ?? '';
                $inner = $match[3] ?? '';
                $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5));

                if ($text === '') {
                    return $full;
                }

                if (preg_match('/\sid=["\']([^"\']+)["\']/i', $attributes, $existing)) {
                    $id = $existing[1];
                } else {
                    $base = Str::slug($text) ?: 'section';
                    $id = $base;
                    for ($i = 2; isset($used[$id]); $i++) {
                        $id = "{$base}-{$i}";
                    }
                    $attributes .= ' id="'.e($id).'"';
                }

                $used[$id] = true;
                $toc[] = ['id' => $id, 'text' => $text, 'level' => $level];

                return "<h{$level}{$attributes}>{$inner}</h{$level}>";
            },
            $html
        );

        return ['html' => $result ?? $html, 'toc' => $toc];
    }
}
