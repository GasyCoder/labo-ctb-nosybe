<div class="nk-header fixed start-0 w-full h-16 top-0 z-[1021] transition-all duration-300 min-w-[320px]">
    <div class="h-16 border-b bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-900 px-1.5 sm:px-5">
        <div class="container max-w-none">
            <div class="relative flex items-center -mx-1">
                <!-- Mobile Toggle Button -->
                <div class="px-1 me-4 -ms-1.5 xl:hidden">
                    <a href="#" class="sidebar-toggle *:pointer-events-none inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2 before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900">
                        <em class="text-2xl text-slate-600 dark:text-slate-300 ni ni-menu"></em>
                    </a>
                </div>

                <!-- Mobile Logo -->
                <div class="px-1 py-3.5 flex xl:hidden">
                    <a href="{{ url('/') }}" class="relative inline-block transition-opacity duration-300">
                        <img class="h-9 w-auto object-contain opacity-0 dark:opacity-100" 
                            src="{{ \App\Models\Setting::getLogo() }}" 
                            alt="{{ \App\Models\Setting::getNomEntreprise() }}">
                        <img class="h-9 w-auto object-contain opacity-100 dark:opacity-0 absolute left-0 top-0" 
                            src="{{ \App\Models\Setting::getLogo() }}" 
                            alt="{{ \App\Models\Setting::getNomEntreprise() }}">
                    </a>
                </div>

                <!-- Right Side Controls -->
                <div class="px-1 py-3.5 ms-auto">
                    <ul class="flex item-center -mx-1.5 sm:-mx-2.5">
                        <!-- Notification Bell -->
                        <li class="dropdown px-1.5 sm:px-2.5 relative inline-flex">
                            <a tabindex="0" href="#" class="dropdown-toggle *:pointer-events-none peer inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 [&.show]:before:h-10 [&.show]:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 [&.show]:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2 before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900 -me-1.5" data-offset="0,10" data-placement="bottom-end" data-rtl-placement="bottom-start">
                                <div class="relative inline-flex after:content-[''] after:absolute after:rounded-full after:end-0 after:top-px after:h-2.5 after:w-2.5 after:border-2 after:border-white after:bg-sky-400">
                                    <em class="text-2xl leading-none text-slate-600 dark:text-slate-300 ni ni-bell"></em>
                                </div>
                            </a>
                        </li>

                        <!-- User Dropdown -->
                        <li class="dropdown px-1.5 sm:px-2.5 relative inline-flex">
                            <a tabindex="0" href="#" class="dropdown-toggle *:pointer-events-none peer inline-flex items-center group" data-offset="0,10" data-placement="bottom-end" data-rtl-placement="bottom-start">
                                <div class="flex items-center">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-500 h-8 w-8 rounded-full font-medium">
                                        <em class="ni ni-user-alt"></em>
                                    </div>
                                    <div class="hidden md:block ms-4">
                                        <div class="text-xs font-medium leading-none pt-0.5 pb-1.5 text-primary-500 group-hover:text-primary-600">
                                            @auth
                                                @switch(auth()->user()->type)
                                                    @case('admin') Administrator @break
                                                    @case('secretaire') Secrétaire @break
                                                    @case('technicien') Technicien @break
                                                    @case('biologiste') Biologiste @break
                                                    @default Utilisateur
                                                @endswitch
                                            @endauth
                                        </div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs font-bold flex items-center">
                                            {{ Auth::user()->name ?? 'Guest' }}
                                            <em class="text-sm leading-none ms-1 ni ni-chevron-down"></em>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <!-- Dropdown Menu -->
                            <div tabindex="0" class="dropdown-menu clickable absolute max-xs:min-w-[240px] max-xs:max-w-[240px] min-w-[280px] max-w-[280px] border border-t-3 border-gray-200 dark:border-gray-800 border-t-primary-600 dark:border-t-primary-600 bg-white dark:bg-gray-950 rounded shadow hidden peer-[.show]:block z-[1000]">
                                <!-- User Profile -->
                                <div class="hidden sm:block px-7 py-5 bg-slate-50 dark:bg-slate-900 border-b border-gray-200 dark:border-gray-800">
                                    <div class="flex items-center">
                                        <div class="relative flex-shrink-0 flex items-center justify-center text-sm text-white bg-primary-500 h-10 w-10 rounded-full font-medium">
                                            <span>{{ Str::upper(Str::substr(Auth::user()->name ?? 'GU', 0, 2)) }}</span>
                                        </div>
                                        <div class="ms-4 flex flex-col">
                                            <span class="text-sm font-bold text-slate-700 dark:text-white">{{ Auth::user()->name ?? 'Guest User' }}</span>
                                            <span class="text-xs text-slate-400 mt-1">{{ Auth::user()->email ?? 'guest@example.com' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <ul class="py-3">
                                    <li>
                                        <a class="relative px-7 py-2.5 flex items-center rounded-[inherit] text-sm leading-5 font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 hover:dark:text-primary-600 transition-all duration-300" href="{{ route('profile.edit') }}">
                                            <em class="text-lg leading-none w-7 ni ni-user-alt"></em>
                                            <span>Profil</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="relative px-7 py-2.5 flex items-center rounded-[inherit] text-sm leading-5 font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 hover:dark:text-primary-600 transition-all duration-300" href="{{ route('admin.settings') }}">
                                            <em class="text-lg leading-none w-7 ni ni-setting-alt"></em>
                                            <span>Paramètres</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="theme-toggle *:pointer-events-none relative px-7 py-2.5 flex items-center rounded-[inherit] text-sm leading-5 font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 hover:dark:text-primary-600 transition-all duration-300" href="javascript:void(0)">
                                            <div class="flex dark:hidden items-center">
                                                <em class="text-lg leading-none w-7 ni ni-moon"></em>
                                                <span>Mode sombre</span>
                                            </div>
                                            <div class="hidden dark:flex items-center">
                                                <em class="text-lg leading-none w-7 ni ni-sun"></em>
                                                <span>Mode claire</span>
                                            </div>
                                            <div class="ms-auto relative h-6 w-12 rounded-full border-2 border-gray-200 dark:border-primary-600 bg-white dark:bg-primary-600">
                                                <div class="absolute start-0.5 dark:start-6.5 top-0.5 h-4 w-4 rounded-full bg-gray-200 dark:bg-white transition-all duration-300"></div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="block border-t border-gray-200 dark:border-gray-800 my-3"></li>
                                    <li>
                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="relative px-7 py-2.5 flex items-center rounded-[inherit] text-sm leading-5 font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 hover:dark:text-primary-600 transition-all duration-300" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                                <em class="text-lg leading-none w-7 ni ni-signout"></em>
                                                <span>{{ __('Log Out') }}</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- container -->
    </div>
</div><!-- header -->