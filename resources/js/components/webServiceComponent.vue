<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-3">
        <div class="card">
          <div class="card-header">Example Component</div>

          <div class="card-body">
            <span>{{message}}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Echo from "laravel-echo";
window.Pusher = require("pusher-js");
export default {
  data() {
    return {
      message: ""
    };
  },
  mounted() {
    window.Echo = new Echo({
      broadcaster: "pusher",
      key: "b46f98164b270b0a3c38",
      wsHost: window.location.hostname,
      wsPort: 6001,
      disableStats: true
    });
    window.Echo.channel("home").listen("NewMessage", e => {
      console.log(e);
      this.message = e.message;
    });
    console.log("Component mounted.");
  },
  created() {}
};
</script>
