<template>
<div class="container-fluid">
    <div row>
        <div class="col col-6 float-left">
            <h1>Productos</h1>
            <div class="input-group mb-3">
                <input type="text" class="form-control" v-model="search" placeholder="Buscar productos" aria-label="Recipient's username" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" v-on:click="busca()" type="button">Buscar</button>
                </div>
            </div>
            <table class="table-responsive-sm table-bordered" v-if="datos==true">
                <th>Producto</th>
                <th>Acciones</th>
            </table>

        </div>
        <div class="col col-6 float-left">
            <h1>Ventas</h1>

            <table class="table-responsive-md  table-bordered" v-if="carrito.length>0" >
                <th>Producto</th>
                <th>Cantidades</th>
            </table>
        </div>

    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            search: '',
            datos:false,
            carrito:[],
        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
