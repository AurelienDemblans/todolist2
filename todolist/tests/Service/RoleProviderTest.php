<?php

declare (strict_types=1);

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

    public function testIsValidRole()
    {
        $role = 'test';
        $roleProvider = new RoleProvider();
        $isValidRole = $roleProvider->isValidRole($role);

        self::assertIsBool($isValidRole);
        self::assertSame(false, $isValidRole);
    }

    public function testIsNotValidRole()
    {
        $role = 'ROLE_USER';
        $roleProvider = new RoleProvider();
        $isValidRole = $roleProvider->isValidRole($role);

        self::assertIsBool($isValidRole);
        self::assertSame(true, $isValidRole);
    }
}
