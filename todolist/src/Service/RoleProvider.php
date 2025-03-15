<?php

namespace App\Service;

class RoleProvider
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @return array<string>
     */
    public static function getRoleList(): array
    {
        return [
            'utilisateur' => self::ROLE_USER,
            'admin' => self::ROLE_ADMIN,
        ];
    }

    /**
     * @return bool
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::getRoleList());
    }

}
