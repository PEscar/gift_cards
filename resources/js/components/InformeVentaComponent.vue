
<template>
    <div>
        <div class="row">
            <div class="col-xl-2">
                <label>Estados</label>
                <select v-model="estados" ref="estados" multiple>
                    <option value="1">Válida</option>
                    <!-- <option value="2">Consumida</option> -->
                    <option value="3">Asignada</option>
                    <option value="4">Vencida</option>
                    <option value="5">Cancelada</option>
                </select>
            </div>

            <div class="col-xl-2">
                <label>Concepto</label>
                <select v-model="conceptos" ref="concepto" multiple>
                    <option value="0">Tienda Nube</option>
                    <option value="1">Canje</option>
                    <option value="2">Invitación</option>
                    <option value="3">Venta</option>
                </select>
            </div>

            <div class="col-xl-2">
                <label>Sede</label><br>
                <select v-model="sedes" ref="sede" multiple>
                    <option value="1">Madero 1</option>
                    <option value="2">Madero 2</option>
                    <option value="3">Madero 3</option>
                    <option value="4">Madero 5</option>
                    <option value="5">Libertador</option>
                    <option value="6">Dolce</option>
                    <option value="7">Riobamba</option>
                    <option value="8">Botánico</option>
                    <option value="9">Recoleta</option>
                    <option value="10">San Isidro</option>
                    <option value="11">Pilar</option>
                </select>
            </div>

            <div class="col-xl-2">
                <button @click="refresh" class="btn btn-primary">Buscar</button>
            </div>
        </div>

        <div class="row">

            <div class="col-xl-12">
                <v-server-table ref="table" :options=options :url=url :columns="['codigo', 'estado', 'precio', 'consumio', 'asigno', 'concepto', 'fecha_consumicion', 'fecha_vencimiento', 'fecha_asignacion', 'fecha_cancelacion', 'cantidad', 'descripcion', 'nro_mesa', 'sede', 'cancelo', 'motivo_cancelacion']">

                    <div slot="estado" slot-scope="data">
                        {{ data.row.estado == 5 ? 'Cancelada' : ( data.row.estado == 1 ? 'Valida' : ( data.row.estado == 2 ? 'Consumida' : ( data.row.estado == 4 ? 'Vencida' : 'Asignada' ) ) ) }}
                    </div>

                </v-server-table>
            </div>
        </div>
        
    </div>
</template>

<script>
  export default {
    name: 'InformeVentaComponent',
    props: ['urlBase'],
    data() {
        return {
            estados: [],
            conceptos: [],
            sedes: [],
            options: {
                perPageValues: [],
                filterByColumn: false,
                filterable: false,
                sortIcon: {
                    base : 'fas',
                    is: 'fa-sort',
                    up: 'fa-sort-up',
                    down: 'fa-sort-down'
                }
            },
        }
    },
    methods:
    {
        refresh: function()
        {
            this.$refs.table.refresh();
        }
    },

    computed:
    {
        url: function()
        {
            return this.urlBase + '&sort=fecha_vencimiento&direction=asc&estados=' + this.estados + '&conceptos=' + this.conceptos + '&sedes=' + this.sedes
        }
    }
  }
</script>
