<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionesCierresCajaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $pdfFilePath;

    public function __construct($data, $pdfFilePath)
    {
        $this->data = $data;
        $this->pdfFilePath = $pdfFilePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $datos= $this->data ;
        $nombre = $datos['name'];
        return $this->from(env('MAIL_FROM_ADDRESS'), 'Notificaciones')
                    ->view('correos.cierresCaja')
                    ->subject('Cierre de caja')
                    ->attach($this->pdfFilePath, [
                        'as' => $nombre,
                        'mime' => 'application/pdf',
                    ])
                    ->with($this->data);
    }
}
