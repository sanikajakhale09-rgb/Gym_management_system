<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyMeasurement;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class BodyMeasurementController extends Controller
{
    // 1. Show Body Measurements Form & Member List
    public function index(Request $request)
    {
        $members = Member::orderBy('name', 'asc')->get();
        $selectedMemberId = $request->input('member_id'); // 'get' ऐवजी 'input' वापरले आहे
        
        return view('trainers.measurements', compact('members', 'selectedMemberId'));
    }

    // 2. Store New Measurement Entry
    public function store(Request $request)
    {
        $request->validate([
            'member_id'        => 'required|exists:members,id',
            'measurement_date' => 'required|date',
            'weight'           => 'required|numeric|min:1',
            'height'           => 'required|numeric|min:1',
            'body_fat'         => 'nullable|numeric|min:0',
            'chest'            => 'nullable|numeric|min:0',
            'waist'            => 'nullable|numeric|min:0',
            'hip'              => 'nullable|numeric|min:0',
            'biceps'           => 'nullable|numeric|min:0',
            'thigh'            => 'nullable|numeric|min:0',
        ]);

        $weight = $request->weight;
        $heightM = $request->height / 100;
        $bmi = ($heightM > 0) ? round($weight / ($heightM * $heightM), 2) : null;

        BodyMeasurement::create([
            'gym_id'           => $request->gym_id ?? 1,
            'branch_id'        => $request->branch_id ?? 1,
            'member_id'        => $request->member_id,
            'measurement_date' => $request->measurement_date,
            'weight'           => $weight,
            'height'           => $request->height,
            'BMI'              => $bmi,
            'body_fat'         => $request->body_fat,
            'chest'            => $request->chest,
            'waist'            => $request->waist,
            'hip'              => $request->hip,
            'biceps'           => $request->biceps,
            'thigh'            => $request->thigh,
        ]);

        return redirect()->back()->with('success', 'Body measurements saved successfully with date-wise history!');
    }

    // 3. AJAX: Fetch Date-wise History for Selected Member
    public function getMemberHistory(int $memberId)
    {
        $history = BodyMeasurement::where('member_id', $memberId)
            ->orderBy('measurement_date', 'desc')
            ->get();

        return response()->json($history);
    }

    public function edit(int $id) {
        $measurement = BodyMeasurement::findOrFail($id);
        return response()->json($measurement);
    }

    public function update(Request $request, int $id) {
        $measurement = BodyMeasurement::findOrFail($id);
        
        $weight = $request->weight;
        $heightM = $request->height / 100;
        $bmi = ($heightM > 0) ? round($weight / ($heightM * $heightM), 2) : null;

        $data = $request->all();
        $data['BMI'] = $bmi;

        $measurement->update($data);

        return redirect()->back()->with('success', 'Body measurements updated successfully!');
    }

    // 4. Delete Measurement Entry (बदलासह अपडेट केलेली मेथड)
    public function destroy(int $id)
    {
        $measurement = BodyMeasurement::findOrFail($id);
        $memberId = $measurement->member_id; // मेम्बर आयडी सेव्ह करून ठेवला
        $measurement->delete();

        // डिलीट झाल्यावर मेम्बरची आयडी सोबत पाठवली जाईल जेणेकरून तोच मेम्बर पुन्हा सिलेक्टेड राहील
        return redirect()->route('fitness.measurements.index', ['member_id' => $memberId])
                         ->with('success', 'Body measurement deleted successfully!');
    }

    // --- नवीन जोडलेली मेथड (Diet Plan साठी विशिष्ट तारखेचे measurements आणण्यासाठी) ---
   public function getMeasurementByDate(Request $request, int $memberId)
    {
        $date = $request->input('date');
        
        $measurement = BodyMeasurement::where('member_id', $memberId)
            ->where('measurement_date', $date)
            ->first();

        return response()->json($measurement);
    }
}