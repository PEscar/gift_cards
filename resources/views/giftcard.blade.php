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
                                <input type="text" name="codigo" class="form-control form-control-lg" value="{{ $codigo }}" placeholder="Gift Card  Code">
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
                <div class="card-header">Gift Card: {{ $gc->codigo_gift_card }} <strong>{{ $gc->descripcion }}</strong> cant.: {{ $gc->cantidad }}</div>

                <div class="card-body">

                    @if ( $gc->valida )
                    <div class="alert alert-success" role="alert">
                        ESTADO: <strong>VÁLIDA</strong>!<br>
                        CÓDIGO: <strong>{{ $gc->codigo_gift_card }}</strong>.<br>
                        CANTIDAD: <strong>#{{ $gc->cantidad }}</strong>

                        <form method="POST" action="{{ route('giftcards.entregar', ['codigo' => $gc->codigo_gift_card]) }}">
                            @csrf
                            <button style="margin-top: -2rem;" id="btn_entregar" class="btn btn-success float-right">Consumir</button>
                        </form>
                    </div>
                    @else
                    <div class="alert alert-danger" role="alert">

                        @if ($gc->tipo_producto != 1)
                        TIPO: <strong>producto normal</strong>.<br>
                        @endif

                        @if ($gc->fecha_canje != null)
                        ESTADO: <strong>CANJEADA</strong> el {{strtoupper(date('d/M/Y', strtotime($gc->fecha_canje)))}}<br>
                        ENTREGÓ: <strong>{{ $gc->entregadoPor->name }}</strong>
                        @endif

                        @if ($gc->fecha_canje == null && $gc->fecha_vencimiento < date('Y-m-d'))
                        ESTADO: <strong>VENCIDA</strong>. el {{strtoupper(date('d/M/Y', strtotime($gc->fecha_vencimiento)))}}<br>
                        @endif

                    </div>
                    @endif

                    @if ( false )
                        <div class="alert alert-success" role="alert">
                            Es Admin !
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