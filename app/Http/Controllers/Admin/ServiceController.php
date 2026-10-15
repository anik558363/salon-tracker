<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', (bool) $request->status);
            })
            ->when($request->filled('sort'), function ($q) use ($request) {
                if ($request->sort === 'price_asc') {
                    $q->orderBy('price', 'asc');
                } elseif ($request->sort === 'price_desc') {
                    $q->orderBy('price', 'desc');
                }
            }, function ($q) {
                $q->latest();
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        Service::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => true,
        ]);

        // Diretso itong babalik sa index view listahan pagkatapos mag-create
        return redirect()->route('admin.services.index')->with('success', 'Matagumpay na naidagdag ang serbisyo!');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'      => 'nullable|boolean',
        ]);

        $data = [
            'name'        => $request->name,
            'price'       => $request->price,
            'description' => $request->description,
            'status'      => $request->has('status') ? (bool) $request->status : $service->status,
        ];

        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        // Diretso itong babalik sa index view listahan pagkatapos mag-edit/update
        return redirect()->route('admin.services.index')->with('success', 'Matagumpay na na-update ang serbisyo!');
    }

    public function toggleStatus(Service $service)
    {
        $service->update([
            'status' => !$service->status,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Na-update ang estado ng serbisyo!');
    }

    public function destroy(Service $service)
    {
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Matagumpay na naalis ang serbisyo!');
    }
}
