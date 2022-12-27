<?php

declare(strict_types=1);

namespace Costa\DomainPackage\Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestValidation
{
    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        $ruleParams = []
    ) {
        $response = $this->postJson($this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        $ruleParams = []
    ) {
        $response = $this->putJson($this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                \Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
            ]);
        }
    }

    protected abstract function model();

    protected abstract function routeStore();

    protected abstract function routeUpdate();
}
