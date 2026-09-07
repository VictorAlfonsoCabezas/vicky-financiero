<div class="modal fade" id="frmModalFile" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Archivos</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="id_credit">
                    <div class="row padding-10">
                        <section class="col col-lg-5">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Descripción</label>
                                <input type="text" class="form-control" name="nombre_archivo" id="nombre_archivo" placeholder="DESCRIPCIO DEL ARCHIVO">
                            </div>
                        </section>
                        <section class="col col-lg-6">
                            <label>Archivo</label>
                            <div class="custom-file">
                                <input id="file" type="file" name="file" class="custom-file-input"  onchange="$('#showImg').html(this.value)">
                                <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                            </div>
                        </section>
                        <section class="col col-lg-1 padding-10">
                            <a class="btn btn-circle btn-success btn-lg" href="javascript:anadirArchivos()" style="position: relative; top: 26px;"><i class="fa fa-plus" style="position:relative;top: 2px;"></i></a>
                        </section>

                        <section class="col col-lg-12">
                            <table id="table_files" class="table table-bordered" border="1">
                                <thead>
                                    <tr>
                                        <th>Archivo</th>
                                        <th>Nombre</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>    
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i> CERRAR</button>
                <!--<button onclick="javascript:creditoIncobrableFinal();" class="btn btn-primary btn-lg"><i class="fas fa-money-bill-alt"></i> LIQUIDAR CRÉDITO</button>-->
            </div>
        </div>
    </div>
</div>
</div> 