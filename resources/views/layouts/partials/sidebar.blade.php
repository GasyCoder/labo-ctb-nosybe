{{-- resources/views/components/sidebar.blade.php --}}
<div class="nk-sidebar group/sidebar peer dark fixed w-72 [&.is-compact:not(.has-hover)]:w-[74px] min-h-screen max-h-screen overflow-hidden h-full start-0 top-0 z-[1031] transition-[transform,width] duration-300 -translate-x-full rtl:translate-x-full xl:translate-x-0 xl:rtl:translate-x-0 [&.sidebar-visible]:translate-x-0">
    <div class="flex items-center min-w-full w-72 h-16 border-b border-e bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-900 px-6 py-3 overflow-hidden">
        <div class="-ms-1 me-4">
            <div class="hidden xl:block">
                <a href="#" class="sidebar-compact-toggle *:pointer-events-none inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2  before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900">
                    <em class="text-2xl text-slate-600 dark:text-slate-300 ni ni-menu"></em>
                </a>
            </div>
            <div class="xl:hidden">
                <a href="#" class="sidebar-toggle *:pointer-events-none inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2  before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900">
                    <em class="text-2xl text-slate-600 dark:text-slate-300 rtl:-scale-x-100 ni ni-arrow-left"></em>
                </a>
            </div>
        </div>
        <div class="relative flex flex-shrink-0">
            <a href="{{ url('/') }}" class="relative inline-block transition-opacity duration-300 h-9 group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0">
                <img class="h-full opacity-0 dark:opacity-100" src="{{ asset('images/logo.png') }}" srcset="{{ asset('images/logo2x.png 2x') }}" alt="{{ site_info('name') }}">
                <img class="h-full opacity-100 dark:opacity-0 absolute start-0 top-0" src="{{ asset('images/logo-dark.png') }}" srcset="{{ asset('images/logo-dark2x.png 2x') }}" alt="{{ site_info('name') }}">
            </a>
        </div>
    </div>
    
    <div class="nk-sidebar-body max-h-full relative overflow-hidden w-full bg-white dark:bg-gray-950 border-e border-gray-200 dark:border-gray-900">
        <div class="flex flex-col w-full h-[calc(100vh-theme(spacing.16))]">
            <div class="h-full pt-4 pb-10" data-simplebar>
                
                <ul class="nk-menu">
                    <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                        <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Menus</h6>
                    </li>
                    
                    <!-- Accueil - pour tous -->
                    <li class="nk-menu-item py-0.5{{ request()->routeIs('dashboard') ? ' active' : '' }} group/item" data-route="dashboard">
                        <a href="{{ route('dashboard') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                            <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-home"></em>
                            </span>
                            <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Accueil</span>
                        </a>
                    </li>
                    
                    {{-- Menus spécifiques aux secrétaires --}}
                    @if(auth()->check() && auth()->user()->type === 'secretaire')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Secrétariat</h6>
                        </li>
                        
                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.prescriptions') ? ' active' : '' }} group/item" data-route="secretaire.prescriptions">
                            <a href="{{ route('secretaire.prescriptions') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-edit-alt"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Prescriptions</span>
                            </a>
                        </li>
                        
                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.paiements') ? ' active' : '' }} group/item" data-route="secretaire.paiements">
                            <a href="{{ route('secretaire.paiements') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-cc-alt2"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Paiements</span>
                            </a>
                        </li>
                        
                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.patients') ? ' active' : '' }} group/item" data-route="secretaire.patients">
                            <a href="{{ route('secretaire.patients') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-users"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Patients</span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.prescripteurs') ? ' active' : '' }} group/item" data-route="secretaire.prescripteurs">
                            <a href="{{ route('secretaire.prescripteurs') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-user-list"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Prescripteurs</span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('archives') ? ' active' : '' }} group/item" data-route="archives">
                            <a href="{{ route('archives') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-archived"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Archives</span>
                            </a>
                        </li>
                    @endif

                    {{-- Menus pour les techniciens --}}
                    @if(auth()->check() && auth()->user()->type === 'technicien')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Technique</h6>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('techniciens') ? ' active' : '' }} group/item" data-route="techniciens">
                            <a href="{{ route('techniciens') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-account-setting-alt"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Techniciens</span>
                            </a>
                        </li>
                    @endif

                    {{-- Menu spécifique aux biologistes --}}
                    @if(auth()->check() && auth()->user()->type === 'biologiste')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Biologie</h6>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('biologistes') ? ' active' : '' }} group/item" data-route="biologistes">
                            <a href="{{ route('biologistes') }}"   class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-edit-profile"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Biologistes</span>
                            </a>
                        </li>
                    @endif

                    {{-- Section Traitements - visible pour techniciens, biologistes et admins --}}
                    @if(auth()->check() && in_array(auth()->user()->type, ['technicien', 'biologiste', 'admin']))
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Traitements</h6>
                        </li>

                        @php
                            // Détecter si on est dans la section analyses
                            $analysesRoutes = ['examens', 'types-analyses', 'listes-analyses', 'prelevements'];
                            $isAnalysesActive = collect($analysesRoutes)->contains(fn($route) => request()->routeIs($route));
                        @endphp

                        <!-- Menu Analyses avec sous-menus -->
                        <li class="nk-menu-item py-0.5 has-sub group/item{{ $isAnalysesActive ? ' active' : '' }}" data-menu="analyses">
                            <a href="javascript:void(0)" class="nk-menu-link sub nk-menu-toggle flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-coins"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    Analyses
                                </span>
                                <em class="chevron-icon group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-base leading-none text-slate-400 group-[.active]/item:text-primary-500 absolute end-5 top-1/2 -translate-y-1/2 rtl:-scale-x-100 group-[.active]/item:rotate-90 group-[.active]/item:rtl:-rotate-90 transition-all duration-300 icon ni ni-chevron-right"></em>
                            </a>

                            <ul class="nk-menu-sub submenu mb-1{{ $isAnalysesActive ? ' !block' : ' hidden' }} group-[&.is-compact:not(.has-hover)]/sidebar:!hidden">
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('examens') ? ' active' : '' }}" data-route="examens">
                                    <a href="{{ route('examens') }}"   class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Examens
                                        </span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('types-analyses') ? ' active' : '' }}" data-route="types-analyses">
                                    <a href="{{ route('types-analyses') }}"   class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Types d'analyses
                                        </span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('listes-analyses') ? ' active' : '' }}" data-route="listes-analyses">
                                    <a href="{{ route('listes-analyses') }}"   class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Listes Analyses
                                        </span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('prelevements') ? ' active' : '' }}" data-route="prelevements">
                                    <a href="{{ route('prelevements') }}"  class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Prélèvements
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        @php
                            // Détecter si on est dans la section germes/microbiologie
                            $germesRoutes = ['familles-bacteries', 'bacteries', 'antibiotiques'];
                            $isGermesActive = collect($germesRoutes)->contains(fn($route) => request()->routeIs($route));
                        @endphp

                        <!-- Menu Germes avec sous-menus -->
                        <li class="nk-menu-item py-0.5 has-sub group/item{{ $isGermesActive ? ' active' : '' }}" data-menu="germes">
                            <a href="javascript:void(0)" class="nk-menu-link sub nk-menu-toggle flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-coins"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    Germes
                                </span>
                                <em class="chevron-icon group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-base leading-none text-slate-400 group-[.active]/item:text-primary-500 absolute end-5 top-1/2 -translate-y-1/2 rtl:-scale-x-100 group-[.active]/item:rotate-90 group-[.active]/item:rtl:-rotate-90 transition-all duration-300 icon ni ni-chevron-right"></em>
                            </a>

                            <ul class="nk-menu-sub submenu mb-1{{ $isGermesActive ? ' !block' : ' hidden' }} group-[&.is-compact:not(.has-hover)]/sidebar:!hidden">
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('familles-bacteries') ? ' active' : '' }}" data-route="familles-bacteries">
                                    <a href="{{ route('familles-bacteries') }}"  class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Familles bactéries
                                        </span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('bacteries') ? ' active' : '' }}" data-route="bacteries">
                                    <a href="{{ route('bacteries') }}"  class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Bactéries
                                        </span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('antibiotiques') ? ' active' : '' }}" data-route="antibiotiques">
                                    <a href="{{ route('antibiotiques') }}"  class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">
                                            Antibiotiques
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Section Administration - visible uniquement pour les admins --}}
                    @if(auth()->check() && auth()->user()->type === 'admin')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Administration</h6>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('users') ? ' active' : '' }} group/item" data-route="users">
                            <a href="{{ route('users') }}"  class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-users"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Utilisateurs</span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('settings') ? ' active' : '' }} group/item" data-route="settings">
                            <a href="{{ route('settings') }}"  class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-setting"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Paramètres</span>
                            </a>
                        </li>
                    @endif
                </ul>
                
            </div>
        </div>
    </div>
