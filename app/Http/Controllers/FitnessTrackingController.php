<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyMeasurement;
use App\Models\Member;

class FitnessTrackingController extends Controller
{
    // 1. Fitness Measurements Page दाखवण्यासाठी index मेथड
    public function index()
{
    $members = Member::orderBy('name', 'asc')->get();
    
    return view('trainers.measurements', compact('members'));
}

    // 2. Fitness Measurements / Body Measurements सेव्ह करण्यासाठी storeMeasurement मेथड
    public function storeMeasurement(Request $request)
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

        // Auto calculate BMI: weight (kg) / [height (m)]^2
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

        return redirect()->back()->with('success', 'Fitness measurements saved successfully!');
    }
}