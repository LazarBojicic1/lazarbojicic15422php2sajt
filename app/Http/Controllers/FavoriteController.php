<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('title.genres')
            ->latest()
            ->paginate(30);

        return view('my-list', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['title_id' => 'required|exists:titles,id']);

        $existing = Favorite::where('user_id', Auth::id())
            ->where('title_id', $request->title_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed']);
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'title_id' => $request->title_id,
        ]);

        return response()->json(['status' => 'added']);
    }
}
