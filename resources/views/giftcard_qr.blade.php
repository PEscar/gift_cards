@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Escanea el QR de prueba</div>

                    <br>

                    {{ route('giftcards.show', ['codigo' => $gc->codigo_gift_card]) }}

                    <!-- {!! QrCode::size(200)->generate(route('giftcards.show', ['codigo' => $gc->codigo_gift_card])) !!} -->
                    {!! QrCode::size(200)->generate('http://192.168.0.18/giftcards/' . $gc->codigo_gift_card); !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
