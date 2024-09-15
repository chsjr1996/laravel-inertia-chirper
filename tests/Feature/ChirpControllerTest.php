<?php

use App\Models\Chirp;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

describe("ChirpController", function () {
    it("list current chirps on index route", function () {
        $user = User::factory()->create();
        $chirps = Chirp::factory(3)->create(["user_id" => $user->id]);

        $this->actingAs($user)
            ->get(route("chirps.index"))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Chirps/Index")
                    ->has("chirps", 3)
                    ->where("chirps.0.message", $chirps[0]->message)
                    ->where("chirps.1.message", $chirps[1]->message)
                    ->where("chirps.2.message", $chirps[2]->message)
            );
    });

    it("list the created chirp on index route", function () {
        $user = User::factory()->create();
        $attributes = [
            "message" => "New chirp (store test)",
        ];

        $this->followingRedirects();
        $this->actingAs($user)
            ->post(route("chirps.index"), $attributes)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Chirps/Index")
                    ->has("chirps", 1)
                    ->where("chirps.0.message", $attributes["message"])
            );
    });

    it("only chirp owner can edit it", function () {
        $userA = User::factory()->create();
        $chirpUserA = Chirp::factory()->create(["user_id" => $userA->id]);
        $attributesA = ["message" => "Editing owned chirp"];

        $userB = User::factory()->create();
        $chirpUserB = Chirp::factory()->create(["user_id" => $userB->id]);

        $this->followingRedirects();
        $this->actingAs($userA)
            ->put(route("chirps.update", $chirpUserA), $attributesA)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Chirps/Index")
                    ->where("chirps.0.message", $attributesA["message"])
            );

        $this->actingAs($userA)
            ->put(route("chirps.update", $chirpUserB), [
                "message" => "Editing other user chirp",
            ])
            ->assertStatus(403);
    });

    it("only chirp owner can delete it", function () {
        $userA = User::factory()->create();
        $chirpUserA = Chirp::factory()->create(["user_id" => $userA->id]);

        $userB = User::factory()->create();
        $chirpUserB = Chirp::factory()->create(["user_id" => $userB->id]);

        $this->followingRedirects();
        $this->actingAs($userA)
            ->delete(route("chirps.update", $chirpUserA))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Chirps/Index")
                    ->has('chirps', 1)
            );

        $this->actingAs($userA)
            ->delete(route("chirps.update", $chirpUserB))
            ->assertStatus(403);

        $this->actingAs($userA)
            ->get(route("chirps.index"))
            ->assertStatus(200)
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Chirps/Index")
                    ->has("chirps", 1)
            );
    });
});
