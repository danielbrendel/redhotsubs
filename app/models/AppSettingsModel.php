<?php

/**
 * Class AppSettingsModel
 */ 
class AppSettingsModel extends \Asatru\Database\Model
{
    /**
     * @return string
     * @throws Exception
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
     * @throws Exception
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
     * @return string
     * @throws Exception
     */
    public static function getAbout()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('about');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getAgeConsent()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('age_consent');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getAppContent()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('app');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getHeadCode()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('head_code');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public static function hasInfo()
    {
        try {
            $info = static::getInfo();
            return ($info !== null) && (is_string($info)) && (strlen($info) > 0);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getInfo()
    {
        try {
            $item = AppSettingsModel::where('id', '=', 1)->first();
            return $item->get('info');
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