<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto - #{{$presuNro}}</title>

    <style type="text/css">
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
        }
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        table {
            font-size: x-small;
        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }
        .invoice table {
            margin: 15px;
        }
        .invoice h3 {
            margin-left: 15px;
        }
        .information {
            background-color: #ffff80;
            color: #000000;
        }
        .information .logo {
            margin: 5px;
        }
        .information table {
            padding: 10px;
        }
    </style>

</head>
<body>

<div class="information">
    <table width="100%">
        <tr>
            <td align="left" style="width: 40%;">
                <h3>{{$cliente}}</h3>
                <pre>
<br /><br />
Fecha: {{date('d-m-Y')}}
Presupuesto: {{$presuNro}}
</pre>


            </td>
            <td align="center">
                <img src="img/logo1.png" alt="Logo" width="120" class="logo"/>
                <h2>Presupuesto</h2>
            </td>
            <td align="right" style="width: 40%;">

                <h3>Senda SRL</h3>
                <pre>
                    www.sendaservicios.com.ar

                    Edison 165
                    Trelew
                    Chubut
                </pre>
            </td>
        </tr>

    </table>
</div>


<br/>

<div class="invoice">
    <h3>Detalle </h3>
    <table width="100%">
        <thead>
        <tr>
            <th>Articulo</th>
            <th>Cantidad</th>
            <th>Precio U</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($presupuesto as $item)
        <tr>       
            <td>{{$item->Descripcion}}</td>
            <td style="text-align:center">{{$item->cantidad}}</td>
            <td>{{$item->precio_venta_1}}</td>
            <td align="left">${{$item->precio_venta_1 * $item->cantidad}}</td>
        </tr>
        @endforeach
        </tbody>

        <tfoot>
        <tr>
            <td colspan="1"></td>
            <td colspan="1"></td>
            <td align="left" style="background-color:#ffff80;text-align:center">Total</td>
            <td align="left" style="background-color:#ffff80" class="gray">${{$total}}</td>
        </tr>
        </tfoot>
    </table>
</div>

<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="left" style="width: 50%;">
                Presupuesto valido por 7 dias - IVA Incluido
            </td>
            <td align="right" style="width: 50%;">
                Senda SRL
            </td>
        </tr>

    </table>
</div>
</body>