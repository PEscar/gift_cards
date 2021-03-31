
<template>
    <div>
        <div class="row">
            <div class="col-xl-2">
                <label>Estados</label>
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

            <div class="col-xl-3">
                <label>Producto</label><br>
                <select v-model="selectedProductos" ref="productos" multiple>
                    <option v-for="producto in productos" :value="producto.id">{{ producto.nombre }}</option>
                </select>
            </div>

            <div class="col-xl-3">
                <button @click="refresh" class="btn btn-primary">Buscar</button>

                <button @click="toExcel" class="btn btn-success">Excel</button>

                <button @click="toPDF" class="btn btn-danger">PDF</button>
            </div>
        </div>

        <div class="row">

            <div class="col-xl-4">
                <label>Asignación</label><br>
                <date-range-picker
                    v-model="asignacion"
                    >
                </date-range-picker>
            </div>

            <div class="col-xl-4">
                <label>Vencimiento</label><br>
                <date-range-picker
                    v-model="vencimiento"
                    >
                </date-range-picker>
            </div>

            <div class="col-xl-4">
                <label>Cancelación</label><br>
                <date-range-picker
                    v-model="cancelacion"
                    >
                </date-range-picker>
            </div>
        </div>

        <div class="row">

            <div class="col-xl-12">
                <v-server-table @loaded="onLoaded" ref="table" :options=options :url=url :columns="['codigo', 'estado', 'precio', 'consumio', 'asigno', 'concepto', 'fecha_consumicion', 'fecha_vencimiento', 'fecha_asignacion', 'fecha_cancelacion', 'cantidad', 'descripcion', 'nro_mesa', 'sede', 'cancelo', 'motivo_cancelacion']">

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
    props: ['urlBase', 'productos'],
    data() {
        return {
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
            estados: '',
            conceptos: [],
            sedes: [],
            selectedProductos: [],
            options: {
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

        toExcel: function()
        {
            console.log('exportando a excel, pendiente')
        },

        toPDF: function()
        {
            console.log('exportando a PDF, pendiente')
        }
    },

    computed:
    {
        url: function()
        {
            return this.urlBase + '&sort=fecha_vencimiento&direction=desc&estados=' + this.estados + '&conceptos=' + this.conceptos + '&sedes=' + this.sedes + '&asig_start=' + this.formatDate(this.asignacion.startDate) + '&asig_end=' + this.formatDate(this.asignacion.endDate) + '&venci_start=' + this.formatDate(this.vencimiento.startDate) + '&venci_end=' + this.formatDate(this.vencimiento.endDate)  + '&cance_start=' + this.formatDate(this.cancelacion.startDate) + '&cance_end=' + this.formatDate(this.cancelacion.endDate) + '&productos=' + this.selectedProductos
        }
    }
  }
</script>
