@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Configuración</div>

                <div class="card-body">

                    <div class="form-group row">

                        <div class="col-6 text-md-right">
                            <label for="dias_validez" class="col-form-label">Días validez Gift Cards</label>
                        </div>

                        <div class="col-md-6">
                            <input id="dias_validez" type="number" class="form-control" min="1" name="dias_validez" value="{{ env('VENCIMIENTO_GIFT_CARDS', 30) }}">
                        </div>

                        <div class="form-group row">
                            <div class="col text-center">
                                <button id="btn_actualizar_validez" class="btn btn-primary">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">

        $(function () {

            $('#btn_actualizar_validez').on('click', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('config.update') }}",
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        key: 'VENCIMIENTO_GIFT_CARDS',
                        value: $('#dias_validez').val(),
                    },
                })
                .done(function() {

                    // Show message
                    showSnackbar('Dias de validez actualizados.');
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

        });

    </script>
@endsection