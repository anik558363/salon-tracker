<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->with(['serviceEntries.items.service', 'serviceEntries.employee'])
            ->withCount('serviceEntries as total_visits')
            ->withSum('serviceEntries as total_spent', 'total_amount');

        // Paghahanap gamit ang Pangalan o Numero ng Telepono
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Pagsasaayos (Sort)
        if ($request->get('sort') === 'spent_desc') {
            $query->orderByDesc('total_spent');
        } elseif ($request->get('sort') === 'visits_desc') {
            $query->orderByDesc('total_visits');
        } else {
            $query->latest();
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }
}
