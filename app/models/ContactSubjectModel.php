<?php

/**
 * Class ContactSubjectModel
 */ 
class ContactSubjectModel extends \Asatru\Database\Model
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getAll()
    {
        try {
            return ContactSubjectModel::raw('SELECT * FROM `@THIS`');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}