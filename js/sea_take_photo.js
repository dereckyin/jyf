Vue.component("date-picker", {
  template: "<input/>",
  props: ["dateFormat"],
  mounted: function () {
    var self = this;
    $(this.$el).datepicker({
      dateFormat: "yy/mm/dd",
      showOn: "button",
      buttonImage: "images/calendar.png",
      buttonImageOnly: true,
      buttonText: "",
      onSelect: function (date) {
        self.$emit("update-date", date);
      },
    });
  },
  beforeDestroy: function () {
    $(this.$el).datepicker("hide").datepicker("destroy");
  },
});

Vue.component("edit-date-picker", {
  template: "<input/>",
  props: ["dateFormat"],
  mounted: function () {
    var self = this;
    $(this.$el).datepicker({
      dateFormat: "yy/mm/dd",
      showOn: "button",
      buttonImage: "images/calendar.png",
      buttonImageOnly: true,
      buttonText: "",
      onSelect: function (date) {
        self.$emit("update-date", date);
      },
    });
  },
  beforeDestroy: function () {
    $(this.$el).datepicker("hide").datepicker("destroy");
  },
});

/*
Vue.component('date-picker', {
template: '\
  <input class="form-control datepicker"\
        ref="input"\
        v-bind:value="value"\
        v-on:input="updateValue($event.target.value)"\
        data-date-format="yyyy/mm/dd"\
        buttonImage: "images/calendar.png"\
        buttonImageOnly: true\
        buttonText: ""\
        data-date-end-date="0d"\
        placeholder="yyyy/mm/dd"\
        type="text"  />\
',

props: {
    value: {
      type: String,
      default: ""
    }
},

mounted: function() {
    let self = this;
    this.$nextTick(function() {
        $(this.$el).datepicker({
            startView: 1,
            todayHighlight: true,
            todayBtn: "linked",
            autoclose: true,
            format: "yyyy/mm/dd"
        })
        .on('changeDate', function(e) {
            var date = e.format('yyyy/mm/dd');
            self.updateValue(date);
        });
    });
},

methods: {
    updateValue: function (value) {
        this.$emit('input', value);
    },
}

});

*/

Vue.config.ignoredElements = ["eng"];

let mainState = {
  // edit state
  isEditing: false,

  // table
  clicked: 0,

  snap_me:false,

  // data
  is_checked: false,
  date_receive: "",
  customer: "",
  email: "",
  description: "",
  quantity: "",
  supplier: "",
  kilo: 0.0,
  cuft: 0.0,
  taiwan_pay: 0,
  courier_pay: 0,
  courier_money: 0,
  remark: "",
  picname: "",
  receive_records: [],
  record: {},
  file: "",

  // error
  error_date_receive: "",
  error_customer: "",
  error_email: "",
  error_cuft: "",
  error_courier_money: "",
  error_kilo: "",
  error_quantity: "",

  // pictures
  pic_list: [],
};

var app = new Vue({
  el: "#receive_record",

  data: mainState,

  mounted: function() {},

  created() {
  },

  watch: {

  },

  computed: {
    
  },

  updated: function () {
  },

  methods: {
    
    createReceiveRecord: function () {
      console.log("createReceiveRecord");

      let _this = this;

      var form_Data = new FormData();

      
      var count = 0;
      for (var i = 0; i < this.pic_list.length; i++)
      {
        if(this.pic_list[i].check)
        {
          form_Data.append("files" + count, this.pic_list[i].url);
          count = count + 1;
        }
      }
      form_Data.append("file_count", count);

      form_Data.append("date_receive", this.formatDate(document.querySelector("input[id=adddate]").value));
      form_Data.append("customer", this.customer);
      form_Data.append("email", this.email);
      form_Data.append("description", this.description);
      form_Data.append("quantity", this.quantity);
      form_Data.append("supplier", this.supplier);
      form_Data.append("kilo", this.kilo);
      form_Data.append("cuft", this.cuft);
      form_Data.append("taiwan_pay", this.taiwan_pay);
      form_Data.append("courier_pay", this.courier_pay);
      form_Data.append("courier_money", this.courier_money);
      form_Data.append("remark", this.remark);
      form_Data.append("file", this.file);
      form_Data.append("crud", "insert");
      form_Data.append("id", "");

      var receive_record = {};
      form_Data.forEach(function (value, key) {
        receive_record[key] = value;
      });

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/sea_take_photo.php",
        data: form_Data,
      })
        .then(function (response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        })
        .catch(function (response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    
    },


    createLibraryRecord: function () {
      console.log("createLibraryRecord");

      let _this = this;

      var form_Data = new FormData();

      var count = 0;
      for (var i = 0; i < this.pic_list.length; i++)
      {
        if(this.pic_list[i].check)
        {
          form_Data.append("files" + count, this.pic_list[i].url);
          count = count + 1;
        }
      }
      form_Data.append("file_count", count);

      form_Data.append("date_receive", this.formatDate(document.querySelector("input[id=adddate]").value));
      form_Data.append("customer", this.customer);
      form_Data.append("email", this.email);
      form_Data.append("description", this.description);
      form_Data.append("quantity", this.quantity);
      form_Data.append("supplier", this.supplier);
      form_Data.append("kilo", this.kilo);
      form_Data.append("cuft", this.cuft);
      form_Data.append("taiwan_pay", this.taiwan_pay);
      form_Data.append("courier_pay", this.courier_pay);
      form_Data.append("courier_money", this.courier_money);
      form_Data.append("remark", this.remark);
      form_Data.append("file", this.file);
      form_Data.append("crud", "insert_lib");
      form_Data.append("id", "");

      var receive_record = {};
      form_Data.forEach(function (value, key) {
        receive_record[key] = value;
      });

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/sea_take_photo.php",
        data: form_Data,
      })
        .then(function (response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        })
        .catch(function (response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    
    },
    
    resetForm: function () {
      console.log("resetForm");
      this.date_receive = "";
      this.customer = "";
      this.description = "";
      this.quantity = "";
      this.email = "";
      this.supplier = "";
      this.kilo = "";
      this.cuft = "";
      this.taiwan_pay = "";
      this.courier_pay = "";
      this.courier_money = "";
      this.remark = "";
      this.file = "";
      this.isEditing = false;
      this.record = {};
      this.pic_list = [];

      document.getElementById("base64image").src = "";
      $(window).scrollTop(0);

      this.resetError();
   
    },

    resetError: function () {
      console.log("resetError");
      this.error_date_receive = "";
      this.error_customer = "";
      this.error_email = "";
      this.error_courier_money = "";
      this.error_kilo = "";
      this.error_quantity = "";
    },


    append_pic() {
      if(this.snap_me == true) {
        this.snap_me = false;
      }
      else
        return;

      var file;

      if (document.getElementById("base64image") !== null)
        file = document.getElementById("base64image").src;
      else file = "";

      var obj = {
        check: '1',
        file: file,
        url: file,
      };

      this.pic_list.push(obj);
    },

    formatDate: function(date) {
      if(date !== '')
      {
          var d = new Date(date),
              month = '' + (d.getMonth() + 1),
              day = '' + d.getDate(),
              year = d.getFullYear();

          if (month.length < 2) 
              month = '0' + month;
          if (day.length < 2) 
              day = '0' + day;

          return [year, month, day].join('/');
      }
      else
      {
          return '';
      }
  },

  },
});
