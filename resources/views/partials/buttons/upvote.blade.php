 @if (!is_null(auth()->user()))
    {{ Form::open([
        'data-ctrl' => "upvote-widget",
        'action' => ["AjaxController@upvote" . ucfirst($type)],
        'data-ctrl-mode' => "ajax",
        'data-upvote-type' => $type,
        'data-upvote-object-id' => $id
    ]) }}
        {{ Form::hidden('id', $id) }}
        {{ Form::hidden('vote') }}
        <ul>
            <li>{{ Form::button('Like', ['disabled' => 'disabled', 'title' => 'Like', 'class' => 'up', 'name' => 'cast', 'value' => 1]) }}</li>
            <li>{{ Form::button('Dislike', ['disabled' => 'disabled', 'title' => 'Dislike', 'class' => 'down', 'name' => 'cast', 'value' => 2]) }}</li>
        </ul>
    {{ Form::close() }}
 @endif
