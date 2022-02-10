<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <!-- Tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Artículos
                    <button @click="abrirModal('articulo', 'registrar')" type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <button type="button" @click="cargarPdf()" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp;Reporte
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="nombre">Nombre</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" @keyup.enter="listarArticulo(1, buscar, criterio)" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarArticulo(1, buscar, criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                <td>
                                    <button @click="abrirModal('articulo', 'actualizar', articulo)" type="button" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <template v-if="articulo.condicion">
                                        <button type="button" class="btn btn-danger btn-sm" @click="desactivarArticulo(articulo.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button type="button" class="btn btn-info btn-sm" @click="activarArticulo(articulo.id)">
                                            <i class="icon-check"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="articulo.codigo"></td>
                                <td v-text="articulo.nombre"></td>
                                <td v-text="articulo.nombre_categoria"></td>
                                <td v-text="articulo.precio_venta"></td>
                                <td v-text="articulo.stock"></td>
                                <td v-text="articulo.descripcion"></td>
                                <td>
                                    <div v-if="articulo.condicion">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">In-activo</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Anterior</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- ./Tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Categoría</label>
                                <div class="col-md-9">
                                    <select class="form-control" v-model="idcategoria">
                                        <option value="0" disabled>Seleccione</option>
                                        <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre">Seleccione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Código</label>
                                <div class="col-md-9">
                                    <input v-model="codigo" min="0" max="99999999999" maxlength="11" type="number" class="form-control" placeholder="Código de barras">
                                    <barcode :value="codigo" :option="{ format : 'EAN-13'}">
                                        Generando código de barras
                                    </barcode>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input v-model="nombre" type="text" class="form-control" placeholder="Nombre de artículo">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Precio de venta</label>
                                <div class="col-md-9">
                                    <input v-model="precio_venta" min="0" max="9999999" type="number" maxlength="11" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                                <div class="col-md-9">
                                    <input v-model="stock" type="number" min="0" max="999999999" maxlength="11" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="descripcion-input">Descripción</label>
                                <div class="col-md-9">
                                    <input v-model="descripcion" type="text" class="form-control" placeholder="Ingrese descripción">
                                </div>
                            </div>
                            <div v-show="errorArticulo" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in errorMostrarMsjArticulo" :key="error" v-text="error"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" v-if="tipoAccion == 1" @click="registrarArticulo()">Guardar</button>
                        <button type="button" class="btn btn-primary" v-if="tipoAccion == 2" @click="actualizarArticulo()">Actualizar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>
    import VueBarcode from 'vue-barcode';
    export default {
    components: {
        'barcode': VueBarcode
    },
    data() {
        return {
            articulo_id: '',
            idcategoria: 0,
            nombre_categoria: '',
            codigo: '',
            nombre: '',
            precio_venta: 0,
            stock: 0,
            descripcion: '',
            arrayArticulo: [],
            arrayErrores: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            errorArticulo: 0,
            errorMostrarMsjArticulo: [],
            pagination: {
                'total': 0, 
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0
            },
            offset: 3,
            criterio: 'nombre',
            buscar: '',
            arrayCategoria: []
        }
    },
    computed: {
        isActived: function(){
            return this.pagination.current_page;
        },
        pagesNumber: function(){
            if(!this.pagination.to){
                return [];
            }
            var from = this.pagination.current_page - this.offset;
            if(from < 1){
                from = 1;
            }
            var to = from + (this.offset * 2);
            if(to >= this.pagination.last_page){
                to = this.pagination.last_page;
            }
            var pagesArray = [];
            while(from <= to){
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    methods:{
        listarArticulo(page, buscar, criterio){
            let me = this;
            let url = '/articulo?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
            axios.get(url).then(function (response) {
                me.arrayArticulo = response.data.articulos.data;
                me.pagination = response.data.pagination;
            }).catch(function (error) {
                me.arrayErrores = error.data;
            });
        },
        cargarPdf(){
            window.open('/articulo/listarPdf','_blank');
        },
        selectCategoria(){
            let me = this;
            let url = '/categoria/selectCategoria';
            axios.get(url).then(function (response) {
                me.arrayCategoria = response.data.categorias;
            }).catch(function (error) {
                me.arrayErrores = error.data;
            });
        },
        cambiarPagina(page, buscar, criterio){
            let me = this;
            me.pagination.current_page = page;
            me.listarArticulo(page, buscar, criterio);
        },
        registrarArticulo(){
            if(this.validarArticulo()){
                return;
            }
            let me = this;
            axios.post('/articulo/registrar', {
                'idcategoria' : this.idcategoria,
                'codigo' : this.codigo,
                'nombre' : this.nombre,
                'precio_venta' : this.precio_venta,
                'stock' : this.stock,
                'descripcion' : this.descripcion
            }).then(function (response){
                me.cerrarModal();
                me.listarArticulo(1, '', 'nombre');
            }).catch(function (error) {
                me.arrayErrores = error.data;
            });
        },
        validarArticulo(){
            this.errorArticulo = 0;
            this.errorMostrarMsjArticulo = [];
            if([null, undefined, 0].includes(this.idcategoria)){
                this.errorMostrarMsjArticulo.push("Seleccione una categoría.");
            }else if([null, undefined, ''].includes(this.nombre)){
                this.errorMostrarMsjArticulo.push("El nombre del artículo es obligatorio.");
            }else if([null, undefined, 0].includes(this.precio_venta) || this.precio_venta < 0){
                this.errorMostrarMsjArticulo.push("El precio del  artículo no debe ser gratis, es decir, 0.");
            }else if([null, undefined, 0].includes(this.stock) || this.stock < 0){
                this.errorMostrarMsjArticulo.push("El stock debe ser mayor a 0.");
            }
            if(this.errorMostrarMsjArticulo.length) this.errorArticulo = 1;
            return this.errorArticulo;
        },
        actualizarArticulo(){
            if(this.validarArticulo()){
                return;
            }
            let me = this;
            axios.put('/articulo/actualizar', {
                'idcategoria' : this.idcategoria,
                'codigo' : this.codigo,
                'nombre' : this.nombre,
                'precio_venta' : this.precio_venta,
                'stock' : this.stock,
                'descripcion' : this.descripcion,
                'id' : this.articulo_id
            }).then(function (response){
                me.cerrarModal();
                me.listarArticulo(1, '', 'nombre');
            }).catch(function (error) {
                me.arrayErrores = error.data;
            });
        },
        desactivarArticulo(id){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: '¿Estás seguro de desactivar el artículo?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si, desactivar!',
            cancelButtonText: 'No, cancelar!',
            reverseButtons: true
            }).then((result) => {
            if (result.isConfirmed) {
                let me = this;
                axios.put('/articulo/desactivar',{
                    'id': id
                }).then(function (response) {
                    me.listarArticulo(1, '', 'nombre');
                    swalWithBootstrapButtons.fire(
                    'Desactivada!',
                    'El artículo ha sido desactivado.',
                    'success'
                    )
                }).catch( function (error) {
                    me.arrayErrores = error.data;
                });
            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Proceso cancelado',
                'El artículo no fue desactivado.',
                'error'
                )
            }
            })
        },
        activarArticulo(id){
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
            title: '¿Estás seguro de activar el artículo?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si, activar!',
            cancelButtonText: 'No, cancelar!',
            reverseButtons: true
            }).then((result) => {
            if (result.isConfirmed) {
                let me = this;
                axios.put('/articulo/activar',{
                    'id': id
                }).then(function (response) {
                    me.listarArticulo(1, '', 'nombre');
                    swalWithBootstrapButtons.fire(
                    'Activada!',
                    'El artículo ha sido activado.',
                    'success'
                    )
                }).catch( function (error) {
                    me.arrayErrores = error.data;
                });
            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                'Proceso cancelado',
                'El artículo no fue activado.',
                'error'
                )
            }
            })
        },
        cerrarModal(){
            this.modal = 0;
            this.tituloModal = '';
            this.idcategoria = 0;
            this.nombre_categoria = '';
            this.codigo = '';
            this.articulo_id = '';
            this.nombre = '';
            this.precio_venta = 0;
            this.stock = 0;
            this.descripcion = '';
            this.errorArticulo = 0;
            this.errorMostrarMsjArticulo = [];
            this.criterio = 'nombre';
            this.buscar = '';
        },
        abrirModal(modelo, accion, data = []){
            switch(modelo){
                case "articulo":{
                    switch(accion){
                        case 'registrar':{
                            this.modal = 1;
                            this.tituloModal = "Registrar Artículo";
                            this.idcategoria = 0;
                            this.nombre_categoria = '';
                            this.codigo = '';
                            this.nombre = '';
                            this.precio_venta = 0;
                            this.stock = 0;
                            this.descripcion = '';
                            this.tipoAccion = 1;
                            break;
                        }
                        case 'actualizar':{
                            this.modal = 1;
                            this.tituloModal = 'Actualizar Artículo';
                            this.tipoAccion = 2;
                            this.articulo_id = data['id'];
                            this.idcategoria = data['idcategoria'];
                            this.codigo = data['codigo'];
                            this.nombre = data['nombre'];
                            this.precio_venta = data['precio_venta'];
                            this.stock = data['stock'];
                            this.descripcion = data['descripcion'];
                            break;
                        }
                    }
                }
            }
            this.selectCategoria();
        }
    },
    mounted() {
            this.listarArticulo(1, this.buscar, this.criterio);
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>