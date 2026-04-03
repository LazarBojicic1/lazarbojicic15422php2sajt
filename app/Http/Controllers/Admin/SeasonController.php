<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeasonController extends Controller
{
    public function index(Request $request)
    {
        $seasons = Season::query()
            ->with('title')
            ->when($request->filled('title'), fn ($query) => $query->where('title_id', $request->integer('title')))
            ->withCount('episodes')
            ->orderBy('season_number')
            ->paginate(20)
            ->withQueryString();

        $titles = Title::query()->orderBy('name')->get();

        return view('admin.seasons.index', compact('seasons', 'titles'));
    }

    public function create()
    {
        $titles = Title::query()->orderBy('name')->get();

        return view('admin.seasons.create', [
            'season' => new Season(),
            'titles' => $titles,
        ]);
    }

    public function store(Request $request)
    {
        $title = Title::findOrFail($request->integer('title_id'));
        $data = $this->validatedData($request, $title);

        $season = $title->seasons()->create($data);

        return redirect()
            ->route('admin.seasons.edit', $season)
            ->with('message', 'Season created successfully.');
    }

    public function edit(Season $season)
    {
        $season->load(['title', 'episodes']);
        $season->loadCount('episodes');
        $titles = Title::query()->orderBy('name')->get();

        return view('admin.seasons.edit', compact('season', 'titles'));
    }

    public function update(Request $request, Season $season)
    {
        $title = Title::findOrFail($request->integer('title_id'));
        $data = $this->validatedData($request, $title, $season);

        $season->update($data);

        return redirect()
            ->route('admin.seasons.edit', $season)
            ->with('message', 'Season updated successfully.');
    }

    public function destroy(Season $season)
    {
        $season->delete();

        return redirect()
            ->route('admin.seasons.index')
            ->with('message', 'Season deleted successfully.');
    }

    private function validatedData(Request $request, Title $title, ?Season $season = null): array
    {
        $validated = $request->validate([
            'title_id' => ['required', 'exists:titles,id'],
            'tmdb_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('seasons', 'tmdb_id')->ignore($season?->id),
            ],
            'season_number' => [
                'required',
                'integer',
                'min:0',
                Rule::unique('seasons', 'season_number')
                    ->where(fn ($query) => $query->where('title_id', $title->id))
                    ->ignore($season?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'poster_path' => ['nullable', 'string', 'max:255'],
            'air_date' => ['nullable', 'date'],
            'episode_count' => ['nullable', 'integer', 'min:0'],
        ]);

        return $validated;
    }
}
