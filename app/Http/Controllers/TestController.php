<?php

namespace App\Http\Controllers;

use App\Services\TicketService;

class TestController extends Controller
{
    public function index(TicketService $ticketService)
    {
        $ticketDtos = $ticketService->getTickets();

        dd($ticketDtos);
        return response()->json($ticketDtos);
    }
}