@extends('layouts.giftcard')

@section('content')
<div>
  <div class="caption caption-producto">
      <p>{{ $item->descripcion }}</p>
  </div>
  <div class="caption caption-vencimiento">
      <p>{{ strtoupper(date('d/m/Y', strtotime( $item->fecha_vencimiento ))) }}</p>
  </div>
  <div class="caption caption-codigo">
      <p>{{ $item->codigo_gift_card }}</p>
  </div>
  <div class="caption caption-qr">
    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(200)->generate(route('giftcards.show', ['codigo' => $item->codigo_gift_card]))) !!} ">
  </div>
</div>
@endsection