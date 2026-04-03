@foreach($comments as $comment)
    @include('partials.comment-single', ['comment' => $comment, 'depth' => 0])
@endforeach
