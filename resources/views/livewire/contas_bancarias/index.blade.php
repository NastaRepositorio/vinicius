<?php

use Livewire\Volt\Component;
use App\Models\ContaBanco;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public function with()
    {
        return [
            'contas' => ContaBanco::all(),
        ];
    }

    public function delete(ContaBanco $contaBanco): void
    {
        $contaBanco->delete();
        $this->success('Conta deletada!');
    }

    #[On('conta-index-refresh')]
    public function refresh(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Contas bancárias" separator progress-indicator>
        <x-slot:actions>
            <livewire:contas_bancarias.create />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($contas as $conta)
            <x-card title="Conta: {{ $conta->nome }}" subtitle="{{ $conta->convenio }}" shadow separator class="border">
                <p>Conta: {{ $conta->conta }}</p>
                <p>Agência: {{ $conta->agencia }}</p>
                <br>
                <x-button wire:click="delete({{ $conta->id }})"
                    wire:confirm="Tem certeza que deja excluir a conta?" spinner icon="o-trash" class="btn-error btn-sm text-white" />
            </x-card>
        @empty
            Nenhuma conta cadastrada ainda
        @endforelse
    </div>
</div>
