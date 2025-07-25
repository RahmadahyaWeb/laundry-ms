<nav class="fixed top-0 z-40 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="/" class="flex ms-2 md:me-24">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <div>
                        <button type="button"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full"
                                src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                        </button>
                    </div>
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm"
                        id="dropdown-user">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900" role="none">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate" role="none">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Dashboard</a>
                            </li>
                            <li>
                                <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Sign out</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-30 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white">
        @php
            $menus = json_decode(file_get_contents(resource_path('data/menu.json')), true);
        @endphp

        <ul class="space-y-2 font-medium">
            @foreach ($menus as $menu)
                @php
                    $hasChildren = isset($menu['children']);
                    $canViewMenu = $hasChildren
                        ? collect($menu['children'])->contains(
                            fn($child) => auth()->user()->hasAnyPermission($child['permissions']),
                        )
                        : auth()->user()->hasAnyPermission($menu['permissions']);
                @endphp

                @if ($canViewMenu)
                    @if (!$hasChildren)
                        <li>
                            <a href="{{ route($menu['route']) }}"
                                class="flex items-center py-2 rounded-lg group
                        {{ request()->routeIs($menu['route']) ? 'text-zinc-800 bg-gray-100' : 'text-gray-900 hover:bg-gray-100 ' }}">
                                <span class="ms-3">{{ $menu['title'] }}</span>
                            </a>
                        </li>
                    @else
                        @php $menuId = Str::slug($menu['title']) . '-dropdown'; @endphp
                        <li>
                            <button type="button"
                                class="flex items-center w-full py-2 pr-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 "
                                aria-controls="{{ $menuId }}" data-collapse-toggle="{{ $menuId }}">
                                <span
                                    class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">{{ $menu['title'] }}</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>
                            <ul id="{{ $menuId }}"
                                class="{{ Request::is(Str::slug($menu['active']) . '*') ? '' : 'hidden' }} py-2 space-y-2">
                                @foreach ($menu['children'] as $child)
                                    @canany($child['permissions'])
                                        <li>
                                            <a href="{{ route($child['route']) }}"
                                                class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group 
                                        {{ request()->routeIs($child['route'] . '*') ? 'text-zinc-800 bg-gray-100' : 'text-gray-900 hover:bg-gray-100 ' }}">
                                                {{ $child['title'] }}
                                            </a>
                                        </li>
                                    @endcanany
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </div>
</aside>
