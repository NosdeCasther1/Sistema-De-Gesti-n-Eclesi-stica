<?php

namespace Tests\Feature;

use App\Models\FinancialAccount;
use App\Models\FinancialAccountAdjustment;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAccountAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $adminUser;
    private FinancialAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->adminUser = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin_test@iglesia.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('administrador');

        // Satisfacer la clave foránea de 'user_id' en 'financial_account_adjustments' que apunta a 'users'
        \DB::table('users')->insert([
            'id' => $this->adminUser->id,
            'name' => 'Admin Test',
            'email' => 'admin_test@iglesia.com',
            'password' => bcrypt('password'),
        ]);

        $this->account = FinancialAccount::create([
            'name' => 'Caja General',
            'initial_balance' => 100.00,
            'description' => 'Caja para gastos comunes',
        ]);
    }

    public function test_updating_only_description_does_not_require_justification(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->put(route('configuracion.accounts.update', $this->account->id), [
                'name' => 'Caja General',
                'initial_balance' => 100.00,
                'description' => 'Nueva descripcion sin cambiar campos criticos',
            ]);

        $response->assertRedirect(route('configuracion.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_accounts', [
            'id' => $this->account->id,
            'description' => 'Nueva descripcion sin cambiar campos criticos',
        ]);

        $this->assertEquals(0, FinancialAccountAdjustment::count());
    }

    public function test_updating_balance_without_justification_fails_validation(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->put(route('configuracion.accounts.update', $this->account->id), [
                'name' => 'Caja General',
                'initial_balance' => 150.00,
                'description' => $this->account->description,
            ]);

        $response->assertSessionHasErrors(['justification']);
        $this->assertEquals(100.00, $this->account->fresh()->initial_balance);
        $this->assertEquals(0, FinancialAccountAdjustment::count());
    }

    public function test_updating_balance_with_short_justification_fails_validation(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->put(route('configuracion.accounts.update', $this->account->id), [
                'name' => 'Caja General',
                'initial_balance' => 150.00,
                'description' => $this->account->description,
                'justification' => 'Corto',
            ]);

        $response->assertSessionHasErrors(['justification']);
        $this->assertEquals(100.00, $this->account->fresh()->initial_balance);
        $this->assertEquals(0, FinancialAccountAdjustment::count());
    }

    public function test_updating_balance_and_name_with_valid_justification_logs_adjustments(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->put(route('configuracion.accounts.update', $this->account->id), [
                'name' => 'Caja Chica',
                'initial_balance' => 150.00,
                'description' => 'Caja chica actualizada',
                'justification' => 'Ajuste anual de saldos y renombre',
            ]);

        $response->assertRedirect(route('configuracion.index'));
        $response->assertSessionHas('success');

        $updatedAccount = $this->account->fresh();
        $this->assertEquals('Caja Chica', $updatedAccount->name);
        $this->assertEquals(150.00, $updatedAccount->initial_balance);

        // Se deben registrar dos entradas de ajuste en la base de datos
        $this->assertEquals(2, FinancialAccountAdjustment::count());

        $this->assertDatabaseHas('financial_account_adjustments', [
            'financial_account_id' => $this->account->id,
            'user_id' => $this->adminUser->id,
            'field_changed' => 'name',
            'old_value' => 'Caja General',
            'new_value' => 'Caja Chica',
            'justification' => 'Ajuste anual de saldos y renombre',
        ]);

        $this->assertDatabaseHas('financial_account_adjustments', [
            'financial_account_id' => $this->account->id,
            'user_id' => $this->adminUser->id,
            'field_changed' => 'initial_balance',
            'old_value' => '100.00',
            'new_value' => '150',
            'justification' => 'Ajuste anual de saldos y renombre',
        ]);
    }
}
