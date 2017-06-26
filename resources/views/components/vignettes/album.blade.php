<div class="vignette album-vignette">
    <div class="picture">
        <a href="{{ route('album', ['slug' => $album->slug]) }}">
            <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
        </a>
    </div>
    <div class="name">
        <h3><a href="{{ route('album', ['slug' => $album->slug]) }}">{{ $album->name }}</a></h3>
        @include('components.buttons.upvote', ['type' => "album", 'id' => $album->id])
    </div>

    @if (isset($score) && (bool)$score)
        @include('components.scores.percent-badge', ['label' => "Global", 'percent' => $album->globalScore()])
        @include('components.scores.percent-badge', ['label' => "Subs", 'percent' => $album->subsScore()])
    @endif
</div>
