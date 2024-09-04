<?php

use App\Models\User;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public $user;

    protected $listeners = ['user-table-refresh' => '$refresh'];

    public array $usertypes = [['id' => 'user', 'name' => 'Cliente'], ['id' => 'seller', 'name' => 'Vendedor'], ['id' => 'admin', 'name' => 'Administrador']];

    public bool $editUserModal = false;

    #[Validate('required|int|max:255')]
    public string $usercode;

    #[Validate('required|string|max:255')]
    public string $name;

    #[Validate('required|string|email|max:255|unique:users')]
    public string $email;

    #[Validate('string|min:8')]
    public string $password = '';

    #[Validate('required|string|in:user,admin,seller')]
    public string $usertype;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->initializeProperties();
    }

    protected function initializeProperties(): void
    {
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->usertype = $this->user->usertype;
        $this->usercode = $this->user->usercode;
    }

    public function editUser()
    {
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'usertype' => $this->usertype,
            'usercode' => $this->usercode,
            'password' => $this->password ? bcrypt($this->password) : $this->user->password,
        ]);

        $this->editUserModal = false;
        $this->dispatch('user-table-refresh');
        $this->success('Usuário atualizado com sucesso!');
    }
}; ?>

<div>
    <x-modal wire:model="editUserModal" class="backdrop-blur">
        <x-form wire:submit="editUser">
            <x-input label="Nome" wire:model="name" />
            <x-input label="Email" wire:model="email" />
            <x-input label="Senha" wire:model="password" type="password" />
            @if($user->usertype === 'user' || $user->usertype === 'seller')
                <x-input label="Código" wire:model="usercode" />
            @endif
            <x-select label="Nível de permissão" wire:model="usertype" :options="$this->usertypes" />

            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.editUserModal = false" />
                <x-button label="Salvar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-button @click="$wire.editUserModal = true" icon="o-pencil" spinner class="btn-ghost btn-sm text-indigo-500" />
</div>
