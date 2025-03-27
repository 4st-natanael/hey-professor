<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

it('should be able to create a new question bigger than 255 characters', function () {

    // Arrange::preparar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 260).'?',
    ]);

    // Assert :: verificar
    $request->assertRedirect(route('dashboard'));
    assertDatabaseCount('questions', 1);
    assertDatabaseHas('questions', ['question' => str_repeat('*', 260).'?']);
});

it('should check if ends with question mark ?', function () {
    // Arrange::preparar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 10),
    ]);

    // Assert :: verificar
    $request->assertSessionHasErrors('question'); // Verifica se há um erro para o campo 'question'
    $this->assertEquals(
        'Are you sure that is a question? It is missing the question mark in the end.',
        $request->getSession()->get('errors')->getBag('default')->first('question')
    );
    assertDatabaseCount('questions', 0);
});

it('should have at least 10 characters', function () {

    // Arrange::preparar
    $user = User::factory()->create();
    actingAs($user);

    // Act:: agir
    $request = post(route('question.store'), [
        'question' => str_repeat('*', 8).'?',
    ]);

    // Assert :: verificar
    $request->assertSessionHasErrors(['question' => __('validation.min.string', ['min' => 10, 'attribute' => 'question'])]);
    assertDatabaseCount('questions', 0);

});
