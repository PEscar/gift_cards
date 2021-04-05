@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">INFORMES</div>

                <div class="card-body">
                    <informe-ventas-component url-pdf="{{ route('informes.download.pdf') }}" v-bind:productos="<?= htmlentities(json_encode($productos)) ?>" url-base="{{ route('api.giftcards.index') }}?api_token={{ auth()->user()->api_token }}"></informe-ventas-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
