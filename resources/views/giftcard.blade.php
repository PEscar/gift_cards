@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Validar Gift Card</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="form_validar_giftcard" action="{{ route('giftcards.show') }}/">
                        <div class="form-row">
                            <div class="col-9">
                                <input type="text" name="codigo" autocomplete="off" class="form-control form-control-lg" value="{{ $codigo }}" placeholder="Gift Card  Code">
                            </div>
                            <div class="col-3">
                                <input style="width: 100%" type="submit" value="Buscar" name="Buscar" class="btn btn-primary btn-lg">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>

    @if ($gc)
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Gift Card:
                    <strong>{{ $gc->codigo_gift_card }}</strong>
                </div>

                <div class="card-body">
                    @if ( $gc->estado == App\Models\VentaProducto::ESTADO_CONSUMIDA )
                    <div class="alert alert-danger" role="alert">
                        ESTADO: <strong>CONSUMIDA</strong>!<br>
                        CONSUMIÓ: <strong>{{ $gc->consumidaPor ? $gc->consumidaPor->name : null }}</strong><br>
                        FECHA CONSUMICION: <strong>{{strtoupper(date('d/M/Y H:i', strtotime($gc->fecha_consumicion)))}}</strong><br>
                    </div>
                    @elseif( $gc->estado == App\Models\VentaProducto::ESTADO_ASIGNADA )
                    <div class="alert alert-warning" role="alert">
                        ESTADO: <strong>ASIGNADA</strong>!<br>
                        ASIGNÓ: <strong>{{ $gc->asignadaPor->name }}</strong><br>
                        FECHA ASIGNACION: <strong>{{strtoupper(date('d/M/Y H:i', strtotime($gc->fecha_asignacion)))}}</strong><br>
                        NRO MESA: <strong>{{ $gc->nro_mesa }}</strong><br>
                        SEDE: <strong>{{ $gc->sede->nombre }}</strong>
                    </div>
                    @elseif ( $gc->estado == App\Models\VentaProducto::ESTADO_VENCIDA )
                    <div class="alert alert-danger" role="alert">
                        ESTADO: <strong>VENCIDA</strong>!<br>
                        FECHA VENCIMIENTO: <strong>{{strtoupper(date('d/M/Y', strtotime($gc->fecha_vencimiento)))}}</strong><br>
                    </div>
                    @else
                    <div class="alert alert-success" role="alert">
                        ESTADO: <strong>VÁLIDA</strong>!<br>
                        CANTIDAD: <strong>#{{ $gc->cantidad }}</strong><br>
                        PRODUCTO: <strong>{{ $gc->descripcion }}</strong><br><br>

                        <form method="POST" action="{{ route('giftcards.asignar', ['codigo' => $gc->codigo_gift_card]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control" type="text" name="nro_mesa" placeholder="N° Mesa" required="">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" name="sede" required>
                                        <option value="" selected disabled>Seleccione Sede</option>
                                        @foreach( auth()->user()->sedes as $sede )
                                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button style="" class="btn btn-success">Asignar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @elseif ( $codigo )
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Sin resultados</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $('#form_validar_giftcard').on('submit', function(e) {
            e.preventDefault();
            window.location.href = $('#form_validar_giftcard').attr('action') + $('input[name=codigo]').val();
        });
    </script>
@endsection