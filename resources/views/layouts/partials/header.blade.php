<div class="nk-header fixed start-0 w-full h-16 top-0 z-[1021] transition-all duration-300 min-w-[320px]">
    <div class="h-16 border-b bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-900 px-1.5 sm:px-5">
        <div class="container max-w-none">
            <div class="relative flex items-center -mx-1">
                <div class="px-1 me-4 -ms-1.5 xl:hidden">
                    <a href="#" class="sidebar-toggle *:pointer-events-none inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2  before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900">
                        <em class="text-2xl text-slate-600 dark:text-slate-300 ni ni-menu"></em>
                    </a>
                </div>
                <div class="px-1 py-3.5 flex xl:hidden">
                    <a href="{{ url('/') }}" class="relative inline-block transition-opacity duration-300 h-9">
                        <img class="h-full opacity-0 dark:opacity-100" src="{{ asset('images/logo.png') }}" srcset="{{ asset('images/logo2x.png 2x') }}" alt="{{ site_info('name') }}">
                        <img class="h-full opacity-100 dark:opacity-0 absolute start-0 top-0" src="{{ asset('images/logo-dark.png') }}" srcset="{{ asset('images/logo-dark2x.png 2x') }}" alt="{{ site_info('name') }}">
                    </a>
                </div>
                <div class="px-1 py-2 hidden xl:block">
                    <a class="flex items-center transition-all duration-300" href="#">
                        <div class="w-8 inline-flex flex-shrink-0">
                            <em class="text-2xl leading-none text-primary-600 ni ni-card-view"></em>
                        </div>
                        <div class="flex items-center max-w-[calc(100%-theme(spacing.8))]">
                            <p class="text-sm text-slate-600 dark:text-slate-300 font-medium text-ellipsis overflow-hidden whitespace-nowrap w-[calc(100%-theme(spacing.8))]">Do you know the latest update of 2022? <span class="text-slate-400 dark:text-slate-500 font-normal"> A overview of our is now available on YouTube</span></p>
                            <em class="text-slate-400 ms-1 ni ni-external"></em>
                        </div>
                    </a>
                </div>
                <div class="px-1 py-3.5 ms-auto">
                    <ul class="flex item-center -mx-1.5 sm:-mx-2.5">
                        <li class="dropdown px-1.5 sm:px-2.5 relative hidden sm:inline-flex">
                            <a href="#" tabindex="0" class="dropdown-toggle *:pointer-events-none peer inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10 [&.show]:before:h-10 [&.show]:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 [&.show]:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2  before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900" data-offset="0,10" data-placement="bottom-end"  data-rtl-placement="bottom-start">
                                <div class="inline-flex rounded-full h-6 w-6 overflow-hidden">
                                    <img src="{{ asset('images/flags/english-sq.png') }}" alt="">
                                </div>
                            </a>
                            <div tabindex="0" class="dropdown-menu absolute min-w-[180px] border border-t-3 border-gray-200 dark:border-gray-800 border-t-primary-600 dark:border-t-primary-600 bg-white dark:bg-gray-950 rounded shadow hidden peer-[.show]:block z-[1000]">
                                <ul>
                                    <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800">
                                        <a href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-normal text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-600 hover:bg-slate-50 hover:dark:bg-slate-900 transition-all duration-300">
                                            <img src="{{ asset('images/flags/english.png') }}" alt="" class="w-6 me-3">
                                            <span class="language-name">English</span>
                                        </a>
                                    </li>
                                    <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800">
                                        <a href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-normal text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-600 hover:bg-slate-50 hover:dark:bg-slate-900 transition-all duration-300">
                                            <img src="{{ asset('images/flags/spanish.png') }}" alt="" class="w-6 me-3">
                                            <span class="language-name">Español</span>
                                        </a>
                                    </li>
                                    <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800">
                                        <a href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-normal text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-600 hover:bg-slate-50 hover:dark:bg-slate-900 transition-all duration-300">
                                            <img src="{{ asset('images/flags/french.png') }}" alt="" class="w-6 me-3">
                                            <span class="language-name">Français</span>
                                        </a>
                                    </li>
                                    <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800">
                                        <a href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-normal text-slate-600 dark:text-slate-200 hover:text-primary-600 hover:dark:text-primary-600 hover:bg-slate-50 hover:dark:bg-slate-900 transition-all duration-300">
                                            <img src="{{ asset('images/flags/turkey.png') }}" alt="" class="w-6 me-3">
                                            <span class="language-name">Türkçe</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown px-1.5 sm:px-2.5 relative inline-flex">
                            <a tabindex="0" href="#" class="dropdown-toggle *:pointer-events-none peer inline-flex items-center group" data-offset="0,10" data-placement="bottom-end"  data-rtl-placement="bottom-start">
                                <div class="flex items-center">
                                    <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-500 h-8 w-8 rounded-full font-medium">
                                        <em class="ni ni-user-alt"></em>
                                    </div>
                                    <div class="hidden md:block ms-4">
                                        <div class="text-xs font-medium leading-none pt-0.5 pb-1.5 text-primary-500 group-hover:text-primary-600">Administrator</div>
                                        <div class="text-slate-600 dark:text-slate-400 text-xs font-bold flex items-center">Abu Bin Ishityak <em class="text-sm leading-none ms-1 ni ni-chevron-down"></em></div>
                                    </div>
                                </div>
                            </a>
                            <div tabindex="0" class="dropdown-menu clickable absolute max-xs:min-w-[240px] max-xs:max-w-[240px] min-w-[280px] max-w-[280px] border border-t-3 border-gray-200 dark:border-gray-800 border-t-primary-600 dark:border-t-primary-600 bg-white dark:bg-gray-950 rounded shadow hidden peer-[.show]:block z-[1000]">
                                <div class="hidden sm:block px-7 py-5 bg-slate-50 dark:bg-slate-900 border-b border-gray-200 dark:border-gray-800">
                                    <div class="flex items-center">
                                        <div class="relative flex-shrink-0 flex items-center justify-center text-sm text-white bg-primary-500 h-10 w-10 rounded-full font-medium">
                                            <span>AB</span>
                                        </div>
                                        <div class="ms-4 flex flex-col">
                                            <span class="text-sm font-bold text-slate-700 dark:text-white">Abu Bin Ishtiyak</span>
                                            <span class="text-xs text-slate-400 mt-1">info@softnio.com</span>
                                        </div>
                                    </div>
                                </div>
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
                                        <a class="relative px-7 py-2.5 flex items-center rounded-[inherit] text-sm leading-5 font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 hover:dark:text-primary-600 transition-all duration-300" href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                            <em class="text-lg leading-none w-7 ni ni-signout"></em>
                                            <span>{{ __('Log Out') }}</span>
                                        </a>
                                    </form>    
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="dropdown px-1.5 sm:px-2.5 relative inline-flex">
                            <a tabindex="0" href="#" class="dropdown-toggle *:pointer-events-none peer inline-flex items-center isolate relative h-9 w-9 px-1.5 before:content-[''] before:absolute before:-z-[1] before:h-5 before:w-5 hover:before:h-10 hover:before:w-10  [&.show]:before:h-10 [&.show]:before:w-10 before:rounded-full before:opacity-0 hover:before:opacity-100 [&.show]:before:opacity-100 before:transition-all before:duration-300 before:-translate-x-1/2  before:-translate-y-1/2 before:top-1/2 before:left-1/2 before:bg-gray-200 dark:before:bg-gray-900 -me-1.5" data-offset="0,10" data-placement="bottom-end"  data-rtl-placement="bottom-start">
                                <div class="relative inline-flex after:content-[''] after:absolute after:rounded-full after:end-0 after:top-px after:h-2.5 after:w-2.5 after:border-2 after:border-white after:bg-sky-400">
                                    <em class="text-2xl leading-none text-slate-600 dark:text-slate-300 ni ni-bell"></em>
                                </div>
                            </a>
                            <div tabindex="0" class="dropdown-menu absolute max-xs:min-w-[240px] max-xs:max-w-[240px] min-w-[360px] max-w-[360px] border border-t-3 border-gray-200 dark:border-gray-800 border-t-primary-600 dark:border-t-primary-600 bg-white dark:bg-gray-950 rounded shadow hidden peer-[.show]:block z-[1000]">
                                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-800">
                                    <span class="text-sm font-normal">Notifications</span>
                                    <a class="text-sm font-normal text-primary-600 hover:text-primary-700" href="#">Mark All as Read</a>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center p-5 border-b last:border-b-0 border-gray-200 dark:border-gray-800">
                                        <div class="flex-shrink-0 me-3 h-9 w-9 inline-flex items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-950 text-yellow-600">
                                            <em class="text-lg leading-none ni ni-curve-down-right"></em>
                                        </div>
                                        <div>
                                            <div class="text-sm text-slate-600 dark:text-slate-300">You have requested to <span>Widthdrawl</span></div>
                                            <div class="text-xs text-slate-400">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-5 border-b last:border-b-0 border-gray-200 dark:border-gray-800">
                                        <div class="flex-shrink-0 me-3 h-9 w-9 inline-flex items-center justify-center rounded-full bg-green-100 dark:bg-green-950 text-green-600">
                                            <em class="text-lg leading-none ni ni-curve-down-left"></em>
                                        </div>
                                        <div>
                                            <div class="text-sm text-slate-600 dark:text-slate-300">Your Deposit Order is placed</div>
                                            <div class="text-xs text-slate-400">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center p-5 border-b last:border-b-0 border-gray-200 dark:border-gray-800">
                                        <div class="flex-shrink-0 me-3 h-9 w-9 inline-flex items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-950 text-yellow-600">
                                            <em class="text-lg leading-none ni ni-curve-down-right"></em>
                                        </div>
                                        <div>
                                            <div class="text-sm text-slate-600 dark:text-slate-300">You have requested to <span>Widthdrawl</span></div>
                                            <div class="text-xs text-slate-400">2 hrs ago</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center px-5 py-3 border-t border-gray-200 dark:border-gray-800">
                                    <a class="text-sm font-normal text-primary-600 hover:text-primary-700" href="#">View All</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- container -->
    </div>
</div><!-- header -->