@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Ventas Mayoristas
                    <div class="float-right">
                        <nueva-venta-item :ruta-crear="{{ json_encode(route('api.ventas.create')) }}" :validez_default="{{ env('VENCIMIENTO_GIFT_CARDS') }}" :productos="{{ json_encode($productos) }}" :empresas="{{ json_encode($empresas) }}"></nueva-venta-item>
                    </div>
                </div>

                <div class="card-body">
                    <table id="ventas_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>codigos</th>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Concepto</th>
                                <th>Venta</th>
                                <th>Empresa</th>
                                <th>Pago</th>
                                <th>Vencimiento</th>
                                <th>Factura</th>
                                <th>Comentario</th>
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

    <!-- set up the modal to start hidden and fade in and out -->
    <div id="update_venta_modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    Editar Venta
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- dialog body -->
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" id="form_update_venta">
                        <input type="hidden" id="update_venta_id">

                        <div class="form-group row">
                            <div class="col-2 text-md-right">
                                <label for="nro_factura" class="col-form-label">N° Factura</label>
                            </div>

                            <div class="col-9">
                                <input type="text" class="form-control" id="nro_factura" placeholder="0001-0000001">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-2 text-md-right">
                                <label for="comentario" class="col-form-label">Comentario</label>
                            </div>

                            <div class="col-9">
                                <textarea id="comentario" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-2 text-md-right">
                                <label for="pagada" class="col-form-label">Pagada</label>
                            </div>

                            <div class="col-1">
                                <input id="pagada" type="checkbox" value="">
                            </div>

                            <div class="col-3 text-md-right" id="fecha_pago_container">
                                <label for="fecha_pago" class="col-form-label">Fecha Pago</label>
                            </div>

                            <div class="col-5">
                                <input type="date" class="form-control" id="fecha_pago">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col text-center">
                                <button id="confirm_update_venta_btn" type="submit" class="btn btn-primary">
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

            // Load table through ajax
            var table = $('#ventas_table').DataTable({

                columnDefs: [{ //createdCell wasnt working on its own so i had to define it as a default
                    targets: [4, 6, 7],
                    createdCell: function (td, cellData, rowData, row, col) {

                        let data = cellData ? moment(cellData).format('DD/MM/YYYY') : '';
                        $(td).html(data);
                    }
                }, { targets: [0], visible: false}],

                dom: 'Bfrtip',

                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        pageSize: 'LEGAL',
                        title: "{{ date('Y-m-d') }}" + ' - Gift Cards',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: "{{ date('Y-m-d') }}" + ' - Gift Cards',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],

                processing: true,

                serverSide: true,

                ajax: "{{ route('api.ventas.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    {data: 'codigos', name: 'codigos'},

                    {data: 'id', name: 'id'},

                    {data: 'producto', name: 'producto'},

                    {data: 'concepto', name: 'concepto'},

                    {data: 'fecha_venta', name: 'fecha_venta'},

                    {data: 'empresa', name: 'empresa'},

                    {data: 'fecha_pago', name: 'fecha_pago'},

                    {data: 'fecha_vencimiento', name: 'fecha_vencimiento'},

                    {data: 'nro_factura', name: 'nro_factura'},

                    {data: 'comentario', name: 'comentario', searchable: false},

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

            $('#update_venta_modal').on('show.bs.modal', function (e) {

                // Populate url & id
                $("#form_update_venta").attr('action', $(e.relatedTarget).attr('data-url'));
                $("#update_venta_id").val($(e.relatedTarget).attr('data-id'));

                // Fill modal
                $("#form_update_venta #nro_factura").val($(e.relatedTarget).attr('data-nro_factura'));
                $("#form_update_venta #comentario").val($(e.relatedTarget).attr('data-comentario'));
                $("#form_update_venta #pagada").prop('checked', $(e.relatedTarget).attr('data-pagada') == 1);
                $("#form_update_venta #fecha_pago").val($(e.relatedTarget).attr('data-fecha_pago'));

                showOrHideFechaPago();
            });

            $('#update_venta_modal').on('click', '#confirm_update_venta_btn', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#form_update_venta').attr('action'),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        api_token: '{{ auth()->user()->api_token }}',
                        nro_factura: $('#update_venta_modal #nro_factura').val(),
                        comentario: $('#update_venta_modal #comentario').val(),
                        pagada: $('#update_venta_modal #pagada').prop('checked'),
                        fecha_pago: $('#update_venta_modal #fecha_pago').val(),
                    },
                })
                .done(function() {
                    table.ajax.reload();
                    $('#update_venta_modal').modal('hide');

                    // Show message
                    showSnackbar('Venta #' + $('#update_venta_id').val() + ' actualizada.');

                    $('#form_update_venta').trigger("reset");
                })
                .fail(function(data) {
                    showSnackBarFromErrors(data);
                });
            });

            function showOrHideFechaPago()
            {
                if ( !$("#update_venta_modal #pagada").prop('checked') )
                {
                    $("#update_venta_modal #fecha_pago_container").hide();
                    $("#update_venta_modal #fecha_pago").hide();
                    $("#update_venta_modal #fecha_pago").val(null);
                }
                else
                {
                    $("#update_venta_modal #fecha_pago_container").show();
                    $("#update_venta_modal #fecha_pago").show();
                }
            }

            $("#update_venta_modal #pagada").click(function(event) {

                showOrHideFechaPago();

            });

        });

    </script>
@endsection