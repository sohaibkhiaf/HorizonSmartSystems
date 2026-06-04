<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
public function products() {
        $products = Product::all();
        return view('admin.operator.products', compact('products'));
    }

    public function requests() {
        $requests = \App\Models\Request::where('operator_id', auth()->id())->latest()->paginate(10);
        return view('admin.operator.requests', compact('requests'));
    }

    public function store(Request $req) {
        \App\Models\Request::create([
            'product_id' => $req->product_id,
            'operator_id' => auth()->id(),
            'client_name' => $req->client_name,
            'client_contact' => $req->client_contact
        ]);

        return back();
    }

    public function show($id)
    {
        $request = \App\Models\Request::with(['product'])
            ->where('operator_id', auth()->id())
            ->findOrFail($id);

        return view('admin.operator.request', compact('request'));
    }

    public function confirmPayment($id) {

        \App\Models\Request::find($id)->update([
            'status' => 'paid'
        ]);

        return back();
    }

    public function addNote(Request $req, $id) {
        $req->validate([
            'operator_response' => 'required|string|max:512',
        ]);

        $request = \App\Models\Request::find($id);
        $request->update([
            'operator_response' => $request->operator_response != NULL ? 
                trim($request->operator_response) . "\n" . trim($req->operator_response) 
                : trim($req->operator_response)   
        ]);

        return back();
    }

    public function destroy($id) {
        \App\Models\Request::find($id)->delete();
        return back();
    }
}
