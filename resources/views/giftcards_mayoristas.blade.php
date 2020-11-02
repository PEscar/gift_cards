@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Administrar Gift Cards Mayoristas
                    <div class="float-right">
                        <nueva-venta-item :ruta-crear="{{ json_encode(route('api.ventas.create')) }}" :validez_default="{{ env('VENCIMIENTO_GIFT_CARDS') }}" :productos="{{ json_encode($productos) }}" :empresas="{{ json_encode($empresas) }}"></nueva-venta-item>
                    </div>
                </div>

                <div class="card-body">
                    <table id="gcs_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                                <th>Venta</th>
                                <th>Empresa</th>
                                <th>Pago</th>
                                <th>Vencimiento</th>
                                <th>Asignación</th>
                                <th>Consumo</th>
                                <th>Sede / Mesa</th>
                                <th>Usuario</th>
                                <th>N° Factura</th>
                                <th>Comentario</th>
                                <th>Cancelación</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="cancel_giftcard_modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    Cancelar Gift Card
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- dialog body -->
                <div class="modal-body">
                    ¿ Estás seguro de cancelar esta giftcard ?

                    <form id="form_confirm_cancel_giftcard">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group row">
                            <div class="col text-center">
                                <input type="text" class="form-control is-invalid" name="motivo" id="motivo" placeholder="Motivo" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col text-center">
                                <input type="hidden" id="cancel_giftcard_id">
                                <input type="submit" id="confirm_cancel_giftcard_btn" class="btn btn-danger" value="Si">
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

            $('#confirm_cancel_giftcard_btn').addClass('disabled');

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

                serverSide: false,

                ajax: "{{ route('api.giftcards.mayoristas.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    // {data: 'id', name: 'id'},

                    {data: 'codigo', name: 'codigo'},

                    {data: 'producto', name: 'producto'},

                    {data: 'concepto', name: 'concepto'},

                    {data: 'estado', name: 'estado'},

                    {data: 'fecha_venta', name: 'fecha_venta'},

                    {data: 'empresa', name: 'empresa'},

                    {data: 'fecha_pago', name: 'fecha_pago'},

                    {data: 'fecha_vencimiento', name: 'fecha_vencimiento'},

                    {data: 'fecha_asignacion', name: 'fecha_asignacion'},

                    {data: 'fecha_consumicion', name: 'fecha_consumicion'},

                    {data: 'sede_mesa', name: 'sede_mesa'},

                    {data: 'usuario_asignacion', name: 'usuario_asignacion'},

                    {data: 'nro_factura', name: 'nro_factura'},

                    {data: 'comentario', name: 'comentario'},

                    {data: 'action', name: 'action'},
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

                search: {

                     regex: false,
                     smart: false
                },

                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.id);
                  },
            });

            $('#cancel_giftcard_modal').on('show.bs.modal', function (e)
            {
                // Populate url & id
               $("#form_confirm_cancel_giftcard").attr('action', $(e.relatedTarget).attr('data-url'));
               $("#cancel_giftcard_id").val($(e.relatedTarget).attr('data-id'));
            });

            $('#motivo').keyup(function(event) {

                var val = $('#motivo').val();

                if ( val )
                {
                    $('#motivo').addClass('is-valid');
                    $('#motivo').removeClass('is-invalid');
                    $('#confirm_cancel_giftcard_btn').removeClass('disabled');
                } else {
                    $('#motivo').removeClass('is-valid');
                    $('#motivo').addClass('is-invalid');
                    $('#confirm_cancel_giftcard_btn').addClass('disabled');
                }
            });

            $('#cancel_giftcard_modal').on('click', '#confirm_cancel_giftcard_btn', function(e) {

                e.preventDefault();

                if ( $('#motivo').val() )
                {
                    $.ajax({
                        url: $('#form_confirm_cancel_giftcard').attr('action'),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'api_token': '{{ auth()->user()->api_token }}',
                            'motivo': $('#motivo').val(),
                        },
                        headers: {
                            'accept': 'application/json',
                        }
                    })
                    .done(function() {

                        // Hide modal
                        $('#cancel_giftcard_modal').modal('hide');

                        table.draw();

                        // Show message
                        showSnackbar('Giftcard cancelada.');

                        $('#motivo').addClass('is-invalid');
                        $('#motivo').removeClass('is-valid');
                        $('#motivo').val(null);
                        $('#confirm_cancel_giftcard_btn').addClass('disabled');
                    })
                    .fail(function(data) {

                        showSnackBarFromErrors(data);
                        $('#cancel_giftcard_modal').modal('hide');
                    });
                }
            });

        });

    </script>
@endsection