@push('header')
    @if (isset($entity) && (bool)$entity->thumbnail)
        <style type="text/css">
            .ctrl-cover-image .blur { background-image: url({{ $entity->getThumbnailUrl("blur_mobile") }}); }
            .ctrl-cover-image .cover { background-image: url({{ $entity->getThumbnailUrl("cover_mobile") }}); }

            @media (min-width: 501px) {
                .ctrl-cover-image .blur { background-image: url({{ $entity->getThumbnailUrl("blur") }}); }
                .ctrl-cover-image .cover { background-image: url({{ $entity->getThumbnailUrl("cover") }}); }
            }
        </style>
    @endif
@endpush

@if (isset($entity) && (bool)$entity->thumbnail)
    <cover-image
        cover="{{ $entity->getThumbnailUrl("cover") }}"
        cover-mobile="{{ $entity->getThumbnailUrl("cover_mobile") }}"
        blur="{{ $entity->getThumbnailUrl("blur") }}"
        blur-mobile="{{ $entity->getThumbnailUrl("blur_mobile") }}"
        thumbnail="{{ $entity->getThumbnailUrl("thumb") }}"
        thumbnail-mobile="{{ $entity->getThumbnailUrl("thumb_mobile") }}"
        hex="{{ $entity->hex }}"
        alt="{{ $entity->name }}">
    </cover-image>
@endif
