<script>
    export default {
        name: "view_product",
        data() {
            return {
                user: window.user,
                product: {
                    name: '',
                    description: '',
                    price: 0,
                    stock: 0,
                    orders_count: 0,
                    revenue: 0,
                    created_at: '',
                    image_url: '',
                },
            };
        },
        methods: {
            fetchProduct(id) {
                window.axios.get(`/products/view_product/${id}`)
                    .then(response => {
                        this.product = response.data;
                    })
                    .catch(error => {
                        window.Swal.fire({
                            title: 'Error',
                            text: error,
                            icon: 'error',
                        });
                        console.error('Error fetching product:', error);
                    });
            },
            deleteProduct(){
                window.Swal.fire({
                    title: 'Delete Product?',
                    text: `Confirm You Want To Delete Product`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0adc39',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, Delete`
                }).then((result) => {
                    if (result.value) {
                        this.loading = true;
                        window.axios.post(`/products/delete_product/${this.$route.params.id}`)
                            .then(response => {
                                Swal.fire(
                                    'Deleted!',
                                    'Product has been deleted.',
                                    'success'
                                );
                                this.$router.push('/');
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to delete product.',
                                    icon: 'error',
                                });
                            });
                    }
                });
            },
            navigateToUpdate() {
                this.$router.push('/products/update_product/'+ this.$route.params.id);
            }
        },
        mounted() {
            const productId = this.$route.params.id;
            this.fetchProduct(productId);
        },
    };
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Product Details</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ product.name }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <img :src="product.image_url" class="img-fluid" style="max-width: 280px;" alt="Product Image" />
                            </div>
                            <div class="col-lg-7">
                                <form class="ps-lg-4">
                                    <!-- Product title -->
                                    <h3 class="mt-0">{{ product.name }}</h3>
                                    <p class="mb-1">Added Date: {{ product.created_at | string_limit(10) }}</p>

                                    <!-- Product price -->
                                    <div class="mt-4">
                                        <h6 class="font-14">Retail Price:</h6>
                                        <h3> ${{ product.price }}</h3>
                                    </div>

                                    <!-- Product description -->
                                    <div class="mt-4">
                                        <h6 class="font-14">Description:</h6>
                                        <p>{{ product.description }}</p>
                                    </div>

                                    <!-- Product stock information -->
                                    <div class="mt-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6 class="font-14">Available Stock:</h6>
                                                <p class="text-sm lh-150">{{ product.stock }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="font-14">Number of Orders:</h6>
                                                <p class="text-sm lh-150">{{ product.orders_count }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="font-14">Viewed by:</h6>
                                                <p class="text-sm lh-150">{{ product.revenue }} people</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="user.access_level === 'admin'">
                                    <button type="submit" @click="deleteProduct" class="btn btn-primary">Delete</button>
                                     ||
                                    <button @click.prevent="navigateToUpdate" class="btn btn-outline-primary ms-2">Edit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>