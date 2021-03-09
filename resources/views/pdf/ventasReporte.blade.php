<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>

    <style type="text/css">
    @page teacher {
     size: A4 portrait;
     margin: 2cm;
        }

        .teacherPage {
            page: teacher;
            page-break-after: always;
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
                <h3>John Doe</h3>
                <pre>
<br /><br />
Fecha: {{date('d-m-Y')}}

</pre>


            </td>
            <td align="center">
                <img src="img/logo1.png" alt="Logo" width="120" class="logo"/>
                <h2>Reporte</h2>
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
            <th>id</th>
            <th>factura</th>
            <th>cliente</th>
            <th>Total</th>
            <th>tipo_venta</th>
            <th>condicion</th>
            <th>fecha</th>
            <th>Vendedor</th>
        </tr>
        </thead>
        <tbody>
        
        @foreach ($ventas as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td >{{$item->factura}}</td>
            <td>{{$item->cliente}}</td>
            <td style="color:green">${{$item->total}}</td>
            <td>{{$item->tipo_venta}}</td>
            <td>{{$item->condicion_venta}}</td>
            <td colspan="2">{{date('d-m-y',strtotime($item->fecha))}}</td>
            <td colspan="2">{{$item->nombre}}</td>
            </tr>
        @endforeach
        
    
        </tbody>
        
        <tfoot>
        <tr>
            
            <td colspan="3" align="left" style="background-color:#ffff80;">Total</td>
            <td colspan="7" align="left" style="background-color:#ffff80" class="gray">${{$total[0]->maximo}}</td>
        </tr>
        </tfoot>
    </table>
</div>

<div class="information" style="position: absolute; bottom: 0;">
    <table width="100%">
        <tr>
            <td align="right" style="width: 50%;">
                Senda SRL
            </td>
        </tr>

    </table>
</div>
</body>