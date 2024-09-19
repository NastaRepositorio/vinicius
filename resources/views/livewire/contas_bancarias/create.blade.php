<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use App\Models\ContaBanco;

new class extends Component {
    use Toast;
    public $createContaModal = false;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $agencia = '';

    #[Validate('required')]
    public $conta = '';

    #[Validate('required')]
    public $convenio = '';

    public function createConta(): void
    {
        $this->validate();

        ContaBanco::create([
            'nome' => $this->name,
            'agencia' => $this->agencia,
            'conta' => $this->conta,
            'convenio' => $this->convenio,
        ]);

        $this->dispatch('conta-index-refresh');

        $this->createContaModal = false;
        $this->name = '';
        $this->agencia = '';
        $this->conta = '';
        $this->convenio = '';
        $this->success('Conta criada.');
    }

}; ?>

<div>
    <x-button label="Nova Conta" icon="o-plus" @click="$wire.createContaModal = true" class="btn-primary"/>

    <x-modal wire:model='createContaModal' title="Criar nova conta bancária" subtitle="Preencha as informações abaixo">

        <x-form wire:submit="createConta">

            <x-input label="Nome da conta" wire:model="name" />

            <x-input label="Agência" wire:model="agencia" />

            <x-input label="Conta" wire:model="conta" />

            <x-input label="Convênio" wire:model="convenio" />

            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.createContaModal = false" />
                <x-button label="Salvar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>

    </x-modal>
</div>
