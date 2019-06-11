<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active show" href="#profile" data-toggle="tab">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="profile">
                                <div class="text-center mb-3">
                                    <img class="profile-user-img img-fluid img-circle" :src="getProfilePhoto()" alt="User profile picture">
                                </div>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ user.name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>{{ user.email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Role</td>
                                            <td v-if="user.role">
                                                {{ user.role | capitalize }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane" id="settings">
                                <form @submit.prevent="update">
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
                                        <label >Choose profile photo</label>
                                        <input type="file" class="form-control-file" @change="readImage">
                                    </div>

                                    <div class="form-group">
                                        <input v-model.trim="form.password" type="password" name="password"
                                            placeholder="Password (Leave empty if not changing)"
                                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                        <has-error :form="form" field="password"></has-error>
                                    </div>
                                    <button type="submit" :disabled="form.busy" class="btn btn-success">Update</button>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
         data() {
            return {
                user: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    role: '',
                    photo: '',
                })
            }
        },
        methods: {
            view() {
                this.$Progress.start()
                axios.get('api/profile')
                .then(res => {
                    this.user = res.data;
                    this.form.fill(res.data);
                    this.$Progress.finish();
                })
                .catch(err => {
                    console.log(err)
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                    this.$Progress.fail()
                });

            },
            readImage(e) {
                let file = e.target.files[0];
                let limit = 1024 * 1024 * 2;
                if(file.size <= limit ) {
                    let reader = new FileReader();
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'The photo may not greater than 2MB'
                    });
                }
            },
            update() {
                this.$Progress.start()
                if(this.form.password == ''){
                    this.form.password = undefined;
                }
                this.form.put('api/profile')
                .then(res => {
                    Toast.fire({
                        type: 'success',
                        title: 'A user was updated successfully.'
                        });
                    this.view();
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
            getProfilePhoto() {
                let imagePath = `${this.$url}/img/profile`;
                if(this.user.photo) {
                    return `${imagePath}/${this.user.photo}`;
                }
                return `${imagePath}/user.png`;
            }
        },
        created() {
            this.view();
        }
    }
</script>
