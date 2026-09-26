<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gym;
use App\Models\GymOperatingHour;
use Illuminate\Support\Facades\Storage;

class GymProfileController extends Controller
{
    // Gym Profile View दाखवणे
    public function index()
    {
        $gym = Gym::with('operatingHours')->first();

        // जर आधीच रेकॉर्ड नसेल तर बेसिक रेकॉर्ड तयार करा
        if (!$gym) {$gym = Gym::create([
                'name' => 'FitZone Fitness Club',
                'owner_name' => 'Rahul Sharma',
                'contact_number' => '020-24567890',
                'email' => 'info@fitzone.com',
                'address_line_1' => 'Shop No. 12, Fitness Plaza',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411001',
            ]);

            // Default operating hours add करणे
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as$day) {
                GymOperatingHour::create([
                    'gym_id' => $gym->id,
                    'day' => $day,
                    'opening_time' => '06:00',
                    'closing_time' => '22:00',
                    'is_closed' => ($day == 'Sunday') ? 1 : 0                 ]);             }$gym->load('operatingHours');
        }

        // Blade फाइलमध्ये `$gymProfile` वापरला आहे, म्हणून इथे दोन्ही नावांनी डेटा पास करूया जेणेकरून एरर येणार नाही
        return view('auth.gym-profile.index', [
            'gym' => $gym,
            'gymProfile' => $gym
        ]);
    }

    // Edit Form दाखवणे
    public function edit()
    {
        return $this->index();
    }

    // Profile Update करणे
    public function update(Request $request)
    {
        $gym = Gym::firstOrFail();

        $request->validate([
            'gym_name'       => 'required|string|max:255',
            'owner_name'     => 'nullable|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'gst_number'     => 'nullable|string|max:50',
            'website'        => 'nullable|string|max:255',
            'working_hours'  => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255', 
            'description'    => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $logoPath =$gym->logo;
        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $file = $request->file('logo');$filename = time() . '_' . $file->getClientOriginalName();$logoPath = $file->storeAs('gym_logos',$filename, 'public');
        }

        // Database columns update करणे
        $gym->update([
            'name'           => $request->gym_name,
            'logo'           => $logoPath,
            'owner_name'     => $request->owner_name,
            'contact_number' => $request->phone,
            'email'          => $request->email,
            'gst_number'     => $request->gst_number,
            'website'        => $request->website,             'address_line_1' =>$request->address,
            'working_hours'  => $request->working_hours,
            'description'    => $request->description,
        ]);

        if ($request->has('operating_hours')) {
            foreach ($request->operating_hours as $dayId =>$data) {
                GymOperatingHour::where('id', $dayId)->update([
                    'opening_time' => $data['opening_time'] ?? null,
                    'closing_time' => $data['closing_time'] ?? null,
                    'is_closed'    => isset($data['is_closed']) ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('gym.profile.index')->with('success', 'Gym Profile updated successfully!');
    }

    // Gym Profile Delete करण्यासाठी Method
    public function destroy()
    {
        $gym = Gym::first();

        if ($gym) {
            if ($gym->logo && Storage::disk('public')->exists($gym->logo)) {
                Storage::disk('public')->delete($gym->logo);
            }

            GymOperatingHour::where('gym_id', $gym->id)->delete();$gym->delete();
        }

        return redirect()->route('gym.profile.index')->with('success', 'Gym Profile deleted successfully!');
    }
}