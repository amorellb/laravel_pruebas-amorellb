<table border="1">
    @foreach($data as $pais)
    <tr>
        <td>{{$pais->nombre}}</td>
        <td>{{$pais->cod_numerico}}</td>
        <td>{{$pais->codigoISO3}}</td>
        <td>{{$pais->codigoISO2}}</td>
    </tr>
    @endforeach
</table>
