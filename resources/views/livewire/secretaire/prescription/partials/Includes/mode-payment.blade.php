<div>
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
        Mode de paiement
    </label>
    <div class="grid grid-cols-2 gap-1.5">
        @foreach($this->methodesPaiement as $methode)
            @php
                // Icônes par code
                $icon = match($methode->code) {
                    'ESPECES' => '💵',
                    'CARTE' => '💳', 
                    'CHEQUE' => '📄',
                    'MOBILEMONEY' => '📱',
                    'VIREMENT' => '🏦',
                    default => '💰'
                };
                
                // Classes CSS fixes selon le code
                $peerClass = match($methode->code) {
                    'ESPECES' => 'peer-especes',
                    'CARTE' => 'peer-carte',
                    'CHEQUE' => 'peer-cheque', 
                    'MOBILEMONEY' => 'peer-mobile',
                    'VIREMENT' => 'peer-virement',
                    default => 'peer-default'
                };
                
                $checkedClass = match($methode->code) {
                    'ESPECES' => 'peer-checked-especes:bg-green-500 peer-checked-especes:text-white peer-checked-especes:ring-2 peer-checked-especes:ring-green-400',
                    'CARTE' => 'peer-checked-carte:bg-blue-500 peer-checked-carte:text-white peer-checked-carte:ring-2 peer-checked-carte:ring-blue-400',
                    'CHEQUE' => 'peer-checked-cheque:bg-purple-500 peer-checked-cheque:text-white peer-checked-cheque:ring-2 peer-checked-cheque:ring-purple-400',
                    'MOBILEMONEY' => 'peer-checked-mobile:bg-yellow-500 peer-checked-mobile:text-white peer-checked-mobile:ring-2 peer-checked-mobile:ring-yellow-400',
                    'VIREMENT' => 'peer-checked-virement:bg-indigo-500 peer-checked-virement:text-white peer-checked-virement:ring-2 peer-checked-virement:ring-indigo-400',
                    default => 'peer-checked-default:bg-gray-500 peer-checked-default:text-white peer-checked-default:ring-2 peer-checked-default:ring-gray-400'
                };
                
                $hoverClass = match($methode->code) {
                    'ESPECES' => 'hover:border-green-400',
                    'CARTE' => 'hover:border-blue-400',
                    'CHEQUE' => 'hover:border-purple-400',
                    'MOBILEMONEY' => 'hover:border-yellow-400',
                    'VIREMENT' => 'hover:border-indigo-400',
                    default => 'hover:border-gray-400'
                };
            @endphp
            
            <input type="radio" 
                   id="mode_{{ strtolower($methode->code) }}" 
                   wire:model.live="modePaiement" 
                   value="{{ $methode->code }}" 
                   class="hidden {{ $peerClass }}">
                   
            <label for="mode_{{ strtolower($methode->code) }}"
                   class="{{ $checkedClass }}
                          bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                          rounded-lg px-2 py-1.5 cursor-pointer flex items-center gap-1.5
                          transition-colors text-xs {{ $hoverClass }}
                          text-slate-700 dark:text-slate-300">
                {{ $icon }} {{ $methode->label }}
            </label>
        @endforeach
    </div>
</div>

<style>
/* Styles CSS pour les différentes méthodes de paiement */
.peer-especes:checked ~ label.peer-checked-especes\:bg-green-500 {
    background-color: rgb(34 197 94) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(134 239 172) !important;
}

.peer-carte:checked ~ label.peer-checked-carte\:bg-blue-500 {
    background-color: rgb(59 130 246) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(147 197 253) !important;
}

.peer-cheque:checked ~ label.peer-checked-cheque\:bg-purple-500 {
    background-color: rgb(168 85 247) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(196 181 253) !important;
}

.peer-mobile:checked ~ label.peer-checked-mobile\:bg-yellow-500 {
    background-color: rgb(234 179 8) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(253 224 71) !important;
}

.peer-virement:checked ~ label.peer-checked-virement\:bg-indigo-500 {
    background-color: rgb(99 102 241) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(165 180 252) !important;
}

.peer-default:checked ~ label.peer-checked-default\:bg-gray-500 {
    background-color: rgb(107 114 128) !important;
    color: white !important;
    box-shadow: 0 0 0 2px rgb(156 163 175) !important;
}
</style>