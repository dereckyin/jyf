Vue.component("v-select", VueSelect.VueSelect);
Vue.filter("dateString", function(value, format = "YYYY-MM-DD HH:mm:ss") {
  return moment(value).format(format);
});


Vue.config.ignoredElements = ['eng', 'cht']


var app = new Vue({
  el: "#app",
  data: {
    baseURL: "https://storage.cloud.google.com/feliiximg/",

    id: 0,

    payees: [],
    sales_date: "",
    sales_name: "",
    customer_name: "",

    product_name: "",
    qty: "",
    price: "",
    free: "",

    items: [],
    payments:[],


    total_amount:"",
    discount:"",
    invoice: "",
    payment_method: "",
    teminal: "",
    remark: "",

    keyword: "",

    myVar: null,
    lockVar: null,

    start_date: "",
    end_date: "",

    name: "",
    is_viewer: 0,
    mail_ip: "https://storage.googleapis.com/feliiximg/",

    amt_tw: 0.0,
    amt_php: 0.0,
    amt_total: 0.0,
    amt_over: 0.0,

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],
    page: 1,
    pages: [],
    perPage: 10000,

    editing: false,
    e_id : 0,

    record: {},
    receive_record: {},
    item: {},
    is_locked: false,

    is_edited: false,

    receive_date: "",
    payment_method: "",
    account_number:"",
    check_details: "",
    receive_amount: "",

  },

  created() {
    this.setupRecord();
    this.getMonthDay();
  
    this.getRecords();
    // this.getPayees();
    this.get_today();
  },
  mounted() {},

  watch: {


    handler(val, oldval) {
      console.log("value changed~");
    },
    deep: true,
  },
  component: {},

  methods: {

    setupRecord() {
        this.record = {
            id: 0,
            client_name: '',
            payee_name:'',
            amount: '',
            rate_yahoo: '',
            rate: '',
            amount_php: '',
            details: [],
            total_receive: '',
            overpayment : '',
            remark: '',
            pay_date : '',
            payee: '',
            status: '',
        }
    },

    calculate_total_amount: function() {

      this.record.overpayment = (Number(this.record.total_receive) - (this.record.amount_php == '' ? 0.0 : Number(this.record.amount_php))).toFixed(2);

      if(this.record.overpayment <= 0.0) {
        this.record.overpayment = '';
      }
    },

    calculate_total: function() {
      let amount = 0.0;
      for (i = 0; i < this.record.details.length; i++) {
          amount += this.record.details[i].receive_amount == '' ? 0.0 : Number(this.record.details[i].receive_amount);
      }

      this.record.total_receive = amount.toFixed(2);

      this.record.overpayment = (amount - (this.record.amount_php == '' ? 0.0 : Number(this.record.amount_php))).toFixed(2);

      if(this.record.overpayment <= 0.0) {
        this.record.overpayment = '';
      }
    },

    save_item: function() {
      var element = this.record.details.find(({ id }) => id === this.e_id);

      element.receive_date = this.receive_date;
      element.payment_method = this.payment_method;
      element.account_number = this.account_number;
      element.check_details = this.check_details;
      element.receive_amount = this.receive_amount;

      this.clear_payment()

      this.editing = false;
      this.e_id = 0;
    },

    clear_item: function() {
      this.receive_date = '';
      this.payment_method = '';
      this.account_number = '';
      this.check_details = '';
      this.receive_amount = '';

      this.editing = false;
      this.e_id = 0;
    },

    del_plus_detail : function(id) {
      var index = this.record.details.findIndex(x => x.id ===id);
      if (index > -1) {
        this.record.details.splice(index, 1);
      }

      this.calculate_total();
    },

    edit_plus_detail : function(eid) {
      var element = this.record.details.find(({ id }) => id === eid);

      this.receive_date = element.receive_date;
      this.payment_method = element.payment_method;
      this.account_number = element.account_number;
      this.check_details = element.check_details;
      this.receive_amount = element.receive_amount;

      this.editing = true;
      this.e_id = eid;
    },

    add_plus_detail: function() {
      let order = 1;
      var details = this.record.details;

      if(this.payment_method == '')
        return;

      if(this.account_number == '')
        return;

      if(details.length != 0)
      {
        let max = 0;
        for(let i = 0; i < details.length; i++)
        {
          if(details[i].id > max)
            max = details[i].id;

        }
        order = max + 1;
      }
        
      
      obj = {
        "id" : order,
        "receive_date" : this.receive_date,
        "payment_method" : this.payment_method,
        "account_number": this.account_number,
        "check_details": this.check_details,
        "receive_amount": this.receive_amount,
      }, 

      details.push(obj);

      this.clear_payment();
    },

    clear_payment: function() {
      this.receive_date = "";
      this.payment_method = "";
      this.account_number = "";
      this.check_details = "";
      this.receive_amount = "";

      this.calculate_total();
    },

    apply: function() {

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("payment", JSON.stringify(this.record));
      form_Data.append("id", this.record.id);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/details_ntd_php_add.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          _this.reload();
          
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reload();
        });

    },
    
    selectByDate: function() {
      this.action = 4; //select by date
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("action", this.action);
      form_Data.append("start_date", this.start_date);
      form_Data.append("end_date", this.end_date);
      form_Data.append("category", this.category);
      form_Data.append("sub_category", this.sub_category);
      form_Data.append("project_name", this.project_name);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.items = response.data;
          console.log(_this.items);
          this.displayedPosts();
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
        url: "api/add_or_edit_price_record_salary",
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
    
    deleteRecord: function(id) {
      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          let _this = this;

          form_Data.append("jwt", token);
          form_Data.append("id", id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/details_ntd_php_del.php",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              //_this.items = response.data
              _this.reload();
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: JSON.stringify(response),
                icon: "error",
                confirmButtonText: "OK",
              });
              _this.reload();
            });
          
        } else {
          return;
        }
      });
    },
    lockRecord: function(id) {
      let _this = this;
      _this.clear();
      _this.edit(id);
      _this.action = 8; //lock
      var token = localStorage.getItem("token");
      var form_Data = new FormData();

      _this.lockVar = setTimeout(function() {
        if (_this.is_locked == 0) {
          $locked = 1;
        } else {
          $locked = 0;
        }
        form_Data.append("jwt", token);
        form_Data.append("id", id);
        form_Data.append("action", _this.action);
        form_Data.append("updated_by", _this.name);
        form_Data.append("is_locked", $locked);
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/add_or_edit_price_record_salary",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            //_this.items = response.data
            console.log(response.data);
          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: "error",
              confirmButtonText: "OK",
            });
          });
      }, 500);
      _this.reload();
    },
    sliceDate: function(str) {
      var mdy = str.slice(0, 10);
      return mdy;
    },
    printRecord: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("start_date", this.start_date);
      form_Data.append("end_date", this.end_date);
      form_Data.append("keyword", this.keyword);

      axios({
        method: "post",
        url: "api/details_ntd_php_print.php",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "details_ntd_php.xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
    },

    editRow(row) {
      if(this.is_edited == true) {
        return;
      }

      this.id = row.id;
      this.record = row;

      this.is_edited = true;
      row.is_edited = 0;
    },

    confirmRow(row) {
      this.apply();
      row.is_edited = 1;
      this.is_edited = false;
    },

    cancelRow (row) {
      row.is_edited = 1;
      this.is_edited = false;
    },

    edit(row) {
      if(this.is_edited == true) {
        return;
      }

      this.id = row.id;
      this.record = row;

      this.is_edited = true;
      row.is_edited = 0;
    },
 
 
    setPages: function() {
      //console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.items.length / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
      console.log(this.pages);
    },

    paginate: function(posts) {
      //console.log('paginate');
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      this.items = this.items.slice(from, to);
    },

    getRecords: function() {
      let _this = this;
      _this.clear();
      _this.amt_tw = 0.0;
      _this.amt_php = 0.0;
      _this.amt_total = 0.0;
      _this.amt_over = 0.0;

      const params = {
        start_date: _this.start_date,
        end_date: _this.end_date,
        keyword: _this.keyword,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/details_ntd_php.php", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
       
            _this.items = res.data;
    
            _this.items.forEach((element, index) => {
              if(element.status != "-1") {
                _this.amt_tw += parseFloat(element.amount == '' ? 0 : element.amount);
                _this.amt_php += parseFloat(element.amount_php == '' ? 0 : element.amount_php);
                _this.amt_total += parseFloat(element.total_receive == '' ? 0 : element.total_receive);
                _this.amt_over += parseFloat(element.overpayment == '' ? 0 : element.overpayment);
              }
            });
     
            this.displayedPosts();
          },
          (err) => {
            alert(err.res);
          }
        )
        .finally(() => {});
    },
    displayedPosts: function() {
      //this.setPages();
      //return this.paginate(this.items);
      return this.items;
    },
    
    getUserName: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/on_duty_get_myname",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.name = response.data.username;

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

    logout: function() {
      Swal.fire({
        title: "Logout",
        text: "Are you sure to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          setTimeout(function(){
            window.location.href="index.php";
          },500);
        }
      });
    },

    get_today: function() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      
      this.paid_date = yyyy + '-' + mm + '-' + dd;
    },

    reset: function() {
      this.setupRecord();
      this.getRecords();      
      this.get_today();
      this.submit = false;
    },

    reload: function() {
       this.reset();
   
    },

    clear: function() {
      let _this = this;
      this.is_edited = false;
      clearTimeout(_this.myVar);
      clearTimeout(_this.lockVar);
    },
   
    getMonthDay: function() {
      let _this = this;
      var today = new Date();
      var first = new Date();
      var dd = ("0" + today.getDate()).slice(-2);
      var mm = ("0" + (today.getMonth() + 1)).slice(-2);
      var yyyy = today.getFullYear();
      today = yyyy + "-" + mm + "-" + dd;
      first = yyyy + "-" + mm + "-01";
      _this.file_day = yyyy + mm + dd;
      _this.start_date = first;
      _this.end_date = today;
    },

    scrollMeTo(refName) {
        var element = this.$refs[refName];
        element.scrollIntoView({ behavior: 'smooth' });
    },
  },
});
