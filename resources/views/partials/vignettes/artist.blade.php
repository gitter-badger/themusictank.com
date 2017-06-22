<div class="vignette artist-vignette">
    <div class="picture">
        <a href="{{ route('artist', ['slug' => $artist->slug]) }}">
            <img src="{{ $artist->getThumbnailUrl() }}" alt="{{ $artist->name }}">
        </a>
    </div>
    <div class="name">
        <a href="{{ route('artist', ['slug' => $artist->slug]) }}">{{ $artist->name }}</a>
    </div>
</div>
