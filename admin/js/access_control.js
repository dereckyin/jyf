Vue.component("v-select", VueSelect.VueSelect);
var app = new Vue({
  el: "#app",
  data: {
  
    car_access1: [],
    car_access2: [],
 
    payees: [],

    innova: false,

    editable:[],

  },

  created() {
    this.getRecords();
    this.getPayees();
  },

  computed: {},

  mounted() {},

  methods: {
    getRecords: function(kind) {
      let _this = this;
      const params = {
        action: 1,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("../api/access_control.php", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            if (kind === 1 || kind === undefined)
              _this.car_access1 = res.data[0]["car_access1"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 2 || kind === undefined)
              _this.car_access2 = res.data[0]["car_access2"].split(",").filter(function (el) {
                return el != "";
              });
         
              if (kind === 3 || kind === undefined)
            _this.innova = res.data[0]["innova"] != -1 ? true : false;

              if (kind === 4 || kind === undefined)
                _this.editable = res.data[0]["editable"].split(",").filter(function (el) {
                  return el != "";
                });

          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    save_innova: function() {
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("action", 4);
      form_Data.append("innova", this.innova == false ? -1 : 0);
 
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/access_control.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: "Update Success.",
            icon: "success",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    getPayees: function() {
      var form_Data = new FormData();
      let _this = this;
      this.action = 5; //select payee
      form_Data.append("action", this.action);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/add_or_edit_price_record.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          for (var i = 0; i < response.data.length; i++) {
            _this.payees.push(response.data[i].username);
          }
          console.log(_this.payees);
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    save: function(kind) {
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("action", 3);
      form_Data.append("car_access1", this.car_access1.toString());
      form_Data.append("car_access2", this.car_access2.toString());
      form_Data.append("editable", this.editable.toString());
   
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/access_control.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: "Update Success.",
            icon: "success",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    cancel: function(kind) {
      this.getRecords(kind);
    },

    reset: function() {
      this.getRecords();
    },
  },
});
