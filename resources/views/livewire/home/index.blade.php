<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public int $userCount;
    public int $sellerCount;
    public int $clientesCount;
    public int $vendedoresCount;

    public int $contasReceberCount;
    public int $contasReceberRecebidasCount;
    public int $contasReceberAtrasadasCount;
    public int $contasReceberAVencerCount;

    public int $contasReceberValue = 0;
    public int $contasReceberRecebidasValue = 0;
    public int $contasReceberAtrasadasValue = 0;
    public int $contasReceberAVencerValue = 0;

    public string $contasReceberValueFormatted = '';
    public string $contasReceberRecebidasValueFormatted = '';
    public string $contasReceberAtrasadasValueFormatted = '';
    public string $contasReceberAVencerValueFormatted = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->userCount = User::where('usertype', 'user')->count();
        $this->sellerCount = User::where('usertype', 'seller')->count();

        $response = $this->listarVendedores();
        $this->vendedoresCount = $response['total_de_registros'] ?? 0;

        $response = $this->listarClientes();
        $this->clientesCount = $response['total_de_registros'] ?? 0;

        $response = $this->listarContasReceber();
        $this->contasReceberCount = $response['total_de_registros'] ?? 0;
        $this->contasReceberValue = $this->sumValues($response);
        $this->contasReceberValueFormatted = $this->formatNumber($this->contasReceberValue);

        $response = $this->listarContasReceberRecebidas();
        $this->contasReceberRecebidasCount = $response['total_de_registros'] ?? 0;
        $this->contasReceberRecebidasValue = $this->sumValues($response);
        $this->contasReceberRecebidasValueFormatted = $this->formatNumber($this->contasReceberRecebidasValue);

        $response = $this->listarContasReceberAtrasadas();
        $this->contasReceberAtrasadasCount = $response['total_de_registros'] ?? 0;
        $this->contasReceberAtrasadasValue = $this->sumValues($response);
        $this->contasReceberAtrasadasValueFormatted = $this->formatNumber($this->contasReceberAtrasadasValue);

        $response = $this->listarContasReceberAVencer();
        $this->contasReceberAVencerCount = $response['total_de_registros'] ?? 0;
        $this->contasReceberAVencerValue = $this->sumValues($response);
        $this->contasReceberAVencerValueFormatted = $this->formatNumber($this->contasReceberAVencerValue);
        
    }

    private function sumValues(array $response): int
    {
        $total = 0;
        if (isset($response['conta_receber_cadastro'])) {
            foreach ($response['conta_receber_cadastro'] as $conta) {
                $total += $conta['valor_documento'];
            }
        }
        return $total;
    }

    public function listarVendedores(): array
    {
        $url = config('app.omie_url') . '/geral/vendedores/#ListarVendedores';

        $data = [
            'call' => 'ListarVendedores',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                ],
            ],
        ];

        return $this->makeApiRequest($url, $data);
    }

    public function listarClientes(): array
    {
        $url = config('app.omie_url') . '/geral/clientes/#ListarClientes';

        $data = [
            'call' => 'ListarClientes',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                ],
            ],
        ];

        return $this->makeApiRequest($url, $data);
    }

    public function listarContasReceber(): array
    {
        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                ],
            ],
        ];

        $this->applyUserFilter($data);

        return $this->makeApiRequest($url, $data);
    }

    public function listarContasReceberRecebidas(): array
    {
        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                    'filtrar_por_status' => 'PAGO',
                ],
            ],
        ];

        $this->applyUserFilter($data);

        return $this->makeApiRequest($url, $data);
    }

    public function listarContasReceberAtrasadas(): array
    {
        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                    'filtrar_por_status' => 'ATRASADO',
                ],
            ],
        ];

        $this->applyUserFilter($data);

        return $this->makeApiRequest($url, $data);
    }

    public function listarContasReceberAVencer(): array
    {
        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000,
                    'apenas_importado_api' => 'N',
                    'filtrar_por_status' => 'AVENCER',
                ],
            ],
        ];

        $this->applyUserFilter($data);

        return $this->makeApiRequest($url, $data);
    }

    public function formatNumber($number)
    {
        $formattedNumber = number_format($number, 2, ',', '.');
        return 'R$ ' . $formattedNumber;
    }

    private function applyUserFilter(array &$data): void
    {
        if (Auth::user()->usertype === 'user') {
            $data['param'][0]['filtrar_cliente'] = Auth::user()->usercode;
        }

        if (Auth::user()->usertype === 'seller') {
            $data['param'][0]['filtrar_por_vendedor'] = Auth::user()->usercode;
        }
    }

    private function makeApiRequest(string $url, array $data): array
    {
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
};
?>

<div>
    <x-header title="Bem vindo, {{ Auth::user()->name }}!" separator progress-indicator />

    <x-card>
        @if (Auth::user()->usertype === 'admin')
            <div class="md:grid-cols-4 grid grid-cols-2 gap-4">
                <p class="md:col-span-4 col-span-2 px-4 opacity-40 bg-gray-100"> Informações de usuários </p>
                <x-stat title="Vendedores" value="{{ $vendedoresCount }}" icon="o-user-group" color="text-orange-400" />
                <x-stat title="Vendedores cadastrados" value="{{ $sellerCount }}" icon="o-user-group"
                    color="text-yellow-500" />
                <x-stat title="Clientes" value="{{ $clientesCount }}" icon="o-users" color="text-red-700" />
                <x-stat title="Clientes cadastrados" value="{{ $userCount }}" icon="o-users" />
            </div>
        @endif
        <div class="md:grid-cols-4 grid grid-cols-2 gap-4">
            <p class="md:col-span-4 col-span-2 px-4 mt-4 opacity-40 bg-gray-100"> Informações de contas a pagar </p>

            <x-stat title="{{ Auth::user()->usertype === 'user' ? 'Contas totais' : 'Contas a receber' }}"
                value="{{ $contasReceberCount }}" icon="o-banknotes" color="text-gray-700"/>

            <x-stat title="{{ Auth::user()->usertype === 'user' ? 'Contas pagas' : 'Contas recebidas' }}"
                value="{{ $contasReceberRecebidasCount }}" icon="o-check-circle" color="text-green-500"
                description="{{ $contasReceberRecebidasValueFormatted }}" />

            <x-stat title="Contas Atrasadas" value="{{ $contasReceberAtrasadasCount }}" icon="o-exclamation-circle"
                description="{{ $contasReceberAtrasadasValueFormatted }}" color="text-red-500" />

            <x-stat title="Contas próximas de vencer" value="{{ $contasReceberAVencerCount }}"
                icon="o-exclamation-triangle" color="text-yellow-500" description="{{ $contasReceberAVencerValueFormatted }}" />

        </div>
        <div class="md:grid-cols-4 grid grid-cols-2 gap-4">
            <p class="md:col-span-4 col-span-2 px-4 mt-4 opacity-40 bg-gray-100"> Fila de cobrança: </p>

            <x-stat title="Boletos atrasados" value="0" icon="o-document-currency-dollar" color="text-red-500" />

            <x-stat title="Clientes credores" value="0" icon="o-users" color="text-gray-500" />

            <x-stat title="Emails enviados" value="0" icon="o-envelope" color="text-gray-500" />

            <x-stat title="Mensagens enviadas" value="0" icon="o-chat-bubble-oval-left-ellipsis"
                color="text-yellow-500" />

        </div>
    </x-card>
</div>
