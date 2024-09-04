<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    public $response;
    public $currentPage = 1;
    public $totalPages;
    public $statusFilter = '';
    public $dataDe;
    public $dataAte;
    public $clientes = [];
    public $selectedCliente = null; // Mudar de $cliente para $selectedCliente
    public $titulosEmAberto = 'N'; // Por padrão, 'N'
    public $statuses = ['CANCELADO', 'PAGO', 'LIQUIDADO', 'EMABERTO', 'PAGTO_PARCIAL', 'VENCEHOJE', 'AVENCER', 'ATRASADO'];

    public function mount(): void
    {
        $this->loadData();
    }

    public function getClienteNome($codigoCliente)
    {
        foreach ($this->clientes as $cliente) {
            if ($cliente['codigo_cliente'] === $codigoCliente) {
                return $cliente['razao_social'];
            }
        }
        return 'N/A'; // Retorna 'N/A' se o cliente não for encontrado
    }

    public function loadData(): void
    {
        // Carregar dados iniciais
        $this->clientes = $this->getClientes(); // Carregar todos os clientes
        $this->response = $this->enviarRequisicao($this->currentPage, $this->statusFilter, $this->dataDe, $this->dataAte, $this->selectedCliente, $this->titulosEmAberto);
        $this->totalPages = $this->response['total_de_paginas'];
    }

    public function getClientes(): array
    {
        $url = config('app.omie_url') . '/geral/clientes/#ListarClientesResumido';

        $params = [
            'pagina' => 1,
            'registros_por_pagina' => 1000, // Ajuste conforme necessário
            'apenas_importado_api' => 'N',
        ];

        $data = [
            'call' => 'ListarClientesResumido',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [$params],
        ];

        $response = Http::post($url, $data);

        if ($response->successful()) {
            return $response->json()['clientes_cadastro_resumido'] ?? [];
        } else {
            throw new \Exception('Erro ao consumir a API: ' . $response->status());
        }
    }

    public function enviarRequisicao(int $page, string $status, ?string $dataDe, ?string $dataAte, ?string $selectedCliente, string $titulosEmAberto): array
    {
        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $usercode = Auth::user()->usercode;

        $params = [
            'pagina' => $page,
            'registros_por_pagina' => 10,
            'apenas_importado_api' => 'N',
            'ordenar_por' => 'DATA_PAGAMENTO',
            'filtrar_por_status' => $status,
            'filtrar_por_vendedor' => $usercode,
        ];

        if (!empty($dataDe)) {
            $params['filtrar_por_data_de'] = $dataDe;
        }

        if (!empty($dataAte)) {
            $params['filtrar_por_data_ate'] = $dataAte;
        }

        if (!empty($selectedCliente)) {
            $params['filtrar_cliente'] = $selectedCliente;
        }

        $params['filtrar_apenas_titulos_em_aberto'] = $titulosEmAberto;

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [$params],
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
    <x-header title="Contas a receber" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Filtro de Status -->
                <div class="flex items-center">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Filtrar por Status:</span>
                        </div>
                        <select id="statusFilter" wire:model="statusFilter" class="select select-bordered w-full">
                            <option value="">TODOS</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <!-- Filtro Data De -->
                <div class="flex items-center">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Data De:</span>
                        </div>
                        <input type="text" id="dataDe" wire:model="dataDe" placeholder="dd/mm/aaaa"
                            class="input input-bordered w-full">
                    </label>
                </div>
                <!-- Filtro Data Até -->
                <div class="flex items-center">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Data Até:</span>
                        </div>
                        <input type="text" id="dataAte" wire:model="dataAte" placeholder="dd/mm/aaaa"
                            class="input input-bordered w-full">
                    </label>
                </div>
                <!-- Filtro de Cliente -->
                <div>
                    <label for="clienteSelect" class="form-control">
                        <div class="label">
                            <span class="label-text">Cliente</span>
                        </div>
                        <input list="clientes" id="clienteSelect" name="clienteSelect" wire:model="selectedCliente"
                            placeholder="Digite para pesquisar..." class="input input-bordered">

                        <datalist id="clientes">
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente['codigo_cliente'] }}">{{ $cliente['razao_social'] }}</option>
                            @endforeach
                        </datalist>

                        @if (session()->has('error'))
                            <div class="text-red-500 mt-2">{{ session('error') }}</div>
                        @endif
                    </label>
                </div>
                <!-- Filtro de Títulos em Aberto -->
                <div class="flex items-center">
                    <label class="form-control w-full">
                        <div class="label">
                            <span class="label-text">Títulos em Aberto:</span>
                        </div>
                        <select id="titulosEmAberto" wire:model="titulosEmAberto" class="select select-bordered w-full">
                            <option value="S">Sim</option>
                            <option value="N">Não</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <x-button class="w-full" wire:click="search">Pesquisar</x-button>
            </div>
        </x-slot:middle>
    </x-header>

    <!-- Tabela de Resultados -->
    @if (isset($response['conta_receber_cadastro']) && count($response['conta_receber_cadastro']) > 0)
        <div class="overflow-x-auto">
            <table class="table table-xs w-full">
                <thead>
                    <tr class="text-center">
                        <th class="px-4 py-2">Ações</th>
                        <th class="px-4 py-2">Número do documento fiscal</th>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Número da parcela</th>
                        <th class="px-4 py-2">Código do pedido</th>
                        <th class="px-4 py-2">Número do Boleto</th>
                        <th class="px-4 py-2">Data de Emissão</th>
                        <th class="px-4 py-2">Data de Vencimento</th>
                        <th class="px-4 py-2">Valor do Documento</th>
                        <th class="px-4 py-2">Status do Título</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($response['conta_receber_cadastro'] as $conta)
                        <tr class="text-center hover">
                            <td class="border px-4 py-2">
                                @if (isset($conta['codigo_barras_ficha_compensacao']))
                                    <livewire:seller_lancamentos.boleto-opcoes wire:key="conta-{{ $conta['nCodPedido'] }}-{{ now()->timestamp }}" />
                                @else
                                    <livewire:seller_lancamentos.criar-boleto wire:key="conta-{{ $conta['nCodPedido'] }}-{{ now()->timestamp }}"/>
                                @endif
                            </td>
                            <td class="border px-4 py-2">{{ $conta['numero_documento_fiscal'] ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">
                                {{ $this->getClienteNome($conta['codigo_cliente_fornecedor']) }}</td>
                            <td class="border px-4 py-2">{{ $conta['numero_parcela'] ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $conta['nCodPedido'] ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $conta['codigo_barras_ficha_compensacao'] ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $conta['data_emissao'] }}</td>
                            <td class="border px-4 py-2">{{ $conta['data_vencimento'] }}</td>
                            <td class="border px-4 py-2">R$ {{ number_format($conta['valor_documento'], 2, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2">{{ $conta['status_titulo'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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
