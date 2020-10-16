@extends('layouts.app')

@section('content')
<div class="container">
    <gc-validation-item :ruta-asignar="{{ json_encode(route('api.giftcards.asignar')) }}" :ruta-validar="{{ json_encode(route('api.giftcards.validar')) }}" :sedes="{{ json_encode($sedes) }}" :codigo="{{ json_encode($codigo) }}" :estados="{{  json_encode($estados) }}"></gc-validation-item>
</div>
@endsection