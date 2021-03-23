
<template>
    <div>
        <v-server-table :options=options :url=url :columns="['codigo', 'estado', 'consumio', 'asigno', 'concepto', 'fecha_consumicion', 'fecha_vencimiento', 'fecha_asignacion', 'fecha_cancelacion', 'cantidad', 'descripcion', 'nro_mesa', 'sede', 'cancelo', 'motivo_cancelacion']">

            <div slot="estado" slot-scope="data">
                {{ data.row.estado == 5 ? 'Cancelada' : ( data.row.estado == 1 ? 'Valida' : ( data.row.estado == 2 ? 'Consumida' : ( data.row.estado == 4 ? 'Vencida' : 'Asignada' ) ) ) }}
            </div>

        </v-server-table>
    </div>
</template>

<script>
  export default {
    name: 'InformeVentaComponent',
    props: ['estados', 'codigo', 'sedes', 'rutaAsignar', 'rutaValidar', 'url'],
    data() {
        return {
            options: {
                perPageValues: [],
                filterByColumn: false,
                filterable: false,
                requestAdapter(data) {

                    console.log(data)

                    return {
                        // sort: data.orderBy ? data.orderBy : 'fecha_vencimiento',
                        // direction: data.ascending ? 'asc' : 'desc',
                        sort: 'fecha_vencimiento',
                        direction: 'asc',
                        limit: data.limit ? data.limit : 5,
                        page: data.page,
                        query: data.query,
                    }
                },
                sortIcon: {
                    base : 'fas',
                    is: 'fa-sort',
                    up: 'fa-sort-up',
                    down: 'fa-sort-down'
                }
            },
        }
    }
  }
</script>
