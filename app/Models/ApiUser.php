<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
* Domain
*/
class ApiUser extends Authenticatable {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'api_users';
  public $timestamps = false;
  
}

