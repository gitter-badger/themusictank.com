<ul class="profile">
@if(isset($user))

    <li class="picture">
        @if ($user->thumbnail)
            <a href="{{ action('UserController@dashboard') }}">
                <img src="{{ $user->thumbnail }}" alt="{{ $user->name }}" title="{{ $user->name }}">
            </a>
        @endif
    </li>
    <li><a href="{{ action('UserController@dashboard') }}">Dashboard</a></li><li>
        <notifier href="{{ action('NotificationController@index') }}"></notifier>
    </li>
    <li>
        <i class="fa fa-cog"></i>
        <ul>
            <li><a href="{{ action('UserController@show', ['id' => $user->slug]) }}">Your page</a></li>
            <li><a href="{{ action('UserController@edit') }}">Settings</a></li>
            <li><a href="{{ action('Auth\AuthController@logout') }}">Logout</a></li>
        </ul>
    </li>
    @if ($user->is_admin)
        <li>
            <i class="fa fa-sliders"></i>
            <ul>
                <li><a href="{{ action('AdminController@console') }}">Admin Console</a></li>
            </ul>
        </li>
    @endif
@else
    <li><a href="{{ action('Auth\AuthController@index') }}">Your account</a></li>
@endif
</ul>
