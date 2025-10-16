<?php

namespace Lutfiyapr\KehadiranPegawai\Models;

use Winter\Storm\Database\Model;

/**
 * Pegawai Model
 */
class Pegawai extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lutfiyapr_kehadiranpegawai_pegawai';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'email',
        'no_telp',
        'alamat',
        'status'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'nip' => 'required|unique:lutfiyapr_kehadiranpegawai_pegawai',
        'nama' => 'required|max:100',
        'email' => 'required|email|unique:lutfiyapr_kehadiranpegawai_pegawai',
        'jabatan' => 'required|max:100'
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
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'presensi' => [
            'Lutfiyapr\KehadiranPegawai\Models\Presensi',
            'key' => 'pegawai_id'
        ]
    ];
    public $hasOneThrough = [];
    public $hasManyThrough = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
