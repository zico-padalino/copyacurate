<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $projects = Project::query()->latest('id')->get();
        $stats = [
            'active' => $projects->where('status', 'active')->count(),
            'planning' => $projects->where('status', 'planning')->count(),
            'budget' => (float) $projects->whereIn('status', ['planning', 'active'])->sum('budget'),
            'completed' => $projects->where('status', 'completed')->count(),
        ];

        return view('projects.index', compact('user', 'projects', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('projects'), 403);
        $request->merge(['budget' => $this->sanitizeMoney($request->input('budget'))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:160'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:planning,active,completed,archived'],
        ]);

        Project::query()->create($data);

        return redirect()->route('projects.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('projects'), 403);
        $data = $request->validate([
            'status' => ['required', 'in:planning,active,completed,archived'],
        ]);
        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Status proyek '.$project->code.' diperbarui.');
    }
}
