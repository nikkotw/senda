<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        * {
            font-family: sans-serif;
            font-size: 14px;
        }
        h1{
            font-size: 20px;
        }
        body {
            font-family: sans-serif;
        }
        #page-wrap {
            margin: 50px;
        }
        p {
            margin: 20px 0;
        }

        /*
        Generic Styling, for Desktops/Laptops
        */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        /* Zebra striping */
        tr:nth-of-type(odd) {
            background: #eee;
        }
        th {
            background: #333;
            color: white;
            font-weight: bold;
        }
        td, th {
            padding: 6px;
            border: 1px solid #ccc;
            text-align: left;
        }
    </style>
    <title>Productos del proveedor {{$proveedor->nombre}}</title>
</head>
<body>
    <h1>Datos del proveedor</h1>

    <table>
        <tbody>
            <tr>
                <th width="30%">Nombre:</th>
                <td>{{$proveedor->nombre}}</td>
            </tr>
            <tr>
                <th width="30%">CUIT:</th>
                <td>{{$proveedor->cuit}}</td>
            </tr>
            <tr>
                <th width="30%">Teléfono:</th>
                <td>{{$proveedor->telefono}}</td>
            </tr>
            <tr>
                <th width="30%">Email:</th>
                <td>{{$proveedor->email}}</td>
            </tr>
        </tbody>
    </table>
    <h1>Listado de productos</h1>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Stock</th>
                <th>Precio de costo</th>
                <th>Precio 1</th>
                <th>Precio 2</th>
                <th>Precio 3</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td style="height: 15px;" width="50%">{{$producto->Descripcion}}</td>
                    <td>{{$producto->stock}}</td>
                    <td width="12%">${{$producto->precio_costo}}</td>
                    <td>${{$producto->precio_venta_1}}</td>
                    <td>${{$producto->precio_venta_2}}</td>
                    <td>${{$producto->precio_venta_3}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
