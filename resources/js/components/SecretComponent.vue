<template>
    <div class="container">
        <!-- login form -->
        <div class="row mt-4" v-if="!secrets.length">
            <div class="col-6 offset-3">
                <form action="#" @submit.prevent="handleLogin">
                    <h3>Sign in for secrets</h3>
                    <div class="form-row">
                        <input type="text" name="email" class="form-control" v-model="formData.login_key" placeholder="Email Address">
                    </div>
                    <div class="form-row">
                        <input type="password" name="password" class="form-control" v-model="formData.password" placeholder="Password">
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                secrets: [],
                formData: {
                    login_key: '',
                    password: ''
                }
            }
        },
        methods: {
            handleLogin() {
                // send axios request to the login route
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/api/login', this.formData).then(response => {
                        this.showMe();
                    });
                });
            },
            showMe  () {
                axios.get('/api/user/me').then(response => {
                    console.log(response);
                });
            }
        }
    }
</script>

<style>
    .form-row {
        margin-bottom: 8px;
    }
</style>
