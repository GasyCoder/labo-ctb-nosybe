        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- En attente --}}
            <div class="group relative overflow-hidden bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl rounded-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-orange-500/10 dark:from-amber-400/10 dark:to-orange-400/10"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">En attente</dt>
                                <dd class="text-3xl font-bold text-slate-900 dark:text-slate-100 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors duration-300">
                                    {{ $stats['en_attente'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Prescriptions à traiter</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- En cours --}}
            <div class="group relative overflow-hidden bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl rounded-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 dark:from-blue-400/10 dark:to-indigo-400/10"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">En cours</dt>
                                <dd class="text-3xl font-bold text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                                    {{ $stats['en_cours'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Analyses en traitement</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terminé --}}
            <div class="group relative overflow-hidden bg-white dark:bg-slate-800 shadow-lg hover:shadow-xl rounded-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-green-500/10 dark:from-emerald-400/10 dark:to-green-400/10"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-6 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terminé</dt>
                                <dd class="text-3xl font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                                    {{ $stats['termine'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Prêtes à valider</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>