<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public bool $createUserModal = false;
    public array $vendedor;

    #[Validate('required|int|max:255')]
    public string $usercode;
    #[Validate('required|string|max:255')]
    public string $name;
    #[Validate('required|string|email|max:255|unique:users')]
    public string $email;
    #[Validate('required|string|min:8')]
    public string $password = '';
    #[Validate('required|string|in:seller')]
    public string $usertype = 'seller';

    public function mount(array $vendedor): void
    {
        $this->vendedor = $vendedor;
        $this->name = $vendedor["nome"];
        $this->email = $vendedor["email"];
        $this->usercode = $vendedor['codigo'];
    }

    public function createUser()
    {
        User::create([
            'usercode' => $this->usercode,
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'usertype' => $this->usertype,
        ]);

        $this->dispatch('user-table-refresh');

        $this->usercode = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->usertype = 'seller';
        $this->createUserModal = false;

        $this->success('Usuário criado.');
    }
}; ?>

<div>
    <x-button icon="o-users" @click="$wire.createUserModal = true" class="btn-ghost btn-sm text-indigo-500" tooltip="Criar conta para o vendedor"/>

    <x-modal wire:model='createUserModal' title="Criar novo usuário" subtitle="Preencha as informações abaixo">

        <x-form wire:submit="createUser" x-data="{ usertype: 'user' }">

            <x-input class="input-bordered" label="Nome" wire:model="name" />

            <!-- Campo de Código -->
            <div x-show="usertype === 'user' || usertype === 'seller'" x-transition>
                <x-input label="Código" wire:model="usercode" />
            </div>

            <x-input label="Email" wire:model="email" />
            <x-input label="Senha" wire:model="password" type="password" />

            <label class="form-control">
                <div class="label">
                    <span class="label-text">Nível de permissão</span>
                </div>
                <select class="select select-primary" wire:model="usertype" x-model="usertype" disabled>
                    <option value="user">Cliente</option>
                    <option value="admin">Administrador</option>
                    <option value="seller" selected>Vendedor</option>
                </select>
            </label>

            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.createUserModal = false" />
                <x-button label="Salvar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
