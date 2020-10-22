@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Empresas
                    <div class="float-right"><nueva-empresa-item :ruta-crear="{{ json_encode(route('api.empresas.create')) }}"></nueva-empresa-item></div>
                </div>

                <div class="card-body">
                    <table id="empresas_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- set up the modal to start hidden and fade in and out -->
<div id="delete_empresa_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                Eliminar Empresa
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                ¿ Estás seguro de boorar esta empresa ?

                <form id="form_confirm_delete_empresa">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <input type="hidden" id="delete_empresa_id">
                            <input type="submit" id="confirm_delete_empresa_btn" class="btn btn-danger" value="Si">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- set up the modal to start hidden and fade in and out -->
<div id="update_empresa_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                Editar Empresa
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="form_update_empresa">
                    <input type="hidden" id="update_empresa_id">

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="nombre" class="col-form-label">{{ __('Name') }}</label>
                        </div>

                        <div class="col-md-9">
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autocomplete="nombre" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="email" class="col-form-label">Email</label>
                        </div>

                        <div class="col-md-9">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col text-center">
                            <button id="confirm_update_empresa_btn" type="submit" class="btn btn-primary">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="create_empresa_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                Nueva Empresa
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="form_create_empresa" action="{{ route('api.empresas.create') }}">

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="nombre" class="col-form-label">{{ __('Name') }}</label>
                        </div>

                        <div class="col-md-9">
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autocomplete="nombre" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="email" class="col-form-label">Email</label>
                        </div>

                        <div class="col-md-9">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col text-center">
                            <button id="confirm_create_empresa_btn" type="submit" class="btn btn-success">
                                Crear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- set up the modal to start hidden and fade in and out -->
<div id="view_empresa_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- dialog body -->
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <div class="form-group row">

                    <div class="col-md-3 text-md-right">
                        <label for="nombre" class="col-form-label">{{ __('Name') }}</label>
                    </div>

                    <div class="col-md-9">
                        <p id="view_nombre"></p>
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-md-3 text-md-right">
                        <label for="email" class="col-form-label">Email</label>
                    </div>

                    <div class="col-md-9">
                        <p id="view_email"></p>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
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

            // Load table through ajax
            var table = $('#empresas_table').DataTable({

                processing: true,

                serverSide: true,

                ajax: "{{ route('api.empresas.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    {data: 'id', name: 'id'},

                    {data: 'nombre', name: 'nombre'},

                    {data: 'email', name: 'email'},

                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ],

                language: {

                    url: "{{ asset('js/datatables.spanish.json') }}"
                },

                responsive: true,

                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.id);
                  },

            });

            $('#delete_empresa_modal').on('show.bs.modal', function (e)
            {
                // Populate url & id
               $("#form_confirm_delete_empresa").attr('action', $(e.relatedTarget).attr('data-url'));
               $("#delete_empresa_id").val($(e.relatedTarget).attr('data-id'));
            });

            $('#delete_empresa_modal').on('click', '#confirm_delete_empresa_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_confirm_delete_empresa').attr('action'),
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        'api_token': '{{ auth()->user()->api_token }}'
                    },
                    headers: {
                        'accept': 'application/json',
                    }
                })
                .done(function() {

                    // Hide modal
                    $('#delete_empresa_modal').modal('hide');

                    table.draw();

                    // Show message
                    showSnackbar('Empresa #' + $('#delete_empresa_id').val() + ' borrado.');
                })
                .fail(function(data) {

                    showSnackBarFromErrors(data);
                    $('#delete_empresa_modal').modal('hide');
                });
            });

            $('#update_empresa_modal').on('show.bs.modal', function (e) {

                // Populate url & id
                $("#form_update_empresa").attr('action', $(e.relatedTarget).attr('data-url'));
                $("#update_empresa_id").val($(e.relatedTarget).attr('data-id'));

                // Fill modal
                $("#form_update_empresa #nombre").val($(e.relatedTarget).attr('data-nombre'));
                $("#form_update_empresa #email").val($(e.relatedTarget).attr('data-email'));
            });

            $('#update_empresa_modal').on('click', '#confirm_update_empresa_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_update_empresa').attr('action'),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        nombre: $('#update_empresa_modal #nombre').val(),
                        email: $('#update_empresa_modal #email').val(),
                    },
                })
                .done(function() {
                    table.draw();
                    $('#update_empresa_modal').modal('hide');

                    // Update name in nav bar
                    $("#navbarDropdown").html($('#update_empresa_modal #name').val());

                    // Show message
                    showSnackbar('Empresa #' + $('#update_empresa_id').val() + ' actualizado.');

                    $('#form_update_empresa').trigger("reset");
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            $('#create_empresa_modal').on('click', '#confirm_create_empresa_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_create_empresa').attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        name: $('#create_empresa_modal #name').val(),
                        email: $('#create_empresa_modal #email').val(),
                        nivel: $('#create_empresa_modal #nivel').val(),
                        sedes: $('#create_empresa_modal #sedes').val(),
                    },
                })
                .done(function() {
                    table.draw();
                    $('#create_empresa_modal').modal('hide');

                    // Show message
                    showSnackbar('Usuario creado.');

                    $('#form_create_empresa').trigger("reset");
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            $('#view_empresa_modal').on('show.bs.modal', function (e) {

                // Fill modal
                $("#view_nombre").html($(e.relatedTarget).attr('data-nombre'));
                $("#view_email").html($(e.relatedTarget).attr('data-email'));
            });
        });

    </script>
@endsection