<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;

class DocumentTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('documents.VIEW');
    }

    public function view(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $user->can('documents.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('documents.CREATE');
    }

    public function update(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $user->can('documents.UPDATE');
    }

    public function delete(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $user->can('documents.DELETE');
    }
}
