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

    @php
        $authUser = auth()->user();
    @endphp
    @if ($authUser && $authUser->id !== $user->id)
        <follow user-id="{{ $user->id }}"></follow>
    @endif
</div>
