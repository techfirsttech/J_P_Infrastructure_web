<?php

namespace Modules\User\Database\Seeders\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\User;

trait HandlesSeedingAuthentication
{
    /**
     * Ensure we have a valid system user for seeding operations
     */
    protected function ensureSystemUserForSeeding(): int
    {
        try {
            // First, try to get the first available user
            $firstUser = User::first();
            if ($firstUser) {
                if (isset($this->command)) {
                    $this->command->info("✅ Using existing user (ID: {$firstUser->id}) for seeding operations");
                }
                return $firstUser->id;
            }

            // If no users exist, check if we can create a temporary system user
            $systemUser = $this->createTemporarySystemUser();
            if ($systemUser) {
                if (isset($this->command)) {
                    $this->command->info("✅ Created temporary system user (ID: {$systemUser->id}) for seeding operations");
                }
                return $systemUser->id;
            }

            // As a last resort, use ID 1 as fallback
            if (isset($this->command)) {
                $this->command->warn("⚠️  No users found and cannot create system user. Using ID 1 as fallback.");
            }
            return 1;

        } catch (\Exception $e) {
            if (isset($this->command)) {
                $this->command->error("❌ Error ensuring system user: " . $e->getMessage());
                $this->command->warn("⚠️  Using ID 1 as fallback user ID.");
            }
            return 1;
        }
    }

    /**
     * Create a temporary system user for seeding operations if none exists
     */
    protected function createTemporarySystemUser(): ?User
    {
        try {
            // Check if users table exists and has the required columns
            if (!$this->tableExistsInDatabase('users')) {
                if (isset($this->command)) {
                    $this->command->warn("⚠️  Users table does not exist. Cannot create system user.");
                }
                return null;
            }

            // Create a minimal system user for seeding
            $systemUser = User::create([
                'name' => 'System Seeder User',
                'email' => 'system.seeder.' . now()->timestamp . '@system.local',
                'username' => 'system_seeder_' . now()->timestamp,
                'mobile' => '0000000000',
                'password' => bcrypt('system-seed-' . now()->timestamp),
                'status' => 'Active'
            ]);

            return $systemUser;

        } catch (\Exception $e) {
            if (isset($this->command)) {
                $this->command->warn("⚠️  Could not create temporary system user: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Check if a table exists in the database
     */
    protected function tableExistsInDatabase(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Temporarily set an authenticated user for model events during seeding
     */
    protected function setAuthenticatedUserForSeeding(int $userId): void
    {
        try {
            // Create a mock authenticated user for the seeding process
            $user = User::find($userId);
            if ($user) {
                Auth::login($user);
                if (isset($this->command)) {
                    $this->command->info("🔐 Temporarily authenticated as user ID: {$userId} for seeding");
                }
            } else {
                if (isset($this->command)) {
                    $this->command->warn("⚠️  Could not authenticate user ID: {$userId}. Model events may use null values.");
                }
            }
        } catch (\Exception $e) {
            if (isset($this->command)) {
                $this->command->warn("⚠️  Could not set authenticated user: " . $e->getMessage());
            }
        }
    }

    /**
     * Clear the authenticated user after seeding
     */
    protected function clearAuthenticatedUserAfterSeeding(): void
    {
        try {
            Auth::logout();
            if (isset($this->command)) {
                $this->command->info("🔓 Cleared authenticated user after seeding");
            }
        } catch (\Exception $e) {
            // Silently handle logout errors - not critical for seeding
        }
    }

    /**
     * Complete seeding authentication setup - call this at the start of run()
     */
    protected function setupSeedingAuthentication(): int
    {
        $userId = $this->ensureSystemUserForSeeding();
        $this->setAuthenticatedUserForSeeding($userId);
        return $userId;
    }

    /**
     * Complete seeding authentication cleanup - call this at the end of run()
     */
    protected function cleanupSeedingAuthentication(): void
    {
        $this->clearAuthenticatedUserAfterSeeding();
    }
}