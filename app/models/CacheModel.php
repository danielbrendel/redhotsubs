<?php

/**
 * Class CacheModel
 */ 
class CacheModel extends \Asatru\Database\Model
{
	/**
	 * Obtain value either from cache or from closure
	 *	
		*	@param string $ident The cache item identifier
		*	@param int $timeInSeconds Amount of seconds the item shall be cached
		*	@param $closure Function to be called for the actual value
		*	@return mixed
		*/
	public static function remember($ident, $timeInSeconds, $closure)
	{
		$item = static::find($ident, 'ident');
		if ($item->count() == 0) {
			$value = $closure();
			
			static::raw('INSERT INTO `' . self::tableName() . '` (ident, value, updated_at) VALUES(?, ?, ?)', [
				$ident, $value, date('Y-m-d H:i:s')
			]);
			
			return $value;
		} else {
			$data = $item->get(0);
			$dtLast = new DateTime(date('Y-m-d H:i:s', strtotime($data->get('updated_at'))));
			$dtLast->add(new DateInterval('PT' . $timeInSeconds . 'S'));
			$dtNow = new DateTime('now');

			if ($dtNow < $dtLast) {
				return $data->get('value');
			} else {
				$value = $closure();

				static::raw('UPDATE `' . self::tableName() . '` SET value = ?, updated_at = ? WHERE id = ?', [
					$value, date('Y-m-d H:i:s'), $data->get('id')
				]);
				
				return $value;
			}
		}
		
		return null;
	}
	
	/**
	 * Check for item existence
	 *
	 *	@param $ident
		*  @return bool
		*/
	public static function has($ident)
	{
		$item = static::find($ident, 'ident');
		if ($item->count() > 0) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get item and then delete it
	 *
	 *	@param $ident
		*  @return mixed
		*/
	public static function pull($ident)
	{
		$item = static::find($ident, 'ident');
		if ($item->count() > 0) {
			$data = $item->get(0);
			
			static::where('id', '=', $item->get(0)->get('id'))->delete();
			
			return $data->get('value');
		}
		
		return null;
	}
	
	/**
	 * Forget cache item
	 * 
	 * @param string $ident The item identifier
	 * @return bool
	 */
	public static function forget($ident)
	{
		$item = static::find($ident, 'ident');
		if ($item->count() > 0) {
			static::where('id', '=', $item->get(0)->get('id'))->delete();
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Return the associated table name of the migration
	 * 
	 * @return string
	 */
	public static function tableName()
	{
		return 'cache';
	}
}
    