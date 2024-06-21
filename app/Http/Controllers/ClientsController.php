<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::query()
            ->where('user_id', Auth::id())
            ->get();

        foreach ($clients as $client) {
            $client->append('bookings_count');
        }

        return view('clients.index', ['clients' => $clients]);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function show($client)
    {
        $client = Client::query()
            ->where('id', $client)
            ->with([
                'bookings' => fn ($query) => $query->latest('start')
            ])
            ->first();

        $client->bookings->each->append('time');

        return view('clients.show', ['client' => $client]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'required_without:phone', 'email:dns'],
            'phone' => ['nullable', 'required_without:email', 'regex:/^[\d\s\+]+$/'],
        ]);

        $client = new Client;
        $client->name = $request->get('name');
        $client->email = $request->get('email');
        $client->phone = $request->get('phone');
        $client->adress = $request->get('adress');
        $client->city = $request->get('city');
        $client->postcode = $request->get('postcode');
        $client->save();

        return $client;
    }

    public function destroy($client)
    {
        $client = Client::where('id', $client)->get();

        abort_unless($client->user_id === Auth::id(), Response::HTTP_FORBIDDEN);

        $client->delete();

        return 'Deleted';
    }
}
