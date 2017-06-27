<div class="album-details album-details-{{ $album->id }} clear">
    <i class="radial"></i>
    <i class="top-fade"></i>
    <i class="left-fade"></i>
    <i class="right-fade"></i>

    <h3>
        <a href="{{ route('album', ['slug' => $album->slug]) }}">{{ $album->name }}</a>
        @include('components.buttons.upvote', ['type' => "album", 'id' => $album->id])
    </h3>

    <div class="details">
        <div class="vignette album-vignette">
            <div class="picture">
                <a href="{{ route('album', ['slug' => $album->slug]) }}"><img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}"></a>
            </div>
        </div>
        <div class="scores">
            @if(!is_null(auth()->user()))
                @include('components.scores.pct', ['label' => "You", 'percent' => $artist->score(auth()->user())])
                @include('components.scores.pct', ['label' => "Subscriptions", 'percent' => $artist->subsScore(auth()->user())])
            @endif
            @include('components.scores.pct', ['label' => "Global", 'percent' => $artist->globalScore()])
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
    @endif
@endpush
