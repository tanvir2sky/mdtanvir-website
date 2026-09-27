<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function show(string $slug)
    {
        $project = Project::query()
            ->withPublishedCaseStudy()
            ->where('slug', $slug)
            ->firstOrFail();

        $caseStudies = Project::query()->withPublishedCaseStudy()->ordered()->get(['id', 'slug', 'title', 'sort_order']);
        $index = $caseStudies->search(fn (Project $item) => $item->is($project));

        return view('projects.show', [
            'project' => $project,
            'previous' => $index > 0 ? $caseStudies[$index - 1] : null,
            'next' => $caseStudies[$index + 1] ?? null,
        ]);
    }
}
