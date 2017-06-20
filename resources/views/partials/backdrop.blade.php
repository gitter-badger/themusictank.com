@push('header')
    @if (isset($entity))
        <style type="text/css">
            .backdrop .blurred { background-image: url({{ $entity->getThumbnailUrl("mobile_blur") }}); }
            .backdrop .clean { background-image: url({{ $entity->getThumbnailUrl("mobile_cover") }}); }

            @media (min-width: 501px) {
                .backdrop .blurred { background-image: url({{ $entity->getThumbnailUrl("blur") }}); }
                .backdrop .clean { background-image: url({{ $entity->getThumbnailUrl("cover") }}); }
            }
        </style>
    @endif
@endpush

<div class="backdrop dazzeling"><i class="blurred"></i><i class="clean"></i><i class="mask"></i></div>
