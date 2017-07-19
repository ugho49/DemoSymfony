<?php

namespace AdminBundle\Enum;

/**
 * Created by PhpStorm.
 * User: ustephan2016
 * Date: 18/07/2017
 * Time: 16:49
 */
abstract class RolesEnum
{
    const ROLE_USER = "ROLE_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SUPERADMIN = "ROLE_SUPERADMIN";

    const ALL_ROLES = [
        RolesEnum::ROLE_USER => RolesEnum::ROLE_USER,
        RolesEnum::ROLE_ADMIN => RolesEnum::ROLE_ADMIN
    ];
}