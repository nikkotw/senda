<!doctype html>
<html lang="en">

<head>
    <style>
    * {
        font-family: sans-serif;
        font-size: 14px;
    }
    h1{
        font-size: 10px;
    }
    body {
        font-family: sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    tr:nth-of-type(odd) {
        background: #eee;
    }
    th {
        background: #333;
        color: white;
        font-weight: bold;
    }
    td, th {
        text-align: left;
    }
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }

    .row {

        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col {
        -ms-flex-preferred-size: 0;
        flex-basis: 0;
        -ms-flex-positive: 1;
        flex-grow: 1;
        max-width: 100%;
    }

    body:after {
        content: "";
        position: absolute;
        z-index: -1;
        top: 50;
        bottom: 0;
        left: 50%;
        border-left: 2px dotted #444;
    }

    .check {
        width: 5px;
        height: 5px;
        margin-left: 2rem;

    }

    .derecho {
        float: right;
    }

    .table {
        width: 45%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.3rem;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
        padding: 0.6rem;
    }

    .table-bordered thead th,
    .table-bordered thead td {
        border-bottom-width: 2px;
    }

    .table-borderless th,
    .table-borderless td,
    .table-borderless thead th,
    .table-borderless tbody+tbody {
        border: 0;
    }

    h2,
    .h2 {
        font-size: 2rem;
    }

    .firma {
        font-size: 10px;
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div>
                <h3>Envio Mercaderia</h3>
                <h1>Sucursal origen: {{$sucursalOrigen->sucursal}}</h1>
                <h1>Sucursal destino: {{$sucursalDestino->sucursal}}</h1>

                 <table class="table">
                    <thead>
                        <tr>
                            <th width="20%">Producto</th>
                            <th width="5%">Cantidad</th>
                            <th width="5%">Checking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $item)
                        <tr>
                            <td>{{$item->Descripcion}}</td>
                            <td align="center">{{$item->cantidad}}</td>
                            <td>
                                <div class="check"></div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
                <div class="firma">
                    <p>..........................</p>
                    <p>Firma Deposito</p>
                </div>
            </div>
            <div style="float:right">
                <h3>Recepcion Mercaderia</h3>
                <h1>Sucursal origen: {{$sucursalOrigen->sucursal}}</h1>
                <h1>Sucursal destino: {{$sucursalDestino->sucursal}}</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Checking</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $item)
                            <tr>
                                <td>{{$item->Descripcion}}</td>
                                <td>{{$item->cantidad}}</td>
                                <td>
                                    <div class="check"></div>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    <div class="firma">
                        <p>..........................</p>

                        <p>Firma Sucursal</p>
                    </div>

            </div>
        </div>

    </div>
</body>

</html>
