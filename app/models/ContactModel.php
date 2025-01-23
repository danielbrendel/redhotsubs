<?php

/**
 * Class ContactModel
 */ 
class ContactModel extends \Asatru\Database\Model
{
    /**
     * @param $name
     * @param $email
     * @param $subject
     * @param $content
     * @return void
     * @throws \Exception
     */
    public static function addEntry($name, $email, $subject, $content)
    {
        try {
            ContactModel::raw('INSERT INTO `@THIS` (name, email, subject, content) VALUES(?, ?, ?, ?)', [
                $name, $email, $subject, $content
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}