<table style="border: 1px solid black">
@foreach($data as $agenda)
    <tr>
        <td style="background-color: lightgrey">{{$agenda->firstname}}</td>
        <td>{{$agenda->lastname}}</td>
        <td style="background-color: lightgrey">{{$agenda->contact_number}}</td>
    </tr>
    @endforeach
    </table>
