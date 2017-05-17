@php
    $authUser = auth()->user();
@endphp
<div class="userbadge">
    <a href="{{ route('user', ['slug' => $user->slug]) }}">
        @if ($user->thumbnail)
            <img src="{{ $user->thumbnail }}" alt="{{ $user->name }}" title="{{ $user->name }}">
        @else
            <img src="//static.themusictank.com/assets/images/placeholder.png" alt="{{ $user->name }}" title="{{ $user->name }}">
        @endif
    </a>
    <h3>
        <a href="{{ route('user', ['slug' => $user->slug]) }}">
            {{ $user->name }}
        </a>
    </h3>

    @if ($authUser && $authUser->id !== $user->id)
        <follow user-id="{{ $user->id }}"></follow>
    @endif
</div>
