<?php

declare (strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\RoleProvider;
use PHPUnit\Framework\TestCase;

class RoleProviderTest extends TestCase
{
    public function testGetRoleList()
    {
        $roleProvider = new RoleProvider();
        $roleList = $roleProvider->getRoleList();

        self::assertArrayHasKey('Utilisateur', $roleList);
        self::assertArrayHasKey('Admin', $roleList);
        self::assertCount(2, $roleList);
        foreach ($roleList as $role) {
            self::assertIsString($role);
        }
    }

    /**
    * @dataProvider pricesForFoodProduct
    */
    public function testIsNotValidRole($role, $expectedOutput)
    {
        $roleProvider = new RoleProvider();
        $isValidRole = $roleProvider->isValidRole($role);

        self::assertIsBool($isValidRole);
        self::assertSame($expectedOutput, $isValidRole);
    }

    public function pricesForFoodProduct()
    {
        return [
           ['test', false],
           ['ROLE_USER', true],
        ];
    }
}
