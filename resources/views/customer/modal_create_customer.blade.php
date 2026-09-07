<div class="modal fade" id="formCrearCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Clientes</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="smart-form" id="formCreateCustomerModal">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Tipo Documento</label>
                            <select class="form-control" id="crear_tipo_customer_modal" name="crear_tipo_customer_modal">
                                <option value="04">CEDULA</option>
                                <option value="03">RUC</option>
                                <option value="05">PASAPORTE</option>
                            </select>
                        </div>
                        <div class="form-group col-md-8">
                            <label>Numero de Documento</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-credit-card"></i></span>
                                </div>
                                <input type="number" id="ruc_customer_modal" name="ruc_customer_modal" class="form-control" placeholder="Nombre Empresa" required>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div> 
                        <div class="form-group col-md-6">
                            <label>Nombres</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                </div>
                                <input type="text" id="nombres_customer_modal" name="nombres_customer_modal" class="form-control text-uppercase" placeholder="Nombres" required>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div>  
                        <div class="form-group col-md-6">
                            <label>Apellidos</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-user"></i></span>
                                </div>
                                <input type="text" id="apellidos_customer_modal" name="apellidos_customer_modal" class="form-control text-uppercase" placeholder="Apellidos" required>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div>  
                        <div class="form-group col-md-12">
                            <label>Direccion</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-address-card"></i></span>
                                </div>
                                <textarea type="text" id="direccion_customer_modal" name="direccion_customer_modal" class="form-control text-uppercase" placeholder="Direccion del cliente" required rows="3"></textarea>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div>  
                        <div class="form-group col-md-6">
                            <label>Telefono</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="number" id="telefono_customer_modal" name="telefono_customer_modal" class="form-control" placeholder="Telefono" required>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div>  
                        <div class="form-group col-md-6">
                            <label>Correo</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                </div>
                                <input type="email" id="correo_customer_modal" name="correo_customer_modal" class="form-control" placeholder="Correo" required>
                                <div class="valid-feedback">Listo!!</div>
                            </div>
                        </div>  
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a onclick="javascript:crearCustomerModal();"class="btn btn-primary btn-primary" style="color: white;"><i class="fas fa-save" style="color: white;"></i> Crear</a>
            </div>
        </div>
    </div>
</div>
