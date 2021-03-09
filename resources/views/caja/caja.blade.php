@extends('welcome')
@section("title")
Caja
@endsection
@section('contenido')


<caja-component>
    @can('test')

    @endcan
</caja-component>

@endsection
@section('script')
@endsection
