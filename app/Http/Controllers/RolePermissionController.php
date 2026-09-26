<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RolePermission;

class RolePermissionController extends Controller
{
    // Permissions page load karne ani role nusar permissions fetch karne
    public function index(Request $request)
    {
        $role = $request->input('role');
        $assignedPermissions = [];

        if ($role) {
            $assignedPermissions = RolePermission::where('role', $role)->pluck('permission')->toArray();
        }

        return view('auth.roles-permissions', compact('assignedPermissions', 'role'));
    }

    // Checkboxes save/update karne
    public function update(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'permissions' => 'array',
        ]);

        $role = $request->input('role');
        $newPermissions = $request->input('permissions', []);

        // Ty rolechya purvachya sagalya permissions delete karun navya insert karaychya
        RolePermission::where('role', $role)->delete();

        foreach ($newPermissions as $permission) {
            RolePermission::create([
                'role' => $role,
                'permission' => $permission
            ]);
        }

        return redirect()->route('roles-permissions.index', ['role' => $role])
                         ->with('success', 'Permissions updated successfully!');
    }

    // AJAX request sathi role nusar permissions JSON format madhe return karnari method
    public function getPermissions($role)
    {
        $permissions = RolePermission::where('role', $role)->pluck('permission')->toArray();

        return response()->json([
            'permissions' => $permissions
        ]);
    }

    // Added missing show method for Role View Modal
    public function show($role)
    {
        $permissions = RolePermission::where('role', $role)->pluck('permission')->toArray();

        return response()->json([
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    // Added missing edit method for Role Edit Modal
    public function edit($role)
    {
        $permissions = RolePermission::where('role', $role)->pluck('permission')->toArray();

        return response()->json([
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    // Added missing destroy method to delete role permissions
    public function destroy($role)
    {
        RolePermission::where('role', $role)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role permissions deleted successfully!'
        ]);
    }
}