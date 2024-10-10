<script>
    import PageIndex from "../core/page-index";
    export default {
        components: { PageIndex },
        data() {
            return {
                user: window.user,
                products: [],
                cart: {
                    items: [],
                    totalItems: 0,
                    totalPrice: 0,
                },
            };
        },
        mounted() {
            // Return list of products
            window.axios
                .get("/products/shopping_list")
                .then((response) => {
                    this.products = response.data.products;
                })
                .catch((errors) => {
                    this.errors = errors.response.data.errors;
                });
        },
        methods: {
            updateCart(product, quantity) {
                const existingProduct = this.cart.items.find(
                    (item) => item.id === product.id
                );

                if (existingProduct) {
                    existingProduct.quantity = quantity;
                    existingProduct.total = product.price * quantity;
                } else {
                    this.cart.items.push({
                        ...product,
                        quantity: quantity,
                        total: product.price * quantity,
                    });
                }

                this.updateCartTotals();
            },
            updateCartTotals() {
                this.cart.totalItems = this.cart.items.reduce(
                    (sum, item) => sum + item.quantity,
                    0
                );
                this.cart.totalPrice = this.cart.items.reduce(
                    (sum, item) => sum + item.total,
                    0
                );
            },
            payNow() {
                alert("Proceeding to payment");
                // Handle payment process here
            },
        },
    };
</script>

<template>
    <div>
        <h3 class="mt-4">Items List</h3>

        <page-index url="/products/all_products" prefix="products" name="Listed Items">
            <template slot="sort-fields">
                <option value="created_at">Created</option>
                <option value="updated_at">Updated</option>
            </template>

            <template slot="table-header">
                <th></th>
                <th>Product</th>
                <th>Category</th>
                <th>Date Added</th>
                <th>Price</th>
                <th>Quantity</th>
            </template>

            <template slot="table-row" slot-scope="data">
                <td>
                    <router-link :to="`/products/view_product/${data.row.id}`" class="action-icon text-primary">
                    <img src="/images/shopping_icon.png" alt="contact-img" title="contact-img" class="rounded me-3" height="48" />
                    </router-link>
                </td>
                <td> {{ data.row.name }}</td>
                <td>$ {{ data.row.price }}</td>
                <td>{{ data.row.created_at | string_limit(10) }}</td>
                <td>
                    <input
                            type="number"
                            min="1"
                            v-model.number="data.row.quantity"
                            @input="updateCart(data.row, data.row.quantity)"
                            class="form-control"
                            placeholder="Qty"
                            style="width: 90px;"
                    />
                </td>
                <td>${{ data.row.price * data.row.quantity || 0 }}</td>
            </template>
        </page-index>


        <div class="cart-summary mt-4">
            <h4>Shopping Cart</h4>
            <ul v-if="cart.items.length > 0">
                <li v-for="item in cart.items" :key="item.id">
                    {{ item.name }} - Quantity: {{ item.quantity }} - Total: ${{ item.total }}
                </li>
            </ul>
            <p v-if="cart.items.length > 0">Total Price: ${{ cart.totalPrice }}</p>
            <p v-if="cart.items.length === 0">Your cart is empty</p>
            <button v-if="cart.items.length > 0" class="btn btn-primary" @click="payNow">Pay Now</button>
        </div>
    </div>
</template>
<style scoped>
    .card {
        margin-bottom: 20px;
    }
    .cart-icon {
        position: fixed;
        top: 20px;
        right: 20px;
        cursor: pointer;
        font-size: 24px;
        z-index: 2000; /* Ensure the icon is also on top */
    }
    .cart-summary {
        position: fixed;
        top: 60px;
        right: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        width: 300px;
        z-index: 1000; /* Bring to forefront */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Optional shadow for better visibility */
    }
    .cart-summary button {
        width: 100%;
        margin-top: 10px;
    }
</style>

