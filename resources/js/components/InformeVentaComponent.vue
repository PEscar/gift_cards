<style type="text/css">
/* Absolute Center Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: visible;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.3);
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 1500ms infinite linear;
  -moz-animation: spinner 1500ms infinite linear;
  -ms-animation: spinner 1500ms infinite linear;
  -o-animation: spinner 1500ms infinite linear;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

</style>


<template>
    <div>
    	<div v-if="loading" class="loading">Loading&#8230;</div>
        <div class="row">
            <div class="col-xl-2">
                <label>Estados</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="">&nbsp;Todos</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="1">&nbsp;Válida</label><br>
                <!-- <label class="font-weight-normal"><input v-model="estados" type="radio" value="2">&nbsp;Consumida</label><br> -->
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="3">&nbsp;Asignada</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="4">&nbsp;Vencida</label><br>
                <label class="font-weight-normal"><input v-model="estados" type="radio" value="5">&nbsp;Cancelada</label><br>
            </div>

            <div class="col-xl-2">
                <label><input type="checkbox" checked @click="selectAll($event, 'conceptos')">&nbsp;Conceptos</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="0">&nbsp;Tienda</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="1">&nbsp;Canje</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="2">&nbsp;Invitación</label><br>
                <label class="font-weight-normal"><input v-model="conceptos" type="checkbox" value="3">&nbsp;Mayorista</label><br>
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
				<label>Orden</label><br>
				<label class="font-weight-normal"><input v-model="direction" type="radio" value="asc">&nbsp;Ascendente</label><br>
				<label class="font-weight-normal"><input v-model="direction" type="radio" value="desc">&nbsp;Descendente</label><br>

                <hr>

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
                <v-server-table @loading="onLoading" @loaded="onLoaded" ref="table" :options=options :url=url :columns="['codigo', 'estado', 'precio', 'consumio', 'asigno', 'concepto', 'fecha_venta', 'fecha_vencimiento', 'fecha_asignacion', 'fecha_cancelacion', 'cantidad', 'descripcion', 'nro_mesa', 'sede', 'cancelo', 'motivo_cancelacion']">

                    <template
                      slot="h__fecha_venta"
                    >
                      <div
                        key="fecha_venta"
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
        	loading: false,
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
				sortable: [],
				perPageValues: [10,25,50,100, 200, 500],
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

        onLoaded: function(e)
        {
            this.total = e.data.total
            this.loading = false
        },

        onLoading: function(e)
        {
        	this.loading = true
        }
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
