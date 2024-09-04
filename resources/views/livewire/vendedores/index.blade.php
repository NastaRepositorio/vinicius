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
        $cacheKey = 'vendedor_page_' . $this->currentPage;

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
        $url = config('app.omie_url') . '/geral/vendedores/#ListarVendedores';

        $data = [
            'call' => 'ListarVendedores',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => $page,
                    'registros_por_pagina' => 10,
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
    <x-header title="Vendedores:" separator progress-indicator />

    @if (isset($response['cadastro']) && count($response['cadastro']) > 0)
        <table class="table w-full overflow-y-auto">
            <thead class="text-center">
                <tr>
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Código Vendedor</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($response['cadastro'] as $vendedor)
                    <tr class="text-center hover">
                        <td class="px-4 py-2">
                            {{ isset($vendedor['nome']) && $vendedor['nome'] !== '' ? $vendedor['nome'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($vendedor['email']) && $vendedor['email'] !== '' ? $vendedor['email'] : 'N/A' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ isset($vendedor['codigo']) && $vendedor['codigo'] !== '' ? $vendedor['codigo'] : 'N/A' }}
                        </td>
                        {{-- <td class="px-4 py-2">
                            {{ isset($vendedor['nCodigo']) && $vendedor['nCodigo'] !== '' ? $vendedor['nCodigo'] : 'N/A' }}
                        </td> --}}
                        <td class="px-4 py-2">
                            @php
                                $vendedorCodigo = $vendedor['codigo'];
                                $userExists = User::where('usercode', $vendedorCodigo)->exists();
                            @endphp
                            @if ($userExists)
                                <x-button icon="o-users" class="btn-ghost btn-sm text-indigo-500"
                                    tooltip="Criar conta para o vendedor" disabled/>
                            @else
                                <livewire:vendedores.create :vendedor="$vendedor" />
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
