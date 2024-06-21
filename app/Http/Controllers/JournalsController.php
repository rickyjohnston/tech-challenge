<?php

namespace App\Http\Controllers;

use App\Client;
use App\Journal;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class JournalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Client $client): JsonResponse
    {
        $this->authorize([new Journal(), $client]);

        return Response::json(
            data: $client->journals()->latest('date')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Client $client): JsonResponse
    {
        $this->authorize([Journal::class, $client]);

        $request->validate([
            'text' => ['required', 'string'],
            'date' => ['required', 'date'],
        ]);

        $journal = new Journal;
        $journal->client_id = $client->id;
        $journal->date = $request->date('date');
        $journal->text = $request->get('text');
        $journal->save();

        return Response::json(
            data: $journal
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client, Journal $journal): JsonResponse
    {
        $this->authorize($journal);

        $journal->delete();

        return Response::json(
            data: 'Deleted'
        );
    }
}
