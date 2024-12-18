<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'visits';

    protected $fillable = [
        'name',
        'email',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'name' => 'string',
        'email' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function view(): Builder
    {
        return self::selectRaw("
            visits.*
        ");
    }

    public static function viewWhere($request)
    {
        return self::view()
            ->where(function (Builder $q) use ($request) {
                if ($request->q) {
                    $q->where(function ($q2) use ($request) {
                        $q2->orWhere('visits.name', 'LIKE', "%{$request->q}%");
                    });
                }
            });
    }
}
