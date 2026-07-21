<?php
namespace App\Utility;

class TenantManager
{
    protected static $_currentTenantId = null;

    public static function setCurrentTenantId($id)
    {
        self::$_currentTenantId = $id;
    }

    public static function getCurrentTenantId()
    {
        return self::$_currentTenantId;
    }
}
