@if(isset($user))
    <li><a href="{{ action('ProfileController@dashboard') }}">Dashboard</a></li>
    <li><a href="{{ action('NotificationController@index') }}"><i class="fa fa-bell-o">Notifications</i></a></li>
    <li><a href="{{ action('ProfileController@show', ['id' => 'fake']) }}">You</a></li>
    <li>
        <i class="fa fa-cog"></i>
        <ul>
            <li><a href="{{ action('ProfileController@edit') }}">Settings</a></li>
            <li><a href="{{ action('Auth\LoginController@logout') }}">Logout</li>
        </ul>
    </li>
    @if ($user->isAdmin())
        <li>
            <i class="fa fa-sliders"></i>
            <ul>
                <li><?php echo $this->Html->link(__("Console"), ['controller' => 'tmt', 'action' => 'index']); ?></li>
            </ul>
        </li>
    @endif
@else
    <li><a href="{{ action('ProfileController@auth') }}">Your account</a></li>
@endif
