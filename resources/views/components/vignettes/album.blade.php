<div class="vignette album-vignette">
    @if (isset($score) && (bool)$score)
        <div class="badges clear">
            @include('components.scores.percent-badge', ['label' => "Global", 'percent' => $album->globalScore()])
            @if(!is_null(auth()->user()))
                @include('components.scores.percent-badge', ['label' => "Subs", 'percent' => $album->subsScore(auth()->user())])
            @endif
        </div>
    @endif

    <div class="picture">
        <a href="{{ route('album', ['slug' => $album->slug]) }}">
            <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
        </a>
    </div>
    <div class="name">
        <h3><a href="{{ route('album', ['slug' => $album->slug]) }}">{{ $album->name }}</a></h3>
        @include('components.buttons.upvote', ['type' => "album", 'id' => $album->id])
    </div>
</div>
