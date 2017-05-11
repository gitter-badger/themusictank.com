<div class="userbadge">
    <a href="{{ action('UserController@show', ['slug' => $user->slug]) }}">
        @if ($user->thumbnail)
            <img src="{{ $user->thumbnail }}" alt="{{ $user->name }}" title="{{ $user->name }}">
        @else
            <img src="//static.themusictank.com/assets/images/placeholder.png" alt="{{ $user->name }}" title="{{ $user->name }}">
        @endif
    </a>

    <h3>
        <a href="{{ action('UserController@show', ['slug' => $user->slug]) }}">
            {{ $user->name }}
        </a>
    </h3>
</div>
