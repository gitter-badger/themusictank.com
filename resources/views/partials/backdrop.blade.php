{{-- @push('header')
    @if (isset($entity) && (bool)$entity->thumbnail)
        <style type="text/css">
            .ctrl-cover-image .blurred { background-image: url({{ $entity->getThumbnailUrl("blur_mobile") }}); }
            /*.ctrl-cover-image .clean { background-image: url({{ $entity->getThumbnailUrl("cover_mobile") }}); }*/

            @media (min-width: 501px) {
                .ctrl-cover-image .blurred { background-image: url({{ $entity->getThumbnailUrl("blur") }}); }
                .ctrl-cover-image .clean { background-image: url({{ $entity->getThumbnailUrl("cover") }}); }
            }
        </style>
    @endif
@endpush

<cover-image url="{{ (bool)$entity->thumbnail ? $entity->getThumbnailUrl("cover_mobile") : "" }}" class="cover-image loading">
    @if ((bool)$entity->thumbnail)
        <img src="{{ $entity->getThumbnailUrl("blur_mobile") }}">
    @endif
</cover-image> --}}
