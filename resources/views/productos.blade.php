@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Productos
                    <div class="float-right"><nuevo-producto-item :ruta-crear="{{ json_encode(route('api.productos.create')) }}"></nuevo-producto-item></div>
                </div>

                <div class="card-body">
                    <table id="productos_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripcion</th>
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
<div id="delete_producto_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                Eliminar Producto
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                ¿ Estás seguro de boorar este producto ?

                <form id="form_confirm_delete_producto">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <input type="hidden" id="delete_producto_id">
                            <input type="submit" id="confirm_delete_producto_btn" class="btn btn-danger" value="Si">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- set up the modal to start hidden and fade in and out -->
<div id="update_producto_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                Editar Producto
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="form_update_producto">
                    <input type="hidden" id="update_producto_id">

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="nombre" class="col-form-label">{{ __('Name') }}</label>
                        </div>

                        <div class="col-md-9">
                            <input id="nombre" type="text" class="form-control @error('name') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autocomplete="name" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="sku" class="col-form-label">Código</label>
                        </div>

                        <div class="col-md-9">
                            <input id="sku" type="text" class="form-control @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku') }}" required autocomplete="sku" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-3 text-md-right">
                            <label for="descripcion" class="col-form-label text-md-right">Descripcion</label>
                        </div>
                        <div class="col-md-9">
                            <input id="descripcion" type="text" class="form-control @error('email') is-invalid @enderror" name="descripcion" value="{{ old('descripcion') }}" autocomplete="descripcion">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col text-center">
                            <button id="confirm_update_user_btn" type="submit" class="btn btn-primary">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="create_user_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                Nuevo Usuario
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="form_create_user" action="{{ route('api.users.create') }}">
                    <div class="form-group row">

                        <div class="col-md-3 text-md-right">
                            <label for="name" class="col-form-label">{{ __('Name') }}</label>
                        </div>

                        <div class="col-md-9">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-md-3 text-md-right">
                            <label for="email" class="col-form-label">{{ __('E-Mail Address') }}</label>
                        </div>

                        <div class="col-md-9">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-md-3 text-md-right">
                            <label for="nivel" class="col-form-label">Permiso</label>
                        </div>

                        <div class="col-md-6">
                            <select class="form-control" name="nivel" id="nivel">
                                <option value="Admin">Admin</option>
                                <option value="Nivel1">Nivel 1</option>
                                <option value="Nivel2">Nivel 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col text-center">
                            <button id="confirm_create_user_btn" type="submit" class="btn btn-success">
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
<div id="view_producto_modal" class="modal fade">
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
                        <label for="sku" class="col-form-label">Código</label>
                    </div>

                    <div class="col-md-9">
                        <p id="view_sku"></p>
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-md-3 text-md-right">
                        <label for="descripcion" class="col-form-label">Descripción</label>
                    </div>

                    <div class="col-md-9">
                        <p id="view_descripcion"></p>
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
            var table = $('#productos_table').DataTable({

                processing: true,

                serverSide: true,

                ajax: "{{ route('api.productos.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    {data: 'id', name: 'id'},

                    {data: 'sku', name: 'sku'},

                    {data: 'nombre', name: 'nombre'},

                    {data: 'descripcion', name: 'descripcion'},

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

            $('#delete_producto_modal').on('show.bs.modal', function (e)
            {
                // Populate url & id
               $("#form_confirm_delete_producto").attr('action', $(e.relatedTarget).attr('data-url'));
               $("#delete_producto_id").val($(e.relatedTarget).attr('data-id'));
            });

            $('#delete_producto_modal').on('click', '#confirm_delete_producto_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_confirm_delete_producto').attr('action'),
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
                    $('#delete_producto_modal').modal('hide');

                    table.draw();

                    // Show message
                    showSnackbar('Producto #' + $('#delete_producto_id').val() + ' borrado.');
                })
                .fail(function(data) {

                    showSnackBarFromErrors(data);
                    $('#delete_producto_modal').modal('hide');
                });
            });

            $('#update_producto_modal').on('show.bs.modal', function (e) {

                // Populate url & id
                $("#form_update_producto").attr('action', $(e.relatedTarget).attr('data-url'));
                $("#update_producto_id").val($(e.relatedTarget).attr('data-id'));

                // Fill modal
                $("#form_update_producto #nombre").val($(e.relatedTarget).attr('data-nombre'));
                $("#form_update_producto #descripcion").val($(e.relatedTarget).attr('data-descripcion'));
                $("#form_update_producto #sku").val($(e.relatedTarget).attr('data-sku'));
            });

            $('#update_producto_modal').on('click', '#confirm_update_user_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_update_producto').attr('action'),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        nombre: $('#update_producto_modal #nombre').val(),
                        descripcion: $('#update_producto_modal #descripcion').val(),
                        sku: $('#update_producto_modal #sku').val(),
                    },
                })
                .done(function() {
                    table.draw();
                    $('#update_producto_modal').modal('hide');

                    // Update name in nav bar
                    $("#navbarDropdown").html($('#update_producto_modal #name').val());

                    // Show message
                    showSnackbar('Producto #' + $('#update_producto_id').val() + ' actualizado.');

                    $('#form_update_producto').trigger("reset");
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            $('#create_user_modal').on('click', '#confirm_create_user_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_create_user').attr('action'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        name: $('#create_user_modal #name').val(),
                        email: $('#create_user_modal #email').val(),
                        nivel: $('#create_user_modal #nivel').val(),
                        sedes: $('#create_user_modal #sedes').val(),
                    },
                })
                .done(function() {
                    table.draw();
                    $('#create_user_modal').modal('hide');

                    // Show message
                    showSnackbar('Usuario creado.');

                    $('#form_create_user').trigger("reset");
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            $('#view_producto_modal').on('show.bs.modal', function (e) {

                // Fill modal
                $("#view_nombre").html($(e.relatedTarget).attr('data-nombre'));
                $("#view_descripcion").html($(e.relatedTarget).attr('data-descripcion'));
                $("#view_sku").html($(e.relatedTarget).attr('data-sku'));
            });
        });

    </script>
@endsection