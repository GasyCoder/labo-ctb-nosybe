<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $userIdBeingEdited;
    public $userIdBeingDeleted;

    public $user = [
        'name' => '',
        'username' => '',  // ✅ Changé de 'email' à 'username'
        'type' => 'technicien',
        'password' => '',
        'password_confirmation' => ''
    ];

    protected $rules = [
        'user.name' => 'required|min:3',
        'user.username' => 'required|string|min:3|unique:users,username',  // ✅ Changé de 'email' à 'username'
        'user.type' => 'required|in:admin,secretaire,technicien,biologiste',
        'user.password' => 'sometimes|confirmed|min:6'
    ];

    /**
     * Récupère les données de session de manière robuste
     */
    public function getSessionsData()
    {
        try {
            // Vérifier d'abord si la table sessions existe et a des données
            $sessionsExist = DB::table('sessions')->exists();

            if (!$sessionsExist) {
                \Log::warning('Aucune session trouvée dans la base de données');
                return collect();
            }

            // Récupérer les sessions avec user_id non null
            $sessions = DB::table('sessions')
                ->selectRaw('user_id, MAX(last_activity) as last_activity, COUNT(*) as session_count')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->get();

            if ($sessions->isEmpty()) {
                \Log::warning('Aucune session avec user_id trouvée. Les utilisateurs ne se connectent peut-être pas correctement.');
            }

            return $sessions->keyBy('user_id');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des sessions: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Détermine le statut d'un utilisateur
     */
    public function getUserStatus($userId, $sessions)
    {
        $session = $sessions->get($userId);

        if (!$session) {
            return [
                'status' => 'never_connected',
                'text' => 'Jamais connecté',
                'color' => 'gray-300',
                'text_color' => 'text-gray-400 dark:text-gray-500',
                'last_activity' => null,
                'show_date' => false
            ];
        }

        $lastActivity = $session->last_activity;

        // Vérifier si le timestamp est valide
        if (!$this->isValidTimestamp($lastActivity)) {
            \Log::warning("Timestamp invalide pour user {$userId}: {$lastActivity}");
            return [
                'status' => 'invalid_session',
                'text' => 'Session invalide',
                'color' => 'red-300',
                'text_color' => 'text-red-400',
                'last_activity' => null,
                'show_date' => false
            ];
        }

        $currentTimestamp = now()->timestamp;
        $diffInSeconds = $currentTimestamp - $lastActivity;

        // Seuil pour considérer comme "en ligne" : 5 minutes (300 secondes)
        if ($diffInSeconds < 300) {
            return [
                'status' => 'online',
                'text' => 'En ligne',
                'color' => 'green-500',
                'text_color' => 'text-green-600 dark:text-green-400 font-medium',
                'last_activity' => $lastActivity,
                'show_date' => false
            ];
        }

        // Utilisateur déconnecté
        $lastActivityCarbon = Carbon::createFromTimestamp($lastActivity);

        return [
            'status' => 'offline',
            'text' => 'Déconnecté il y a ' . $lastActivityCarbon->diffForHumans(),
            'color' => 'gray-400',
            'text_color' => 'text-gray-600 dark:text-gray-400',
            'last_activity' => $lastActivity,
            'last_activity_formatted' => $lastActivityCarbon->format('d/m/Y à H:i'),
            'last_activity_full' => $lastActivityCarbon->format('d/m/Y à H:i:s'),
            'show_date' => true
        ];
    }

    /**
     * Vérifie si un timestamp est valide
     */
    private function isValidTimestamp($timestamp)
    {
        if (!is_numeric($timestamp)) {
            return false;
        }

        // Le timestamp doit être dans une fourchette raisonnable
        $currentYear = date('Y');
        $timestampYear = date('Y', $timestamp);

        return $timestampYear >= 2020 && $timestampYear <= ($currentYear + 1);
    }

    /**
     * Réinitialise le formulaire utilisateur
     */
    public function resetUserForm()
    {
        $this->user = [
            'name' => '',
            'username' => '',  // ✅ Changé de 'email' à 'username'
            'type' => 'technicien',
            'password' => '',
            'password_confirmation' => ''
        ];
        $this->userIdBeingEdited = null;
        $this->resetValidation();
    }

    /**
     * Ouvre le modal pour créer un nouvel utilisateur
     */
    public function createUser()
    {
        $this->validate([
            'user.name' => 'required|min:3',
            'user.username' => 'required|string|min:3|unique:users,username',  // ✅ Changé de 'email' à 'username'
            'user.type' => 'required|in:admin,secretaire,technicien,biologiste',
            'user.password' => 'required|confirmed|min:6'
        ]);

        User::create([
            'name' => $this->user['name'],
            'username' => $this->user['username'],  // ✅ Changé de 'email' à 'username'
            'type' => $this->user['type'],
            'password' => Hash::make($this->user['password'])
        ]);

        $this->showEditModal = false;
        $this->resetUserForm();
        $this->dispatch('notify', 'Utilisateur créé avec succès!');
    }

    /**
     * Ouvre le modal pour éditer un utilisateur
     */
    public function editUser($userId)
    {
        $this->resetValidation();
        $this->userIdBeingEdited = $userId;

        if ($user = User::find($userId)) {
            $this->user = [
                'name' => $user->name,
                'username' => $user->username,  // ✅ Changé de 'email' à 'username'
                'type' => $user->type,
                'password' => '',
                'password_confirmation' => ''
            ];
        }

        $this->showEditModal = true;
    }

    /**
     * Met à jour un utilisateur existant
     */
    public function updateUser()
    {
        $rules = [
            'user.name' => 'required|min:3',
            'user.username' => 'required|string|min:3|unique:users,username,' . $this->userIdBeingEdited,  // ✅ Changé de 'email' à 'username'
            'user.type' => 'required|in:admin,secretaire,technicien,biologiste'
        ];

        if (!empty($this->user['password'])) {
            $rules['user.password'] = 'min:6|confirmed';
        }

        $this->validate($rules);

        $userData = [
            'name' => $this->user['name'],
            'username' => $this->user['username'],  // ✅ Changé de 'email' à 'username'
            'type' => $this->user['type']
        ];

        if (!empty($this->user['password'])) {
            $userData['password'] = Hash::make($this->user['password']);
        }

        User::find($this->userIdBeingEdited)->update($userData);

        $this->showEditModal = false;
        $this->resetUserForm();
        $this->dispatch('notify', 'Utilisateur mis à jour avec succès!');
    }

    /**
     * Confirme la suppression d'un utilisateur
     */
    public function confirmUserDeletion($userId)
    {
        $this->userIdBeingDeleted = $userId;
        $this->showDeleteModal = true;
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser()
    {
        $user = User::find($this->userIdBeingDeleted);

        // Vérifier si c'est le dernier admin
        if ($user->type === 'admin' && User::where('type', 'admin')->count() <= 1) {
            $this->dispatch('notify', 'Impossible de supprimer le dernier administrateur!', 'error');
            $this->showDeleteModal = false;
            return;
        }

        // Supprimer également toutes les sessions de cet utilisateur
        DB::table('sessions')->where('user_id', $this->userIdBeingDeleted)->delete();

        $user->delete();

        $this->showDeleteModal = false;
        $this->dispatch('notify', 'Utilisateur supprimé avec succès!');
    }

    /**
     * Déconnecte un utilisateur en supprimant ses sessions
     */
    public function logoutUser($userId)
    {
        DB::table('sessions')->where('user_id', $userId)->delete();
        $this->dispatch('notify', 'Utilisateur déconnecté avec succès!');
    }

    /**
     * Réinitialise les propriétés lors de la fermeture du modal
     */
    public function updatedShowEditModal($value)
    {
        if (!$value) {
            $this->resetUserForm();
        }
    }

    /**
     * Retourne les types d'utilisateurs disponibles
     */
    private function getAvailableTypes()
    {
        return User::TYPES;
    }

    /**
     * Retourne le nombre d'utilisateurs par type
     */
    private function getCountByType()
    {
        return User::getCountByType();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->paginate($this->perPage);

        $sessions = $this->getSessionsData();

        return view('livewire.admin.users-index', [
            'users' => $users,
            'sessions' => $sessions,
            'stats' => $this->getCountByType(),
            'types' => $this->getAvailableTypes()
        ]);
    }
}