<?php

class Admins extends ActiveRecord\Model
{
    static $table_name = 'admins';
    static $primary_key = 'adminToken';
}