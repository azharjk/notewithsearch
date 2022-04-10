<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

use App\Http\Controllers\Controller;

class ControllerTest extends TestCase
{
    protected $controller;

    protected function setControllerAndValidator()
    {
        Validator::shouldReceive('make')->andReturnSelf();
        Validator::shouldReceive('errors')->andReturnValues([]);

        $this->controller = new Controller();
    }

    public function test_should_throw_bad_request_exception_when_validator_fails()
    {
        $this->setControllerAndValidator();
        Validator::shouldReceive('fails')->andReturnTrue();

        $this->expectException(BadRequestException::class);

        $this->controller->__validate([], []);
    }

    public function test_should_not_throw_exception_when_validator_success()
    {
        $this->setControllerAndValidator();
        Validator::shouldReceive('fails')->andReturnFalse();

        $this->controller->__validate([], []);
    }
}
