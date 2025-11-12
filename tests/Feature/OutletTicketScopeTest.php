<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Users;
use App\Models\Role;
use App\Models\Outlet;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\SLA;

class OutletTicketScopeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_sees_only_tickets_for_their_outlet_and_superadmin_sees_all()
    {
        // Seed roles
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleSuper = Role::create(['name' => 'superadmin']);

        // Outlets
        $jakarta = Outlet::create(['name' => 'Jakarta', 'address' => 'Jakarta Office']);
        $surabaya = Outlet::create(['name' => 'Surabaya', 'address' => 'Surabaya Office']);

        // Ticket type and SLA required by tickets
        $type = TicketType::create(['name' => 'Issue', 'description' => 'General', 'is_active' => 1]);
        $sla = SLA::create(['name' => 'Default', 'response_time' => 24, 'resolution_time' => 72, 'description' => 'Default SLA']);

        // Create a superadmin user
        $super = Users::create([
            'name' => 'Super Admin',
            'email' => 'super@example.test',
            'password' => bcrypt('password'),
            'role_id' => $roleSuper->id,
            'job_tittle' => 'Super Admin',
        ]);

        // Create an admin assigned to Jakarta
        $adminJakarta = Users::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jkt@example.test',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
            'outlet_id' => $jakarta->id,
            'job_tittle' => 'Admin',
        ]);

        // Create tickets
        $t1 = Ticket::create([
            'ticket_type_id' => $type->id,
            'sla_id' => $sla->id,
            'created_by' => $super->id,
            'outlet_id' => null,
            'title' => 'Jakarta Ticket 1',
            'description' => 'Problem in Jakarta 1',
        ]);
        $t1->outlet_id = $jakarta->id;
        $t1->save();

        $t2 = Ticket::create([
            'ticket_type_id' => $type->id,
            'sla_id' => $sla->id,
            'created_by' => $super->id,
            'outlet_id' => null,
            'title' => 'Jakarta Ticket 2',
            'description' => 'Problem in Jakarta 2',
        ]);
        $t2->outlet_id = $jakarta->id;
        $t2->save();

        $t3 = Ticket::create([
            'ticket_type_id' => $type->id,
            'sla_id' => $sla->id,
            'created_by' => $super->id,
            'outlet_id' => null,
            'title' => 'Surabaya Ticket 1',
            'description' => 'Problem in Surabaya',
        ]);
        $t3->outlet_id = $surabaya->id;
        $t3->save();

    // Ensure tickets exist in DB
    $this->assertDatabaseHas('tbl_tickets', ['title' => 'Jakarta Ticket 1']);
    $this->assertDatabaseHas('tbl_tickets', ['title' => 'Jakarta Ticket 2']);
    $this->assertDatabaseHas('tbl_tickets', ['title' => 'Surabaya Ticket 1']);
    // Confirm ticket counts per outlet
    $this->assertEquals(2, Ticket::where('outlet_id', $jakarta->id)->count());
    $this->assertEquals(1, Ticket::where('outlet_id', $surabaya->id)->count());

    // As admin Jakarta, should see only Jakarta tickets
        $this->actingAs($adminJakarta)
            ->get(route('admin.tickets.index'))
            ->assertStatus(200)
            ->assertSee('Jakarta Ticket 1')
            ->assertSee('Jakarta Ticket 2')
            ->assertDontSee('Surabaya Ticket 1');

        // As superadmin, should see all tickets
        $this->actingAs($super)
            ->get(route('admin.tickets.index'))
            ->assertStatus(200)
            ->assertSee('Jakarta Ticket 1')
            ->assertSee('Jakarta Ticket 2')
            ->assertSee('Surabaya Ticket 1');
    }
}
