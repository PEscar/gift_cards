<?php

namespace App;

use App\Models\Sede;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'habilitado'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // RELATIONS

    public function sedes()
    {
        return $this->belongsToMany(Sede::class)->withTimestamps();
    }

    // END RELATIONS

    // FUNCTIONS

    public function setNivel($nivel)
    {
        if ( $nivel == 'Nivel2'  )
        {
            $this->removeRole('Admin');
            $this->removeRole('Nivel1');

        } elseif ( $nivel == 'Admin' )
        {
            $this->assignRole('Admin');
            $this->removeRole('Nivel1');
        }
        else if ( $nivel == 'Nivel1' )
        {
            $this->assignRole('Nivel1');
            $this->removeRole('Admin');
        }
    }

    public function setSedes($sedes)
    {
        $this->sedes()->sync($sedes);
    }

    public function getNivelAttribute()
    {
        return $this->hasRole('Admin') ? 'Admin' : ( $this->hasRole('Nivel1') ? 'Nivel1' : 'Nivel2' ) ;
    }

    // END FUNCTIONS
}
