<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BookingsController extends Controller
{
    public function destroy(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize($booking);

        $booking->delete();

        return Response::json(
            data: 'Deleted'
        );
    }
}
