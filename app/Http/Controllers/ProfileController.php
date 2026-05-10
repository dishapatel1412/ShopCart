<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;

class ProfileController extends Controller
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    public function show()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->ignore($user->id),
            ],
            'mobile_num' => 'required|digits:10',
            'address' => 'required',
        ]);

        if ($request->hasFile('profile_image')) {

            // Delete old image
            if ($user->profile_image &&
                Storage::disk('public')->exists($user->profile_image)) {

                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $data['profile_image'] = $request
                ->file('profile_image')
                ->store('profile_images', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function saveDeviceToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user->update([
            'device_token' => $request->device_token
        ]);

        return response()->json([
            'message' => 'Device token saved successfully'
        ]);
    }
}
