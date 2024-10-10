import create_product from "../components/products/create_product";
import view_products from "../components/products/view_products";
import view_product from "../components/products/view_product";
import update_product from "../components/products/update_product";
import shop from "../components/products/shop";

const routes = [
    {
        path : '/create_product',
        component : create_product
    },
    {
        path : '/view_products',
        component : view_products
    },
    {
        path: '/products/view_product/:id',
        component: view_product
    },
    {
        path: '/products/update_product/:id',
        component: update_product
    },
    {
        path: '/products/shop',
        component: shop
    },

];


export default routes;
