<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EpisodeController extends Controller
{
    public function index(Request $request)
    {
        $episodes = Episode::query()
            ->with('season.title')
            ->when($request->filled('season'), fn ($query) => $query->where('season_id', $request->integer('season')))
            ->orderBy('episode_number')
            ->paginate(25)
            ->withQueryString();

        $seasons = Season::query()->with('title')->orderBy('season_number')->get();

        return view('admin.episodes.index', compact('episodes', 'seasons'));
    }

    public function create()
    {
        $seasons = Season::query()->with('title')->orderBy('season_number')->get();

        return view('admin.episodes.create', [
            'episode' => new Episode(),
            'seasons' => $seasons,
        ]);
    }

    public function store(Request $request)
    {
        $season = Season::findOrFail($request->integer('season_id'));
        $data = $this->validatedData($request, $season);

        $episode = $season->episodes()->create($data);

        return redirect()
            ->route('admin.episodes.edit', $episode)
            ->with('message', 'Episode created successfully.');
    }

    public function edit(Episode $episode)
    {
        $episode->load('season.title');
        $seasons = Season::query()->with('title')->orderBy('season_number')->get();

        return view('admin.episodes.edit', compact('episode', 'seasons'));
    }

    public function update(Request $request, Episode $episode)
    {
        $season = Season::findOrFail($request->integer('season_id'));
        $data = $this->validatedData($request, $season, $episode);

        $episode->update($data);

        return redirect()
            ->route('admin.episodes.edit', $episode)
            ->with('message', 'Episode updated successfully.');
    }

    public function destroy(Episode $episode)
    {
        $episode->delete();

        return redirect()
            ->route('admin.episodes.index')
            ->with('message', 'Episode deleted successfully.');
    }

    private function validatedData(Request $request, Season $season, ?Episode $episode = null): array
    {
        $validated = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'tmdb_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('episodes', 'tmdb_id')->ignore($episode?->id),
            ],
            'episode_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('episodes', 'episode_number')
                    ->where(fn ($query) => $query->where('season_id', $season->id))
                    ->ignore($episode?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'still_path' => ['nullable', 'string', 'max:255'],
            'air_date' => ['nullable', 'date'],
            'runtime' => ['nullable', 'integer', 'min:0'],
            'vote_average' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'vote_count' => ['nullable', 'integer', 'min:0'],
        ]);

        return $validated;
    }
}
