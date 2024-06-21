<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientsController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->where('user_id', Auth::id())
            ->withCount('bookings')
            ->get();

        return view('clients.index', ['clients' => $clients]);
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): JsonResponse
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

        return Response::json($client);
    }


    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        $client->load([
            'bookings' => fn ($query) => $query->latest('start'),
            'journals' => fn ($query) => $query->latest('date'),
        ]);

        $client->bookings->each->append('time');

        return view('clients.show', ['client' => $client]);
    }


    public function destroy(Client $client): JsonResponse
    {
        $this->authorize('delete', $client);

        $client->delete();

        return Response::json('Deleted');
    }
}
