<template>
    <div class="container-fluid">
        <div class="row"  v-if="$gate.isAdmin()">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="card-title mb-0">Users list <span class="badge badge-info">{{ users.total }}</span></h3>
                        <div class="card-tools">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group input-group-sm" style="width: 150px;">
                                        <input type="text" @keyup="search" v-model="q"
                                        name="table_search" class="form-control" placeholder="Search">

                                        <div class="input-group-append">
                                        <button type="button" @click="search" class="btn btn-secondary"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <button class="btn btn-success btn-sm" @click="create">
                                        <i class="fa fa-user-plus"></i>
                                        Add user
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td>{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.role | capitalize }}</td>
                                    <td>{{ user.created_at | formatedDate }}</td>
                                    <td>
                                        <a href="#" @click="edit(user)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="destroy(user.id)">
                                            <i class="fa fa-trash-alt red"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
                <pagination :data="users" @pagination-change-page="getResults"></pagination>
            </div> <!-- col-md-12 -->
        </div> <!-- .row -->
    
        <div v-else>
            <page-not-found></page-not-found>
        </div>
    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-if="!editMode">Add New User</h5>
                <h5 class="modal-title" v-if="editMode">Update User's Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="editMode ? update() : store()">
                <div class="modal-body">
                    <div class="form-group">
                        <input v-model="form.name" type="text" name="name" 
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                        <input v-model="form.email" type="email" name="email" 
                            placeholder="Email"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="email"></has-error>
                    </div>
                    <div class="form-group">
                        <select v-model="form.role" name="role"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('role') }">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                        </select>
                        <has-error :form="form" field="role"></has-error>
                    </div>
                    <div class="form-group">
                        <input v-model="form.password" type="password" name="password" 
                            placeholder="Password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" v-if="!editMode" :disabled="form.busy"  class="btn btn-primary">Create</button>
                    <button type="submit" v-if="editMode" :disabled="form.busy"  class="btn btn-success">Update</button>
                </div>
            </form>
            </div>
        </div>
    </div> <!-- .modal -->

    </div> <!-- container-fluid -->
</template>

<script>
window._ = require('lodash');

    export default {
        data() {
            return {
                q: '',
                editMode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    role: '',
                }) 
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/users?page=' + page + "&q=" + this.q)
                    .then(response => {
                        this.users = response.data;
                    });
            },
            view(search = false) {
                if(this.$gate.isAdmin()) {
                    this.$Progress.start()
                    let url = search ? 'api/users?q=' + this.q : 'api/users';
                    axios.get(url)
                    .then(res => {
                        this.users = res.data
                        this.$Progress.finish()
                    })
                    .catch(err => {
                        console.log(err)
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                        his.$Progress.fail()
                    });
                }
            },
            search: _.debounce(()=>{
                Fire.$emit('Search')
            }, 1000),
            create() {
                this.$Progress.start()
                this.editMode = false;
                this.form.reset();
                this.form.clear();
                $('#formModal').modal('show');
                this.$Progress.finish()
            },
            store() {
                this.$Progress.start()
                this.form.post('api/users')
                .then(res => {
                    this.view();
                    $('#formModal').modal('hide');
                    Toast.fire({
                        type: 'success',
                        title: 'A user was created successfully.'
                        })
                    this.form.reset();
                    this.$Progress.finish()
                })
                .catch(err => {
                    console.log(err)
                     Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                    this.$Progress.fail()
                }) 
            },
            edit(user) {
                this.form.clear();
                this.form.fill(user)
                this.editMode = true;
                $('#formModal').modal('show');
            },
            update() {
                this.$Progress.start()
                this.form.put(`api/users/${this.form.id}`)
                .then(res => {
                    this.view()
                    $('#formModal').modal('hide');
                    Toast.fire({
                        type: 'success',
                        title: 'A user was created successfully.'
                        })
                    this.editMode = false;
                    this.form.reset();
                    this.$Progress.finish()
                })
                .catch(err => {
                    console.log(err)
                     Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                    this.$Progress.fail()
                }) 
            },
            destroy(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true,
                    }).then((result) => {
                    if (result.value) {
                        this.$Progress.start()
                        this.form.delete(`api/users/${id}`)
                        .then(res => {
                            this.view()
                            this.$Progress.finish()
                            Toast.fire({
                            type: 'success',
                            title: 'A user has been deleted.'
                            })
                        })
                        .catch(err => {
                            console.log(err)
                            Swal.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                            this.$Progress.fail()
                        })
                    }
                })
                
            }
        },
        created() {
            this.view();
            Fire.$on('Search', () => {
                this.view(true);
            })
        }
    }
</script>
