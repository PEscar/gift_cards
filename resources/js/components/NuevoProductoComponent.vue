<template>

    <div>
        <button data-toggle="modal" data-target="#create_producto_modal" class="btn btn-success">Nuevo Producto</button>
    
        <div id="create_producto_modal" class="modal fade text-xs-left text-sm-right text-md-right text-lg-right">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        Nuevo Producto
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- dialog body -->
                    <div class="modal-body">
                        <form class="form-horizontal" id="form_nuevo_producto" @submit.prevent="crearProducto()">
                            <div class="form-group row">
                                
                                <div class="col-2">
                                    <label for="nombre" class="col-form-label" >Nombre</label>
                                </div>

                                <div class="col-9">
                                    <input type="text" id="nombre" class="form-control" v-model="nombre" v-bind:class="{ 'is-valid' : nombre, 'is-invalid': !nombre }">
                                </div>

                                <div class="col-2">
                                    <label for="sku" class="col-form-label">Código</label>
                                </div>

                                <div class="col-9">
                                    <input type="text" class="form-control" v-bind:class="{ 'is-valid': sku, 'is-invalid': !sku }" v-model="sku" id="sku">
                                </div>

                                <div class="col-2">
                                    <label for="descripcion" class="col-form-label" >Descripcion</label>
                                </div>

                                <div class="col-9">
                                    <input type="text" class="form-control" v-model="descripcion" id="descripcion">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col text-center">
                                    <input type="submit" name="Crear" value="Crear" class="btn btn-success" v-bind:class="{ disabled: !validateNuevoProductoForm }">
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
        name: 'NuevoProductoComponent',
        props: ['rutaCrear'],
        data: () => ({
            nombre: null,
            sku: null,
            descripcion: null,
        }),
        methods: {
            crearProducto: function() {

                if ( this.validateNuevoProductoForm ) {

                    axios.post(this.rutaCrear, {
                        api_token: window.Laravel.api_token,
                        nombre: this.nombre,
                        descripcion: this.descripcion,
                        sku: this.sku
                    })
                        .then(res => {

                            this.resetFormNuevoProducto()
                            this.closeModal()
                            this.reloadTable()

                            showSnackbar('Producto creado.');
                        }).catch(err => {
                            showSnackBarFromAxiosErrors(err);

                    })
                }
            },

            resetFormNuevoProducto: function() {

                this.nombre = null
                this.sku = null
                this.descripcion = null
            },

            closeModal: function() {

                window.$('#create_producto_modal').modal('hide');
            },

            reloadTable: function() {

                window.$('#productos_table').DataTable().ajax.reload()
            },
        },

        computed: {

            validateNuevoProductoForm: function() {
                return this.nombre && this.sku
            },
        },
    }
</script>
