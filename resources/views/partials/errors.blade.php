@if ( $errors->count() > 0 )
    <ul class="error">
    @foreach( $errors->all() as $message )
        <li>{{ $message }}</li>
    @endforeach
    </ul>
@endif