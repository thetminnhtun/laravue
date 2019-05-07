<template>
    <div class="container-fluid">
        <div class="row">
        <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users list</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="create">
                        <i class="fa fa-user-plus"></i>
                        Add user
                    </button>
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
                        <tr v-for="user in users" :key="user.id">
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
        </div> <!-- col-md-12 -->
    </div> <!-- .row -->
    
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
                <button v-if="!editMode" @click="store" :disabled="form.busy" type="submit" class="btn btn-primary">Create</button>
                <button v-if="editMode" @click="update" :disabled="form.busy" type="submit" class="btn btn-success">Update</button>
            </div>
            </div>
        </div>
    </div> <!-- .modal -->

    </div> <!-- container-fluid -->
</template>

<script>
import { log } from 'util';
    export default {
        data() {
            return {
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
            view() {
                this.$Progress.start()
                axios.get('api/users')
                .then(res => this.users = res.data)
                .catch(err => console.log(err));
                this.$Progress.finish()
            },
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
                            this.$Progress.fail()
                        })
                    }
                })
                
            }
        },
        created() {
            this.view();
        }
    }
</script>
