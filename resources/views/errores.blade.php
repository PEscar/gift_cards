@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Errores
                </div>

                <div class="card-body">
                    <table id="errores_table" class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>Venta ID</th>
                                <th>Error</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Fecha Envío</th>
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
            var table = $('#errores_table').DataTable({

                processing: true,

                serverSide: true,

                ajax: "{{ route('api.errores.index') }}?api_token={{ auth()->user()->api_token }}",

                columns: [

                    {data: 'id', name: 'id'},

                    {data: 'error', name: 'error'},

                    {data: 'fecha', name: 'fecha'},

                    {data: 'estado', name: 'estado'},

                    {data: 'fecha_envio', name: 'fecha_envio'}

                ],

                language: {

                    url: "{{ asset('js/datatables.spanish.json') }}"
                },

                responsive: true
            });
        });

    </script>
@endsection