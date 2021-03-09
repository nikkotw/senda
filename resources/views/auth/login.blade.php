<!doctype html>
<html class="no-js" lang="">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="ie=edge">
      <title>

      </title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{url('css/alertify.css')}}">
	    <link rel="shortcut icon" href="{{url('img/logo1.png')}}">
      <link rel="stylesheet" href="{{url('css/bootstrap.css')}}">
      <link rel="stylesheet" href="{{url('css/font-awesome.min.css')}}">
      <link rel="stylesheet" href="{{url('css/normalize.css')}}">
      <link rel="stylesheet" href="{{url('css/main.css')}}">
      <link rel="stylesheet" href="{{url('css/style.css')}}">
      <script src="{{url('js/vendor/modernizr-2.8.3.min.js')}}"></script>
      <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    </head>
    <body>
		<div class="main-content-wrapper">
			<div class="login-area">
				<div class="login-header">
					<a href="#" class="logo">
						<img src="img/logo1.png" height="60" alt="">
					</a>
					<h2 class="title">Login</h2>
				</div>
				<div class="login-content">
                    <form method="post" role="form" id="form_login" action="{{url('acceso')}}">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="input-field form-control form-control-sm" name="user" id="user" placeholder="Usuario" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <input type="password" class="input-field form-control form-control-sm" name="password" id="password" placeholder="Contraseña" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </form>

					<div class="login-bottom-links">
						<a href="" class="link">
						</a>
					</div>
				</div>
            </div>

            <div class="container">
                <div class="overlay">
                    <div class="text text-center">
                    </div>
                </div>
            </div>
		</div>

    <script src="{{url('js/alertify.js')}}"></script>

    </body>

    @if (session('message'))
    <script>
        window.onload = function(){
            alertify.alert('ERROR', 'Las credenciales ingresadas son incorrectas');
        }
    </script>
    @endif

    @if (session('inhabilitado'))
    <script>
        window.onload = function(){
            alertify.alert('ERROR', 'El usuario se encuentra inhabilitado');
        }
    </script>
    @endif

</html>
