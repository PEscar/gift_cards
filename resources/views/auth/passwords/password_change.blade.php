@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Cambiar Contraseña</div>

                <div class="card-body">
                    <form id="form_password_update">

                        <div class="form-group row">
                            <label for="password_actual" class="col-md-4 col-form-label text-md-right">Actual</label>

                            <div class="col-md-6">
                                <input id="password_actual" type="password" class="form-control" name="password_actual" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Nueva</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirmar</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">

        $(function () {

            $('#form_password_update').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('password.change') }}",
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        'api_token': '{{ auth()->user()->api_token }}',
                        'password_actual': $('#password_actual').val(),
                        'password': $('#password').val(),
                        'password_confirmation': $('#password-confirm').val(),
                    },
                    headers: {
                        'accept': 'application/json',
                    }
                })
                .done(function() {

                    $("form_password_update").trigger('reset');

                    // Show message
                    showSnackbar('Contraseña actualizada correctamente.');
                })
                .fail(function(data) {

                    showSnackBarFromErrors(data);
                });
            });
        });
    </script>
@endsection