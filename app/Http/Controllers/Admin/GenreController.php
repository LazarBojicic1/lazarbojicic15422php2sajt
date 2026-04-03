<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $genres = Genre::query()
            ->withCount('titles')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create', [
            'genre' => new Genre(),
        ]);
    }

    public function store(Request $request)
    {
        $genre = Genre::create($this->validatedData($request));

        return redirect()
            ->route('admin.genres.edit', $genre)
            ->with('message', 'Genre created successfully.');
    }

    public function edit(Genre $genre)
    {
        $genre->loadCount('titles');

        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $genre->update($this->validatedData($request, $genre));

        return redirect()
            ->route('admin.genres.edit', $genre)
            ->with('message', 'Genre updated successfully.');
    }

    public function destroy(Genre $genre)
    {
        $genre->titles()->detach();
        $genre->delete();

        return redirect()
            ->route('admin.genres.index')
            ->with('message', 'Genre deleted successfully.');
    }

    private function validatedData(Request $request, ?Genre $genre = null): array
    {
        return $request->validate([
            'tmdb_id' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('genres', 'tmdb_id')->ignore($genre?->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('genres', 'name')->ignore($genre?->id),
            ],
        ]);
    }
}
