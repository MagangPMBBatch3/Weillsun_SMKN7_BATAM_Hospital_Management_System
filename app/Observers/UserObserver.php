<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAktivitas\LogAktivitas;

class UserObserver
{
    public function created(User $user)
    {
        $this->catatAktivitas("Adding new User: {$user->name}");
    }

    public function updated(User $user)
    {
        $changes = array_keys($user->getChanges());

        if (count($changes) === 1 && in_array('deleted_at', $changes)) {
            return;
        }

        $this->catatAktivitas("Edit User: {$user->name}");
    }

    public function deleted(User $user)
    {
        if (method_exists($user, 'isForceDeleting') && $user->isForceDeleting()) {
            return;
        }

        $this->catatAktivitas("Archive user: {$user->name}");
    }

    public function restored(User $user): void
    {
        $this->catatAktivitas("Restore user: {$user->name}");
    }

    public function forceDeleted(User $user): void
    {
        $this->catatAktivitas("Delete Permanent: {$user->name}");
    }

    protected function catatAktivitas($pesan)
    {
        LogAktivitas::create([
            'user_id' => Auth::id() ?? 1,
            'aktivitas' => $pesan,
        ]);
    }
}
