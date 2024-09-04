<?php

use App\Models\Todo;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Mary\Traits\Toast;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public array $expanded = [];

    public string $search = '';

    public array $sortBy = ['column' => 'limit_date', 'direction' => 'asc'];

    #[On('todo-table-refresh')]
    public function refreshUsers(): void
    {
        $this->resetPage();
    }

    public function headers(): array
    {
        return [['key' => 'priority', 'label' => 'Prioridade', 'class' => 'w-10 text-center'], ['key' => 'user', 'label' => 'Responsável', 'class' => 'w-12 text-center', 'sortable' => false], ['key' => 'title', 'label' => 'Título', 'sortable' => false], ['key' => 'description', 'label' => 'Descrição', 'class' => 'hidden lg:table-cell', 'sortable' => false], ['key' => 'limit_date', 'label' => 'Data Limite', 'class' => 'w-32 text-center']];
    }

    public function todos(): LengthAwarePaginator
    {
        return Todo::query()
            ->orderBy('finished', 'asc')
            ->orderBy('priority', 'asc')
            ->orderBy('limit_date', 'asc')
            ->orderBy('updated_at', 'desc')
            ->where('title', 'like', '%' . $this->search . '%')
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'todos' => $this->todos(),
            'headers' => $this->headers(),
        ];
    }

    public function toggleFinished(Todo $todo): void
    {
        $todo->finished = !$todo->finished;
        $todo->save();
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
        $this->dispatch('todo-table-refresh');
        $this->success('Tarefa deletada!');
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Tarefas" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Procurar..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <livewire:todos.create />
            <x-button label="Filtros" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-warning" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        @php
            $row_decoration = [
                'bg-red-500/25' => fn(Todo $todo) => $todo->limit_date<now() && $todo->finished === 0,
                'text-red-500' => fn(Todo $todo) => $todo->limit_date<now() && $todo->finished === 0,
                'opacity-10 dark:opacity-50' => fn(Todo $todo) => $todo->finished === 1,
            ];
        @endphp
        <x-table wire:model="expanded" :headers="$headers" :rows="$todos" :sort-by="$sortBy" expandable with-pagination
            :row-decoration="$row_decoration">


            @scope('expansion', $todo)
                <div class="border border-dashed rounded-lg p-5 ">
                    <div class="grid grid-cols-3">
                        <div>
                            <strong>Data limite:</strong> {{ $todo->limit_date }}
                        </div>
                        <div class="col-span-2">
                            <strong>Título:</strong> {{ $todo->title }}
                        </div>
                        <div class="col-span-3 mt-2">
                            <strong>Descrição:</strong> {{ $todo->description }}
                        </div>
                        <div class="mt-2">
                            <strong>Prioridade:</strong> {{ $todo->priority }}
                        </div>
                        <div class="mt-2">
                            <strong>Status:</strong> {{ $todo->finished ? 'Concluído' : 'Em andamento' }}
                        </div>
                        <div class="mt-2">
                            <strong>Responsável:</strong> {{ $todo->user->name }}
                        </div>
                    </div>
                </div>
            @endscope
            @scope('cell_user', $todo)
                {{ $todo->user->name }}
            @endscope
            @scope('actions', $todo)
                <div class="flex gap-1">
                    <livewire:todos.edit :todo="$todo"
                        wire:key="edit-todo-{{ $todo->id }}-{{ now()->timestamp }}" />

                    <x-button :icon="$todo->finished ? 'o-x-mark' : 'o-check'" wire:click="toggleFinished({{ $todo->id }})" spiner
                        class="{{ 'btn-ghost btn-sm ' . ($todo->finished ? 'text-gray-600' : 'text-green-500') }}"
                        :tooltip="$todo->finished ? 'Desmarcar concluída' : 'Concluir'" />
                    <x-button icon="o-trash" wire:click="delete({{ $todo->id }})"
                        wire:confirm="Tem certeza que deja excluir a tarefa (Essa ação não pode ser desfeita)?" spinner
                        class="btn-ghost btn-sm text-red-500" tooltip="Deletar" />
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
