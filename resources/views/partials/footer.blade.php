<footer>
    <nav>
        <ul class="social">
            <li><a href="http://www.facebook.com/themusictank/" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook"></i></a></li>
            <li><a href="http://www.twitter.com/themusictank/" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://plus.google.com/117543200043480372792" target="_blank" rel="noopener noreferrer"><i class="fa fa-google-plus"></i></a></li>
        </ul>

        <ul class="internal">
            <li><a href="{{ route('achievements') }}">Achievements</a></li>
            <li><a href="{{ route('about') }}">About</a></li>
            <li><a href="{{ route('legal') }}">Legal</a></li>
            <li>
                @include('components.buttons.bugreport', ['identity' => "general", 'location' => Request::url(), 'label' => "Found a bug?"])
            </li>
        </ul>
    </nav>
    <p class="copyright">
        1999 - {{ date('Y') }} The Music Tank <a href="https://www.gnu.org/licenses/quick-guide-gplv3.html" target="_blank" rel="noopener noreferrer">GPL-3.0</a>
    </p>
</footer>
