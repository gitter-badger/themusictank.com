<div class="album-details album-details-{{ $album->id }} clear">
    <i class="mask radial"></i><i class="mask top"></i><i class="mask left"></i><i class="mask right"></i>
    <h3>
        <a href="{{ route('album', ['slug' => $album->slug]) }}">{{ $album->name }}</a>
        @include('components.buttons.upvote', ['type' => "album", 'id' => $album->id])
    </h3>
    <div class="details">
        @include('components.vignettes.album', ['album' => $album, 'name' => false])
        <div class="scores">
            @if(!is_null(auth()->user()))
                @include('components.scores.pct', ['label' => "You", 'percent' => $album->score(auth()->user())])
                @include('components.scores.pct', ['label' => "Subscriptions", 'percent' => $album->subsScore(auth()->user())])
            @endif
            @include('components.scores.pct', ['label' => "Global", 'percent' => $album->globalScore()])
        </div>
        <div class="charts">
            <line-chart object-id="{{ $album->id }}" datasource="{{ is_null(auth()->user()) ? 'global' : 'global,subscriptions,user' }}"></line-chart>
            <a href="{{ route('album', ['slug' => $album->slug]) }}">View details <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</div>

@push('app-styles')

    @if (!is_null($album->hex))
        .album-details-{{ $album->id }} a { color: {{ $album->hex }}; }
    @endif

    @if ((bool)$album->thumbnail)
        .album-details-{{ $album->id }} { background-image: url({{ $album->getThumbnailUrl("blur_mobile") }}); }
        @media (min-width: 501px) {
            .album-details-{{ $album->id }} { background-image: url({{ $album->getThumbnailUrl("blur") }}); }
        }
    @else
        .album-details-{{ $album->id }} { border-bottom: 1px solid #eee; }
    @endif

@endpush
