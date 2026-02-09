<?php

namespace Tests\Feature;

use App\Models\TS\Ticket;
use App\Models\User;
use App\States\Ticket\Cancelled;
use App\States\Ticket\InProgress;
use App\StateTransitions\Ticket\InProgressToCancelled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TicketStateTransitionTest extends TestCase
{
    // use RefreshDatabase;

    // /**
    //  * Indicates whether the default seeder should run before each test.
    //  *
    //  * @var bool
    //  */
    // protected $seed = true;

    // /** @test */
    // public function it_denies_transition_if_user_cannot_cancel()
    // {
    //     $ticket = Ticket::create(['state' => InProgress::class]);
    //     $user = User::create([
    //         'firstname' => '1',
    //         'lastname' => '1',
    //         'login' => '1',
    //         'password' => Hash::make('0000'),
    //         'first_login' => 0,
    //     ]);

    //     $this->actingAs($user);

    //     $transition = new InProgressToCancelled($ticket, Auth::guard());

    //     $this->assertFalse($transition->canTransition());

    //     $this->expectException(\Exception::class);

    //     $ticket->state->transitionTo(Cancelled::class);
    //     $this->assertEquals(InProgress::class, $ticket->state);
    // }
}
