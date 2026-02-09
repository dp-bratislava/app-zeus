<?php

namespace Tests\Traits;

use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;

trait FilamentPermissionAssertions
{
    use InteractsWithAuthentication;

    /**
     * Assert that a user can access a resource page
     */
    public function assertUserCanAccessPage($user, string $pageClass): bool
    {
        $this->actingAs($user);

        $url = $pageClass::getUrl();

        return $this->get($url) === 200;
    }

    /**
     * Assert that a user can access a resource page
     */
    public function assertUserCanAccessResource($user, string $resourceClass, string $page = 'index'): void
    {
        $this->actingAs($user);

        $url = $resourceClass::getUrl($page);

        $this->get($url)->assertStatus(200);
    }

    /**
     * Assert that a user cannot access a resource page
     */
    public function assertUserCannotAccessResource($user, string $resourceClass, string $page = 'index'): void
    {
        $this->actingAs($user);

        $url = $resourceClass::getUrl($page);

        // $this->get($url)->assertStatus(403, 'gg');
        $response = $this->get($url);
        if ($response->status() !== 403) {
            $this->fail("User '{$user->login}' should not have access to {$resourceClass}");
        }
    }

    /**
     * Assert that a user sees (or does not see) the resource in navigation
     */
    // public function assertUserSeesNavigationResource($user, string $resourceClass, bool $shouldSee = true): void
    // {
    //     $this->actingAs($user);

    //     $response = $this->get(route('filament.pages.dashboard'));

    //     $title = $resourceClass::getNavigationLabel() ?? class_basename($resourceClass);

    //     if ($shouldSee) {
    //         $response->assertSee($title);
    //     } else {
    //         $response->assertDontSee($title);
    //     }
    // }

    /**
     * Assert all pages (index, create, edit) for a given role/user
     */
    public function assertResourcePermissions($user, string $resourceClass, array $permissions = []): void
    {
        // Index / List
        $canRead = in_array('read', $permissions);
        $canRead ? $this->assertUserCanAccessResource($user, $resourceClass, 'index')
            : $this->assertUserCannotAccessResource($user, $resourceClass, 'index');

        // Create
        $canCreate = in_array('create', $permissions);
        $canCreate ? $this->assertUserCanAccessResource($user, $resourceClass, 'create')
            : $this->assertUserCannotAccessResource($user, $resourceClass, 'create');

        // Edit (we’ll just check the route with a dummy id = 1)
        $canEdit = in_array('update', $permissions);
        $editUrl = $resourceClass::getUrl('edit', ['record' => 1]);
        $this->actingAs($user);
        if ($canEdit) {
            $this->get($editUrl)->assertStatus(200);
        } else {
            $this->get($editUrl)->assertStatus(403);
        }

        // Navigation
        // $this->assertUserSeesNavigationResource($user, $resourceClass, $canRead);
    }
}
