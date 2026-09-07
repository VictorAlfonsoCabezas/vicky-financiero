<table>
    <thead>
        <tr role="row">                                                              
            <th colspan="17" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>            
        </tr>
        <tr role="row">  
            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CARPETA</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>NOMBRE</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>DIRECCION</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>TELEFONO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA DE VENCIMIENTO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>DIAS VENCIMIENTO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>RANGO</b></th> 	
            <th scope="col" style="background-color: #7eb8da;"><b>INTERÉS VENCIDO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>CAPITAL VENCIDO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>FONDO DESGRAVAMEN VENCIDO</b></th>	
            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA VENCIDA</b></th>
            <th scope="col" style="background-color: #f7f48d;"><b>CI GARANTE</b></th>	
            <th scope="col" style="background-color: #f7f48d;"><b>GARANTE</b></th>	
            <th scope="col" style="background-color: #f7f48d;"><b>TELEFONO GARANTE</b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach($creditos as  $credit)
        <tr>
            <td>{{$credit->numero_cuota}}</td>
            <td>{{$credit->code_folder_header}}</td>
            <td>{{$credit->customerName}}</td>
            <td>{{$credit->direccion}}</td>
            <td>{{$credit->telefono}}</td>
            <td>{{$credit->date_vencimiento}}</td>
            <td>{{$credit->diasVencido}}</td>
            <td>{{$credit->rango}}</td>
            <td>{{$credit->interes_periodo}}</td>
            <td>{{$credit->capital_amortizado}}</td>
            <td>{{$credit->fondo_desgravamen}}</td>
            <td>{{$credit->valor_cuota}}</td>            
            <td>{{$credit->ciGarante}}</td>
            <td>{{$credit->nombreGarante}}</td>
            <td>{{$credit->telefonoGarante}}</td>
        </tr>
        @endforeach
    </tbody>
</table>