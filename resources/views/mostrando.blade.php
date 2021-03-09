<h1>Testeo DATA UPDATE AJX<h1>
<table class="table">
    <thead>
        <th>id</th>
        <th>Usuario</th>
        <th>pass</th>
    </thead>
    <tbody>
        @foreach($usuarios as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>{{$item->usuario}}</td>
            <td>{{$item->pass}}</td>
           </tr>
        @endforeach
    
    </tbody>

</table>    