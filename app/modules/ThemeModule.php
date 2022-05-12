<?php

/**
 * Class ThemeModule
 */
class ThemeModule 
{
    /**
     * Get theme to include if any
     * 
     * @return mixed
     */
    public static function getIncludeTheme()
    {
        if ((isset($_COOKIE['theme'])) && (file_exists(public_path() . '/css/themes/' . $_COOKIE['theme'] . '.css'))) {
            return asset('css/themes/' . $_COOKIE['theme'] . '.css');
        }

        return null;
    }

    /**
     * Get a HTML code to include the theme
     * 
     * @return string
     */
    public static function includeThemeAsHtml()
    {
        $theme = static::getIncludeTheme();

        if ($theme === null) {
            return '';
        }

        return '<link rel="stylesheet" type="text/css" href="' . $theme . '">';
    }

    /**
     * Get list of available themes
     * 
     * @return array
     */
    public static function getListOfThemes()
    {
        $result = array();

        if (is_dir(public_path() . '/css/themes')) {
            $files = scandir(public_path() . '/css/themes');
            foreach ($files as $file) {
                if (($file[0] === '.') || (pathinfo($file, PATHINFO_EXTENSION) !== 'css')) {
                    continue;
                }

                $result[] = substr($file, 0, strpos($file, '.css'));
            }
        }

        return $result;
    }
}
