<?php

namespace Lutfiyapr\KehadiranPegawai\Models;

use Winter\Storm\Database\Model;

/**
 * Presensi Model
 */
class Presensi extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lutfiyapr_kehadiranpegawai_presensi';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'keterangan'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'pegawai_id' => 'required|exists:lutfiyapr_kehadiranpegawai_pegawai,id',
        'tanggal' => 'required|date'
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
        'tanggal',
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
}
