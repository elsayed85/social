<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="form-group">
                <div v-if="!authorized">
                    <input class="form-control" placeholder="enter login key" :type="passwordFieldType" v-model="login_key" />

                    <br>

                    <input class="form-control" placeholder="enter password" :type="passwordFieldType" v-model="password"/>

                    <br>

                    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" type="password" @click="switchVisibility">
                        show / hide
                    </button>

                    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" @click="socketConnect">Connect</button>
                </div>
                <div v-else>
                    User <b><mark>{{ user_id }}</mark></b> is connected now :)
                    <br>
                    <button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" @click="logout">logout</button>

                </div>
            </div>
        </div>
    </div>
    <hr/>
     <div v-if="authorized">
        <div class="row">
            <div class="col-md-12">
                <table class="leading-normal socket_table">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Payload
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Created At
                                </th>
                            </tr>
                        </thead>
                        <tbody id="device_data">
                            <tr v-for="(data) in socket_data.slice().reverse()" :key="data.id">
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                    <tree-view :data="data.payload" :options="{maxDepth: 10}"></tree-view>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm w-2/5">
                                    <time-ago
                                        :datetime="data.created_at"
                                        :refresh="1"
                                        :tooltip="true"
                                        :long="false"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
</template>
<style >
    .socket_table{
        max-height: 440px;
        overflow: auto;
        display: inline-block;
    }
</style>
<script>
import TimeAgo from "vue2-timeago";
import TreeView from "vue-json-tree-view";
Vue.use(TreeView)
import Echo from "laravel-echo";
window.Pusher = require("pusher-js");

function initialState (){
  return {
        socket_data: [],
        user_token: null,
        user_id: null,
        user : null,
        authorized: false,
        login_key: "",
        password: "",
        passwordFieldType: 'password',
    }
}

export default {
    components: {
    TimeAgo
    },
    data() {
        return initialState();
    },
    computed: {
        socket_data: function() {
            return this.items.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
        },
    },
    methods: {
        socketConnect: function () {
            this.authorize();
        },
        logout :  function(){
            localECHO.leave(`App.Models.User.${this.user_id}`);
             axios({
                    method: "POST",
                    url: "/api/user/logout",
                    headers: {
                        Authorization: `Bearer ${this.user_token}`,
                    }
                })
                .then((response) => {
                    Object.assign(this.$data, initialState());
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        switchVisibility() {
            this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'
        },
        clearSocketData: function () {
            this.socket_data = [];
        },
        authorize: function () {
            axios
                .post("/api/login", {
                    login_key: this.login_key,
                    password: this.password,
                } , {withCredentials: true})
                .then(({
                    data
                }) => {
                    console.log(data)
                    this.user_token = data.data.access_token;
                    this.user_id = data.data.user_id;
                    window.localECHO = new Echo({
                        broadcaster: "pusher",
                        key: process.env.MIX_PUSHER_APP_KEY,
                        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                        forceTLS: false,
                        wsHost: window.location.hostname,
                        wsPort: 6001,
                        disableStats: true,
                        enabledTransports: ['ws', 'wss'], // <- added this param
                        authorizer: (channel, options) => {
                            console.log(options, channel);
                            return {
                                authorize: (socketId, callback) => {
                                    axios({
                                            method: "POST",
                                            url: "/api/broadcasting/auth",
                                            headers: {
                                                Authorization: `Bearer ${this.user_token}`,
                                            },
                                            data: {
                                                socket_id: socketId,
                                                channel_name: channel.name,
                                            },
                                        })
                                        .then((response) => {
                                            console.log(response);
                                            console.log("Connected")
                                            this.authorized = true;
                                            callback(false, response.data);
                                        })
                                        .catch((error) => {
                                            console.log(error);
                                            this.authorized = false;
                                           console.log("Failed To Connect")
                                            callback(true, error);
                                        });
                                },
                            };
                        },
                    });

                    localECHO.private(`App.Models.User.${this.user_id}`)
                        .listen(
                            ".send_data_event",
                            (e) => {
                                console.log(e)
                                this.socket_data.push(e);
                                this.handelResposne(e)
                            }
                        );
                }).catch((error) => {
                    console.log("Failed To Connect")
                });
        },
        handelResposne : function(e){
            //
        },
        socketPayloadContainsKey(payload,key) {
            return Object.keys(payload).includes(key);
        }
    },
    mounted() {
        //
    },
    created() {
        //
    },
};
</script>
