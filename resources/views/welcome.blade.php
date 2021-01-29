@extends( 'layouts.landing' )

@section( 'page', 'landing' )

@section( 'main' )

    <div class="landing-main-logo">
        @svg('logo')
    </div>

    <div class="splash__buttons p-5 width-30 pull-center">
        <a class="btn splash__button mb-3 d-block" href="{{ route( 'login') }}">LOGIN</a>
        <a class="btn splash__button d-block" href="{{ route( 'register') }}">REGISTER</a>
    </div>

@endsection
