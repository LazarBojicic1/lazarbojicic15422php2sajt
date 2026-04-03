<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::query()
            ->with(['user.role', 'title'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('title', fn ($titleQuery) => $titleQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.favorites.index', compact('favorites'));
    }

    public function create()
    {
        return view('admin.favorites.create', [
            'favorite' => new Favorite(),
            'users' => User::query()->with('role')->orderBy('name')->limit(200)->get(),
            'titles' => Title::query()->orderBy('name')->limit(300)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $favorite = Favorite::create($this->validatedData($request));

        return redirect()
            ->route('admin.favorites.edit', $favorite)
            ->with('message', 'Favorite created successfully.');
    }

    public function edit(Favorite $favorite)
    {
        $favorite->load(['user.role', 'title']);

        return view('admin.favorites.edit', [
            'favorite' => $favorite,
            'users' => User::query()->with('role')->orderBy('name')->limit(200)->get(),
            'titles' => Title::query()->orderBy('name')->limit(300)->get(),
        ]);
    }

    public function update(Request $request, Favorite $favorite)
    {
        $favorite->update($this->validatedData($request, $favorite));

        return redirect()
            ->route('admin.favorites.edit', $favorite)
            ->with('message', 'Favorite updated successfully.');
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return redirect()
            ->route('admin.favorites.index')
            ->with('message', 'Favorite deleted successfully.');
    }

    private function validatedData(Request $request, ?Favorite $favorite = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title_id' => [
                'required',
                'exists:titles,id',
                Rule::unique('favorites', 'title_id')
                    ->where(fn ($query) => $query->where('user_id', $request->input('user_id')))
                    ->ignore($favorite?->id),
            ],
        ]);
    }
}
