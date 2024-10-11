<script>
    import PageIndex from "../core/page-index";
    export default {
        components: { PageIndex },
        data() {
            return {
                totalUsers: 0,
                totalProducts: 0,
                totalSales: 0,
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
            // Fetch products from the shopping list endpoint
            window.axios
                .get("/products/shopping_list")
                .then((response) => {
                    this.products = response.data.products;
                })
                .catch((errors) => {
                    this.errors = errors.response.data.errors;
                });
            window.axios.get('/dashboardStats')
                .then(response => {
                    this.totalUsers = response.data.totalUsers;
                    this.totalProducts = response.data.totalProducts;
                    this.totalSales = response.data.totalSales;
                })
                .catch(error => {
                    console.error('Error fetching dashboard data:', error);
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
                if (this.cart.items.length === 0) {
                    alert("Your cart is empty.");
                    return;
                }

                const totalAmount = this.cart.totalPrice;
                const productDetails = this.cart.items.map(item => ({ product: item.name, amount: item.total }));

                window.axios.post('/payment/create', {
                    user_id: this.user.id,
                    product: productDetails,
                    amount: totalAmount,
                    user_email: this.user.email
                })
                    .then(response => {
                        if (response.data.redirectUrl) {
                            // Redirect the user to Paynow
                            window.location.href = response.data.redirectUrl;
                        }
                    })
                    .catch(error => {
                        console.error("Payment error:", error);
                        window.Swal.fire('Error', 'Payment could not be processed.', 'error');
                    });
            }

        },
    };
</script>
<template>
    <div>
            <div v-if="user.access_level === 'admin'" class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text">{{totalUsers}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text">{{totalProducts}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text">{{totalSales}}</p>
                        </div>
                    </div>
                </div>
            </div>
        <h4 class="mt-4">Items List</h4>
        <!-- Show products for all users -->
        <page-index url="/products/all_products" prefix="products" name="Listed Items">
            <template slot="sort-fields">
                <option value="created_at">Created</option>
                <option value="updated_at">Updated</option>
            </template>

            <template slot="table-header">
                <th></th>
                <th>Product</th>
                <th v-if="user.access_level === 'admin'">Category</th>
                <th>Price</th>
                <th>Date Added</th>
                <th v-if="user.access_level !== 'admin'">Quantity</th>
                <th v-if="user.access_level !== 'admin'">Product Total</th>
            </template>

            <template slot="table-row" slot-scope="data">
                <td>
                    <router-link :to="`/products/view_product/${data.row.id}`" class="action-icon text-primary">
                        <img src="/images/shopping_icon.png" alt="contact-img" title="contact-img" class="rounded me-3" height="48" />
                    </router-link>
                </td>
                <td> {{ data.row.name }}</td>
                <td v-if="user.access_level === 'admin'"> {{ data.row.category }}</td>
                <td>$ {{ data.row.price }}</td>
                <td>{{ data.row.created_at | string_limit(10) }}</td>
                <td v-if="user.access_level !== 'admin'">
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
                <td v-if="user.access_level !== 'admin'">${{ data.row.price * data.row.quantity || 0 }}</td>
                <td>
                    <router-link :to="`/products/view_product/${data.row.id}`" class="action-icon text-primary">
                        <i class="mdi mdi-eye mdi-24px"/>
                    </router-link>
                </td>
            </template>
        </page-index>

        <!-- Show cart summary if user is not an admin -->
        <div v-if="user.access_level !== 'admin'" class="cart-summary">
            <h4>Shopping Cart</h4>
            <ul v-if="cart.items.length > 0">
                <li v-for="item in cart.items" :key="item.id">
                    {{ item.name }} - Quantity: {{ item.quantity }} - Total: ${{ item.total }}
                </li>
            </ul>
            <p v-if="cart.items.length === 0">Your cart is empty</p>
            <button v-if="cart.items.length > 0" class="btn btn-primary" @click="payNow">Pay Now</button>
        </div>
    </div>
</template>
<style scoped>
    .card {
        margin-bottom: 20px;
    }

    .cart-summary {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        width: 300px;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .cart-summary button {
        width: 100%;
        margin-top: 10px;
    }
</style>
