<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

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
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by Status (Active / Inactive)
        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'status'   => ['required', 'boolean'],
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        $employee = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => (bool) $request->status,
        ]);

        $employee->assignRole($employeeRole);

        return redirect()->route('admin.employees.index')->with('success', 'Staff member added successfully!');
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'status'   => ['required', 'boolean'],
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => (bool) $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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
