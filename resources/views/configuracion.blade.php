@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Configuración</div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="dias_validez" class="col-md-4 col-form-label text-md-right">Días validez Gift Cards</label>

                        <div class="col-md-6">
                            <input id="dias_validez" type="number" class="form-control" min="1" name="dias_validez" value="{{ env('VENCIMIENTO_GIFT_CARDS', 30) }}">
                        </div>

                        <div class="col-md-2">
                            <button id="btn_actualizar_validez" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
