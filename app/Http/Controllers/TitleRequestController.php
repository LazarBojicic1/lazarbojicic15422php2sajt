<?php

namespace App\Http\Controllers;

use App\Models\TitleRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TitleRequestController extends Controller
{
    private const TITLE_REGEX = "/^(?=.{2,150}$)[\\pL\\pN][\\pL\\pN\\s\\-':&.,!?()\\/+#]*$/u";
    private const MESSAGE_REGEX = "/^(?=.{0,1000}$)[\\pL\\pN\\s\\-':&.,!?()\\/+#\"\\n\\r]*$/u";

    public function index(Request $request)
    {
        $requests = $request->user()
            ->titleRequests()
            ->with('reviewedBy')
            ->latest()
            ->paginate(12);

        return view('title-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $suggestedTitle = trim((string) $request->query('q', ''));
        $suggestedType = $request->query('type');

        if (! in_array($suggestedType, ['movie', 'tv'], true)) {
            $suggestedType = null;
        }

        return view('title-requests.create', [
            'suggestedTitle' => $suggestedTitle,
            'suggestedType' => $suggestedType,
            'currentUser' => $request->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'requested_title' => ['required', 'string', 'min:2', 'max:150', 'regex:' . self::TITLE_REGEX],
            'requested_type' => ['required', Rule::in(['movie', 'tv'])],
            'message' => ['nullable', 'string', 'max:1000', 'regex:' . self::MESSAGE_REGEX],
        ], [
            'requested_title.regex' => 'Title can only contain letters, numbers, spaces, and common punctuation.',
            'message.regex' => 'Notes can only contain letters, numbers, spaces, and common punctuation.',
        ]);

        $user = $request->user();

        TitleRequest::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'requested_title' => trim($data['requested_title']),
            'requested_type' => $data['requested_type'],
            'message' => filled($data['message'] ?? null) ? trim($data['message']) : null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('my-requests')
            ->with('message', 'Your request has been sent. A moderator will review it soon.');
    }
}
