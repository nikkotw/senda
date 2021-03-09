<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
    header('Access-Control-Allow-Headers: *');
    @endphp
    <title>Senda - @yield("title")</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="{{url('css/bootstrap.css')}}" crossorigin="anonymous">
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}" crossorigin="anonymous">
    <link rel="stylesheet" href="{{url('css/styles.css')}}">
    <link rel="stylesheet" href="{{url('css/alertify.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('css/datatables.min.css')}}" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/css/mdb.min.css" /> -->
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        .dropdown-submenu {
        position: relative;
        }

        .dropdown-submenu a::after {
        transform: rotate(-90deg);
        position: absolute;
        right: 6px;
        top: .8em;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .example::-webkit-scrollbar {
        display: none;
        }

        /* Hide scrollbar for IE and Edge */
        body {
        -ms-overflow-style: none;
        }

        .dropdown-submenu .dropdown-menu {
        top: 0;
        left: 100%;
        margin-left: .1rem;
        margin-right: .1rem;
        }
    </style>
</head>

<body class="example">
    <div>

        <nav class="navbar navbar-expand-lg navbar-light bg-yellow">
            <a class="navbar-brand " href="{{url('/')}}"> <span class="titulo"> <b> Senda </b></span></a>

            <button class="bg-yellow navbar-toggler" type="button" data-toggle="collapse"
                data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">

                    <li class="nav-item dropdown">
                        <a class="bg-yellow nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Facturación
                        </a>
                        <ul class="dropdown-menu bg-yellow" aria-labelledby="navbarDropdownMenuLink">
                            @canany(['Abrir Caja','Cerrar Caja','Extraer Dinero Caja','Ver Listado Ventas','Ver Listado Ventas CC'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('Caja')}}">Caja</a></li>
                            @endcanany

                            @canany(['Realizar Ventas'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('Ventas')}}">Realizar Ventas</a></li>
                            @endcanany
                            @canany(['Presupuestos'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('presupuestos')}}">Presupuestos</a></li>
                            @endcanany
                            @canany(['Emitir Nota Credito'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('NotasCredito')}}">Notas de crédito</a></li>
                            @endcanany
                            @canany(['Autorizar Ventas CC'])
                            <li class="bg-yellow dropdown-submenu"><a class="bg-yellow dropdown-item dropdown-toggle" href="#">Cuentas Corrientes</a>

                            <ul class="bg-yellow dropdown-menu">
                                @canany(['Autorizar Ventas CC','Realizar Ingreso Cobranza','Ver Ingresos Cobranza','Ver Ventas CC'])
                                <li><a class="bg-yellow dropdown-item" href="{{url('AutorizarVentasCuentaCorriente')}}">Autorizar ventas</a></li>
                                @endcanany
                                @canany(['Realizar Ingreso Cobranza','Ver Ingresos Cobranza','Ver Ventas CC'])
                                <li><a class="bg-yellow dropdown-item" href="{{url('IngresoCobranza')}}">Ingresos de Cobranza</a></li>
                                @endcanany
                            </ul>
                            </li>
                            @endcanany
                        </ul>
                    </li>

                    @canany(['Ver Info Proveedores','Editar Proveedores','Eliminar Proveedores','Crear Proveedores','Ver Info Pedidos','Gestionar Pagos Pedido','Eliminar Pedidos','Descargar Detalle Pedido','Crear Pedidos'])
                    <li class="nav-item dropdown">
                        <a class="bg-yellow nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Proveedores
                        </a>
                        <div class="bg-yellow dropdown-menu " aria-labelledby="navbarDropdownMenuLink">

                            @canany(['Ver Info Proveedores','Editar Proveedores','Eliminar Proveedores','Crear Proveedores'])
                                <a class="bg-yellow dropdown-item" href="{{url('Proveedores')}}">Proveedores</a>
                            @endcanany

                            @canany(['Crear Pedidos'])
                                <a class="bg-yellow dropdown-item" href="{{url('PedidosProveedor')}}">Realizar pedidos</a>
                            @endcanany

                            @canany(['Ver Info Pedidos','Gestionar Pagos Pedido','Eliminar Pedidos','Descargar Detalle Pedido'])
                                <a class="bg-yellow dropdown-item" href="{{url('GestionPedidos')}}">Gestión de pedidos</a>
                            @endcanany
                        </div>
                    </li>
                    @endcanany
                    @canany(['Pedidos'])
                    <li class="nav-item active">
                        <a class="nav-link" href="{{url('pedidos')}}" style="color:#279d09 !important;font-size:15px;font-weight: bold;">Pedidos</a>
                    </li>
                    @endcanany

                    @canany(['Crear Cliente','Editar Cliente','Eliminar Cliente','Ver Informacion Cliente'])
                    <li class="nav-item active">
                        <a class="nav-link" href="{{url('Clientes')}}" style="color:#279d09 !important;font-size:15px;font-weight: bold;">Clientes</a>
                    </li>
                    @endcanany

                    <li class="bg-yellow nav-item dropdown">
                        <a class="bg-yellow nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Stock
                        </a>
                        <div class="bg-yellow dropdown-menu " aria-labelledby="navbarDropdownMenuLink">
                            @canany(['Realizar Transferencias'])
                                <a class="bg-yellow dropdown-item" href="{{url('transferencia')}}">Realizar transferencias</a>
                            @endcanany

                            <a class="bg-yellow dropdown-item" href="{{url('listadoTransferencias')}}">Listado de transferencias</a>
                            <a class="bg-yellow dropdown-item" href="{{url('recepcion')}}">Recepciones</a>

                            @canany(['Crear Productos','Aumentar Bajar Precios Productos','Editar Productos','Eliminar Productos','Aumentar Stock'])
                                <a class="bg-yellow dropdown-item" href="{{url('Productos')}}">Productos</a>
                            @endcanany
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="bg-yellow nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Administracion
                        </a>
                        <ul class="dropdown-menu bg-yellow" aria-labelledby="navbarDropdownMenuLink">
                            @canany(['Crear Usuario'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('RegistrarUsuarios')}}">Registar usuarios</a></li>
                            @endcanany

                            @canany(['Crear Usuario','Eliminar Usuario'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('GestionUsuarios')}}">Gestión de usuarios</a></li>
                            @endcanany

                            

                            @canany(['Ver Reportes'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('Reportes')}}">Reportes</a></li>
                            @endcanany
                            @canany(['anula-factura'])
                            <li><a class="bg-yellow dropdown-item" href="{{url('anulaciones')}}">Anular/Refacturar</a></li>
                            @endcanany
                            <li><a class="bg-yellow dropdown-item" href="{{url('/')}}">Dashboard</a> </li>
                            

                        </ul>
                    </li>
                </ul>
            </div>
            @php
            $sucursales = App\Sucursales::Where('estado','=','P')->first();
            @endphp
            <div class="form-inline row my-2 my-lg-0">
                <span class="sucursal col col-lg text-right">Usuario: <span class="font-weight-bold">{{Auth::user()->user}}</span></span>
                <div class="w-100"></div>
                <span class="sucursal col col-lg text-right"> Sucursal: <span class="font-weight-bold"> {{$sucursales->sucursal}} </span></span>
                <div class="w-100"></div>
                <div class="col col-lg text-right">
                <a class="sucursal font-weight-bold" href="{{url('logout')}}">Cerrar sesión</a>
                </div>
            </div>


        </nav>
    </div>
    <div class="content">
        <div name="app" id="app">

            @yield('contenido')

        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FINModal -->
    </div>

    <script>
        window.user = @json(
            [
                'user'=> auth()->user(),
                'roles'=> auth()->user()->roles,
                'permissions' => auth()->user()->permissions
            ]
        );
    </script>

    <script src="{{url('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{url('js/alertify.js')}}"></script>
    <script type="text/javascript" src="{{url('js/datatables.min.js')}}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script src="https://unpkg.com/vue-chartjs/dist/vue-chartjs.min.js"></script>
    <script>
        $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');


            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show').removeClass("show");
            });


            return false;
            });
    </script>
    @yield('script')

</body>

</html>
