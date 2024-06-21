<?php

namespace App\Http\Controllers;

use App\Client;
use App\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class JournalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Client $client)
    {
        abort_unless($client->user_id === Auth::id(), Response::HTTP_FORBIDDEN);

        return $client->journals->latest('date')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Client $client)
    {
        abort_unless($client->user_id === Auth::id(), Response::HTTP_FORBIDDEN);

        $request->validate([
            'text' => ['required', 'string'],
            'date' => ['required', 'date'],
        ]);

        $journal = new Journal;
        $journal->client_id = $client->id;
        $journal->date = $request->date('date');
        $journal->text = $request->get('text');
        $journal->save();

        return $journal;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, Journal $journal)
    {
        abort_unless($journal->client->user_id === Auth::id(), Response::HTTP_FORBIDDEN);

        $journal->delete();

        return 'Deleted';
    }
}
