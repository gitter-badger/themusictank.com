<div class="cover-image-wrapper">
    @if (isset($entity) && (bool)$entity->thumbnail)
        <cover-image
            blur="{{ $entity->getThumbnailUrl("blur") }}"
            blur-mobile="{{ $entity->getThumbnailUrl("blur_mobile") }}"
            thumbnail="{{ $entity->getThumbnailUrl("thumb") }}"
            thumbnail-mobile="{{ $entity->getThumbnailUrl("thumb_mobile") }}"
            hex="{{ $entity->hex }}"
            alt="{{ $entity->name }}">
        </cover-image>
    @endif

    <i class="mask radial"></i><i class="mask w-bottom"></i><i class="mask bottom"></i><i class="mask left"></i>
    <i class="mask triangles"></i>

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
