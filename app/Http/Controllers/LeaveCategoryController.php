<?php

namespace App\Http\Controllers;

use App\Models\LeaveCategory;
use Illuminate\Http\Request;

class LeaveCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show categories in insertion order so new ones appear last
        $categories = LeaveCategory::orderBy('id', 'asc')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('names')) {
            $request->validate([
                'names' => ['required', 'array', 'min:1'],
                'names.*' => ['required', 'string', 'max:255'],
                'descriptions' => ['nullable', 'array'],
                'descriptions.*' => ['nullable', 'string', 'max:255'],
                'vl_default_credits' => ['nullable', 'numeric'],
                'sl_default_credits' => ['nullable', 'numeric'],
            ]);
            $names = $request->input('names', []);
            $descriptions = $request->input('descriptions', []);
            $vlDefault = (float)$request->input('vl_default_credits', 0);
            $slDefault = (float)$request->input('sl_default_credits', 0);
            $created = 0;
            $seen = [];
            $count = count($names);
            for ($i = 0; $i < $count; $i++) {
                $name = trim((string)($names[$i] ?? ''));
                $desc = isset($descriptions[$i]) ? trim((string)$descriptions[$i]) : null;
                if ($name === '') continue;
                if (isset($seen[mb_strtolower($name)])) continue; // skip duplicates from same submission
                $seen[mb_strtolower($name)] = true;
                if (!LeaveCategory::where('name', $name)->exists()) {
                    LeaveCategory::create([
                        'name' => $name,
                        'description' => $desc ?: null,
                        'default_credits' => 0,
                        'vl_default_credits' => $vlDefault,
                        'sl_default_credits' => $slDefault,
                    ]);
                    $created++;
                }
            }
            return redirect()->route('categories.index')->with('status', $created.' categor'.($created===1?'y':'ies').' added.');
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:leave_categories,name'],
                'description' => ['nullable', 'string', 'max:255'],
                'vl_default_credits' => ['nullable', 'numeric'],
                'sl_default_credits' => ['nullable', 'numeric'],
            ]);
            $payload = $validated + [
                'default_credits' => 0.0,
                'vl_default_credits' => (float)($validated['vl_default_credits'] ?? 0),
                'sl_default_credits' => (float)($validated['sl_default_credits'] ?? 0),
            ];
            LeaveCategory::create($payload);
            return redirect()->route('categories.index')->with('status', 'Category created.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveCategory $leaveCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveCategory $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:leave_categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:255'],
            'vl_default_credits' => ['nullable', 'numeric'],
            'sl_default_credits' => ['nullable', 'numeric'],
        ]);
        $category->update($validated + [
            'default_credits' => 0.0,
            'vl_default_credits' => (float)($validated['vl_default_credits'] ?? 0),
            'sl_default_credits' => (float)($validated['sl_default_credits'] ?? 0),
        ]);
        return redirect()->route('categories.index')->with('status', 'Category updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Category deleted.');
    }
}
