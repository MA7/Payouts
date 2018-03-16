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
 * @property    string $zp
 * @property    integer $purseId
 * @property    string $iban
 * @property    double $amount
 * @property    string $description
 * @property    string $createAt
 *
 * @package App
 */
class Settlement extends Model
{
    protected $fillable = [
        'user_id','name','family','mobile','zp','purseId','iban','amount','amount','description','status','transaction_public_id','transfer_ref_id','withdraw_ref_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settlements';


    /**
     * Return full name of user.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->family;
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }



}
