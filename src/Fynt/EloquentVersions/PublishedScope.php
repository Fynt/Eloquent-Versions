<?php namespace Fynt\EloquentVersions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class PublishedScope implements ScopeInterface {

  /**
   * Apply the scope to a given Eloquent query builder.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $builder
   * @return void
   */
  public function apply(Builder $builder)
  {
    $model = $builder->getModel();
    $builder->whereNotNull($model->getQualifiedPublishedAtColumn());
  }

  /**
   * Remove the scope from the given Eloquent query builder.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $builder
   * @return void
   */
  public function remove(Builder $builder)
  {
    $column = $builder->getModel()->getQualifiedPublishedAtColumn();

    $query = $builder->getQuery();

    foreach ((array) $query->wheres as $key => $where)
    {
      // If the where clause is a soft delete date constraint, we will remove it from
      // the query and reset the keys on the wheres. This allows this developer to
      // include deleted model in a relationship result set that is lazy loaded.
      if ($this->isPublishedConstraint($where, $column))
      {
        unset($query->wheres[$key]);

        $query->wheres = array_values($query->wheres);
      }
    }
  }

  /**
   * Determine if the given where clause is a soft delete constraint.
   *
   * @param array $where
   * @param string $column
   * @return bool
   */
  protected function isPublishedConstraint(array $where, $column)
  {
    return $where['type'] == 'NotNull' && $where['column'] == $column;
  }
  
}