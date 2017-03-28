 @if (!is_null(auth()->user()))
    {{ Form::open([
        'data-ctrl' => "upvote-widget",
        'action' => ["AjaxController@upvote", $type],
        'data-ctrl-mode' => "ajax",
        'data-upvote-type' => $type,
        'data-upvote-id' => $id
    ]) }}
        {{ Form::hidden('id', $id) }}
        <ul>
            <li>{{ Form::button('Like', ['disabled' => 'disabled', 'class' => 'up', 'type' => 'submit', 'name' => 'type', 'value' => 1]) }}</li>
            <li>{{ Form::button('Dislike', ['disabled' => 'disabled', 'class' => 'down', 'type' => 'submit', 'name' => 'type', 'value' => 2]) }}</li>
        </ul>
    {{ Form::close() }}
 @endif
