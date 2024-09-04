<?php

use App\Models\User;
use App\Models\Todo;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public bool $createTodoModal = false;
    public $users;

    public $priorities = [
        ['id' => 1, 'name' => 'P1'],
        ['id' => 2, 'name' => 'P2'],
        ['id' => 3, 'name' => 'P3'],
        ['id' => 4, 'name' => 'P4'],
    ];

    #[Validate('required|string|max:255')]
    public string $title = '';
    #[Validate('required|string')]
    public string $description = '';
    #[Validate('required|int|in:1,2,3,4')]
    public int $priority = 4;
    #[Validate('required|int')]
    public int $user_id = 1;
    #[Validate('required|date')]
    public $limit_date = null;

    public function mount()
    {
        $this->users = User::all();
    }

    public function createTodo()
    {
        Todo::create([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'user_id' => $this->user_id,
            'limit_date' => $this->limit_date,
        ]);

        $this->dispatch('todo-table-refresh');

        $this->title = '';
        $this->description = '';
        $this->priority = 4;
        $this->user_id = 1;
        $this->limit_date = null;
        
        $this->createTodoModal = false;
        $this->success('Tarefa criada.');
    }
}; ?>

<div>
    <x-button label="Nova tarefa" icon="o-clipboard-document-list" @click="$wire.createTodoModal = true" />

    <x-modal wire:model='createTodoModal' title="Criar nova tarefa" subtitle="Preencha as informações abaixo">

        <x-form wire:submit="createTodo">
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
                <x-button label="Cancelar" @click="$wire.createTodoModal = false" />
                <x-button label="Salvar" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
