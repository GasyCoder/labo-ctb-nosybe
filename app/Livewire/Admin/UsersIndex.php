<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

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
        'email' => '',
        'type' => 'technicien',
        'password' => '',
        'password_confirmation' => ''
    ];

    protected $rules = [
        'user.name' => 'required|min:3',
        'user.email' => 'required|email|unique:users,email',
        'user.type' => 'required|in:admin,secretaire,technicien,biologiste',
        'user.password' => 'sometimes|confirmed|min:6'
    ];

    public function editUser($userId)
    {
        $this->resetValidation();
        $this->userIdBeingEdited = $userId;

        if ($user = User::find($userId)) {
            $this->user = [
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'password' => '',
                'password_confirmation' => ''
            ];
        }

        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $rules = [
            'user.name' => 'required|min:3',
            'user.email' => 'required|email|unique:users,email,' . $this->userIdBeingEdited,
            'user.type' => 'required|in:admin,secretaire,technicien,biologiste'
        ];

        if (!empty($this->user['password'])) {
            $rules['user.password'] = 'min:6|confirmed';
        }

        $this->validate($rules);

        $userData = [
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'type' => $this->user['type']
        ];

        if (!empty($this->user['password'])) {
            $userData['password'] = Hash::make($this->user['password']);
        }

        User::find($this->userIdBeingEdited)->update($userData);

        $this->showEditModal = false;
        $this->dispatch('notify', 'Utilisateur mis à jour avec succès!');
    }

    public function confirmUserDeletion($userId)
    {
        $this->userIdBeingDeleted = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        User::find($this->userIdBeingDeleted)->delete();
        $this->showDeleteModal = false;
        $this->dispatch('notify', 'Utilisateur supprimé avec succès!');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.users-index', [
            'users' => $users,
            'stats' => User::getCountByType(),
            'types' => User::getAvailableTypes()
        ]);
    }
}