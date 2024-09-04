<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    public $response;
    public $currentPage = 1;
    public $totalPages;
    public $statusFilter = 'ATRASADO';
    public $statuses = ['CANCELADO', 'PAGO', 'LIQUIDADO', 'EMABERTO', 'PAGTO_PARCIAL', 'VENCEHOJE', 'AVENCER', 'ATRASADO'];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $sessionKey = "contas_Receber_{$this->statusFilter}_{$this->currentPage}";

        $this->response = session($sessionKey, function () use ($sessionKey) {
            $data = $this->enviarRequisicao($this->currentPage, $this->statusFilter);
            session([$sessionKey => $data]);
            return $data;
        });

        $this->totalPages = $this->response['total_de_paginas'];
    }

    public function enviarRequisicao(int $page, string $status): array
    {
        $usercode = Auth::user()->usercode;

        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => $page,
                    'registros_por_pagina' => 10,
                    'apenas_importado_api' => 'N',
                    'ordenar_por' => 'DATA_PAGAMENTO',
                    'filtrar_cliente' => $usercode,
                    'filtrar_por_status' => $status,
                ],
            ],
        ];

        $response = Http::post($url, $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Erro ao consumir a API: ' . $response->status());
        }
    }

    public function search(): void
    {
        $this->currentPage = 1;
        $this->clearSessionCache();
        $this->loadData();
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadData();
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadData();
        }
    }

    protected function clearSessionCache(): void
    {
        $keys = array_keys(session()->all());
        foreach ($keys as $key) {
            if (str_starts_with($key, "contasReceber_{$this->statusFilter}_")) {
                session()->forget($key);
            }
        }
    }
};
?>

<div>

    <x-header title="Contas a Receber" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <!-- Filtro de Status -->
            <div class="flex items-center">
                <label class="form-control w-full max-w-xs mr-4">
                    <div class="label">
                        <span class="label-text">Filtrar por Status:</span>
                    </div>
                    <select id="statusFilter" wire:model="statusFilter" class="select select-bordered w-full max-w-xs">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
                <x-button wire:click="search" class="mt-9">Pesquisar</x-button>
            </div>
        </x-slot:middle>
    </x-header>

    <!-- Tabela de Resultados -->
    @if (isset($response['conta_receber_cadastro']) && count($response['conta_receber_cadastro']) > 0)
        <table class="table w-full overflow-y-auto">
            <thead>
                <tr class="text-center">
                    <th class="px-4 py-2">Número do Boleto</th>
                    <th class="px-4 py-2">Data de Emissão</th>
                    <th class="px-4 py-2">Data de Vencimento</th>
                    <th class="px-4 py-2">Valor do Documento</th>
                    <th class="px-4 py-2">Status do Título</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($response['conta_receber_cadastro'] as $conta)
                    <tr class="text-center">
                        <td class="border px-4 py-2">{{ $conta['codigo_barras_ficha_compensacao'] ?? 'N/A' }}</td>
                        <td class="border px-4 py-2">{{ $conta['data_emissao'] }}</td>
                        <td class="border px-4 py-2">{{ $conta['data_vencimento'] }}</td>
                        <td class="border px-4 py-2">R$ {{ number_format($conta['valor_documento'], 2, ',', '.') }}</td>
                        <td class="border px-4 py-2">{{ $conta['status_titulo'] }}</td>
                        <td class="border px-4 py-2">
                            @if(isset($conta['codigo_barras_ficha_compensacao']))
                            <x-button icon="o-arrow-down-tray" spinner class="btn-ghost btn-sm text-blue-500" tooltip="Baixar Boleto"/>
                            @else
                            <x-button icon="o-arrow-uturn-right" spinner class="btn-ghost btn-sm text-gray-500" tooltip="Solicitar boleto"/>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginação -->
        <div class="mt-4 flex justify-between">
            <button wire:click="previousPage" class="px-4 py-2 bg-gray-300"
                @if ($currentPage == 1) disabled @endif>Anterior</button>
            <span>Página {{ $currentPage }} de {{ $totalPages }}</span>
            <button wire:click="nextPage" class="px-4 py-2 bg-gray-300"
                @if ($currentPage == $totalPages) disabled @endif>Próxima</button>
        </div>
    @else
        <p>Nenhum registro encontrado.</p>
    @endif
</div>
