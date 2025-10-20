<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\UsersProfile\UsersProfile;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $tenagaMedis = TenagaMedis::with('profile')->get();
        $userTenagaMedis = TenagaMedis::where('profile_id', $request->user()->profile?->id)->first();

        return view('profile.edit', [
            'user' => $request->user(),
            'tenagaMedisList' => $tenagaMedis,
            'userTenagaMedis' => $userTenagaMedis,
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id' => 'required|exists:users_profile,id'
        ]);

        try {
            
            $userProfile = UsersProfile::findOrFail($request->id);

            if ($request->hasFile('foto')) {
                if ($userProfile->foto) {
                    Storage::disk('public')->delete($userProfile->foto);
                }

                $filename = Str::uuid() . '.' . $request->file('foto')->getClientOriginalExtension();
                $path = $request->file('foto')->storeAs('', $filename, 'public');


                $userProfile->foto = $path;
                $userProfile->save();

                return response()->json([
                    'success' => true,
                    'filename' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
                'nickname' => $data['nickname'],
                // 'email' => $data['email'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'foto' => $data['foto'] ?? 'defult_pp.jpg',
            ]);
        } else {
            $userProfile->update($data);
        }

        // Update tenaga medis jika ada
        // if ($request->tenaga_medis_id) {
        //     $tenagaMedis = TenagaMedis::find($request->tenaga_medis_id);
        //     if ($tenagaMedis) {
        //         $tenagaMedis->update(['id' => $userProfile->id]);
        //     }
        // }

        $request->user()->update([
            // 'email' => $request->email,
            'name' => $request->nickname,
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
