<?php

use Livewire\Volt\Component;
use App\Models\User;

new class extends Component {
    public $response;
    public $currentPage = 1;
    public $totalPages;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $cacheKey = 'clientes_resumido_page' . $this->currentPage;

        $this->response = Cache::remember($cacheKey, 3600, function () {
            return $this->enviarRequisicao($this->currentPage);
        });

        $this->totalPages = $this->response['total_de_paginas'];
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadData();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadData();
        }
    }

    public function enviarRequisicao($page)
    {
        $url = config('app.omie_url') . '/geral/clientes/#ListarClientesResumido';

        $data = [
            'call' => 'ListarClientesResumido',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => $page,
                    'registros_por_pagina' => 20,
                    'apenas_importado_api' => 'N',
                ],
            ],
        ];

        try {
            $response = Http::timeout(10)->retry(3, 100)->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Erro ao consumir a API: ' . $response->status());
            }
        } catch (RequestException $e) {
            throw new \Exception('Erro ao consumir a API: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <x-header title="Clientes" separator progress-indicator>
        {{-- <x-slot:middle class="!justify-end">
            <x-input placeholder="Procurar..." clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Pesquisar" responsive icon="o-magnifying-glass" class="btn-primary" />
        </x-slot:actions> --}}
    </x-header>

    @if (isset($response['clientes_cadastro_resumido']) && count($response['clientes_cadastro_resumido']) > 0)
        <table class="table w-full overflow-y-auto">
            <thead class="text-center">
                <tr>
                    <th class="px-4 py-2">Razao Social</th>
                    <th class="px-4 py-2">Nome Fantasia</th>
                    <th class="px-4 py-2">Código de integração</th>
                    <th class="px-4 py-2">CNPJ ou CPF</th>
                    <th class="px-4 py-2">Código do cliente</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($response['clientes_cadastro_resumido'] as $cliente)
                    <tr class="text-center hover">
                        <td class="px-4 py-2">
                            {{ isset($cliente['razao_social']) && $cliente['razao_social'] !== '' ? $cliente['razao_social'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($cliente['nome_fantasia']) && $cliente['nome_fantasia'] !== '' ? $cliente['nome_fantasia'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($cliente['codigo_cliente_integracao']) && $cliente['codigo_cliente_integracao'] !== '' ? $cliente['codigo_cliente_integracao'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($cliente['cnpj_cpf']) && $cliente['cnpj_cpf'] !== '' ? $cliente['cnpj_cpf'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($cliente['codigo_cliente']) && $cliente['codigo_cliente'] !== '' ? $cliente['codigo_cliente'] : 'N/A' }}
                        </td>
                        {{-- <td class="px-4 py-2">
                            <livewire:clientes.show :cliente="$cliente" :key="$cliente['codigo_cliente_omie']" />
                        </td> --}}
                        <td class="px-4 py-2">
                            @php
                                $clienteCodigo = $cliente['codigo_cliente'];
                                $userExists = User::where('usercode', $clienteCodigo)->exists();
                            @endphp
                            @if ($userExists)
                                <x-button icon="o-users" class="btn-ghost btn-sm text-indigo-500" disabled />
                            @else
                                <livewire:clientes.create :cliente="$cliente"
                                    wire:key="$cliente['codigo_cliente_omie']-{{ now()->timestamp }}" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginação -->
        <div class="mt-4 flex justify-between items-center">
            <x-button icon="o-chevron-left" class="btn-square" wire:click="previousPage" :disabled="$currentPage == 1" />
            <span>Página {{ $currentPage }} de {{ $totalPages }}</span>
            <x-button icon="o-chevron-right" class="btn-square" wire:click="nextPage" :disabled="$currentPage == $totalPages" />
        </div>
    @endif
</div>
