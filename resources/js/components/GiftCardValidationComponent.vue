
<template>
    <div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Validar Gift Card</div>
                    <div class="card-body">
                        <form
                            @submit.prevent="validarGiftcard()">
                            <div class="form-row">
                                <div class="col-9">
                                    <input type="text" autocomplete="off" class="form-control form-control-lg" v-model="codigo" placeholder="#########" v-bind:class="{ 'is-valid': codigo, 'is-invalid': !codigo }">
                                </div>
                                <div class="col-3">
                                    <input @click="validarGiftcard()" type="submit" value="Buscar" name="Buscar" class="btn btn-primary btn-lg">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" v-if="gc">
            <div class="col-md-12">
                <div class="card" v-if="gc.codigo">
                    <div class="card-header">Gift Card:
                        <strong>{{ gc.codigo }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success" role="alert" v-if="gc.estado == estados.valida">
                            ESTADO: <strong>VÁLIDA</strong>!<br>
                            CANTIDAD: <strong>#{{ gc.cantidad }}</strong><br>
                            PRODUCTO: <strong>{{ gc.descripcion }}</strong><br><br>
                            <form
                              @submit.prevent="asignarGiftcard()"
                            >
                                <div class="form-row">
                                    <div class="col-4">
                                        <input type="text" class="form-control" v-model="nro_mesa" placeholder="Mesa" v-bind:class="{ 'is-valid': nro_mesa, 'is-invalid': !nro_mesa }">
                                    </div>
                                    <div class="col-md-4">
                                        <select v-model="sede" class="form-control" v-bind:class="{ 'is-valid': sede, 'is-invalid': !sede }">
                                            <option selected value="null" disabled>Seleccionar Sede</option>
                                            <option v-for="sede in sedes" :value="sede.id">{{ sede.nombre }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button @click="asignarGiftcard()" class="btn btn-primary" v-bind:class="{'disabled': !sede || !nro_mesa }">Asignar</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="alert alert-danger" role="alert" v-if="gc.estado == estados.vencida">
                          ESTADO: <strong>VENCIDA</strong>!<br>
                          FECHA VENCIMIENTO: <strong>{{ gc.fecha_vencimiento }}</strong><br>
                      </div>

                      <div class="alert alert-warning" role="alert" v-if="gc.estado == estados.asignada">
                          ESTADO: <strong>ASIGNADA</strong>!<br>
                          ASIGNÓ: <strong>{{ gc.asigno }}</strong><br>
                          FECHA ASIGNACION: <strong>{{ gc.fecha_asignacion }}</strong><br>
                          NRO MESA: <strong>{{ gc.nro_mesa }}</strong><br>
                          SEDE: <strong>{{ gc.sede }}</strong>
                      </div>
                    </div>
                </div>

                <div class="card" v-if="!gc.codigo">
                    <div class="card-header">Sin resultados para el código <strong>{{ codigo }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'GiftCardValidationComponent',
    props: ['estados', 'codigo', 'sedes', 'rutaAsignar', 'rutaValidar'],
    data: () => ({
      sede: null,
      nro_mesa: null,
      gc: null,
    }),
    methods: {
      validarGiftcard: function () {
        if ( this.codigo )
        {
          axios.get(this.rutaValidar + '/' + this.codigo + '?api_token=' + window.Laravel.api_token, {
            api_token: window.Laravel.api_token,
          })
            .then(res => {
              this.gc = res.data

            }).catch(err => {
              console.log(err)
          })
        }
      },
      asignarGiftcard: function () {
        if ( this.nro_mesa && this.sede )
        {
          axios.post(this.rutaAsignar + '/' + this.codigo, {
            api_token: window.Laravel.api_token,
            sede: this.sede,
            nro_mesa: this.nro_mesa,
          })
            .then(res => {
              this.gc = res.data
              showSnackbar('GC Asignada.');
            }).catch(err => {
              console.log(err)
          })
        }
      }
    },
    mounted: function() {
      if ( this.codigo )
      {
        this.validarGiftcard()
      }
    },
  }
</script>
