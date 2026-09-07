<div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Nota:</h5>
                        Antes de cargar el archivo verifique que todos los datos esten llenos <b>NO SE PODRAN REVERTIR LOS CAMBIOS</b>
                    </div>
                    <div id="formulario_fondo" class="card">
                        <div class="">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div class="col-sm-12 invoice-col">
                                        <form id="form_credito" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" id="id_customer_generar" name="id_customer_generar" value="0">

                                            <div class="modal-footer justify-content-between">
                                                <div class="custom-file">
                                                    <label>Archivos</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" wire:model="excelFile" accept=".xlsx,.xls" id="file" name="file">
                                                        <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                                                    </div>
                                                </div>
                                                <br>
                                                <br>
                                                <div class="custom-file">
                                                    <a class="btn btn-info" href="{{URL::to('custom/plantilla-movimientos')}}" style="color: white;"><i class="fa fa-download"></i>Descargar Plantilla</a>
                                                    <a wire:click="saveData" class="btn btn-success" style="color: white;">
                                                        <i class="fas fa-thumbs-up"></i> Guardar Registros
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">

                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Número Cuenta</b></th>
                                <th><b>Valor </b></th>
                                <th><b>Operación</b></th>
                                <th><b>Fecha</b></th>
                                <th><b>Observación</b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach ($rows as $index => $row)

                            <tr>
                                @foreach ($row as $cell)
                                <td class="border border-gray-400 px-4 py-2">{{ $cell }}</td>
                                @endforeach
                                <td class="border border-gray-400 px-4 py-2 text-center">
                                    <button wire:click="deleteRow({{ $index }})"
                                        class="btn btn-xs btn-danger">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>