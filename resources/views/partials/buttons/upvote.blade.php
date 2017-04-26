 @if (!is_null(auth()->user()))
    <upvote type="{{ $type }}" object-id="{{ $id }}"></upvote>
 @endif
