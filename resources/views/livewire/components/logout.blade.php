<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public bool $myModal2 = false;
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}; ?>

<div>
    <x-button
        icon="o-arrow-left-start-on-rectangle"
        class="btn-ghost btn-sm btn"
        tooltip-bottom="Logout"
        @click="$wire.myModal2 = true" />


    <x-modal wire:model="myModal2" title="Logout" subtitle="Sair da sua conta">
        <div>Tem certeza que deseja sair, {{ Auth::user()->name }}?</div>
     
        <x-slot:actions>
            <x-button label="Cancelar" @click="$wire.myModal2 = false" />
            <x-button label="Confirmar" class="btn-error" wire:click='logout'/>
        </x-slot:actions>
    </x-modal>
</div>
