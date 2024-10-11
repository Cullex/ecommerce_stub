<template>
    <div class="col-lg-12">
        <div style="height: 360px; overflow-y: auto" class="card">
            <div class="">
                <h6 class="text-muted px-3 py-2 text-uppercase">Users List</h6>
            </div>
            <div class="table-responsive font-12">
                <table class="table table-centered">
                    <thead class="thead-light">
                    <tr>
                        <th />
                        <th />
                        <th>#</th>
                        <th>Name</th>
                        <th>Last Name</th>
                        <th>Access Level</th>
                        <th>Date Created</th>
                        <th>Action</th>
                        <th />
                        <th />
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users" :key="user.id">
                        <td />
                        <td class="text-primary"></td>
                        <td class="font-weight-bolder">
                            {{ user.id }}
                        </td>
                        <td class="font-weight-bolder">
                            {{ user.name}}
                        </td>
                        <td class="font-weight-bolder">
                            {{ user.last_name }}
                        </td>
                        <td class="font-weight-bolder">
                            {{ user.isSuperAdmin }}
                        </td>
                        <td class="font-weight-bolder">
                            {{ user.created_at }}
                        </td>
                        <td>
                            <router-link
                                :to="`/admin/getUserLevel/${user.id}`"
                                class="action-icon text-muted"
                            >
                                <i class="mdi mdi-eye mdi-24px" />
                            </router-link>
                        </td>
                        <td />
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
            users: [],
        }
    },
    mounted: function () {

        window.axios.get('/accessUsers').then((response) => {
            (this.users = response.data.body.users.data)})
            .then((errors) =>{
                console.log(errors)
            })
    }
}
</script>
