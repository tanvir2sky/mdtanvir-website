<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesContentInput;
use App\Http\Controllers\Controller;
use App\Models\SkillGroup;
use Illuminate\Http\Request;

class SkillGroupController extends Controller
{
    use HandlesContentInput;

    public function index()
    {
        return view('admin.skills.index', ['skillGroups' => SkillGroup::query()->ordered()->get()]);
    }

    public function create()
    {
        return view('admin.skills.create');
    }

    public function store(Request $request)
    {
        SkillGroup::create($this->validatedData($request) + ['sort_order' => $this->nextSortOrder(SkillGroup::class)]);

        return redirect()->route('admin.skills.index')->with('status', 'Skill group added.');
    }

    public function edit(SkillGroup $skillGroup)
    {
        return view('admin.skills.edit', compact('skillGroup'));
    }

    public function update(Request $request, SkillGroup $skillGroup)
    {
        $skillGroup->update($this->validatedData($request));

        return redirect()->route('admin.skills.index')->with('status', 'Skill group updated.');
    }

    public function destroy(SkillGroup $skillGroup)
    {
        $skillGroup->delete();

        return redirect()->route('admin.skills.index')->with('status', 'Skill group deleted.');
    }

    public function move(SkillGroup $skillGroup, string $direction)
    {
        $this->moveModel($skillGroup, $direction);

        return back();
    }

    private function validatedData(Request $request): array
    {
        $request->validate([
            'icon' => ['required', 'string', 'max:60'],
            ...$this->translatableRules('name', max: 80),
            ...$this->translatableRules('items', max: 2000),
        ]);

        return [
            'icon' => trim($request->input('icon')),
            'name' => $this->translated($request, 'name'),
            'items' => $this->translatedLines($request, 'items'),
        ];
    }
}