</div><!-- sidebar -->
<div class="sidebar-toggle fixed inset-0 bg-slate-950 bg-opacity-20 z-[1030] opacity-0 invisible peer-[.sidebar-visible]:opacity-100 peer-[.sidebar-visible]:visible xl:!opacity-0 xl:!invisible"></div>

{{-- JavaScript pour la gestion des menus --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    let openMenus = new Set();
    
    // Obtenir la route actuelle
    const currentRoute = getCurrentRoute();
    
    // Initialiser les menus
    initializeMenus();
    
    // Restaurer l'état des menus
    restoreMenuState();
    
    // Configurer les event listeners
    setupEventListeners();

    function getCurrentRoute() {
        // Extraire le nom de route depuis l'URL Laravel
        const path = window.location.pathname;
        const segments = path.split('/').filter(Boolean);
        
        // Gérer les différents patterns d'URL
        if (segments.length === 0) return 'dashboard';
        if (segments[0] === 'dashboard') return 'dashboard';
        if (segments[0] === 'admin' && segments[1]) return segments[1];
        if (segments[0] === 'secretaire' && segments[1]) return `secretaire.${segments[1]}`;
        if (segments[0] === 'technicien' && segments[1]) return segments[1];
        if (segments[0] === 'biologiste' && segments[1]) return segments[1];
        
        return segments[segments.length - 1] || 'dashboard';
    }

    function initializeMenus() {
        // Ouvrir le menu parent si nécessaire pour la route actuelle
        if (menuConfig[currentRoute]) {
            const parentMenu = menuConfig[currentRoute];
            openSubmenu(parentMenu);
        }
    }

    function setupEventListeners() {
        // Gérer les clics sur les toggles de menu
        document.querySelectorAll('.nk-menu-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const menuItem = this.closest('.has-sub');
                const menuName = menuItem?.getAttribute('data-menu');
                
                if (menuName) {
                    toggleSubmenu(menuName);
                }
            });
        });

        // Écouter les événements Livewire
        document.addEventListener('live d', function() {
            setTimeout(() => {
                restoreMenuState();
            }, 100);
        });

        // Sauvegarder avant la navigation
        document.addEventListener('live ', function() {
            saveMenuState();
        });
    }

    function toggleSubmenu(menuName) {
        if (openMenus.has(menuName)) {
            closeSubmenu(menuName);
        } else {
            openSubmenu(menuName);
        }
        saveMenuState();
    }

    function openSubmenu(menuName) {
        const menuItem = document.querySelector(`[data-menu="${menuName}"]`);
        if (!menuItem) return;

        const submenu = menuItem.querySelector('.nk-menu-sub');
        if (!submenu) return;

        submenu.classList.remove('hidden');
        submenu.classList.add('!block');
        menuItem.classList.add('active');
        openMenus.add(menuName);
    }

    function closeSubmenu(menuName) {
        const menuItem = document.querySelector(`[data-menu="${menuName}"]`);
        if (!menuItem) return;

        const submenu = menuItem.querySelector('.nk-menu-sub');
        if (!submenu) return;

        // Vérifier s'il y a un sous-menu actif
        const hasActiveSubmenu = submenu.querySelector('.nk-menu-item.active');
        
        if (!hasActiveSubmenu) {
            submenu.classList.add('hidden');
            submenu.classList.remove('!block');
            menuItem.classList.remove('active');
            openMenus.delete(menuName);
        }
    }

    function saveMenuState() {
        try {
            const state = {
                openMenus: Array.from(openMenus),
                timestamp: Date.now()
            };
            localStorage.setItem('sidebar_menus', JSON.stringify(state));
        } catch (e) {
            console.warn('Impossible de sauvegarder l\'état des menus:', e);
        }
    }

    function restoreMenuState() {
        try {
            const saved = localStorage.getItem('sidebar_menus');
            if (!saved) return;

            const state = JSON.parse(saved);
            
            // Vérifier que l'état n'est pas trop ancien (1 heure)
            if (Date.now() - state.timestamp > 3600000) {
                localStorage.removeItem('sidebar_menus');
                return;
            }

            // Restaurer les menus ouverts
            if (state.openMenus) {
                state.openMenus.forEach(menuName => {
                    openSubmenu(menuName);
                });
            }

            // S'assurer que le menu parent de la route actuelle est ouvert
            if (menuConfig[currentRoute]) {
                openSubmenu(menuConfig[currentRoute]);
            }

        } catch (e) {
            console.warn('Erreur lors de la restauration des menus:', e);
            localStorage.removeItem('sidebar_menus');
        }
    }
});
</script>

