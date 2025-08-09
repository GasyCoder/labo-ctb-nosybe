import { config, today, yesterday, currentMonth } from "./function";
import Message, { info_break } from "./app/messages";
import Calendar from "./app/calendar";


document.addEventListener('DOMContentLoaded', 
    function(){
        Message.autohide();
        Message.profile_toggle();
        Message.conversation_show();
        Message.conversation_hide();
        config.win.width >= info_break ? Message.profile_show() : Message.profile_hide();
    }
);

window.addEventListener('resize', function(){
    Message.page_resize();
});
// Améliorations JavaScript pour l'interface prescriptions

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Amélioration de la recherche avec debounce et feedback visuel
    const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="search"]');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            const searchContainer = this.closest('.relative');
            searchContainer.classList.add('searching');
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchContainer.classList.remove('searching');
            }, 500);
        });
        
        // Ajout d'un indicateur visuel de recherche
        const searchIcon = searchInput.parentElement.querySelector('.ni-search');
        if (searchIcon) {
            const loadingIcon = document.createElement('div');
            loadingIcon.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-primary-500';
            loadingIcon.innerHTML = '<div class="animate-spin h-4 w-4 border-2 border-primary-500 border-t-transparent rounded-full"></div>';
            loadingIcon.style.display = 'none';
            
            searchInput.parentElement.appendChild(loadingIcon);
            
            // Basculer entre l'icône de recherche et l'indicateur de chargement
            document.addEventListener('livewire:start', () => {
                if (document.activeElement === searchInput) {
                    searchIcon.style.display = 'none';
                    loadingIcon.style.display = 'block';
                }
            });
            
            document.addEventListener('livewire:finish', () => {
                searchIcon.style.display = 'block';
                loadingIcon.style.display = 'none';
            });
        }
    }
    
    // 2. Animation des onglets
    const tabButtons = document.querySelectorAll('[wire\\:click*="switchTab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Retirer la classe active de tous les boutons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600');
                btn.classList.add('border-transparent', 'text-slate-500');
            });
            
            // Ajouter la classe active au bouton cliqué
            this.classList.add('border-primary-500', 'text-primary-600');
            this.classList.remove('border-transparent', 'text-slate-500');
            
            // Animation du contenu
            const tabContent = document.querySelector('.bg-white.dark\\:bg-slate-900.rounded-xl');
            if (tabContent) {
                tabContent.style.opacity = '0.5';
                tabContent.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    tabContent.style.opacity = '1';
                    tabContent.style.transform = 'translateY(0)';
                }, 150);
            }
        });
    });
    
    // 3. Amélioration des tooltips pour les actions
    const actionButtons = document.querySelectorAll('[title]');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'fixed z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg pointer-events-none';
            tooltip.textContent = this.getAttribute('title');
            tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            
            // Animation d'apparition
            tooltip.style.opacity = '0';
            setTimeout(() => {
                tooltip.style.opacity = '1';
            }, 10);
            
            this.tooltipId = tooltip.id;
        });
        
        button.addEventListener('mouseleave', function() {
            if (this.tooltipId) {
                const tooltip = document.getElementById(this.tooltipId);
                if (tooltip) {
                    tooltip.style.opacity = '0';
                    setTimeout(() => {
                        tooltip.remove();
                    }, 150);
                }
            }
        });
    });
    
    // 4. Confirmation stylée pour les suppressions
    window.confirmDelete = function(message = 'Voulez-vous vraiment supprimer cet élément ?') {
        return new Promise((resolve) => {
            // Créer une modal de confirmation personnalisée
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50';
            modal.innerHTML = `
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <em class="ni ni-alert-circle text-2xl text-red-600"></em>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Confirmer la suppression</h3>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 mb-6">${message}</p>
                        <div class="flex gap-3 justify-end">
                            <button id="cancelBtn" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                Annuler
                            </button>
                            <button id="confirmBtn" class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Animation d'apparition
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
            
            // Gestion des clics
            modal.querySelector('#confirmBtn').addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });
            
            modal.querySelector('#cancelBtn').addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });
            
            // Fermer en cliquant en dehors
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });
            
            // Fermer avec Escape
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.removeEventListener('keydown', handleEscape);
                    resolve(false);
                }
            };
            document.addEventListener('keydown', handleEscape);
        });
    };
    
    // 5. Amélioration de l'accessibilité avec les raccourcis clavier
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K pour focus sur la recherche
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Navigation avec les flèches dans les onglets
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            const focusedTab = document.activeElement;
            if (focusedTab && focusedTab.matches('[wire\\:click*="switchTab"]')) {
                e.preventDefault();
                const tabs = Array.from(tabButtons);
                const currentIndex = tabs.indexOf(focusedTab);
                let nextIndex;
                
                if (e.key === 'ArrowLeft') {
                    nextIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
                } else {
                    nextIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
                }
                
                tabs[nextIndex].focus();
                tabs[nextIndex].click();
            }
        }
    });
    
    // 6. Notification toast pour les actions réussies
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 
                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 text-white rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${bgColor}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto-suppression après 3 secondes
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    };
    
    // 7. Écouter les événements Livewire pour les notifications
    document.addEventListener('livewire:finish', function() {
        // Vous pouvez écouter des événements personnalisés depuis votre composant Livewire
        // Par exemple: this.dispatch('prescription-deleted', ['message' => 'Prescription supprimée avec succès']);
    });
    
    // 8. Amélioration du responsive - masquer/afficher des colonnes
    function handleResponsive() {
        const table = document.querySelector('table');
        if (table && window.innerWidth < 768) {
            // Masquer certaines colonnes sur mobile
            const columnsToHide = table.querySelectorAll('th:nth-child(4), td:nth-child(4)'); // Analyses
            columnsToHide.forEach(col => {
                col.style.display = window.innerWidth < 768 ? 'none' : '';
            });
        }
    }
    
    window.addEventListener('resize', handleResponsive);
    handleResponsive(); // Appel initial
});

// Fonction utilitaire pour formater les dates
window.formatDate = function(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};


Calendar('.js-calendar',[
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Reader will be distracted',
        start: currentMonth() + '-03T13:30:00',
        className: "fc-event-danger",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 1",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Rabfov va hezow.',
        start: currentMonth() + '-14T13:30:00',
        end: currentMonth() + '-14',
        className: "fc-event-success",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 2",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'The leap into electronic',
        start: currentMonth() + '-05',
        end: currentMonth() + '-06',
        className: "fc-event-primary",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 3",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Lorem Ipsum passage - Product Release',
        start: currentMonth() + '-02',
        end: currentMonth() + '-04',
        className: "fc-event-primary",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 4",
    },
    {
        title: 'Gibmuza viib hepobe.',
        start: currentMonth() + '-12',
        end: currentMonth() + '-10',
        className: "fc-event-pink-soft",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 5",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Jidehse gegoj fupelone.',
        start: currentMonth() + '-07T16:00:00',
        className: "fc-event-danger-soft",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 6",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Ke uzipiz zip.',
        start: currentMonth() + '-16T16:00:00',
        end: currentMonth() + '-14',
        className: "fc-event-info-soft",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 7",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Piece of classical Latin literature',
        start: today(),
        end: today() + '-01',
        className: "fc-event-primary",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 8",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Nogok kewwib ezidbi.',
        start: today() + 'T10:00:00',
        className: "fc-event-info",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 9",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Mifebi ik cumean.',
        start: today() + 'T14:30:00',
        className: "fc-event-warning-soft",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 10",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Play Time',
        start: today() + 'T17:30:00',
        className: "fc-event-info",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 11",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'Rujfogve kabwih haznojuf.',
        start: yesterday() + 'T05:00:00',
        className: "fc-event-danger",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 12",
    },
    {
        id: 'default-event-id-' + Math.floor(Math.random()*9999999),
        title: 'simply dummy text of the printing',
        start: yesterday() + 'T07:00:00',
        className: "fc-event-primary-soft",
        description: "Use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden. 13",
    },
]);

