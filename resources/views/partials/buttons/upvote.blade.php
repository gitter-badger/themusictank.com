 @if (!is_null(auth()->user()))
    <upvote
        upvote-href="{{ route('upvote-' . $type)}}"
        deupvote-href="{{ route('deupvote-' . $type)}}"
        type="{{ $type }}"
        object-id="{{ $id }}"
    ></upvote>
 @endif
