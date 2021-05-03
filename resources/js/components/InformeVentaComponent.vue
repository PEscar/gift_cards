
<template>
    <div>
        <div class="row">
            <div class="col-xl-2">
                <label>Estados</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="">&nbsp;Todos</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="1">&nbsp;Válida</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="2">&nbsp;Consumida</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="3">&nbsp;Asignada</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="4">&nbsp;Vencida</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="5">&nbsp;Cancelada</label><br>
            </div>

            <div class="col-xl-2">
                <label><input type="checkbox" checked @click="selectAll($event, 'conceptos')">&nbsp;Conceptos</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="0">&nbsp;Tienda</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="1">&nbsp;Canje</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="2">&nbsp;Invitación</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="3">&nbsp;Venta</label><br>
            </div>

            <div class="col-xl-2">
                <label><input type="checkbox" checked @click="selectAll($event, 'sedes')">&nbsp;Sedes</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="0">&nbsp;Sin Sede</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="1">&nbsp;Madero 1</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="2">&nbsp;Madero 2</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="3">&nbsp;Madero 3</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="4">&nbsp;Madero 5</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="5">&nbsp;Libertador</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="6">&nbsp;Dolce</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="7">&nbsp;Riobamba</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="8">&nbsp;Botánico</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="9">&nbsp;Recoleta</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="10">&nbsp;San Isidro</label><br>
                <label class="font-weight-normal"><input v-model="sedes" type="checkbox" value="11">&nbsp;Pilar</label><br>
            </div>

            <div class="col-xl-3">
                <label><input type="checkbox" checked @click="selectAll($event, 'selectedProductos')">&nbsp;Productos</label><br>
                <label v-for="producto in productos" class="font-weight-normal"><input v-model="selectedProductos" type="checkbox" :value="producto.id">&nbsp;{{ producto.nombre }}</label><br>
            </div>

            <div class="col-xl-3">
                <button @click="refresh" class="btn btn-primary">Buscar</button>

                <a target="_blank" :href="urlExcel + '?' + params" class="btn btn-success">Excel</a>

                <a target="_blank" :href="urlPdf + '?' + params" class="btn btn-danger">PDF</a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <label>Fecha de Venta</label><br>
                <date-range-picker
                    opens="right"
                    v-model="venta"
                    :localeData="localeData"
                    v-bind:ranges="ranges"
                    v-bind:autoApply="true"
                    >
                </date-range-picker>
                <button v-if="venta.startDate" @click="clearFilter('venta')" type="button">&times;</button>
            </div>

            <div class="col-xl-6">
                <label>Fecha de Asignación</label><br>
                <date-range-picker
                    opens="left"
                    v-model="asignacion"
                    :localeData="localeData"
                    v-bind:ranges="ranges"
                    v-bind:autoApply="true"
                    >
                </date-range-picker>
                <button v-if="asignacion.startDate" @click="clearFilter('asignacion')" type="button">&times;</button>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <label>Fecha de Vencimiento</label><br>
                <date-range-picker
                    opens="right"
                    v-model="vencimiento"
                    :localeData="localeData"
                    v-bind:ranges="ranges"
                    v-bind:autoApply="true"
                    >
                </date-range-picker>
                <button v-if="vencimiento.startDate" @click="clearFilter('vencimiento')" type="button">&times;</button>
            </div>

            <div class="col-xl-6">
                <label>Fecha de Cancelación</label><br>
                <date-range-picker
                    opens="left"
                    v-model="cancelacion"
                    :localeData="localeData"
                    v-bind:ranges="ranges"
                    v-bind:autoApply="true"
                    >
                </date-range-picker>
                <button v-if="cancelacion.startDate" @click="clearFilter('cancelacion')" type="button">&times;</button>
            </div>
        </div>

        <div class="row">

            <div class="col-xl-12">
                <v-server-table @loaded="onLoaded" ref="table" :options=options :url=url :columns="['codigo', 'estado', 'precio', 'consumio', 'asigno', 'concepto', 'fecha_venta', 'fecha_consumicion', 'fecha_vencimiento', 'fecha_asignacion', 'fecha_cancelacion', 'cantidad', 'descripcion', 'nro_mesa', 'sede', 'cancelo', 'motivo_cancelacion']">

                    <template
                      slot="h__fecha_venta"
                    >
                      <div
                        key="fecha_venta"
                        @click="direction = direction == 'desc' ? 'asc' : 'desc'"
                      >Fecha Venta</div>
                    </template>

                    <div slot="estado" slot-scope="data">
                        {{ data.row.estado == 5 ? 'Cancelada' : ( data.row.estado == 1 ? 'Valida' : ( data.row.estado == 2 ? 'Consumida' : ( data.row.estado == 4 ? 'Vencida' : 'Asignada' ) ) ) }}
                    </div>

                    <tr slot="appendBody">
                        <td colspan="2"><b>Total</b></td>
                        <td><b>{{ new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total) }}</b></td>
                        <td colspan="14"></td>
                    </tr>

                    <div slot="precio" slot-scope="data">
                        {{ data.row.precio ? new Intl.NumberFormat('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.row.precio) : '-' }}
                    </div>

                </v-server-table>
            </div>
        </div>
        
    </div>
</template>

<script>
  export default {
    name: 'InformeVentaComponent',
    props: ['urlBase', 'productos', 'urlPdf', 'urlExcel'],
    data() {
        return {
            direction: 'desc',
            ranges: false,
            localeData: {
                direction: 'ltr',
                separator: ' - ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                weekLabel: 'W',
                daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sáb'],
                monthNames: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            },
            total: 0,
            cancelacion: {
                startDate: null,
                endDate: null
            },
            asignacion: {
                startDate: null,
                endDate: null
            },
            vencimiento: {
                startDate: null,
                endDate: null
            },
            venta: {
                startDate: null,
                endDate: null
            },
            estados: '',
            conceptos: [0, 1, 2 ,3],
            sedes: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            selectedProductos: [],
            options: {
            	pagination: false,
                sortable: ['fecha_venta'],
                filterByColumn: false,
                filterable: false,
                sortIcon: {
                    base : 'fas',
                    is: 'fa-sort',
                    up: 'fa-sort-up',
                    down: 'fa-sort-down'
                },
                texts: {
                  count:
                    "Mostrando {from} a {to} de {count} resultados|{count} resultados|Un resultado",
                  first: "Primera",
                  last: "Última",
                  filter: "Filtro:",
                  filterPlaceholder: "Buscar",
                  limit: "Resultados:",
                  page: "Página:",
                  noResults: "Sin resultados",
                  noRequest:"Selecciona por lo menos un filtro para buscar resultados",
                  filterBy: "Filtrar por {column}",
                  loading: "Cargando...",
                  defaultOption: "Seleccionar {column}",
                  columns: "Columnas"
                },
                // requestAdapter(data) {

                //     console.log('data: ', data)

                //      // + '&sort=fecha_vencimiento&direction=desc&estados=' + this.estados + '&conceptos=' + this.conceptos + '&sedes=' + this.sedes + '&asig_start=' + this.formatDate(this.asignacion.startDate) + '&asig_end=' + this.formatDate(this.asignacion.endDate) + '&venci_start=' + this.formatDate(this.vencimiento.startDate) + '&venci_end=' + this.formatDate(this.vencimiento.endDate)  + '&cance_start=' + this.formatDate(this.cancelacion.startDate) + '&cance_end=' + this.formatDate(this.cancelacion.endDate) + '&productos=' + this.selectedProductos + '&venta_start=' + this.formatDate(this.venta.startDate) + '&venta_end=' + this.formatDate(this.venta.endDate

                //     return {
                //       sort: data.orderBy ? data.orderBy : 'ventas.fecha_vencimiento',
                //       direction: data.ascending ? 'asc' : 'desc',
                //       limit: data.limit ? data.limit : 10,
                //       page: data.page ? data.page : 1,
                //       estados: this.estados,
                //       conceptos: this.conceptos,
                //       sedes: this.sedes,
                //       asig_start: this.asignacion ? this.formatDate(this.asignacion.startDate) : '',
                //       asig_end: this.asignacion ? this.formatDate(this.asignacion.endDate) : '',
                //       venci_start: this.vencimiento ? this.formatDate(this.vencimiento.startDate) : '',
                //       venci_end: this.vencimiento ? this.formatDate(this.vencimiento.endDate) : '',
                //       cance_start: this.cancelacion ? this.formatDate(this.cancelacion.startDate) : '',
                //       cance_end: this.cancelacion ? this.formatDate(this.cancelacion.endDate) : '',
                //       productos: this.selectedProductos,
                //       venta_start: this.venta ? this.formatDate(this.venta.startDate) : '',
                //       venta_end: this.venta ? this.formatDate(this.venta.endDate) : ''
                //     }
                // }
            },
        }
    },
    methods:
    {
        clearFilter: function(filter)
        {
            this[filter].startDate = null
            this[filter].endDate = null
        },

        selectAll: function(ev, filter)
        {
            if ( ev.target.checked )
            {
                if ( filter == 'conceptos' )
                {
                    this.conceptos = [0, 1, 2 ,3];
                }
                else if( filter == 'sedes' )
                {
                    this.sedes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                }
                else if( filter == 'selectedProductos')
                {
                    this.selectedProductos = this.productos.map(item => item.id)
                }
            }
            else
            {
                this[filter] = []
            }
        },

        refresh: function()
        {
            this.$refs.table.refresh();
        },

        formatDate(date)
        {
            if ( date )
            {
                let offset = date.getTimezoneOffset()
                let yourDate = new Date(date.getTime() - (offset*60*1000))
                return date.toISOString().split('T')[0]
                
            }

            return ''
        },

        onLoaded: function()
        {
            this.total = this.$refs.table.data.reduce(function(prev, cur) {
              return prev + cur.precio;
            }, 0)
        },
    },

    computed:
    {
        url: function()
        {
            let ruta = this.urlBase + '&' + this.params

            return ruta
        },

        params: function()
        {
            return 'sort=fecha_vencimiento&direction=' + this.direction + '&estados=' + this.estados + '&conceptos=' + this.conceptos + '&sedes=' + this.sedes + '&asig_start=' + this.formatDate(this.asignacion.startDate) + '&asig_end=' + this.formatDate(this.asignacion.endDate) + '&venci_start=' + this.formatDate(this.vencimiento.startDate) + '&venci_end=' + this.formatDate(this.vencimiento.endDate)  + '&cance_start=' + this.formatDate(this.cancelacion.startDate) + '&cance_end=' + this.formatDate(this.cancelacion.endDate) + '&productos=' + this.selectedProductos + '&venta_start=' + this.formatDate(this.venta.startDate) + '&venta_end=' + this.formatDate(this.venta.endDate)
        }
    },

    mounted: function()
    {
        this.selectedProductos = this.productos.map(item => item.id)
    }
  }
</script>
