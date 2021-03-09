@foreach($sucursal as $sucu)

@if($carrito->contains('idsucursal',$sucu->id))
<div class="form-group">
<h1>{{$sucu->sucursal}}</h1>
<a class="btn btn-primary" target="_blank"href="{{url('realiza_transferencia',$sucu->id)}}" style="color:white !important" identi="{{$sucu->id}}">Realizar Transferencia</a>
</div>

<table class="table table-bordered">

    <thead>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Acciones</th>
    </thead>
    <tbody>

        <tr>
        @foreach($carrito as $item)
            @if($item->idsucursal == $sucu->id)
            <td>{{$item->Descripcion}}</td>
            <td>{{$item->cantidad}}</td>
            <td><button identi="{{$item->id}}" id="Eliminar" class="btn btn-danger">Eliminar</button></td>
            @endif
        </tr>
        
    </tbody>

    @endforeach
    
</table>
@endif
@endforeach
@if($carrito->isEmpty())
    <h2 class="mt-5 ml-5" style="color:red !important"> <b> No Hay transferencias Activas </b></h2>
@endif