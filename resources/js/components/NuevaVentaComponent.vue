<template>

    <div>
        <button data-toggle="modal" data-target="#create_venta_modal" class="btn btn-success">Nueva Venta</button>
    
        <div id="create_venta_modal" class="modal fade text-xs-left text-sm-right text-md-right text-lg-right">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        Nueva Venta
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- dialog body -->
                    <div class="modal-body">
                        <form class="form-horizontal" id="form_nueva_venta" @submit.prevent="crearVentaMayorista()">
                            <div class="form-group row">

                                <div class="col-2">
                                    <label for="empresa" class="col-form-label" >Empresa</label>
                                </div>

                                <div class="col-9">
                                    <select class="form-control" v-bind:class="{ 'is-valid': empresa, 'is-invalid': !empresa }" v-model="empresa" id="empresa">
                                        <option selected value="null" disabled>Seleccionar Empresa</option>
                                        <option v-for="empresa in empresas" :value="empresa.id">{{ empresa.nombre }}</option>
                                    </select>
                                    <small v-if="empresa">{{ empresaEmail }}</small>
                                </div>

                                <div class="col-2">
                                    <label for="sku" class="col-form-label">Gift Card</label>
                                </div>

                                <div class="col-9">
                                    <select class="form-control" v-bind:class="{ 'is-valid': sku, 'is-invalid': !sku }" v-model="sku" id="sku">
                                        <option selected value="null" disabled>Seleccionar Producto</option>
                                        <option v-for="producto in productos" :value="producto.sku">{{ producto.nombre }}</option>
                                    </select>
                                </div>

                                <div class="col-2">
                                    <label for="concepto" class="col-form-label" >Concepto</label>
                                </div>

                                <div class="col-9">
                                    <select class="form-control" v-bind:class="{ 'is-valid': concepto, 'is-invalid': !concepto }" v-model="concepto" id="concepto">
                                        <option selected value="null" disabled>Seleccionar Producto</option>
                                        <option value="1">Canje</option>
                                        <option value="2">Invitación</option>
                                        <option value="3">Venta</option>
                                    </select>
                                </div>
                            </div>

                             <div class="form-group row">
                                <div class="col-2">
                                    <label for="cantidad" class="col-form-label">Cantidad</label>
                                </div>

                                <div class="col-3">
                                    <input type="number" min="1" v-bind:class="{ 'is-valid': validateCantidad, 'is-invalid': !validateCantidad }" v-model="cantidad" class="form-control" id="cantidad">
                                </div>

                                <div class="col-2 offset-1">
                                    <label for="Validez" class="col-form-label">Validez</label>
                                </div>

                                <div class="col-3">
                                    <input type="number" min="1" v-bind:class="{ 'is-valid': validateValidez, 'is-invalid': !validateValidez }" v-model="validez" class="form-control" id="validez">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-2">
                                    <label for="Precio" class="col-form-label">Precio</label>
                                </div>

                                <div class="col-4">
                                    <input type="number" min="0" v-bind:class="{ 'is-valid': precio, 'is-invalid': !precio }" v-model="precio" class="form-control" id="precio">
                                </div>
                            </div>

                            <div class="form-group row">

                                <div class="col-2">
                                    <label for="pagada" class="col-form-label">Pagada</label>
                                </div>

                                <div class="col-1">
                                    <input id="pagada" @change="resetFechaPago" type="checkbox" v-model="pagada" value="">
                                </div>

                                <div class="col-3" v-if="pagada">
                                    <label for="fecha_pago" class="col-form-label">Fecha Pago</label>
                                </div>

                                <div class="col-5" v-if="pagada">
                                    <input type="date" class="form-control" v-bind:class="{ 'is-valid': fecha_pago, 'is-invalid': !fecha_pago }" v-model="fecha_pago">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-2">
                                    <label for="nro_factura" class="col-form-label">N° Factura</label>
                                </div>

                                <div class="col-9">
                                    <input type="text" v-model="nro_factura" class="form-control" id="nro_factura" placeholder="0001-0000001">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-2">
                                    <label for="comentario" class="col-form-label">Comentario</label>
                                </div>

                                <div class="col-9">
                                    <textarea class="form-control" v-model="comentario" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-2">
                                    <label for="notificacion" class="col-form-label">Notificación</label>
                                </div>

                                <div class="col-9">
                                    <select class="form-control" v-bind:class="{ 'is-valid': notificacion, 'is-invalid': !notificacion }" v-model="notificacion" id="notificacion">
                                        <option selected value="null" disabled>Seleccionar Notificación</option>
                                        <option value="1">PDF's Adjuntos</option>
                                        <option value="2">ZIP Link</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col text-center">
                                    <input type="submit" name="Crear" value="Crear" class="btn btn-success" v-bind:class="{ disabled: !validateNuevaVentaForm }">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'NuevaVentaComponent',
        props: ['validez_default', 'rutaCrear', 'productos', 'empresas'],
        data: () => ({
            empresa: null,
            sku: null,
            cantidad: 1,
            pagada: false,
            comentario: null,
            reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
            validez: null,
            nro_factura: null,
            fecha_pago: null,
            concepto: null,
            notificacion: null,
            precio: null,
        }),
        methods: {
            crearVentaMayorista: function() {

                if ( this.validateNuevaVentaForm ) {

                    var data = {
                        api_token: window.Laravel.api_token,
                        empresa: this.empresa,
                        sku: this.sku,
                        cantidad: this.cantidad,
                        pagada: this.pagada,
                        validez: this.validez,
                        nro_factura: this.nro_factura,
                        fecha_pago: this.fecha_pago,
                        concepto: this.concepto,
                        comentario: this.comentario,
                        tipo_notificacion: this.notificacion,
                        precio: this.precio
                    }

                    this.resetFormNuevaVenta()
                    this.closeModal()

                    showSnackbar('Registrando venta ...')

                    axios.post(this.rutaCrear, data)
                        .then(res => {

                            this.reloadTable()

                            showSnackbar('Venta registrada. Enviando voucher...')
                        }).catch(err => {
                            showSnackBarFromAxiosErrors(err)
                    })
                }
            },

            resetFormNuevaVenta: function() {

                this.empresa = null
                this.sku = null
                this.cantidad = 1
                this.pagada = null
                this.comentario = null
                this.validez = this.validez_default
                this.nro_factura = null
                this.fecha_pago = null
                this.concepto = null
                this.notificacion = null
                this.precio = null
            },

            resetFechaPago: function() {

                if ( !this.pagada )
                {
                    this.fecha_pago = null
                }
            },

            closeModal: function() {

                window.$('#create_venta_modal').modal('hide')
            },

            reloadTable: function() {

                window.$('#ventas_table').DataTable().ajax.reload()
            },
        },

        computed: {

            validateCantidad: function() {
                return this.cantidad > 0
            },

            validateValidez: function() {
                return this.validez >= 1
            },

            validateSku: function() {
                return this.sku && 1
            },

            validateNuevaVentaForm: function() {
                return this.empresa && this.validateSku && this.validateCantidad && this.validez && ( !this.pagada || ( this.pagada && this.fecha_pago ) ) && this.concepto && this.notificacion && this.precio
            },

            empresaEmail: function() {

                var emp = this.empresas.filter(c => c.id == this.empresa)

                if ( emp )
                    return emp[0].email
                else
                    return null
            },
        },

        mounted: function() {
            this.validez = this.validez_default
        },
    }
</script>
