<template>

    <div>
        <button data-toggle="modal" data-target="#create_empresa_modal" class="btn btn-success">Nueva Empresa</button>
    
        <div id="create_empresa_modal" class="modal fade text-xs-left text-sm-right text-md-right text-lg-right">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        Nueva Empresa
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- dialog body -->
                    <div class="modal-body">
                        <form class="form-horizontal" id="form_nuevo_empresa" @submit.prevent="crearEmpresa()">
                            <div class="form-group row">
                                
                                <div class="col-2">
                                    <label for="nombre" class="col-form-label" >Nombre</label>
                                </div>

                                <div class="col-9">
                                    <input type="text" id="nombre" class="form-control" v-model="nombre" v-bind:class="{ 'is-valid' : nombre, 'is-invalid': !nombre }">
                                </div>

                                <div class="col-2">
                                    <label for="sku" class="col-form-label">Email</label>
                                </div>

                                <div class="col-9">
                                    <input type="email" class="form-control" v-bind:class="{ 'is-valid': validateEmpresaEmail, 'is-invalid': !validateEmpresaEmail }" v-model="email" id="email">
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col text-center">
                                    <input type="submit" name="Crear" value="Crear" class="btn btn-success" v-bind:class="{ disabled: !validateNuevaEmpresaForm }">
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
        name: 'NuevaEmpresaComponent',
        props: ['rutaCrear'],
        data: () => ({
            nombre: null,
            email: null,
            reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
        }),
        methods: {
            crearEmpresa: function() {

                if ( this.validateNuevaEmpresaForm ) {

                    axios.post(this.rutaCrear, {
                        api_token: window.Laravel.api_token,
                        nombre: this.nombre,
                        email: this.email,
                    })
                        .then(res => {

                            this.resetFormNuevaEmpresa()
                            this.closeModal()
                            this.reloadTable()

                            showSnackbar('Empresa creada.');
                        }).catch(err => {
                            showSnackBarFromAxiosErrors(err);

                    })
                }
            },

            resetFormNuevaEmpresa: function() {

                this.nombre = null
                this.email = null
            },

            closeModal: function() {

                window.$('#create_empresa_modal').modal('hide');
            },

            reloadTable: function() {

                window.$('#empresas_table').DataTable().ajax.reload()
            },
        },

        computed: {

            validateEmpresaEmail: function() {
                return this.email && this.reg.test(this.email)
            },

            validateNuevaEmpresaForm: function() {
                return this.nombre && this.email
            },
        },
    }
</script>
