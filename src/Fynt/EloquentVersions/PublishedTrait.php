<?php namespace Fynt\EloquentVersions;

trait PublishedTrait {

  public static function bootPublishedTrait()
  {
    static::addGlobalScope(new PublishedScope);
  }

  /**
   * Publish a model instance.
   *
   * @return bool|null
   */
  public function publish()
  {
    if ($this->fireModelEvent('publishing') === false)
    {
      return false;
    }

    $this->{$this->getPublishedAtColumn()} = $this->freshTimestamp();
    $result = $this->save();

    $this->fireModelEvent('published', false);

    return $result;
  }

  /**
   * Determine if the model instance has been published.
   *
   * @return bool
   */
  public function published()
  {
    return ! is_null($this->{$this->getPublishedAtColumn()});
  }

  /**
   * Get a new query builder that includes soft deletes.
   *
   * @return \Illuminate\Database\Eloquent\Builder|static
   */
  public static function withUnpublished()
  {
    return (new static)->newQueryWithoutScope(new PublishedScope);
  }

  /**
   * Register a publishing model event with the dispatcher.
   *
   * @param  \Closure|string  $callback
   * @return void
   */
  public static function publishing($callback)
  {
    static::registerModelEvent('publishing', $callback);
  }

  /**
   * Register a published model event with the dispatcher.
   *
   * @param  \Closure|string  $callback
   * @return void
   */
  public static function published($callback)
  {
    static::registerModelEvent('published', $callback);
  }

  /**
   * Get the name of the "published at" column.
   *
   * @return string
   */
  public function getPublishedAtColumn()
  {
    return defined('static::PUBLISHED_AT') ? static::PUBLISHED_AT : 'published_at';
  }

  /**
   * Get the fully qualified "published at" column.
   *
   * @return string
   */
  public function getQualifiedPublishedAtColumn()
  {
    return $this->getTable() . '.' . $this->getPublishedAtColumn();
  }

}
