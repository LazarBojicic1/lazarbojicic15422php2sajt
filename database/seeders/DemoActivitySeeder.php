<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Favorite;
use App\Models\SearchLog;
use App\Models\Title;
use App\Models\TitleRequest;
use App\Models\TitleView;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoActivitySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first();
        $moderator = User::query()->where('email', 'moderator@example.com')->first();
        $reviewer = $moderator ?? $admin;
        $users = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'user'))
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $primaryUser = $users->first();
        $secondaryUser = $users->get(1) ?? $primaryUser;
        $thirdUser = $users->get(2) ?? $secondaryUser;

        $this->seedTitleRequests($primaryUser, $secondaryUser, $thirdUser, $reviewer);
        $this->seedSearchLogsWithoutTitles($primaryUser);

        $titles = Title::query()
            ->where('is_published', true)
            ->orderByDesc('popularity')
            ->orderBy('id')
            ->get();

        if ($titles->isEmpty()) {
            return;
        }

        $movie = $titles->firstWhere('tmdb_type', 'movie') ?? $titles->first();
        $series = $titles->firstWhere('tmdb_type', 'tv') ?? $titles->skip(1)->first() ?? $movie;
        $catalog = collect([$movie, $series, $titles->skip(2)->first()])
            ->filter()
            ->unique('id')
            ->values();

        $this->seedFavorites($catalog, $primaryUser, $secondaryUser, $thirdUser);
        $comments = $this->seedComments($movie, $series, $primaryUser, $secondaryUser, $reviewer);
        $this->seedReports($movie, $primaryUser, $secondaryUser, $admin, $reviewer, $comments);
        $this->seedSearchLogsWithTitles($catalog, $primaryUser, $secondaryUser);
        $this->seedTitleViews($catalog, $primaryUser, $secondaryUser);
    }

    private function seedTitleRequests(
        User $primaryUser,
        User $secondaryUser,
        User $thirdUser,
        ?User $reviewer,
    ): void {
        $requests = [
            [
                'user' => $primaryUser,
                'requested_title' => 'The Matrix',
                'requested_type' => 'movie',
                'message' => 'Please add the original 1999 movie.',
                'status' => 'pending',
            ],
            [
                'user' => $secondaryUser,
                'requested_title' => 'Dark',
                'requested_type' => 'tv',
                'message' => 'The German series would fit the catalog really well.',
                'status' => 'approved',
            ],
            [
                'user' => $thirdUser,
                'requested_title' => 'Arcane',
                'requested_type' => 'tv',
                'message' => 'Animation request for the series section.',
                'status' => 'rejected',
            ],
        ];

        foreach ($requests as $request) {
            $user = $request['user'];

            TitleRequest::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'requested_title' => $request['requested_title'],
                    'requested_type' => $request['requested_type'],
                ],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'message' => $request['message'],
                    'status' => $request['status'],
                    'reviewed_by_user_id' => $request['status'] === 'pending' ? null : $reviewer?->id,
                ]
            );
        }
    }

    private function seedSearchLogsWithoutTitles(User $primaryUser): void
    {
        SearchLog::updateOrCreate(
            [
                'user_id' => null,
                'query' => 'zz-demo-missing-title',
                'selected_title_id' => null,
            ],
            [
                'results_count' => 0,
                'searched_at' => now()->subDays(2)->setTime(11, 15),
            ]
        );

        SearchLog::updateOrCreate(
            [
                'user_id' => $primaryUser->id,
                'query' => 'qq-demo-no-match',
                'selected_title_id' => null,
            ],
            [
                'results_count' => 0,
                'searched_at' => now()->subDay()->setTime(18, 5),
            ]
        );
    }

    private function seedFavorites(Collection $catalog, User $primaryUser, User $secondaryUser, User $thirdUser): void
    {
        $pairs = [
            [$primaryUser, $catalog->get(0)],
            [$primaryUser, $catalog->get(1)],
            [$secondaryUser, $catalog->get(1)],
            [$thirdUser, $catalog->get(0)],
        ];

        foreach ($pairs as [$user, $title]) {
            if (! $user || ! $title) {
                continue;
            }

            Favorite::firstOrCreate([
                'user_id' => $user->id,
                'title_id' => $title->id,
            ]);
        }
    }

    private function seedComments(
        ?Title $movie,
        ?Title $series,
        User $primaryUser,
        User $secondaryUser,
        ?User $reviewer,
    ): array {
        $discussionTitle = $movie ?? $series;
        $replyAuthor = $reviewer ?? $secondaryUser;

        $mainComment = Comment::updateOrCreate(
            [
                'user_id' => $primaryUser->id,
                'title_id' => $discussionTitle?->id,
                'parent_id' => null,
                'content' => 'Loved the pacing and the atmosphere. This one deserves more attention.',
            ],
            [
                'is_approved' => true,
            ]
        );

        $replyComment = Comment::updateOrCreate(
            [
                'user_id' => $replyAuthor?->id,
                'title_id' => $discussionTitle?->id,
                'parent_id' => $mainComment->id,
                'content' => 'Pinned for review. Great example of the type of discussion we want here.',
            ],
            [
                'is_approved' => true,
            ]
        );

        $reportedComment = Comment::updateOrCreate(
            [
                'user_id' => $secondaryUser->id,
                'title_id' => $discussionTitle?->id,
                'parent_id' => null,
                'content' => 'This stream is fake, click my profile for the real link.',
            ],
            [
                'is_approved' => true,
            ]
        );

        $hiddenComment = Comment::updateOrCreate(
            [
                'user_id' => $secondaryUser->id,
                'title_id' => ($series ?? $discussionTitle)?->id,
                'parent_id' => null,
                'content' => 'Posting spoilers in every thread makes the whole comment section worse.',
            ],
            [
                'is_approved' => false,
            ]
        );

        return [
            'main' => $mainComment,
            'reply' => $replyComment,
            'reported' => $reportedComment,
            'hidden' => $hiddenComment,
        ];
    }

    private function seedReports(
        ?Title $movie,
        User $primaryUser,
        User $secondaryUser,
        ?User $admin,
        ?User $reviewer,
        array $comments,
    ): void {
        $reportedComment = $comments['reported'] ?? null;
        $mainComment = $comments['main'] ?? null;
        $hiddenComment = $comments['hidden'] ?? null;

        if ($reportedComment) {
            CommentReport::updateOrCreate(
                [
                    'comment_id' => $reportedComment->id,
                    'reported_by_user_id' => $primaryUser->id,
                    'reason' => 'Spam or misleading',
                ],
                [
                    'comment_snapshot' => $reportedComment->content,
                    'parent_comment_snapshot' => $reportedComment->parent?->content,
                    'comment_author_snapshot' => $reportedComment->user?->name,
                    'title_snapshot' => $reportedComment->title?->name,
                    'status' => 'pending',
                    'reviewed_by_user_id' => null,
                    'review_note' => null,
                ]
            );
        }

        if ($mainComment && $admin) {
            CommentReport::updateOrCreate(
                [
                    'comment_id' => $mainComment->id,
                    'reported_by_user_id' => $admin->id,
                    'reason' => 'Off-topic',
                ],
                [
                    'comment_snapshot' => $mainComment->content,
                    'parent_comment_snapshot' => $mainComment->parent?->content,
                    'comment_author_snapshot' => $mainComment->user?->name,
                    'title_snapshot' => $mainComment->title?->name,
                    'status' => 'kept',
                    'reviewed_by_user_id' => $reviewer?->id,
                    'review_note' => 'Reviewed and kept visible.',
                ]
            );
        }

        if ($hiddenComment && $admin) {
            CommentReport::updateOrCreate(
                [
                    'comment_id' => $hiddenComment->id,
                    'reported_by_user_id' => $admin->id,
                    'reason' => 'Spoilers without warning',
                ],
                [
                    'comment_snapshot' => $hiddenComment->content,
                    'parent_comment_snapshot' => $hiddenComment->parent?->content,
                    'comment_author_snapshot' => $hiddenComment->user?->name,
                    'title_snapshot' => $hiddenComment->title?->name,
                    'status' => 'hidden',
                    'reviewed_by_user_id' => $reviewer?->id,
                    'review_note' => 'Hidden from the public thread after moderation.',
                ]
            );
        }

        CommentReport::updateOrCreate(
            [
                'comment_id' => null,
                'reported_by_user_id' => $secondaryUser->id,
                'reason' => 'Harassment',
            ],
            [
                'comment_snapshot' => 'Removed demo comment kept only for the moderation audit trail.',
                'parent_comment_snapshot' => null,
                'comment_author_snapshot' => $primaryUser->name,
                'title_snapshot' => $movie?->name ?? 'Imported title',
                'status' => 'deleted',
                'reviewed_by_user_id' => $reviewer?->id,
                'review_note' => 'Removed entirely after moderation review.',
            ]
        );
    }

    private function seedSearchLogsWithTitles(Collection $catalog, User $primaryUser, User $secondaryUser): void
    {
        $primaryTitle = $catalog->first();
        $secondaryTitle = $catalog->get(1) ?? $primaryTitle;

        if (! $primaryTitle) {
            return;
        }

        SearchLog::updateOrCreate(
            [
                'user_id' => $primaryUser->id,
                'query' => $primaryTitle->name,
                'selected_title_id' => $primaryTitle->id,
            ],
            [
                'results_count' => $this->countMatchingTitles($primaryTitle->name),
                'searched_at' => now()->subHours(7),
            ]
        );

        if ($secondaryTitle) {
            SearchLog::updateOrCreate(
                [
                    'user_id' => $secondaryUser->id,
                    'query' => $secondaryTitle->name,
                    'selected_title_id' => $secondaryTitle->id,
                ],
                [
                    'results_count' => $this->countMatchingTitles($secondaryTitle->name),
                    'searched_at' => now()->subHours(3),
                ]
            );
        }
    }

    private function seedTitleViews(Collection $catalog, User $primaryUser, User $secondaryUser): void
    {
        $primaryTitle = $catalog->first();
        $secondaryTitle = $catalog->get(1) ?? $primaryTitle;

        if (! $primaryTitle) {
            return;
        }

        TitleView::updateOrCreate(
            [
                'title_id' => $primaryTitle->id,
                'user_id' => $primaryUser->id,
            ],
            [
                'ip_hash' => hash('sha256', 'demo-user-primary'),
                'viewed_at' => now()->subHours(5),
            ]
        );

        TitleView::updateOrCreate(
            [
                'title_id' => $primaryTitle->id,
                'user_id' => null,
                'ip_hash' => hash('sha256', 'demo-guest-view'),
            ],
            [
                'viewed_at' => now()->subHours(2),
            ]
        );

        if ($secondaryTitle) {
            TitleView::updateOrCreate(
                [
                    'title_id' => $secondaryTitle->id,
                    'user_id' => $secondaryUser->id,
                ],
                [
                    'ip_hash' => hash('sha256', 'demo-user-secondary'),
                    'viewed_at' => now()->subHour(),
                ]
            );
        }
    }

    private function countMatchingTitles(string $query): int
    {
        return Title::query()
            ->where('is_published', true)
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('name', 'like', '%' . $query . '%')
                    ->orWhere('original_name', 'like', '%' . $query . '%');
            })
            ->count();
    }
}
