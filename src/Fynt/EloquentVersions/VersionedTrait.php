<?php namespace Fynt\EloquentVersions;

use \Versions;

trait VersionedTrait {

  public function addVersion()
  {
    Versions::add($this);
  }

}
