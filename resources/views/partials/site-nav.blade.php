<nav>
    <ul class="pages">
        <li><a href="{{ route('artists') }}">Artists</a></li>
    </ul>

    <search-form></search-form>

    <ul class="profile">
        @php
            $user = auth()->user();
        @endphp
        @if(!is_null($user))
            <li class="picture">
                @if ($user->thumbnail)
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ $user->thumbnail }}" alt="{{ $user->name }}" title="{{ $user->name }}">
                    </a>
                @endif
            </li>
            <li>
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li>
                <notifier href="{{ action('NotificationController@index') }}"></notifier>
            </li>
            <li>
                <i class="fa fa-cog"></i>
                <ul>
                    <li><a href="{{ route('user', ['id' => $user->slug]) }}">Your page</a></li>
                    <li><a href="{{ route('profile') }}">Settings</a></li>
                    <li><a href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </li>
            @if ($user->is_admin)
                <li>
                    <i class="fa fa-sliders"></i>
                    <ul>
                        <li><a href="{{ route('admin') }}">Admin Console</a></li>
                    </ul>
                </li>
            @endif
        @else
            <li><a href="{{ route('login') }}">Your account</a></li>
        @endif
    </ul>
</nav>
