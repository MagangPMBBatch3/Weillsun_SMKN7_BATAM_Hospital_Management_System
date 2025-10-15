<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $tenagaMedis = TenagaMedis::with('profile')->get();

        return view('profile.edit', [
            'user' => $request->user(),
            'tenagaMedisList' => $tenagaMedis,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $userProfile = $request->user()->profile;
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('foto')) {
            try {
                if ($userProfile && $userProfile->foto && $userProfile->foto !== 'default_pp.jpg') {
                    Storage::disk('public')->delete($userProfile->foto);
                }

                $file = $request->file('foto');
                $hashedName = hash('sha256', time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();

                if ($file->storeAs('', $hashedName, 'public')) {
                    $data['foto'] = $hashedName;
                } else {
                    throw new \Exception('Failed to save file');
                }
            } catch (\Exception $e) {
                return Redirect::route('profile.edit')->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
            }
        }

        if (!$userProfile) {
            $userProfile = $request->user()->profile()->create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'foto' => $data['foto'] ?? 'defult_pp.jpg',
            ]);
        } else {
            $userProfile->update($data);
        }

        // Update tenaga medis jika ada
        if ($request->tenaga_medis_id) {
            $tenagaMedis = TenagaMedis::find($request->tenaga_medis_id);
            if ($tenagaMedis) {
                $tenagaMedis->update(['id' => $userProfile->id]);
            }
        }

        $request->user()->update([
            'email' => $request->email,
            'name' => $request->nama,
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->forceDelete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
