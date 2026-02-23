<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingImageRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_remove_image_when_updating_listing(): void
    {
        putenv('FORCE_CLOUD_STORAGE=true');

        Storage::fake('uploads');

        $user = (static function (User $u): User {
            return $u;
        })(User::factory()->create([
            'email_verified_at' => now(),
        ]));

        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Test title',
            'description' => 'Test description',
            'type' => 'rent',
            'category' => 'house',
            'price' => 1000,
            'currency' => 'ZMW',
            'location' => 'Test location',
            'images' => [
                'properties/a.jpg',
                'properties/b.jpg',
            ],
            'status' => 'active',
            'views' => 0,
        ]);

        Storage::disk('uploads')->put('properties/a.jpg', 'a');
        Storage::disk('uploads')->put('properties/b.jpg', 'b');

        $this->actingAs($user);

        $response = $this->put(route('listings.update', $listing->public_id), [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'type' => 'rent',
            'category' => 'house',
            'price' => 1500,
            'currency' => 'ZMW',
            'location' => 'Updated location',
            'remove_images' => [
                'properties/a.jpg',
            ],
        ]);

        $response->assertRedirect();

        $this->assertFalse(Storage::disk('uploads')->exists('properties/a.jpg'));
        $this->assertTrue(Storage::disk('uploads')->exists('properties/b.jpg'));

        $listing->refresh();
        $this->assertSame(['properties/b.jpg'], array_values($listing->images ?? []));
    }
}
