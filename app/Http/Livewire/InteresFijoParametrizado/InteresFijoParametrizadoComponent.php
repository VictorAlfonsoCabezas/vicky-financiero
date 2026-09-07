<?php

namespace App\Http\Livewire\InteresFijoParametrizado;

use Livewire\Component;
use App\Models\InteresFijoParametrizado;
use Livewire\WithPagination;

class InteresFijoParametrizadoComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $valor_inicio = '';
    public $valor_fin = '';
    public $primer_valor = '';
    public $segundo_valor = '';
    public $tercer_valor = '';
    public $cuarto_valor = '';
    public $id_seleccionado = 0;
    public $porcentaje = 0;


    public function nuevaRegla($id)
    {
        $this->id_seleccionado = $id;
        $this->resetInput();
        if ($id > 0) {
            $tipo = InteresFijoParametrizado::find($id);
            $this->valor_inicio = $tipo->valor_inicio;
            $this->valor_fin = $tipo->valor_fin;
            $this->primer_valor = $tipo->primer_valor;
            $this->segundo_valor = $tipo->segundo_valor;
            $this->tercer_valor = $tipo->tercer_valor;
            $this->cuarto_valor = $tipo->cuarto_valor;
            $this->porcentaje = $tipo->porcentaje;
        }
    }

    public function resetInput()
    {

        $this->valor_inicio = '';
        $this->valor_fin = '';
        $this->primer_valor = '';
        $this->segundo_valor = '';
        $this->tercer_valor = '';
        $this->cuarto_valor = '';
        $this->porcentaje = 0;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            "valor_inicio" => "required",
            "valor_fin" => "required",
            "primer_valor" => "required",
            "segundo_valor" => "required|min:3|max:50",
            "tercer_valor" => "required|numeric",
            "cuarto_valor" => "required|numeric",
            "porcentaje" => "required",
           
        ]);

        if ($this->id_seleccionado > 0) {
            $tipo = InteresFijoParametrizado::find($this->id_seleccionado);
        } else {
            $tipo = new InteresFijoParametrizado();
        }
      
        $tipo->valor_inicio = $this->valor_inicio;
        $tipo->valor_fin = $this->valor_fin;
        $tipo->primer_valor = $this->primer_valor;
        $tipo->segundo_valor = $this->segundo_valor;
        $tipo->tercer_valor = $this->tercer_valor;
        $tipo->cuarto_valor = $this->cuarto_valor;
        $tipo->porcentaje = $this->porcentaje;
        $tipo->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioEstado($id)
    {
        $tipo = InteresFijoParametrizado::find($id);
        $tipo->status = ($tipo->status) ? false : true;
        $tipo->save();
    }

    public function render()
    {
        $intereses =  InteresFijoParametrizado::paginate(10);
        return view('livewire.interes-fijo-parametrizado.interes-fijo-parametrizado-component', compact('intereses'));
    }
}
