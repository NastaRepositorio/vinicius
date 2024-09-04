<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    public int $contasReceberAbertasCount;
    public int $contasReceberAtrasadasCount;
    public int $contasReceberPagasCount;

    public int $contasReceberAbertasValue = 0;
    public int $contasReceberAtrasadasValue = 0;
    public int $contasReceberPagasValue = 0;

    public string $contasReceberAbertasValueFormatted = '';
    public string $contasReceberAtrasadasValueFormatted = '';
    public string $contasReceberPagasValueFormatted = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // Contas em Aberto
        $response = $this->listarContas('AVENCER');
        $this->contasReceberAbertasCount = $response['total_de_registros'];
        $this->contasReceberAbertasValue = $this->sumValues($response);
        $this->contasReceberAbertasValueFormatted = $this->formatNumber($this->contasReceberAbertasValue);

        // Contas Atrasadas
        $response = $this->listarContas('ATRASADO');
        $this->contasReceberAtrasadasCount = $response['total_de_registros'];
        $this->contasReceberAtrasadasValue = $this->sumValues($response);
        $this->contasReceberAtrasadasValueFormatted = $this->formatNumber($this->contasReceberAtrasadasValue);

        // Contas Pagas
        $response = $this->listarContas('PAGO');
        $this->contasReceberPagasCount = $response['total_de_registros'];
        $this->contasReceberPagasValue = $this->sumValues($response);
        $this->contasReceberPagasValueFormatted = $this->formatNumber($this->contasReceberPagasValue);
    }

    public function listarContas(string $status): array
    {
        $usercode = Auth::user()->usercode;

        $url = config('app.omie_url') . '/financas/contareceber/#ListarContasReceber';

        $data = [
            'call' => 'ListarContasReceber',
            'app_key' => config('app.omie_key'),
            'app_secret' => config('app.omie_app_secret'),
            'param' => [
                [
                    'pagina' => 1,
                    'registros_por_pagina' => 5000, // Ajuste conforme necessário
                    'apenas_importado_api' => 'N',
                    'filtrar_por_vendedor' => $usercode,
                    'filtrar_por_status' => $status,
                ],
            ],
        ];

        try {
            $response = Http::timeout(10)->retry(3, 100)->post($url, $data);

            if ($response->successful()) {
                return $response->json(); // Retorne o array completo para manipular no `loadData()`
            } else {
                throw new \Exception('Erro ao consumir a API: ' . $response->status());
            }
        } catch (\Exception $e) {
            throw new \Exception('Erro ao consumir a API: ' . $e->getMessage());
        }
    }

    // Método para somar os valores dos documentos
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

    // Método para formatar os números
    public function formatNumber($number)
    {
        $formattedNumber = number_format($number, 2, ',', '.');
        return 'R$ ' . $formattedNumber;
    }
};
?>

<div>
    <x-header title="Olá, {{ Auth::user()->name }}" separator progress-indicator />

    <x-card>
        <div class="md:grid-cols-3 grid grid-cols-1 gap-4">
            <p class="md:col-span-3 col-span-1 px-4 opacity-40 bg-gray-100"> Informações de Contas do Usuário </p>

            <x-stat title="Contas em Aberto" value="{{ $contasReceberAbertasCount }}" icon="o-exclamation-triangle"
                description="{{ $contasReceberAbertasValueFormatted }}" color="text-yellow-500" />

            <x-stat title="Contas Atrasadas" value="{{ $contasReceberAtrasadasCount }}" icon="o-exclamation-circle"
                description="{{ $contasReceberAtrasadasValueFormatted }}" color="text-red-500" />

            <x-stat title="Contas Pagas" value="{{ $contasReceberPagasCount }}" icon="o-check-circle"
                description="{{ $contasReceberPagasValueFormatted }}" color="text-green-500" />
        </div>
    </x-card>
</div>
