<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesContentInput;
use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Support\Locale;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use HandlesContentInput;

    public function index()
    {
        return view('admin.experiences.index', ['experiences' => Experience::query()->ordered()->get()]);
    }

    public function create()
    {
        return view('admin.experiences.create');
    }

    public function store(Request $request)
    {
        Experience::create($this->validatedData($request) + ['sort_order' => $this->nextSortOrder(Experience::class)]);

        return redirect()->route('admin.experiences.index')->with('status', 'Experience added.');
    }

    public function edit(Experience $experience)
    {
        return view('admin.experiences.edit', compact('experience'));
    }

    public function update(Request $request, Experience $experience)
    {
        $experience->update($this->validatedData($request));

        return redirect()->route('admin.experiences.index')->with('status', 'Experience updated.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return redirect()->route('admin.experiences.index')->with('status', 'Experience deleted.');
    }

    public function move(Experience $experience, string $direction)
    {
        $this->moveModel($experience, $direction);

        return back();
    }

    private function validatedData(Request $request): array
    {
        $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'period' => ['required', 'string', 'max:60'],
            'is_current' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:1000'],
            ...$this->translatableRules('role'),
            ...$this->translatableRules('highlights', required: false, max: 5000),
            ...$this->translatableRules('focus', required: false, max: 2000),
        ]);

        return [
            'company' => trim($request->input('company')),
            'url' => $request->input('url') ?: null,
            'period' => trim($request->input('period')),
            'is_current' => $request->boolean('is_current'),
            'tags' => $this->list($request, 'tags'),
            'role' => $this->translated($request, 'role'),
            'highlights' => $this->translatedLines($request, 'highlights'),
            'focus' => $this->parseFocus($request),
        ];
    }

    /** Focus areas are entered one per line as "icon | title | text". */
    private function parseFocus(Request $request): ?array
    {
        $focus = [];

        foreach (Locale::supported() as $locale) {
            $items = collect($this->splitLines((string) $request->input("focus.{$locale}", '')))
                ->map(function ($line) {
                    $parts = array_map('trim', explode('|', $line, 3));

                    return count($parts) === 3
                        ? ['icon' => $parts[0], 'title' => $parts[1], 'text' => $parts[2]]
                        : ['icon' => 'fas fa-circle-check', 'title' => $parts[0], 'text' => $parts[1] ?? ''];
                })
                ->all();

            if ($items) {
                $focus[$locale] = $items;
            }
        }

        return $focus ?: null;
    }
}
