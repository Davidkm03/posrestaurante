<?php

namespace App\Livewire\POS;

use App\Models\Customer;
use App\Enums\DocumentType;
use Livewire\Component;
use Livewire\Attributes\On;

class QuickCustomer extends Component
{
    public bool $isOpen = false;

    public string $name = '';
    public string $documentType = 'cedula';
    public string $documentNumber = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'documentType' => 'required|string',
        'documentNumber' => 'required|string|max:20|unique:customers,document_number',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'El nombre es obligatorio',
        'documentNumber.required' => 'El número de documento es obligatorio',
        'documentNumber.unique' => 'Este documento ya está registrado',
        'email.email' => 'El email no es válido',
    ];

    #[On('open-quick-customer')]
    public function open()
    {
        $this->reset(['name', 'documentType', 'documentNumber', 'email', 'phone', 'address']);
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();

        $customer = Customer::create([
            'name' => $this->name,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
        ]);

        $this->dispatch('customer-created', customerId: $customer->id);
        $this->dispatch('notify', type: 'success', message: 'Cliente creado exitosamente');
        $this->close();
    }

    public function searchByDocument()
    {
        if (empty($this->documentNumber)) return;

        $existing = Customer::where('document_number', $this->documentNumber)->first();

        if ($existing) {
            $this->dispatch('notify', type: 'info', message: 'Cliente ya existe: ' . $existing->name);
            $this->dispatch('customer-created', customerId: $existing->id);
            $this->close();
        }
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $documentTypes = [
            'cedula' => 'Cédula de Ciudadanía',
            'nit' => 'NIT',
            'cedula_extranjeria' => 'Cédula de Extranjería',
            'pasaporte' => 'Pasaporte',
            'consumidor_final' => 'Consumidor Final',
        ];

        return view('livewire.pos.quick-customer', [
            'documentTypes' => $documentTypes,
        ]);
    }
}
