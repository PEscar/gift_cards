<style type="text/css">
	table
	{
		width: 100%; font-size: 12px;
	}

	table tbody tr td, table thead tr th{
		text-align: center!important;
		border: solid 1px;
	}
</style>

<table>
	<tr>
		<td>Estados: {!! is_null($estados) ? 'TODOS' : $estados_array[$estados]!!}</td>
		<td colspan="2">Conceptos: {!! htmlentities($conceptos) !!}</td>
	</tr>
	<tr>
		<td>Asignaci&oacute;n: {!! $asig_start ? strtoupper(date('d/M/Y', strtotime($asig_start))) : '' !!} - {!! $asig_end ? strtoupper(date('d/M/Y', strtotime($asig_end))) : '' !!}</td>
		<td>Vencimiento: {!! $venci_start ? strtoupper(date('d/M/Y', strtotime($venci_start))) : '' !!} - {!! $venci_end ? strtoupper(date('d/M/Y', strtotime($venci_end))) : '' !!}</td>
		<td>Cancelaci&oacute;n: {!! $cance_start ? strtoupper(date('d/M/Y', strtotime($cance_start))) : '' !!} - {!! $cance_end ? strtoupper(date('d/M/Y', strtotime($cance_end))) : '' !!}</td>
	</tr>

	<tr>
		<td colspan="3">Productos: {!! $productos !!}</td>
	</tr>

	<tr>
		<td colspan="3">Sedes: {!! $sedes !!}</td>
	</tr>
</table>

<table style="width: 100%;">
    <thead>
        <tr>
            <th>C&oacute;digo</th>
			<th>Estado</th>
			<th>Precio</th>
			<!-- <th>Consumi&oacute;</th> -->
			<th>Asign&oacute;</th>
			<th>Concepto</th>
			<th>Venta</th>
			<th>Vencimiento</th>
			<th>Asignaci&oacute;n</th>
			<th>Cancelaci&oacute;n</th>
			<th>Cant.</th>
			<th>Prod.</th>
			<th>N&deg; Mesa</th>
			<th>Sede</th>
			<th>Cancel&oacute;</th>
			<th>Motivo</th>
        </tr>
    </thead>
    <tbody>
    	@php
			$results->each(function (App\Models\VentaProducto $result) {

				$result = new App\Http\Resources\GiftCardResource($result);
				$array = $result->toArray('*');

    	@endphp
        <tr>
            <td>{!! $array['codigo'] !!}</td>
            <td>{!! htmlentities($array['estado_label']) !!}</td>
            <td>{!! $array['precio'] ? number_format($array['precio'], 2, ',', '.') : '' !!}</td>
			<!-- <td>{!! $array['consumio'] !!}</td> -->
			<td>{!! htmlentities($array['asigno']) !!}</td>
			<td>{!! htmlentities($array['concepto']) !!}</td>
			<td>{!! $array['fecha_venta'] !!}</td>
			<td>{!! $array['fecha_vencimiento'] !!}</td>
			<td>{!! $array['fecha_asignacion'] !!}</td>
			<td>{!! $array['fecha_cancelacion'] !!}</td>
			<td>{!! $array['cantidad'] !!}</td>
			<td>{!! htmlentities($array['descripcion']) !!}</td>
			<td>{!! $array['nro_mesa'] !!}</td>
			<td>{!! $array['sede'] !!}</td>
			<td>{!! $array['cancelo'] !!}</td>
			<td>{!! $array['motivo_cancelacion'] !!}</td>
        </tr>
        @php
			});
        @endphp
        <tr>
            <td><b>TOTAL</b></td>
            <td colspan="14"><strong>{!! number_format($total, 2, ',', '.') !!}</strong></td>
        </tr>
    </tbody>
</table>