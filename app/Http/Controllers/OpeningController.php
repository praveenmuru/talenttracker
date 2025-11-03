<?php
namespace App\Http\Controllers;

use App\Models\Opening;
use Illuminate\Http\Request;

class OpeningController extends Controller
{
    public function index(Request $request)
    {
        $query = Opening::query();

        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $openings = $query->latest()->paginate(10);

        return view('openings.index', compact('openings'));
    }

    public function create()
    {
        return view('openings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:open,closed,archived',
        ]);

        Opening::create($validated + $request->only([
            'department', 'description', 'requirements', 'expected_joining_date', 'salary_min', 'salary_max'
        ]));

        return redirect()->route('openings.index')->with('success', 'Opening created successfully.');
    }

    public function edit(Opening $opening)
    {
        return view('openings.edit', compact('opening'));
    }

    public function update(Request $request, Opening $opening)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:open,closed,archived',
        ]);

        $opening->update($validated + $request->only([
            'department', 'description', 'requirements', 'expected_joining_date', 'salary_min', 'salary_max'
        ]));

        return redirect()->route('openings.index')->with('success', 'Opening updated successfully.');
    }

    public function destroy(Opening $opening)
    {
        $opening->delete();

        return redirect()->route('openings.index')->with('success', 'Opening deleted successfully.');
    }
}
