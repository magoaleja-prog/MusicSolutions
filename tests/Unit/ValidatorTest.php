<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function test_required_field_fails_when_empty(): void
    {
        $validator = Validator::make(['title' => ''], ['title' => 'required']);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors());
    }

    public function test_email_rule_rejects_invalid_addresses(): void
    {
        $validator = Validator::make(['email' => 'no-es-un-correo'], ['email' => 'required|email']);

        $this->assertTrue($validator->fails());
    }

    public function test_email_rule_accepts_valid_addresses(): void
    {
        $validator = Validator::make(['email' => 'musico@example.com'], ['email' => 'required|email']);

        $this->assertFalse($validator->fails());
    }

    public function test_min_and_max_length(): void
    {
        $short = Validator::make(['name' => 'ab'], ['name' => 'min:3']);
        $long = Validator::make(['name' => str_repeat('a', 11)], ['name' => 'max:10']);

        $this->assertTrue($short->fails());
        $this->assertTrue($long->fails());
    }

    public function test_confirmed_rule_compares_confirmation_field(): void
    {
        $validator = Validator::make(
            ['password' => 'secreto123', 'password_confirmation' => 'otra-cosa'],
            ['password' => 'confirmed']
        );

        $this->assertTrue($validator->fails());
    }

    public function test_in_rule_uses_whitelist(): void
    {
        $validator = Validator::make(['type' => 'inventado'], ['type' => 'in:public,private']);

        $this->assertTrue($validator->fails());
    }
}
