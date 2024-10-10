<script>
    export default {
        data() {
            return {
                loading : false,
                product: {
                    name: "",
                    description: "",
                    category: "",
                    price: 0,
                    image: null,
                    imagePreview: null,  // Holds the image preview data
                },
                errors: {},
                isDragActive: false
            };
        },
        methods: {
            // Handle file input click
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.product.image = file;
                    this.previewImage(file);  // Preview the image
                }
            },

            // Handle drag-over event
            onDragOver(event) {
                this.isDragActive = true;
            },

            // Handle drag-leave event
            onDragLeave(event) {
                this.isDragActive = false;
            },

            // Handle drop event
            onDrop(event) {
                this.isDragActive = false;
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.product.image = files[0];
                    this.previewImage(files[0]);  // Preview the image
                }
            },

            // Function to trigger file input when clicking the dropzone
            openFilePicker() {
                this.$refs.fileInput.click();
            },

            // Preview image by reading the file using FileReader
            previewImage(file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.product.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            async createProduct() {
                this.loading = true;
                const formData = new FormData();
                formData.append("name", this.product.name);
                formData.append("description", this.product.description);
                formData.append("price", this.product.price);
                formData.append("category", this.product.category);
                formData.append("image", this.product.image);

                try {
                    await window.axios.post("products/add_product", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    });
                    this.$router.push('/');
                    window.Swal.fire(
                        'Created',
                        'Product Successfully Added',
                        'success'
                    );
                    this.product = { name: "", description: "", price: 0, image: null, imagePreview: null };
                    this.errors = {};
                } catch (error) {
                    this.loading = false;
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                        window.Swal.fire({
                            title: 'Error',
                            text: 'An error occurred',
                            icon: 'error',
                        });
                    }
                }
            }
        }
    };
</script>
<template>
    <div class="container mt-5">
        <h2 class="mb-4">Create New Product</h2>
        <form @submit.prevent="createProduct" enctype="multipart/form-data">

            <!-- Product Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" v-model="product.name" id="name" class="form-control" />
                <span v-if="errors.name" class="text-danger">{{ errors.name[0] }}</span>
            </div>

            <!-- Product Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea v-model="product.description" id="description" class="form-control"></textarea>
                <span v-if="errors.description" class="text-danger">{{ errors.description[0] }}</span>
            </div>

            <!-- Product Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" v-model="product.price" id="price" class="form-control" step="0.01" />
                <span v-if="errors.price" class="text-danger">{{ errors.price[0] }}</span>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select v-model="product.category" id="category" class="form-control">
                    <option value="" disabled>Select a category</option>
                    <option value="electronics">Electronics</option>
                    <option value="fashion">Fashion</option>
                    <option value="home appliances">Home Appliances</option>
                </select>
                <span v-if="errors.category" class="text-danger">{{ errors.category[0] }}</span>
            </div>

            <!-- Drag-and-Drop File Upload -->
            <div
                    class="dropzone mb-3"
                    @dragover.prevent="onDragOver"
                    @dragleave.prevent="onDragLeave"
                    @drop.prevent="onDrop"
                    :class="{ 'dropzone-active': isDragActive }"
                    @click="openFilePicker">
                <p v-if="!product.imagePreview">Drag and drop a product image here, or click to select one</p>
                <img v-if="product.imagePreview" :src="product.imagePreview" alt="Image Preview" class="img-fluid mt-2" />
                <input type="file" ref="fileInput" @change="handleFileUpload" class="d-none" id="image" />
            </div>
            <span v-if="errors.image" class="text-danger">{{ errors.image[0] }}</span>

            <div class="form-group">
                <button @click.prevent="createProduct" :class="['btn btn-primary px-4' , loading || loading ? 'btn-loading' : '']" type="submit">Add Product</button>
            </div>

        </form>
    </div>
</template>

<style scoped>
    .dropzone {
        border: 2px dashed #ced4da;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }
    .dropzone-active {
        background-color: #f0f0f0;
    }
    .dropzone p {
        margin: 0;
        color: #6c757d;
    }
    .img-fluid {
        max-width: 100%;
        height: auto;
    }
</style>
