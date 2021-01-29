@extends('layouts.layout')

@section('content')
    <!-- Start App for authorized users -->
    @auth
        <div id="app">
            <app-core></app-core>
        </div>
    @endauth
    <!-- End Auth -->
    <div class="background__wrap pos-cover"></div>
@endsection
