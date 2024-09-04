<?php

use App\Models\User;
use App\Models\Todo;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public bool $editTodoModal = false;

    public $priorities = [
        ['id' => 1, 'name' => 'P1'],
        ['id' => 2, 'name' => 'P2'],
        ['id' => 3, 'name' => 'P3'],
        ['id' => 4, 'name' => 'P4'],
    ];

    public $users;
    public $todo;
    #[Validate('required|string|max:255')]
    public string $title;
    #[Validate('required|string')]
    public string $description;
    #[Validate('required|int|in:1,2,3,4')]
    public int $priority;
    #[Validate('required|int')]
    public int $user_id;
    #[Validate('required|date')]
    public $limit_date;

    public function mount(Todo $todo): void
    {
        $this->users = User::all();
        $this->todo = $todo;
        $this->initializeProperties();
    }

    protected function initializeProperties(): void
    {
        $this->title = $this->todo->title;
        $this->description = $this->todo->description;
        $this->priority = $this->todo->priority;
        $this->user_id = $this->todo->user_id;
        $this->limit_date = $this->todo->limit_date;
    }

    public function editTodo()
    {
        $this->todo->update([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'user_id' => $this->user_id,
            'limit_date' => $this->limit_date,
        ]);

        $this->dispatch('todo-table-refresh');
        
        $this->editTodoModal = false;
        $this->success('Tarefa alterada.');
    }
}; ?>

<div>
    <x-button icon="o-pencil" class="text-blue-500 btn-sm btn-ghost" @click="$wire.editTodoModal = true" tooltip="Editar"/>

    <x-modal wire:model='editTodoModal' title="Editar a tarefa #{{ $todo->id }}" subtitle="Preencha as informações abaixo">

        <x-form wire:submit="editTodo">
            <x-input label="Título da tarefa" wire:model="title" />
            <x-textarea label="Descrição" wire:model="description" />
            <x-radio label="Prioridade" :options="$priorities" wire:model="priority" />
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <x-datetime label="Data de entrega" wire:model="limit_date" />
                </div>
                <x-select label="Executor" icon="o-user" :options="$users" wire:model="selectedUser" />
            </div>
            <x-slot:actions>
                <x-button label="Cancelar" @click="$wire.editTodoModal = false" />
                <x-button label="Salvar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
