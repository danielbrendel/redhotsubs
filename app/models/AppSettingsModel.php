<?php

/**
 * Class AppSettingsModel
 */ 
class AppSettingsModel extends \Asatru\Database\Model
{
    /**
     * @return string
     */
    public static function getImprint()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('imprint');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public static function getPrivacyPolicy()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('privacy');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the associated table name of the migration
     * 
     * @return string
     */
    public static function tableName()
    {
        return 'appsettings';
    }
}