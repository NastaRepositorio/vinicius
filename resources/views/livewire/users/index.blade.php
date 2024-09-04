<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function delete(User $user): void
    {
        if ($user->id === 1) {
            $this->error('O admin não pode ser deletado!');
            return;
        }
        $user->delete();
        $this->success('Usuário deletado!');
    }

    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#', 'class' => 'w-1 text-center'], ['key' => 'name', 'label' => 'Nome', 'class' => 'w-32 text-center'], ['key' => 'email', 'label' => 'E-mail', 'sortable' => false, 'class' => 'text-center'], ['key' => 'usertype', 'label' => 'Nível de permissão', 'class' => 'text-center']];
    }
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->orderBy(...array_values($this->sortBy))
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers(),
        ];
    }

    #[On('user-table-refresh')]
    public function refreshUsers(): void
    {
        $this->resetPage();
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Usuários" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Procurar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <livewire:users.create />
            {{-- <x-button label="Filtros" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-warning" /> --}}
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->

    <x-card>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
            @scope('cell_usertype', $user)
                @if ($user->usertype === 'admin')
                    Administrador
                @endif
                @if ($user->usertype === 'seller')
                    Vendedor
                @endif
                @if ($user->usertype === 'user')
                    Cliente
                @endif
            @endscope
            @scope('actions', $user)
                <div class="flex gap-1">
                    <livewire:users.edit :user="$user"
                        wire:key="edit-user-{{ $user->id }}-{{ now()->timestamp }}" />
                    <x-button icon="o-trash" wire:click="delete({{ $user->id }})"
                        wire:confirm="Tem certeza que deja excluir o usuário?" spinner
                        class="btn-ghost btn-sm text-red-500" />
                </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filtrar" right with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
            @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Cancelar" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Pronto" icon="o-check" class="btn-success" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
