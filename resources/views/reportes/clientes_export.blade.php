<table>
    <thead>
        <tr role="row">
            <th colspan="10" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha Creación</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Código</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombres </b></th>
            <th scope="col" style="background-color: #7eb8da; width: 100px;"><b>Apellidos</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Cedula</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha de nacimiento</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Dirección</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Estado Civil</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombre Conyugue</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CI Conyugue</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Teléfono Conyugue</b></th>
           
        </tr>

    </thead>
    <tbody style="font-size: 14px;">
        @foreach ($customer as $key => $customer)
        <tr>
            <td>{{ $customer->created_at }}</td>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->nombres }}</td>
            <td>{{ $customer->apellidos }}</td>
            <td>{{ $customer->numero_documento }}</td>
            <td>{{ $customer->fecha_nacimiento }}</td>
            <td>{{ $customer->direccion }}</td>
            <td>{{ $customer->estado_civil }}</td>
            <td>{{ $customer->conyugue_nombre }}</td>
            <td>{{ $customer->conyugue_identificacion }}</td>
            <td>{{ $customer->conyugue_telefono }}</td> 
        
        </tr>
        @endforeach

</table>