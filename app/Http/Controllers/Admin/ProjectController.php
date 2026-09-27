<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesContentInput;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    use HandlesContentInput;

    public function index()
    {
        return view('admin.projects.index', ['projects' => Project::query()->ordered()->get()]);
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = Slug::unique(Project::class, $request->input('slug') ?: $data['title']['en']);
        $data['sort_order'] = $this->nextSortOrder(Project::class);
        $data['cover_image'] = $request->file('cover_image')?->store('projects', 'public');

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('status', 'Project added.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validatedData($request);
        $data['slug'] = Slug::unique(Project::class, $request->input('slug') ?: $data['title']['en'], $project->id);

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('projects', 'public');
        } elseif ($request->boolean('remove_cover_image') && $project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
            $data['cover_image'] = null;
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('status', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    public function move(Project $project, string $direction)
    {
        $this->moveModel($project, $direction);

        return back();
    }

    private function validatedData(Request $request): array
    {
        $request->validate([
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'icon' => ['required', 'string', 'max:60'],
            'accent' => ['required', Rule::in(array_keys(Project::ACCENTS))],
            'tags' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
            'year' => ['nullable', 'string', 'max:20'],
            'duration' => ['nullable', 'string', 'max:60'],
            'live_url' => ['nullable', 'url', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'case_study_published' => ['nullable', 'boolean'],
            ...$this->translatableRules('title'),
            ...$this->translatableRules('category', required: false, max: 60),
            ...$this->translatableRules('summary', max: 600),
            ...$this->translatableRules('role', required: false),
            ...$this->translatableRules('challenge', required: false, max: 3000),
            ...$this->translatableRules('approach', required: false, max: 3000),
            ...$this->translatableRules('outcome', required: false, max: 3000),
            ...$this->translatableRules('body', required: false, max: 200000),
        ]);

        return [
            'icon' => trim($request->input('icon')),
            'accent' => $request->input('accent'),
            'tags' => $this->list($request, 'tags'),
            'is_featured' => $request->boolean('is_featured'),
            'is_visible' => $request->boolean('is_visible'),
            'year' => $request->input('year') ?: null,
            'duration' => $request->input('duration') ?: null,
            'live_url' => $request->input('live_url') ?: null,
            'repo_url' => $request->input('repo_url') ?: null,
            'case_study_published' => $request->boolean('case_study_published'),
            'title' => $this->translated($request, 'title'),
            'category' => $this->translated($request, 'category'),
            'summary' => $this->translated($request, 'summary'),
            'role' => $this->translated($request, 'role'),
            'challenge' => $this->translated($request, 'challenge'),
            'approach' => $this->translated($request, 'approach'),
            'outcome' => $this->translated($request, 'outcome'),
            'body' => $this->translated($request, 'body'),
        ];
    }
}
