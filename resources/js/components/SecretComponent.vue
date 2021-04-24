<template>
  <div class="container">
    <!-- login form -->
    <div class="row mt-4">
      <div class="col-6 offset-3">
        <form action="#" @submit.prevent="handleLogin">
          <h3>Sign in for secrets</h3>
          <div class="form-row">
            <input
              type="text"
              name="email"
              class="form-control"
              v-model="formData.login_key"
              placeholder="Email Address"
            />
          </div>
          <div class="form-row">
            <input
              type="password"
              name="password"
              class="form-control"
              v-model="formData.password"
              placeholder="Password"
            />
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
      auth_token: "",
      formData: {
        login_key: "",
        password: "",
      },
    };
  },
  methods: {
    handleLogin() {
      //
    },
  },
  login() {
    axios.post("/api/login", this.formData).then((response) => {
      this.auth_token = response.data.data.token;
    });
  },
  showMe() {
    axios
      .get("/api/user/me", {
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${this.auth_token}`,
        },
      })
      .then((response) => {
        console.log(response);
      });
  },
};
</script>
<style>
.form-row {
  margin-bottom: 8px;
}
</style>
