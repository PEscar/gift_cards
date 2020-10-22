@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Administrar Gift Cards Mayoristas
                    <div class="float-right">
                        <nueva-venta-item :ruta-crear="{{ json_encode(route('api.ventas.create')) }}" :validez_default="{{ env('VENCIMIENTO_GIFT_CARDS') }}" :productos="{{ json_encode($productos) }}"></nueva-venta-item>
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
                                <th>Fecha Venta</th>
                                <th>Fecha Pago</th>
                                <th>Fecha Venc.</th>
                                <th>Fecha Asig.</th>
                                <th>Fecha Cons.</th>
                                <th>Sede / Mesa</th>
                                <th>Usuario</th>
                                <th>N° Factura</th>
                                <th>Comentario</th>
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

                ajax: "{{ route('api.giftcards.mayoristas.index') }}?api_token={{ auth()->user()->api_token }}",

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

                    {data: 'nro_factura', name: 'nro_factura'},

                    {data: 'comentario', name: 'comentario'},
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
        });

    </script>
@endsection