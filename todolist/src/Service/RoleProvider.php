<?php

declare (strict_types=1);

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
            'Utilisateur' => self::ROLE_USER,
            'Admin' => self::ROLE_ADMIN,
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
