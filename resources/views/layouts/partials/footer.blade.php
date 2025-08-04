<div class="w-full min-w-[320px] mt-auto border-t bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-900 px-1.5 sm:px-5 py-5">
    <div class="container max-w-none">
        <div class="flex items-center justify-between flex-wrap">
            <div class="text-sm text-slate-500 pb-1 sm:pb-0"> &copy; {{ copyright() }}. Template by <a class="hover:text-primary-500" href="https://softnio.com" target="_blank">Softnio</a>
            </div>
            <ul class="flex flex-wrap -mx-3.5 -my-2">
                <li class="inline-flex relative dropdown">
                    <button tabindex="0" data-placement="top-end" data-rtl-placement="top-start" class="dropdown-toggle peer *:pointer-events-none relative inline-flex items-center transition-all duration-300 px-4 py-2 text-slate-700 dark:text-white text-sm"><span>English</span> <em class="text-sm ms-1 ni ni-chevron-up"></em></button>
                    <div tabindex="0" class="dropdown-menu absolute min-w-[140px] border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 rounded-md shadow hidden peer-[.show]:block z-[1000]">
                        <ul>
                            <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800"><a  href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 hover:bg-slate-50 hover:dark:bg-gray-900 transition-all duration-300"><span>English</span></a></li>
                            <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800"><a  href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 hover:bg-slate-50 hover:dark:bg-gray-900 transition-all duration-300"><span>Español</span></a></li>
                            <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800"><a  href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 hover:bg-slate-50 hover:dark:bg-gray-900 transition-all duration-300"><span>Français</span></a></li>
                            <li class="first:rounded-t-md last:rounded-b-md first:border-t-0 border-t border-gray-200 dark:border-gray-800"><a  href="#" class="relative px-5 py-2.5 flex items-center rounded-[inherit] text-xs leading-5 font-medium text-slate-600 dark:text-slate-300 hover:text-primary-600 hover:bg-slate-50 hover:dark:bg-gray-900 transition-all duration-300"><span>Türkçe</span></a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <button data-target="#region" class="modal-toggle *:pointer-events-none relative inline-flex items-center transition-all duration-300 px-4 py-2 text-primary-500 text-sm"><em class="text-lg ni ni-globe"></em><span class="ms-1">Select Region</span></button>
                </li>
            </ul>
        </div>
    </div><!-- container -->
</div><!-- footer -->

@push('modals')

<div id="region" class="modal group fixed inset-0 flex items-center py-5 px-3 transition-all duration-500 opacity-0 invisible [&.show]:visible [&.show]:opacity-100 z-[5000]">
    <div class="modal-close absolute inset-0 bg-slate-700 bg-opacity-50"></div>
    <div class="modal-body bg-white dark:bg-gray-950 rounded-md w-full md:w-[720px] sm:w-[520px] mx-auto transition-transform delay-500 group-[.show]:delay-0 group-[.show]:duration-300 ease-out -translate-y-[30px] group-[.show]:translate-y-0 max-h-full overflow-auto">
        <button class="modal-close *:pointer-events-none absolute top-4 end-4 text-slate-500 hover:text-slate-700 dark:text-white">
            <em class="text-xl ni ni-cross"></em>
        </button>
        <div class="px-5 sm:px-6 py-5 sm:py-6 md:p-9">
            <h5 class="text-xl font-bold font-heading text-slate-700 dark:text-white mb-3 pe-6">Select Your Country</h5>
            <ul class="grid grid-flow-dense xs:grid-cols-2 md:grid-cols-3 gap-3">
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/arg.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Argentina</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/aus.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Australia</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/bangladesh.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Bangladesh</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/canada.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Canada <small>(English)</small></span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/china.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Centrafricaine</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/china.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">China</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/french.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">France</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/germany.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Germany</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/iran.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Iran</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/italy.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Italy</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/mexico.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">México</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/philipine.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Philippines</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/portugal.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Portugal</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/s-africa.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">South Africa</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/spanish.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Spain</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/switzerland.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">Switzerland</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/uk.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">United Kingdom</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/flags/english.png') }}" alt="" class="w-5 me-3">
                        <span class="text-base text-slate-600 dark:text-slate-200">United State</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

@endpush