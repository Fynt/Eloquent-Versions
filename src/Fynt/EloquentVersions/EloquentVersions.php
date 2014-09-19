<?php namespace Fynt\EloquentVersions;

use \DB;
use \Config;
use Illuminate\Database\QueryException;

class EloquentVersions {

  /**
   * Get the table name, where we will save the versions from the Laravel config.
   *
   * @return Illuminate\Database\Query\Builder
   */
  public static function getVersionsTable()
  {
    $tableName = Config::get('eloquent-versions::table');
    return DB::table($tableName);
  }

  /**
   * Gets a query object for a model.
   *
   * @param Eloquent $model
   * @return Illuminate\Database\Query\Builder
   */
  public static function queryForModel($model)
  {
    return self::getVersionsTable()
      ->where('object_id', '=', $model->id)
      ->where('object_table', '=', get_class($model));
  }

  /**
   * Save a version of the current state of an object.
   * Will automatically check for duplicates and won't save them.
   * You can specify a name for the version if you want to.
   *
   * @param Elequnt $model
   * @return bool
   */
  public static function add($model)
  {
    $jsonData   = json_encode($model->toArray());

    if($model->id) {
      $creationDate = date('Y-m-d H:i:s');

      try {
        return self::getVersionsTable()->insert([
          'object_table' => get_class($model),
          'object_id' => $model->id,
          'data' => $jsonData,
          'hash' => sha1($jsonData),
          'created_at' => $creationDate,
          'updated_at' => $creationDate
        ]);
      } catch (QueryException $e) {
        return false;
      }
    }

    return false;
  }


  /**
   * Loads a specific saved version by its primary key
   *
   * @param int $versionId
   * @return array
   */
  public static function loadDataByVersionId($versionId)
  {
    $result = self::getVersionsTable()->whereId($versionId)->first();
    return json_decode($result->data);
  }

  /**
   * Loads a latest version for a model
   *
   * @param Eloquent $model
   * @return array
   */
  public static function loadDataForModel($model)
  {
    $result = self::latest($model);
    if ($result) {
      return json_decode($result->data);
    }

    return [];
  }

  /**
   * Get all versions of a model
   *
   * @param Eloquent $model
   * @return array
   */
  public static function all($model) {
    return self::queryForModel($model)->orderBy('updated_at', 'desc')->get();
  }

  /**
   * How many versions are saved for a given model?
   *
   * @param Eloquent $model
   * @return int
   */
  public static function count($model)
  {
    return self::queryForModel($model)->count();
  }

  /**
   * Retrieve the most recent version of a model
   *
   * @param Eloquent $model
   * @return stdClass|null
   */
  public static function latest($model)
  {
    return self::queryForModel($model)->orderBy('created_at', 'desc')->first();
  }

  /**
   * Delete a version of a model
   *
   * @param int $versionId
   * @return bool
   */
  public static function delete($versionId)
  {
    return self::getVersionsTable()->delete($versionId);
  }

  /**
   * Delete all versions of a model
   *
   * @param Eloquent $model
   * @return bool
   */
  public static function deleteAll($model)
  {
    return self::queryForModel($model)->delete();
  }
}
