<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $tipo = '';
    public $precio_a = '';
    public $description = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $product = Product::find($id);
            $this->nombre = $product->name;
            $this->tipo = $product->tipo;
            $this->precio_a = $product->precio_a;
            $this->description = $product->description;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'tipo', 'precio_a', 'description']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeProduct()
    {
        $this->validate([
            'nombre' => 'required',
            'tipo' => 'required',
            'precio_a' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $prodcut = Product::find($this->id_seleccionado);
        } else {
            $prodcut = new Product();
            $prodcut->company_id = Auth::user()->company_id;
        }
        $prodcut->name = $this->nombre;
        $prodcut->tipo = $this->tipo;
        $prodcut->precio_a = $this->precio_a;
        $prodcut->description = $this->description;
        $prodcut->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioEstado($id)
    {
        $product = Product::find($id);
        $product->status = !$product->status;
        $product->save();
    }

    public function render()
    {
        $productos = Product::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->paginate(20);
        return view('livewire.product.product-component', compact('productos'));
    }
}
