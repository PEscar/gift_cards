
<template>
    <div>
        <div class="row">
            <div class="col-xl-2">
                <label>Estados</label><br>
                <select v-model="estados" ref="estados">
                    <option value="">Todos</option>
                    <option value="1">Válida</option>
                    <option value="2">Consumida</option>
                    <option value="3">Asignada</option>
                    <option value="4">Vencida</option>
                    <option value="5">Cancelada</option>
                </select>
            </div>

            <div class="col-xl-2">
                <label>Conceptos</label> <input type="checkbox" checked @click="selectAll($event, 'conceptos')"><br>
                <select v-model="conceptos" ref="concepto" multiple>
                    <option value="0">Tienda Nube</option>
                    <option value="1">Canje</option>
                    <option value="2">Invitación</option>
                    <option value="3">Venta</option>
                </select>
            </div>

            <div class="col-xl-2">
                <label>Sedes</label> <input type="checkbox" checked @click="selectAll($event, 'sedes')"><br>
                <select v-model="sedes" ref="sede" multiple>
                    <option value="0">Sin Sede</option>
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

            <div class="col-xl-3">
                <label>Productos</label> <input type="checkbox" checked @click="selectAll($event, 'selectedProductos')"><br>
                <select v-model="selectedProductos" ref="productos" multiple>
                    <option v-for="producto in productos" :value="producto.id">{{ producto.nombre }}</option>
                </select>
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
                <button v-if="asignacion.startDate" @click="clearFilter('asignacion')" type="button">&times;</button>
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

            console.log('this.direction: ' + this.direction)
            console.log('ruta pdf: ' + ruta)
            return ruta

            // return this.urlBase
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
