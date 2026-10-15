<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services with search and filter.
     */
    public function index(Request $request)
    {
        $query = Service::query();

        // Search by Service Name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Status (Active / Disabled)
        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        // Sort option
        if ($request->get('sort') === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->get('sort') === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $services = $query->paginate(10)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => ['required', 'string', 'max:255', 'unique:services,name'],
            'price'  => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        Service::create([
            'name'   => $request->name,
            'price'  => $request->price,
            'status' => (bool) $request->status,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service added successfully!');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'   => ['required', 'string', 'max:255', 'unique:services,name,' . $service->id],
            'price'  => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);

        $service->update([
            'name'   => $request->name,
            'price'  => $request->price,
            'status' => (bool) $request->status,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully!');
    }

    public function toggleStatus(Service $service)
    {
        $service->update([
            'status' => !$service->status,
        ]);

        $statusText = $service->status ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Service successfully {$statusText}!");
    }
}
