<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
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

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = new Client();
        $client->name = $request->get('name');
        $client->email = $request->get('email');
        $client->phone = $request->get('phone');
        $client->address = $request->get('address');
        $client->city = $request->get('city');
        $client->postcode = $request->get('postcode');
        $client->user_id = Auth::id();
        $client->save();

        return Response::json(
            data: $client,
            status: HttpResponse::HTTP_CREATED
        );
    }


    public function show(Client $client): View
    {
        $this->authorize($client);

        $client->load([
            'bookings' => fn ($query) => $query->latest('start'),
            'journals' => fn ($query) => $query->latest('date'),
        ]);

        $client->bookings->each->append('time');

        return view('clients.show', ['client' => $client]);
    }


    public function destroy(Client $client): JsonResponse
    {
        $this->authorize($client);

        $client->delete();

        return Response::json(
            data: 'Deleted'
        );
    }
}
