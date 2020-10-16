@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Administrar Gift Cards
                    <div class="float-right"><button data-toggle="modal" data-target="#create_venta_modal" class="btn btn-success">Nueva Venta</button></div>
                    <!-- data-toggle="modal" data-target="#create_venta_modal" -->
                </div>

                <div class="card-body">
                    <table id="gcs_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <!-- <th>ID VENTA</th> -->
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                                <th>Fecha Venta</th>
                                <th>Fecha Pago</th>
                                <th>Fecha Venc.</th>
                                <th>Fecha Asig.</th>
                                <th>Fecha Cons.</th>
                                <th>Sede / Mesa</th>
                                <th>Usuario</th>
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

<div id="create_venta_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                Nueva Venta
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- dialog body -->
            <div class="modal-body">
                <form class="form-horizontal" method="POST" id="form_create_venta">
                    <div class="form-group row">
                        <label for="client_email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                        <div class="col-md-6">
                            <input id="client_email" type="email" class="form-control" name="client_email" value="" required autocomplete="name" autofocus>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="sku" class="col-md-4 col-form-label text-md-right">Gift Card</label>

                        <div class="col-md-5">
                            <select class="form-control" name="sku" id="sku">
                                <option value="11247">11247</option>
                                <option value="11248">11248</option>
                                <option value="11249">11249</option>
                                <option value="11250">11250</option>
                                <option value="11251">11251</option>
                            </select>
                        </div>
                    </div>

                     <div class="form-group row">
                        <label for="cantidad" class="col-md-4 col-form-label text-md-right">Cantidad</label>

                        <div class="col-md-5">
                            <input type="number" min="1" value="1" name="cantidad" class="form-control" id="cantidad">
                        </div>
                    </div>

                   <div class="form-group row">
                        <label for="pagada" class="col-md-4 col-form-label text-md-right">Pagada</label>

                        <div class="col-md-6">
                            <input id="pagada" type="checkbox" name="pagada" value="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="comentario" class="col-md-4 col-form-label text-md-right">Comentario</label>

                        <div class="col-md-6">
                            <textarea class="form-control" rows="2" id="comentario"></textarea>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button id="confirm_create_venta_btn" type="submit" class="btn btn-success">
                                Crear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">

        $(function () {

            // Load table through ajax
            var table = $('#gcs_table').DataTable({

                dom: 'Bfrtip',

                buttons: [
                    'copy',
                    {
                        extend: 'excel',
                        pageSize: 'LEGAL',
                        title: "{{ date('Y-m-d') }}" + ' - Gift Cards',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: "{{ date('Y-m-d') }}" + ' - Gift Cards',
                    }
                ],

                processing: true,

                serverSide: true,

                ajax: "{{ route('api.giftcards.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    // {data: 'id', name: 'id'},

                    {data: 'codigo', name: 'codigo'},

                    {data: 'producto', name: 'producto'},

                    {data: 'concepto', name: 'concepto'},

                    {data: 'estado', name: 'estado'},

                    {data: 'fecha_venta', name: 'fecha_venta'},

                    {data: 'fecha_pago', name: 'fecha_pago'},

                    {data: 'fecha_vencimiento', name: 'fecha_vencimiento'},

                    {data: 'fecha_asignacion', name: 'fecha_asignacion'},

                    {data: 'fecha_consumicion', name: 'fecha_consumicion'},

                    {data: 'sede_mesa', name: 'sede_mesa'},

                    {data: 'usuario_asignacion', name: 'usuario_asignacion'},
                ],

                language: {

                    url: "{{ asset('js/datatables.spanish.json') }}",

                    buttons: {
                        copyTitle: 'Copiado al portapapeles!',
                        copySuccess: {
                            _: '%d líneas copiadas',
                            1: '1 linea copiada'
                        }
                    }
                },

                responsive: true,

                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.id);
                  },

            });

            $('#create_venta_modal').on('click', '#confirm_create_venta_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('api.ventas.create') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        client_email: $('#create_venta_modal #client_email').val(),
                        pagada: $('#create_venta_modal #pagada').prop('checked') ? 1 : 0,
                        sku: $('#create_venta_modal #sku').val(),
                        cantidad: $('#create_venta_modal #cantidad').val(),
                        comentario: $('#create_venta_modal #comentario').val(),
                    },
                })
                .done(function() {
                    table.draw();
                    $('#create_venta_modal').modal('hide');

                    // Show message
                    showSnackbar('Venta guardada.');

                    $('#form_create_venta').trigger('reset');
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            $('#view_user_modal').on('show.bs.modal', function (e) {

                // Fill modal
                $("#view_name").html($(e.relatedTarget).attr('data-name'));
                $("#view_email").html($(e.relatedTarget).attr('data-email'));
                $("#view_admin").html($(e.relatedTarget).attr('data-admin') ? 'Si' : 'No');

                var sedes = '';
                var sedes_array = $(e.relatedTarget).attr('data-sedes').split(',');
                for ( let key in sedes_array )
                {
                    sedes += sedes_array[key] + '<br>';
                }
                $("#view_sedes").html(sedes);
            });
        });

    </script>
@endsection