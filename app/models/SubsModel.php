<?php

/**
 * Class SubsModel
 */ 
class SubsModel extends \Asatru\Database\Model
{
    const SUB_FEATURED =  true;
    const SUB_UNFEATURED = false;

    /**
     * @return mixed
     */
    public static function getAllSubs()
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` ORDER BY cat_order, sub_ident ASC');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param bool $which
     * @return mixed
     */
    public static function  getFeatureSubs($which)
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE featured = ? ORDER BY sub_ident ASC', [$which]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public static function getSubsForTwitter()
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE twitter_posting = 1 ORDER BY sub_ident ASC');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $sub
     * @return bool
     */
    public static function subExists($sub)
    {
        try {
            $result = SubsModel::raw('SELECT COUNT(*) AS count FROM `' . self::tableName() . '` WHERE sub_ident = ?', [$sub]);
            return $result->get(0)->get('count') > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $ident
     * @return mixed
     * @throws Exception
     */
    public static function getSubData($ident)
    {
        try {
            $result = SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE sub_ident = ?', [$ident]);
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $cats
     * @return mixed
     * @throws Exception
     */
    public static function getRandomFromVideoCategories(array $cats)
    {
        try {
            $inq = implode(',', array_fill(0, count($cats), '?'));
            $result = SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE LOWER(category) IN (' . $inq . ') AND cat_video = 1 ORDER BY RAND() LIMIT 1', $cats);
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $limit
     * @return mixed
     * @throws Exception
     */
    public static function getErrorSubs($limit = 1)
    {
        try {
            $result = [];

            $subs = SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE last_check IS NULL LIMIT ' . $limit);

            if ($subs->count() == 0) {
                //SubsModel::raw('UPDATE `' . self::tableName() . '` SET last_check = NULL');
                return $result;
            }

            foreach ($subs as $sub) {
                $subname = $sub->get('sub_ident');
                if (strpos($subname, 'r/') !== false) {
                    $subname = substr($subname, 2);
                }
                
                $status = CrawlerModule::getSubStatus($subname);
                if ((isset($status->error)) && ($status->error == 404)) {
                    $result[] = [
                        'name' => $subname,
                        'error' => $status->error,
                        'reason' => ((isset($status->reason)) ? $status->reason : null)
                    ];
                }

                SubsModel::raw('UPDATE `' . self::tableName() . '` SET last_check = CURRENT_TIMESTAMP WHERE id = ?', [
                    $sub->get('id')
                ]);
            }

            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $limit
     * @param $hours
     * @param $maxlen
     * @throws \Exception
     */
    public static function updateSubDescriptions($limit = 1, $hours = 24, $maxlen = 30)
    {
        try {
            $subs = SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE last_desc IS NULL OR TIMESTAMPDIFF(HOUR, last_desc, NOW()) >= ? LIMIT ' . $limit, [$hours]);

            if ($subs->count() == 0) {
                //SubsModel::raw('UPDATE `' . self::tableName() . '` SET last_desc = NULL');
            }

            foreach ($subs as $sub) {
                $subname = $sub->get('sub_ident');
                if (strpos($subname, 'r/') !== false) {
                    $subname = substr($subname, 2);
                }
                
                $description = CrawlerModule::querySubDescription($subname);
                if ((is_string($description)) && (strlen($description) > 0)) {
                    SubsModel::raw('UPDATE `' . self::tableName() . '` SET description = ? WHERE id = ?', [
                        ((strlen($description) > $maxlen) ? substr($description, 0, $maxlen - 3) . '...' : $description), $sub->get('id')
                    ]);
                }

                SubsModel::raw('UPDATE `' . self::tableName() . '` SET last_desc = CURRENT_TIMESTAMP WHERE id = ?', [
                    $sub->get('id')
                ]);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function addMissingSubsToCache()
    {
        try {
            $subs = SubsModel::getAllSubs();

            foreach ($subs as $sub) {
                if (!CacheModel::has($sub->get('sub_ident') . '_thumbnail')) {
                    CacheModel::raw('INSERT INTO `@THIS` (ident, value, updated_at) VALUES(?, NULL, CURRENT_TIMESTAMP)', [
                        $sub->get('sub_ident') . '_thumbnail'
                    ]);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $limit
     * @return void
     * @throws \Exception
     */
    public static function updateSubThumbnails($limit = 1)
    {
        try {
            $cached_subs = CacheModel::raw('SELECT * FROM `@THIS` WHERE ident LIKE ? AND TIMESTAMPDIFF(SECOND, updated_at, NOW()) >= ? LIMIT ' . $limit, ['r/%', env('APP_CACHEDURATION')]);

            foreach ($cached_subs as $cached_sub) {
                CrawlerModule::queryThumbnail(str_replace('_thumbnail', '', $cached_sub->get('ident')));
            }
        } catch (\Exception $e) {
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
        return 'subs';
    }
}