<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('uploads an avatar for the authenticated user', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.v1.avatar.update'), [
        'avatar' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Avatar updated successfully.')
        ->assertJsonPath('user.id', $user->id);

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    expect($response->json('user.avatar_url'))->toContain('/storage/avatars/');

    Storage::disk('public')->assertExists('avatars/'.$user->avatar);
});
