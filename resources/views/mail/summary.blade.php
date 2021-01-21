@if( $new )
    <h3>Locations opened:</h3><br><br>

    @foreach( $new as $location )
        <a href="{{ $location->yelp_url }}">{{ $location->name }}</a><br>
    @endforeach
@endif


@if( $closed )
    <h3>Locations closed:</h3><br><br>

    @foreach( $closed as $location )
        <a href="{{ $location->yelp_url }}">{{ $location->name }}</a><br>
    @endforeach
@endif




