<div class="nk-sidebar group/sidebar peer dark fixed w-72 [&.is-compact:not(.has-hover)]:w-[74px] min-h-screen max-h-screen overflow-hidden h-full start-0 top-0 z-[1031] transition-[transform,width] duration-300 -translate-x-full rtl:translate-x-full xl:translate-x-0 xl:rtl:translate-x-0 [&.sidebar-visible]:translate-x-0">
    <!-- Logo et boutons de menu (inchangés) -->
    <div class="flex items-center min-w-full w-72 h-16 border-b border-e bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-900 px-6 py-3 overflow-hidden">
        <!-- ... (contenu inchangé) ... -->
    </div>

    <div class="nk-sidebar-body max-h-full relative overflow-hidden w-full bg-white dark:bg-gray-950 border-e border-gray-200 dark:border-gray-900">
        <div class="flex flex-col w-full h-[calc(100vh-theme(spacing.16))]">
            <div class="h-full pt-4 pb-10" data-simplebar>
                <ul class="nk-menu">
                    <!-- Menu principal -->
                    <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                        <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Menus</h6>
                    </li>

                    <!-- Dashboard -->
                    <li class="nk-menu-item py-0.5{{ request()->routeIs('dashboard') ? ' active' : '' }} group/item">
                        <a href="{{ route('dashboard') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                            <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-home"></em>
                            </span>
                            <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Accueil</span>
                        </a>
                    </li>

                    <!-- Archives (accessible à tous les utilisateurs connectés) -->
                    <li class="nk-menu-item py-0.5{{ request()->routeIs('archives') ? ' active' : '' }} group/item">
                        <a href="{{ route('archives') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                            <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-archived"></em>
                            </span>
                            <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Archives</span>
                        </a>
                    </li>

                    {{-- Section Secrétaire --}}
                    @if(auth()->check() && auth()->user()->type === 'secretaire')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Secrétaire</h6>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.prescriptions', 'secretaire.add-prescription') ? ' active' : '' }} group/item">
                            <a href="{{ route('secretaire.prescriptions') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-edit-alt"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    Prescriptions
                                </span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.patients') ? ' active' : '' }} group/item">
                            <a href="{{ route('secretaire.patients') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-users"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Patients</span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('secretaire.prescripteurs') ? ' active' : '' }} group/item">
                            <a href="{{ route('secretaire.prescripteurs') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-user-list"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Prescripteurs</span>
                            </a>
                        </li>
                    @endif

                    {{-- Section Laboratoire (Techniciens, Biologistes, Admins) --}}
                    @if(auth()->check() && in_array(auth()->user()->type, ['technicien', 'biologiste', 'admin']))
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Laboratoire</h6>
                        </li>

                        <!-- Menu Analyses -->
                        <li class="nk-menu-item py-0.5 has-sub group/item{{ request()->routeIs('laboratoire.analyses.*') ? ' active' : '' }}">
                            <a href="#" class="nk-menu-link sub nk-menu-toggle flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-coins"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Analyses</span>
                                <em class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-base leading-none text-slate-400 group-[.active]/item:text-primary-500 absolute end-5 top-1/2 -translate-y-1/2 rtl:-scale-x-100 group-[.active]/item:rotate-90 group-[.active]/item:rtl:-rotate-90 transition-all duration-300 icon ni ni-chevron-right"></em>
                            </a>

                            <ul class="nk-menu-sub mb-1 hidden group-[&.is-compact:not(.has-hover)]/sidebar:!hidden"{{ request()->routeIs('laboratoire.analyses.*') ? ' style=display:block' : '' }}>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.analyses.examens') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.analyses.examens') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Examens</span> 
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.analyses.types') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.analyses.types') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Types d'analyses</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.analyses.listes') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.analyses.listes') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Listes Analyses</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.analyses.prelevements') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.analyses.prelevements') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Prélèvements</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Menu Microbiologie -->
                        <li class="nk-menu-item py-0.5 has-sub group/item{{ request()->routeIs('laboratoire.microbiologie.*') ? ' active' : '' }}">
                            <a href="#" class="nk-menu-link sub nk-menu-toggle flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-coins"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Germes</span>
                                <em class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-base leading-none text-slate-400 group-[.active]/item:text-primary-500 absolute end-5 top-1/2 -translate-y-1/2 rtl:-scale-x-100 group-[.active]/item:rotate-90 group-[.active]/item:rtl:-rotate-90 transition-all duration-300 icon ni ni-chevron-right"></em>
                            </a>

                            <ul class="nk-menu-sub mb-1 hidden group-[&.is-compact:not(.has-hover)]/sidebar:!hidden"{{ request()->routeIs('laboratoire.microbiologie.*') ? ' style=display:block' : '' }}>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.microbiologie.familles-bacteries') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.microbiologie.familles-bacteries') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Familles bactéries</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.microbiologie.bacteries') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.microbiologie.bacteries') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Bactéries</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item py-px sub has-sub group/sub1{{ request()->routeIs('laboratoire.microbiologie.antibiotiques') ? ' active' : '' }}">
                                    <a href="{{ route('laboratoire.microbiologie.antibiotiques') }}" class="nk-menu-link flex relative items-center align-middle py-1.5 pe-10 ps-[calc(theme(spacing.6)+theme(spacing.9))] font-normal leading-5 text-sm tracking-normal normal-case">
                                        <span class="text-slate-600 dark:text-slate-500 group-[.active]/sub1:text-primary-500 hover:text-primary-500 whitespace-nowrap flex-grow inline-block">Antibiotiques</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- Section Administration (Admin seulement) --}}
                    @if(auth()->check() && auth()->user()->type === 'admin')
                        <li class="relative first:pt-1 pt-10 pb-2 px-6 before:absolute before:h-px before:w-full before:start-0 before:top-1/2 before:bg-gray-200 dark:before:bg-gray-900 first:before:hidden before:opacity-0 group-[&.is-compact:not(.has-hover)]/sidebar:before:opacity-100">
                            <h6 class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 text-slate-400 dark:text-slate-300 whitespace-nowrap uppercase font-bold text-xs tracking-relaxed leading-tight">Administration</h6>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('admin.users') ? ' active' : '' }} group/item">
                            <a href="{{ route('admin.users') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
                                <span class="font-normal tracking-normal w-9 inline-flex flex-grow-0 flex-shrink-0 text-slate-400 group-[.active]/item:text-primary-500 group-hover:text-primary-500">
                                    <em class="text-2xl leading-none text-current transition-all duration-300 icon ni ni-users"></em>
                                </span>
                                <span class="group-[&.is-compact:not(.has-hover)]/sidebar:opacity-0 flex-grow-1 inline-block whitespace-nowrap transition-all duration-300 text-slate-600 dark:text-slate-500 group-[.active]/item:text-primary-500 group-hover:text-primary-500">Utilisateurs</span>
                            </a>
                        </li>

                        <li class="nk-menu-item py-0.5{{ request()->routeIs('admin.settings') ? ' active' : '' }} group/item">
                            <a href="{{ route('admin.settings') }}" class="nk-menu-link flex relative items-center align-middle py-2.5 ps-6 pe-10 font-heading font-bold tracking-snug group">
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