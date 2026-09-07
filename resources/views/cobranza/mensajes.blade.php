<div class="modal fade" id="modalDetalleNotificacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="textodesc"></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">Mensajes Enviados</h3>
                            <div class="card-tools">
                                <span title="3 New Messages" class="badge bg-success" id="totalEnvios"></span>
                                <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                    <i class="fas fa-comments"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="direct-chat-messages" id="listaMensajesLetras">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>