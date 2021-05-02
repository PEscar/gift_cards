<style type="text/css">
	table
	{
		width: 100%; font-size: 12px;
	}
</style>

<table>
	<tr>
		<td>Estados: {!! is_null($estados) ? 'TODOS' : $estados_array[$estados]!!}</td>
		<td colspan="2">Conceptos: {!! $conceptos !!}</td>
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

<table style="width: 100%">
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
    		$total = 0;
    	@endphp

    	@foreach($results as $result)

    	@php
			$array = $result->toArray('*');
			$total += $array['precio'] ?? 0;
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
        @endforeach
        <tr>
            <td><b>TOTAL</b></td>
            <td colspan="14">{!! number_format($total, 2, ',', '.') !!}</td>
        </tr>
    </tbody>
</table>