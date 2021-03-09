<!doctype html>
<html lang="en">

<head>
    <style>
    * {
        font-family: sans-serif;
        font-size: 14px;
    }

    h1 {
        font-size: 15px;
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

    td,
    th {
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
        /* border-left: 2px dotted #444;*/
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

    .infoEnvio {
        width: 100%;
        height: 120px;
        border: 1px solid black;


    }

    .cabecera {
        text-align: center;
        width: 5%;
        border: 1px solid;

        border-style: hidden solid solid solid;
        margin: auto;
    }



    div#izquierda {
        width: 30%;
        float: left;
        text-align: center
    }

    div#derecha {
        width: 30%;
        float: left;
        margin-left: 80%;
    }

    .datosRemito {
        float: right;
        margin-right: 35px;
        margin-top: -30px;


    }

    .datosRemito p {
        font-family: sans-serif;
        font-size: 10px;
    }


    div#valido {
        margin-top: 35px;
        text-align: center;
    }

    .datosSucursal {
        float: left;
        margin-left: 35px;
    }

    .datosSucursal p {
        font-family: sans-serif;
        font-size: 10px;
        margin-top: 20px;
    }

    .datosSucursal img {
        margin-top: 8px;
    }

    .destino {
        width: 100%;
        height: 120px;
        margin-top: -15px;
        border: 1px solid;
        border-style: hidden solid solid solid;


    }

    .destino p {
        padding: 0.3rem;

    }

    .data {
        width: 100%;
        height: auto;
        margin:5px;


    }

    .data table {
        margin-top: 25px;
    }

    .subtotal {
        width: 100%;
        padding:10px;
    }

    #total {
        text-align: right;
    }

    .footer {
        width: 100%;
        height: 120px;
        border: 1px solid;
        border-style: solid solid solid solid;
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
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
            <div class="col">
                <div class="infoEnvio">
                    <div class="cabecera">
                        <div>
                            <h1>R</h1>
                        </div>
                    </div>
                    <div class="datosRemito">
                        <p>REMITO N°:{{$remito}} </p>
                        <p>CUIT: 20-30275892-5</p>
                        <p>Nro ING. Brutos: 541534816</p>
                        <p>Inicio Actividades: 22/10/2010</p>
                    </div>
                    <div class="datosSucursal">
                        <img src="img/logo1.png" alt="">

                        <p>IVA - RESPONSABLE INSCRIPTO</p>
                    </div>
                    <div id=valido>
                        <p>Documento No valido como factura</p>
                    </div>
                </div>


            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="destino">
                    <div style="float=left;margin:auto;">

                        <p><strong>Destinatario</strong>: Senda SRL</p>
                        <p> <strong> Domicilio:</strong> Aristobulo del valle 1343</p>
                        <p><strong>Ciudad</strong>: Comodoro Rivadavia</p>
                    </div>
                    <div style="float:right;margin-right:30px;">

                        <p><strong>CUIT:</strong> 20342758925</p>
                    </div>

                </div>


            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="data">
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <th style="text-align:center">Descripcion</th>
                                <th style="text-align:center">Bultos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Articulos de Limpieza</td>
                                <td style="text-align:center">{{$bultos}}</td>

                            </tr>

                        </tbody>

                    </table>

                </div>


            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="subtotal">
                    <table>
                        <tfoot>
                            <tr>
                                <th id="total" colspan="1">Total Bultos :</th>
                                <td style="text-align:center">{{$bultos}}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>


            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="footer">
                    <div style="float:right; margin-right:150px">
                        <p>CAI N°:</p>
                        <br>
                        <p>Fecha Vto:</p>

                    </div>


                </div>


            </div>
        </div>

    </div>
</body>

</html>