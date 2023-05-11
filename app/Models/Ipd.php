<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ipd extends Model
{
    use HasFactory;

    protected $fillable = [
        'an',
        'vn',
        'adm_officer_id',
        'regdate',
        'regtime',
        'spclty_id',
        'firstward_id',
        'pttype_id',
        'severe_type_id',
        'ipt_admit_type_id',
        'confirm_discharge',
        'dchdate',
        'dchtime',
        'dch_status_id',
        'dch_type_id',
        'dch_officer_id',
        'dch_severe_type_id',
        'dch_spclty_id',
        'admit_for',
        'drainages',
        'line_noty',
        'is_screen_asses',
        'is_vs_new',
        'is_do_med',
        'is_nn_new',
        'reasonadmit_type_id',
        'o2_type_id',
        'occu_type_id',
        'los',
        'sdx1',
        'sdx2',
        'confirm_summary_dc',
        'summary_dc_officer_id',
        'plan',
        'operation',
        'created_by',
        'updated_by',
        'patient_id'
    ];
}
