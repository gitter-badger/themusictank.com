@php
    $key = sprintf("%s-%d", $type, $id);
@endphp

{{ Form::open(['data-ctrl' => "upvote-widget", 'data-ctrl-mode' => "ajax", 'action' => ["AjaxController@upvote", $type], 'data-upvote-key' => $key]) }}
    {{ Form::hidden('id', $id) }}
    {{ Form::hidden('artistid', $artistid) }}
    <ul>
        <li>{{ Form::button('Reset', ['class' => 'remove', 'type' => 'submit', 'name' => 'type', 'value' => 0]) }}</li>
        <li>{{ Form::button('Like', ['class' => 'up', 'type' => 'submit', 'name' => 'type', 'value' => 1]) }}</li>
        <li>{{ Form::button('Dislike', ['class' => 'down', 'type' => 'submit', 'name' => 'type', 'value' => 2]) }}</li>
    </ul>
{{ Form::close() }}
