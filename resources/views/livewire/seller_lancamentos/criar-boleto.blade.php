<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $emitirBoleto = false;
    public bool $apagarDepois = false;
}; ?>
<div>
    <x-button icon="o-document" spinner class="btn-ghost btn-sm text-yellow-500" tooltip="Emitir boleto" @click="$wire.emitirBoleto = true"/>

    <x-modal wire:model="emitirBoleto" title="Emitir novo boleto" separator>

        <div class="flex gap-4 flex-wrap">
            <x-button icon="o-building-library" spinner class="btn w-full text-red-500" label="Emitir boleto Santander" @click="$wire.apagarDepois = true"/>
            <x-button icon="o-building-library" spinner class="btn w-full text-indigo-500" label="Emitir boleto OMIE" @click="$wire.apagarDepois = true"/>
            <x-button icon="o-building-library" spinner class="btn w-full text-yellow-500" label="Emitir boleto Banco do Brasil" @click="$wire.apagarDepois = true"/>
        </div>
     
        <x-slot:actions>
            <x-button label="Voltar" @click="$wire.emitirBoleto = false" />
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
