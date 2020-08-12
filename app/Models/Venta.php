<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Venta extends Model
{
	use Notifiable;

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->client_email;
    }

	// RELATIONS

	public function venta_productos()
	{
		return $this->hasMany(VentaProducto::class);
	}

	// END RELATIONS
}
