@extends('welcome')
@section("title")
Alta Clientes
@endsection
@section('contenido')

<h1><strong>Crear Cliente</strong></h1>

@if ($errors->any())
    <div class="container">
        <div class="alert alert-danger alert-dismissible">
            <ul style="list-style-type: none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
<!--{!! $errors->first('razon', ' is-invalid') !!}-->

    <div class="container">
        <div class="row">
            <div class="col-sm">
            <form class="form-group" method="POST" action="/CargaCliente">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="razon">Razon Social</label>
                            <input type="text" class="form-control" name="razon" id="razon" value="{{old('razon')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Cuit">CUIT</label>
                            <input type="text" class="form-control" name="cuit" id="cuit" value="{{old('cuit')}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefono">Telefono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono" value="{{old('telefono')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{old('email')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="direccion" value="{{old('direccion')}}">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" class="form-control" name="ciudad" id="ciudad" value="{{old('ciudad')}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cp">Codigo Postal</label>
                            <input type="text" class="form-control" name="cp" id="cp" value="{{old('cp')}}">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="obs">Observaciones</label>
                            <textarea class="form-control blockl" name="obs" id="obs" style="resize: none;" value="{{old('obs')}}"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
@endsection
