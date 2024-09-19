<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    @if (Auth::user())
        {{-- The navbar with `sticky` and `full-width` --}}
        <x-nav sticky full-width>
            <x-slot:brand>
                {{-- Drawer toggle for "main-drawer" --}}
                <label for="main-drawer" class="lg:hidden mr-3">
                    <x-icon name="o-bars-3" class="cursor-pointer" />
                </label>

                {{-- Brand --}}
                <div>L2R COMÉRCIO</div>
            </x-slot:brand>

            {{-- Right side actions --}}
            <x-slot:actions>
                {{-- <x-button icon="o-bell" link="###" class="btn-ghost btn-sm relative">
                    <p class="hidden md:block">Notificações</p>
                    <x-badge value="12" class="badge-error absolute -right-2 -top-2" />
                </x-button> --}}
                {{-- <x-theme-toggle class="btn-ghost btn-sm rounded-md" responsive /> --}}
                <livewire:components.profile-edit />
                <livewire:components.logout />
            </x-slot:actions>
        </x-nav>
    @endif
    {{-- The main content with `full-width` --}}
    <x-main with-nav full-width>
        @if (Auth::user())
            {{-- This is a sidebar that works also as a drawer on small screens --}}
            {{-- Notice the `main-drawer` reference here --}}
            <x-slot:sidebar drawer="main-drawer" class="bg-base-200">

                {{-- User --}}

                @if ($user = auth()->user())
                    <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover
                        class="pt-2">
                        <x-slot:avatar>
                            <x-icon name="o-user"
                                class="w-12 h-12 bg-gray-200 dark:bg-gray-700 text-gray-400 p-4 rounded-full" />
                        </x-slot:avatar>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                {{-- Activates the menu item when a route matches the `link` property --}}

                <x-menu active-bg-color="bg-indigo-600 text-white" activate-by-route>
                    <p class="px-4 mb-4 opacity-20" x-data="{}">Atividades</p>

                    @if (Auth::user()->usertype === 'seller')
                        <x-menu-item title="Painel do vendedor" icon="o-home" link="{{ route('home_seller') }}" />
                        <x-menu-item title="Lançamentos de vendas" icon="o-clipboard-document-list"
                            link="{{ route('seller_lancamentos') }}" />
                    @endif

                    @if (Auth::user()->usertype === 'user')
                        <x-menu-item title="Painel do cliente" icon="o-home" link="{{ route('home_user') }}" />
                        <x-menu-item title="Minhas Contas" icon="o-clipboard-document-list"
                            link="{{ route('meusboletos') }}" />
                    @endif

                    @if (Auth::user()->usertype === 'admin')
                        <x-menu-item title="Home" icon="o-home" link="{{ route('home') }}" />
                        <x-menu-item title="Contas a receber" icon="o-clipboard-document-list"
                            link="{{ route('contasreceber') }}" />
                    @endif

                    {{-- <x-menu-item title="Tarefas" icon="o-clipboard-document-list" link="{{ route('todos') }}" /> --}}

                    @if (Auth::user()->usertype === 'admin')
                        <x-menu-separator />
                        <p class="p-4 opacity-20">Administrativo</p>
                        <x-menu-item title="Usuários" icon="o-users" link="{{ route('users') }}" />
                        <x-menu-item title="Vendedores" icon="o-user-group" link="{{ route('vendedores') }}" />
                        <x-menu-item title="Clientes" icon="o-folder" link="{{ route('clientes') }}" />
                        <x-menu-item title="Contas Bancárias" icon="o-building-library" link="{{ route('contasbancarias') }}" />
                    @endif

                    {{-- <x-menu-sub title="Settings" icon="o-cog-6-tooth">
                        <x-menu-item title="Wifi" icon="o-wifi" link="####" />
                        <x-menu-item title="Archives" icon="o-archive-box" link="####" />
                    </x-menu-sub> --}}
                </x-menu>
            </x-slot:sidebar>
        @endif
        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />
</body>

</html>
