<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Settlement
 *  Model of settlements table in database.
 *
 * @property    integer $id
 * @property    string $name
 * @property    string $family
 * @property    string $mobile
 * @property    string $ssn
 * @property    string $birthDate
 * @property    string $zp
 * @property    integer $purseId
 * @property    string $iban
 * @property    double $amount
 * @property    string $description
 * @property    string $createAt
 *
 * @property    string $FullName   read-only
 * @property    string $JalaliDate   read-only
 *
 * @package App
 */
class Settlement extends Model
{
    /**
     * Name of updateAt col in table.
     *
     * @var string
     */
    const CREATED_AT = 'createAt';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settlements';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = array('name', 'type', 'danger_level');

    /**
     * Return full name of user.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->family;
    }
}
