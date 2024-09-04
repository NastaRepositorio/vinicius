<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $boletoOpcoes = false;
    public bool $apagarDepois = false;
}; ?>

<div>
    <x-button icon="o-adjustments-vertical" spinner class="btn-ghost btn-sm text-gray-500" tooltip="Opções" @click="$wire.boletoOpcoes = true"/>

    <x-modal wire:model="boletoOpcoes" title="Emitir novo boleto" separator>

        <div class="flex gap-4 flex-wrap">
            <x-button icon="o-arrow-down-tray" spinner class="btn-primary text-white w-full" label="Baixar boleto"  @click="$wire.apagarDepois = true" />
            <x-button icon="o-calendar-days" spinner class="btn-error text-white w-full" label="Prorrogar/modificar boleto" @click="$wire.apagarDepois = true" />
            <x-button icon="o-archive-box-x-mark" spinner class="btn-neutral text-white w-full" label="Cancelar boleto" @click="$wire.apagarDepois = true" />
        </div>
     
        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.boletoOpcoes = false" />
        </x-slot:actions>
    </x-modal>

    <x-modal wire:model="apagarDepois" title="Em breve..." separator>

        <div class="flex gap-4 flex-wrap">
            <div class="w-full">
                <x-icon name="o-exclamation-triangle" class="text-red-500 w-12 h-12"/>
            </div>
            <div class="w-full text-center">
                <h3 class="text-lg font-semibold">Ainda indisponível</h3>
                <p class="text-sm text-gray-500">Em breve será adicionado essa feature.</p>
            </div>
        </div>
     
        <x-slot:actions>
            <x-button label="Voltar" @click="$wire.apagarDepois = false" />
        </x-slot:actions>
    </x-modal>
</div>
