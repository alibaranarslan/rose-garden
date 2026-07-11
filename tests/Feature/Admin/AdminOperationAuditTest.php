<?php

namespace Tests\Feature\Admin;

use App\Models\AdminOperationAudit;
use App\Models\User;
use App\Support\AdminOperationAuditor;
use App\Support\AdminPrivileges;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOperationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_manager_can_manage_storefront_operations_without_system_privileges(): void
    {
        $this->seed(RoleSeeder::class);

        $manager = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
        $manager->assignRole('client_manager');

        $this->assertTrue(AdminPrivileges::canAccessAdminPanel($manager));
        $this->assertTrue(AdminPrivileges::canManageStorefrontOperations($manager));
        $this->assertFalse(AdminPrivileges::canPublishConfiguration($manager));
        $this->assertFalse(AdminPrivileges::canManageSystemSettings($manager));

        $this->actingAs($manager)->get('/admin/admin-operation-audits')->assertOk();
        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_operation_audit_records_sanitized_context(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
            'email' => 'audit-admin@example.test',
        ]);

        $this->actingAs($admin);

        AdminOperationAuditor::record(
            'settings.sms.test',
            null,
            [
                'recipient' => '+905000000000',
                'password' => 'plain-secret',
                'nested' => ['api_key' => 'real-api-key'],
            ],
            'simulated',
            'SMS test simülasyonu'
        );

        $audit = AdminOperationAudit::query()->firstOrFail();

        $this->assertSame($admin->id, $audit->user_id);
        $this->assertSame('settings.sms.test', $audit->action);
        $this->assertSame('simulated', $audit->status);
        $this->assertSame('[redacted]', $audit->context['password']);
        $this->assertSame('[redacted]', $audit->context['nested']['api_key']);
        $this->assertSame('+905000000000', $audit->context['recipient']);
    }
}
