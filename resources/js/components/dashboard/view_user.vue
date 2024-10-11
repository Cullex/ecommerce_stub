<script>
    export default {
        data() {
            return {
                loading: false,
                user: {
                    name: "",
                    last_name: "",
                    email: "",
                    msisdn: "",
                    status: '',
                },
                errors: {},
            };
        },
        methods: {
            async fetchUser(id) {
                try {
                    const response = await window.axios.get(`view_user/${id}`);
                    this.user = {
                        ...response.data,
                    };
                } catch (error) {
                    console.error('Error fetching user:', error);
                }
            },
            async updateUser() {
                this.loading = true;
                const formData = new FormData();
                formData.append("status", this.user.status);
                try {
                    await window.axios.post(`update_user/${this.$route.params.id}`);
                    this.$router.push('/'); // Redirect after success
                    window.Swal.fire('Updated', 'User Successfully Updated', 'success');
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
        },
        mounted() {
            const userId = this.$route.params.id;
            this.fetchUser(userId);
        }
    };
</script>

<template>
    <div class="container mt-5">
        <h2 class="mb-4">Update User</h2>
        <form @submit.prevent="updateUser" enctype="multipart/form-data">

            <!-- User Name -->
            <div class="mb-3">
                <label for="name" class="form-label">User Name</label>
                <input disabled type="text" v-model="User.name" id="name" class="form-control" />
                <span v-if="errors.name" class="text-danger">{{ errors.name[0] }}</span>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea disabled v-model="User.last_name" id="description" class="form-control"></textarea>
                <span v-if="errors.last_name" class="text-danger">{{ errors.last_name }}</span>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Profile Status</label>
                <select v-model="user.status" id="category" class="form-control">
                    <option value="" disabled>Select a category</option>
                    <option value=1>Enabled</option>
                    <option value=0>Disabled</option>
                </select>
                <span v-if="errors.status" class="text-danger">{{ errors.status[0] }}</span>
            </div>
            <div class="form-group">
                <button :class="['btn btn-primary px-4', loading ? 'btn-loading' : '']" type="submit">Update User</button>
            </div>
        </form>
    </div>
</template>