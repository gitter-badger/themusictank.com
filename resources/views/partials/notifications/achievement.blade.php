<h3>
    <a href="{{ route('achievement', ['id' => $notification->associated_object->slug])  }}">
        {{ $notification->associated_object->name }}
    </a>
</h3>
<p>
    <blockquote>
        {{ $notification->associated_object->description }}
    </blockquote>
</p>
