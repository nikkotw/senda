<html>
<head>
<style>
body:after {
    content:"";
    position: absolute;
    z-index: -1;
    top: 50;
    bottom: 0;
    left: 50%;
    border-left: 2px dotted #444;
}
.h1der{
    margin:5rem;
    float:left;
}
.h1izq{
    float:right;
    margin:5rem
}
.derecho{
    float:right;
    padding:5px;
}
.derecho table{
    float:right;
    font-size:9px;
    width:40%;
    margin-top: 100px;
    margin-left:15px;
    text-align:center;
}
.left{
    float:left;
    padding:5px;
}
.left table{
    font-size:9px;
    width:40%;
    margin-top: 100px;
    margin-right:15px;
    text-align:center;
}

.check{
    border:1px solid;
    width:5px;
    height:5px;
    margin-left:2rem;

}

</style>
</head>
<body>


    
<div class="row">
    <div class="left">
        <div class="col-md-4">
                <table>
                    <thead>
                    <tr>
                        <th width="20%" >Producto</th>
                        <th width="5%" >Cantidad</th>
                        <th width="5%">Checking</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($productos as $item)
                    <tr>
                            <td >{{$item->Descripcion}}</td>
                            <td >{{$item->cantidad}}</td>
                            <td ><div class="check"></div></td>
                            </tr>
                    @endforeach        
                </tbody>

                </table>
        </div>
    </div>
    <div class="derecho">
            <div class="col-md-4">
                    <table>
                        <thead>
                        <tr>
                            <th width="20%" >Producto</th>
                            <th width="5%" >Cantidad</th>
                            <th width="5%">Checking</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($productos as $item)
                        <tr>
                                <td>{{$item->Descripcion}}</td>
                                <td>{{$item->cantidad}}</td>
                                <td> <center> <div class="check"></div></center>   </td>
                                </tr>
                        @endforeach        
                    </tbody>

                    </table>
            </div>
        </div>
</div>

</body>
</html>
