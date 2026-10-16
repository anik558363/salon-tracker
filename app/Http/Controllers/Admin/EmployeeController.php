<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees with search and filter.
     */
    public function index(Request $request)
    {
        $query = User::role('employee');

        // Search by Name or Email
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by Status (Active / Inactive)
        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        // ফিল্টারের ওপর ভিত্তি করে স্ট্যাট কার্ডের কাউন্ট
        $totalStaffCount    = (clone $query)->count();
        $activeStaffCount   = (clone $query)->where('status', true)->count();
        $inactiveStaffCount = (clone $query)->where('status', false)->count();

        $employees = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'table'         => view('admin.employees.index', compact('employees'))->renderSections()['table_content'] ?? '',
                'totalStaff'    => number_format($totalStaffCount),
                'activeStaff'   => number_format($activeStaffCount),
                'inactiveStaff' => number_format($inactiveStaffCount),
            ]);
        }

        return view('admin.employees.index', compact(
            'employees',
            'totalStaffCount',
            'activeStaffCount',
            'inactiveStaffCount'
        ));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'title'    => 'nullable|string|max:100',
            'bio'      => 'nullable|string|max:500',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('stylists', 'public');
        }

        $employee = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'title'    => $request->title ?? 'Executive Stylist',
            'bio'      => $request->bio,
            'photo'    => $photoPath,
            'status'   => true,
        ]);

        $employee->assignRole('employee');

        return redirect()->route('admin.employees.index')->with('success', 'Staff member added successfully!');
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => 'nullable|string|min:6',
            'title'    => 'nullable|string|max:100',
            'bio'      => 'nullable|string|max:500',
            'photo'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'   => 'nullable|boolean',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'title'  => $request->title ?? 'Executive Stylist',
            'bio'    => $request->bio,
            'status' => $request->has('status') ? (bool) $request->status : $employee->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('stylists', 'public');
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Staff details updated successfully!');
    }

    public function toggleStatus(User $employee)
    {
        $employee->update([
            'status' => !$employee->status,
        ]);

        $statusText = $employee->status ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Staff member successfully {$statusText}!");
    }
}
