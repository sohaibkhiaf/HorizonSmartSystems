<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function products() {
        $products = Product::all();
        return view('admin.operator.products.index', compact('products'));
    }

    public function requests() {
        $requests = \App\Models\Request::where('operator_id', auth()->id())->latest()->paginate(10);
        return view('admin.operator.requests.index', compact('requests'));
    }

    public function create() {
        $products = Product::all();
        return view('admin.operator.requests.create', compact('products'));
    }

    public function store(Request $req) {
        \App\Models\Request::create([
            'product_id' => $req->product_id,
            'operator_id' => auth()->id(),
            'client_name' => $req->client_name,
            'client_contact' => $req->client_contact
        ]);

        return redirect()->route('operator.requests')
            ->with('success', 'Demande créée avec succès !');
    }

    public function show($id)
    {
        $request = \App\Models\Request::with(['product'])
            ->where('operator_id', auth()->id())
            ->findOrFail($id);

        return view('admin.operator.requests.show', compact('request'));
    }

    public function confirmPayment(Request $req, $id) {
        $req->validate([
            'operator_notes' => 'required|string|max:512',
        ]);

        $request = \App\Models\Request::find($id);
        $request->update([
            'status' => 'paid',
            'operator_notes' => $request->operator_notes != NULL ? 
                trim($request->operator_notes) . "\n" . trim($req->operator_notes) 
                : trim($req->operator_notes) 
        ]);

        return back();
    }

    public function addNote(Request $req, $id) {
        $req->validate([
            'operator_notes' => 'required|string|max:512',
        ]);

        $request = \App\Models\Request::find($id);
        $request->update([
            'operator_notes' => $request->operator_notes != NULL ? 
                trim($request->operator_notes) . "\n" . trim($req->operator_notes) 
                : trim($req->operator_notes)   
        ]);

        return back();
    }

}
