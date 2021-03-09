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
    <title>Pedido</title>
</head>
<body>
    <h1>Datos básicos del pedido</h1>
    <table>
        <tbody>
            <tr>
                <th width="30%">Proveedor:</th>
                <td>{{$proveedor}}</td>
            </tr>
            <tr>
                <th width="30%">Fecha en la que se realizó el pedido:</th>
                <td>{{$fecha_pedido}}</td>
            </tr>
            <tr>
                <th width="30%">Fecha estimada de arribo del pedido:</th>
                <td>{{$fecha_arribo}}</td>
            </tr>
        </tbody>
    </table>
    <h1>Detalles del pedido</h1>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio de costo</th>
                <th>Subtotal</th>
                <th>Verificar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detallePedido as $detalle)
                <tr>
                    <td style="height: 15px;" width="50%">{{$detalle->Descripcion}}</td>
                    <td>{{$detalle->cantidad}}</td>
                    <td>${{$detalle->precio_costo}}</td>
                    <td>${{$detalle->subtotal}}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h2>Total de la compra: ${{$total}}</h2>
</body>
</html>
