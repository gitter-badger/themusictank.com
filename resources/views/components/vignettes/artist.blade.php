<div class="vignette artist-vignette">
    <div class="picture">
        <a href="{{ route('artist', ['slug' => $artist->slug]) }}">
            <img src="{{ $artist->getThumbnailUrl() }}" alt="{{ $artist->name }}">
        </a>
    </div>
    <div class="name">
        <h3><a href="{{ route('artist', ['slug' => $artist->slug]) }}">{{ $artist->name }}</a></h3>
        @include('components.buttons.upvote', ['type' => "artist", 'id' => $artist->id])
    </div>
</div>
