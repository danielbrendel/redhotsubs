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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
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
            $item = AppSettingsModel::getSettings();
            return $item->get('info');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getInfoStyle()
    {
        try {
            $item = AppSettingsModel::getSettings();
            $value = $item->get('info_style');

            if ((!is_string($value)) || (strlen($value) === 0)) {
                return 'violet';
            }

            return $value;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getCategories()
    {
        try {
            $item = AppSettingsModel::getSettings();
            $cats = $item->get('categories');
            return explode(',', $cats);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $item
     * @return mixed
     * @throws \Exception
     */
    public static function getSettings($item = 1)
    {
        try {
            return AppSettingsModel::raw('SELECT * FROM `@THIS` WHERE id = ?', [$item])->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}