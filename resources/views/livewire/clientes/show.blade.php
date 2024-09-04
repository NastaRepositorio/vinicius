<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $clienteDataModal = false;
    public array $cliente;

    public function mount(array $cliente): void
    {
        $this->cliente = $cliente;
    }
}; ?>

<div>
    <x-button icon="o-eye" @click="$wire.clienteDataModal = true" spinner class="btn-ghost btn-sm text-indigo-500"
        tooltip="Visualizar" />

    <x-modal wire:model="clienteDataModal" class="backdrop-blur ">
        <div class="grid grid-cols-1 gap-4">
            <x-stat title="Pessoa física"
                value="{{ isset($cliente['pessoa_fisica']) && $cliente['pessoa_fisica'] !== '' ? $cliente['pessoa_fisica'] : 'N/A' }}"
                icon="o-user" color="text-orange-400" />
            <x-stat title="CPF ou CNPJ"
                value="{{ isset($cliente['cnpj_cpf']) && $cliente['cnpj_cpf'] !== '' ? $cliente['cnpj_cpf'] : 'N/A' }}"
                icon="o-document-text" color="text-yellow-500" />
              <x-stat title="Nome fantasia"
                value="{{ isset($cliente['nome_fantasia']) && $cliente['nome_fantasia'] !== '' ? $cliente['nome_fantasia'] : 'N/A' }}"
                icon="o-building-office" color="text-blue-500" />
            <x-stat title="Razão social"
                value="{{ isset($cliente['razao_social']) && $cliente['razao_social'] !== '' ? $cliente['razao_social'] : 'N/A' }}"
                icon="o-building-office-2" color="text-green-500" />
            <x-stat title="Inscrição estadual"
                value="{{ isset($cliente['inscricao_estadual']) && $cliente['inscricao_estadual'] !== '' ? $cliente['inscricao_estadual'] : 'N/A' }}"
                icon="o-clipboard-document-list" color="text-red-500" />
            <x-stat title="Inscrição municipal"
                value="{{ isset($cliente['inscricao_municipal']) && $cliente['inscricao_municipal'] !== '' ? $cliente['inscricao_municipal'] : 'N/A' }}"
                icon="o-building-library" color="text-purple-500" />
            <x-stat title="Endereço"
                value="{{ isset($cliente['endereco']) && $cliente['endereco'] !== '' ? $cliente['endereco'] : 'N/A' }}"
                icon="o-map" color="text-teal-500" />
            <x-stat title="Número"
                value="{{ isset($cliente['endereco_numero']) && $cliente['endereco_numero'] !== '' ? $cliente['endereco_numero'] : 'N/A' }}"
                icon="o-home" color="text-pink-500" />
            <x-stat title="Complemento"
                value="{{ isset($cliente['complemento']) && $cliente['complemento'] !== '' ? $cliente['complemento'] : 'N/A' }}"
                icon="o-plus-circle" color="text-indigo-500" />
            <x-stat title="Bairro"
                value="{{ isset($cliente['bairro']) && $cliente['bairro'] !== '' ? $cliente['bairro'] : 'N/A' }}"
                icon="o-map-pin" color="text-yellow-400" />
            <x-stat title="Cidade"
                value="{{ isset($cliente['cidade']) && $cliente['cidade'] !== '' ? $cliente['cidade'] : 'N/A' }}"
                icon="o-map" color="text-blue-400" />
            <x-stat title="CEP"
                value="{{ isset($cliente['cep']) && $cliente['cep'] !== '' ? $cliente['cep'] : 'N/A' }}" icon="o-inbox-arrow-down"
                color="text-red-400" />
            <x-stat title="Estado"
                value="{{ isset($cliente['estado']) && $cliente['estado'] !== '' ? $cliente['estado'] : 'N/A' }}"
                icon="o-flag" color="text-green-400" />
            <x-stat title="País"
                value="{{ isset($cliente['codigo_pais']) && $cliente['codigo_pais'] !== '' ? $cliente['codigo_pais'] : 'N/A' }}"
                icon="o-globe-americas" color="text-purple-400" />
            <x-stat title="Telefone 1"
                value="{{ isset($cliente['telefone1_ddd']) && isset($cliente['telefone1_numero']) ? $cliente['telefone1_ddd'] . ' ' . $cliente['telefone1_numero'] : 'N/A' }}"
                icon="o-device-phone-mobile" color="text-teal-400" />
            <x-stat title="Telefone 2"
                value="{{ isset($cliente['telefone2_ddd']) && isset($cliente['telefone2_numero']) ? $cliente['telefone2_ddd'] . ' ' . $cliente['telefone2_numero'] : 'N/A' }}"
                icon="o-device-phone-mobile" color="text-pink-400" />
            <x-stat title="E-mail"
                value="{{ isset($cliente['email']) && $cliente['email'] !== '' ? $cliente['email'] : 'N/A' }}"
                icon="o-envelope-open" color="text-orange-400" />
            <x-stat title="Bloquear exclusão"
                value="{{ isset($cliente['bloquear_exclusao']) && $cliente['bloquear_exclusao'] !== '' ? $cliente['bloquear_exclusao'] : 'N/A' }}"
                icon="o-exclamation-circle" color="text-red-400" />
            <x-stat title="Bloquear faturamento"
                value="{{ isset($cliente['bloquear_faturamento']) && $cliente['bloquear_faturamento'] !== '' ? $cliente['bloquear_faturamento'] : 'N/A' }}"
                icon="o-x-circle" color="text-yellow-400" />
            <x-stat title="Inativo"
                value="{{ isset($cliente['inativo']) && $cliente['inativo'] !== '' ? $cliente['inativo'] : 'N/A' }}"
                icon="o-minus-circle" color="text-gray-500" />
        </div>
        <x-button label="Cancel" @click="$wire.clienteDataModal = false" />
    </x-modal>
</div>
