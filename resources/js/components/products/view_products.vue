<script>
    import PageIndex from "../core/page-index";
    export default {
        components : {PageIndex},
        data (){
            return {
                user: window.user,
                products: {},
            }
        },
        mounted() {
            //return list of producst
            window.axios.get('/products/all_products').then((response) =>{
                this.products = response.data.products
            }).catch((errors) => {
                this.errors = errors.response.data.errors
            });
        }
    }
</script>
<template>
    <div>
        <h2 class="mt-4">Recent Orders</h2>
        <page-index url="/products/all_products" prefix="products" name="Listed Products">

            <template slot="sort-fields">
                <option value="created_at">Created</option>
                <option value="updated_at">Updated</option>
            </template>
            <template slot="table-header">
                <th></th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Date Added</th>
                <th></th>
                <th class="text-center"></th>
            </template>
            <template slot="table-row" slot-scope="data">
                <td>
                    <span class="text-primary">#{{ data.row.id }}</span>
                </td>
                <td>{{ data.row.name | string_limit(10) }}</td>
                <td>{{ data.row.category | string_limit(12) }}</td>
                <td>{{ data.row.price | string_limit(10) }}</td>
                <td>{{ data.row.created_at  | string_limit(10) }}</td>
                <td>
                    <router-link :to="`/products/view_product/${data.row.id}`" class="action-icon text-primary">
                        <i class="mdi mdi-eye mdi-24px"/>
                    </router-link>
                </td>
            </template>
        </page-index>
    </div>
</template>
<style scoped>
    .card {
        margin-bottom: 20px;
    }
</style>
