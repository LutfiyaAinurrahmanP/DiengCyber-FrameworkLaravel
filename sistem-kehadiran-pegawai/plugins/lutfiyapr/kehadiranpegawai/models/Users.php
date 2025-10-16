<?php

namespace Lutfiyapr\KehadiranPegawai\Models;

use Illuminate\Support\Facades\Hash;
use Winter\Storm\Database\Model;
use Illuminate\Support\Str;

/**
 * Users Model
 */
class Users extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lutfiyapr_kehadiranpegawai_users';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'pegawai_id',
        'username',
        'email',
        'password',
        'role',
        'api_token',
        'last_login_at'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'username' => 'required|unique:lutfiyapr_kehadiranpegawai_users',
        'email' => 'required|email|unique:lutfiyapr_kehadiranpegawai_users',
        'password' => 'required|min:6',
        'role' => 'required|in:admin,pegawai'
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'password',
        'api_token'
    ];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'last_login_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [
        'pegawai' => [
            'Lutfiyapr\KehadiranPegawai\Models\Pegawai',
            'key' => 'pegawai_id'
        ]
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

     // Hash password sebelum disimpan
    public function beforeCreate()
    {
        if (!empty($this->password)) {
            $this->password = Hash::make($this->password);
        }
    }

    // Generate API Token
    public function generateToken()
    {
        $this->api_token = Str::random(80);
        $this->save();
        return $this->api_token;
    }

    // Verify Password
    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    // Update Last Login
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }
}
