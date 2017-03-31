<ul class="profile">
@if(isset($user))
    <li><a href="{{ action('ProfileController@dashboard') }}">Dashboard</a></li>
    <li>
        <span class="notifications-bell">
            <a href="{{ action('NotificationController@index') }}">
                <i class="fa fa-bell"></i>
                <em>--</em>
            </a>
        </span>
    </li>
    <li><a href="{{ action('ProfileController@show', ['id' => $user->getProfile()->slug]) }}">You</a></li>
    <li>
        <i class="fa fa-cog"></i>
        <ul>
            <li><a href="{{ action('ProfileController@edit') }}">Settings</a></li>
            <li><a href="{{ action('Auth\LoginController@logout') }}">Logout</a></li>
        </ul>
    </li>
    @if ($user->isAdmin())
        <li>
            <i class="fa fa-sliders"></i>
            <ul>
                <li><a href="{{ action('AdminController@console') }}">Admin Console</a></li>
            </ul>
        </li>
    @endif
@else
    <li><a href="{{ action('ProfileController@auth') }}">Your account</a></li>
@endif
</ul>
