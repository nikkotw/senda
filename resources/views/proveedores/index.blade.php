@extends('welcome')
@section("title")
Proveedores
@endsection
@section('contenido')

@if(Session::has('successMsg'))
    <script>
        window.onload = function(){
            alertify.success('Operación realizada correctamente');
        }
    </script>
@endif

<div class="container-fluid">
    <proveedor-component></proveedor-component>
</div>

@endsection
@section('script')

@endsection
