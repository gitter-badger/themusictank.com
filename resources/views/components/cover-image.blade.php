<div class="cover-image-wrapper">
    @if (isset($entity) && (bool)$entity->thumbnail)
        <cover-image
            thumbnail="{{ $entity->getThumbnailUrl("thumb") }}"
            thumbnail-mobile="{{ $entity->getThumbnailUrl("thumb_mobile") }}"
            hex="{{ $entity->hex }}"
            alt="{{ $entity->name }}">
        </cover-image>
    @endif

    <i class="mask"></i><i class="radial"></i><i class="wrapper-bottom-gradient"></i>

    <section class="cover-image-content">
        {{ $slot }}
    </section>
</div>

@push('app-styles')
    @if (!is_null($entity->hex) && !empty($entity->hex))
        .cover-image-wrapper { background-color: {{ $entity->hex }}; }
        .cover-image-wrapper h1 a { color: {{ $entity->hex }}; }

    @endif
    @if ((bool)$entity->thumbnail)
        .ctrl-cover-image .blur { background-image: url({{ $entity->getThumbnailUrl("blur_mobile") }}); }
        .ctrl-cover-image .cover { background-image: url({{ $entity->getThumbnailUrl("cover_mobile") }}); }

        @media (min-width: 501px) {
            .ctrl-cover-image .blur { background-image: url({{ $entity->getThumbnailUrl("blur") }}); }
            .ctrl-cover-image .cover { background-image: url({{ $entity->getThumbnailUrl("cover") }}); }
        }
    @endif
@endpush
