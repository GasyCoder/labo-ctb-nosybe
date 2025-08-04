<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Examens') }}
        </h2>
    </x-slot>

    <div class="container-fluid">
        <!-- Header avec titre -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="text-lg font-semibold">
                    {{ __('Gestion des Examens') }}
                </h2>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="row">
            <div class="col-12">
                <!-- Notification -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Carte principale -->
                <div class="card shadow-sm">
                    @if(request()->routeIs('examens.index'))
                        <!-- Vue Liste -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Abbréviation</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($examens as $examen)
                                            <tr>
                                                <td>{{ $examen->id }}</td>
                                                <td>{{ $examen->name }}</td>
                                                <td>{{ $examen->abr }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $examen->status ? 'success' : 'danger' }}">
                                                        {{ $examen->status ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('examens.show', $examen->id) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('examens.edit', $examen->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('examens.destroy', $examen->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet examen?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">{{ __("Aucun examen trouvé") }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($examens->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $examens->links() }}
                                </div>
                            @endif
                        </div>

                    @elseif(request()->routeIs('examens.create') || request()->routeIs('examens.edit'))
                        <!-- Vue Formulaire -->
                        <div class="card-header bg-light">
                            <h3 class="card-title mb-0">
                                {{ request()->routeIs('examens.create') ? __('Ajouter un nouvel Examen') : __("Modifier l'Examen") }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ request()->routeIs('examens.create') ? route('examens.store') : route('examens.update', $examen->id ?? 0) }}">
                                @csrf
                                @if(request()->routeIs('examens.edit'))
                                    @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <label for="name" class="col-sm-3 col-form-label">{{ __('Nom complet') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $examen->name ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="abr" class="col-sm-3 col-form-label">{{ __('Abbréviation') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('abr') is-invalid @enderror" id="abr" name="abr" value="{{ old('abr', $examen->abr ?? '') }}" required maxlength="3">
                                        @error('abr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" {{ old('status', isset($examen) ? $examen->status : true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                {{ __('Actif') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('examens.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                                            <button type="submit" class="btn btn-primary">
                                                {{ request()->routeIs('examens.create') ? __('Enregistrer') : __('Mettre à jour') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    @elseif(request()->routeIs('examens.show'))
                        <!-- Vue Détails -->
                        <div class="card-header bg-light">
                            <h3 class="card-title mb-0">{{ __("Détails de l'Examen") }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('ID') }}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">{{ $examen->id }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('Nom') }}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">{{ $examen->name }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __("Abbréviation") }}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">{{ $examen->abr }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('Statut') }}</label>
                                <div class="col-sm-9">
                                    <span class="badge bg-{{ $examen->status ? 'success' : 'danger' }}">
                                        {{ $examen->status ? __('Actif') : __('Inactif') }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('Créé le') }}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">{{ optional($examen->created_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">{{ __('Mis à jour le') }}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">{{ optional($examen->updated_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('examens.index') }}" class="btn btn-secondary">{{ __('Retour') }}</a>
                                        <a href="{{ route('examens.edit', $examen->id) }}" class="btn btn-primary">{{ __('Modifier') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div> <!-- /.card -->
            </div> <!-- /.col-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container-fluid -->
</div>
