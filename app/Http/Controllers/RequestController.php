<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class RequestController extends Controller
{
    public function index() {
        $requests = \App\Models\Request::with('product')->latest()->paginate(10);
        return view('admin.requests.index', compact('requests'));
    }

    public function show($id) {
        $request = \App\Models\Request::with('product')->findOrFail($id);
        return view('admin.requests.show', compact('request'));
    }

    public function setAvailability(Request $req, $id) {
        $request = \App\Models\Request::findOrFail($id);

        $request->update([
            'status' => $req->status,
            'admin_response' => $request->admin_response != NULL ? 
                trim($request->admin_response) . "\n" . trim($req->response) 
                : trim($req->response)
        ]);

        return back();
    }

    public function deliverCredentials(Request $req, $id)
    {
        $req->validate([
            'credentials' => 'required|string|max:512',
        ]);

        \App\Models\Request::find($id)->update([
            'status' => 'delivered', 
            'delivery_credentials' => $req->credentials
        ]);

        return back();
    }

    public function destroy($id) {
        \App\Models\Request::find($id)->delete();
        return back();
    }
}
