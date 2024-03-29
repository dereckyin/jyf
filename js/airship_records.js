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

    ratio : 0.0,

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

    total: 0.0,
    amount: 0.0,
    amount_php: 0.0,
    
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

    editing_php: false,
    e_id_php : 0,

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

    title_ntd: "",
    qty_ntd: "",
    price_ntd: "",

    title_php: "",
    qty_php: "",
    price_php: "",
    date_receive: "",
    customer: "",
    address: "",
    description: "",
    quantity: "",
    kilo: "",
    supplier: "",
    flight: "",
    flight_date: "",
    currency: "",
    total: "",
    pay_date: "",
    pay_status: "",
    mode : "",

    amount_php: "",
    amount: "",

    total_php:0,

    details: [],
    details_php: [],

    payee: "",
    receiver: "",
    date_arrive: "",

    // paging
    page: 1,
    pages: [],
    cnt : 0,

    rec_total : 0.0,
    rec_amount : 0.0,
    rec_amount_php : 0.0,
    rec_kilo : 0.0,

    rec_ntd : 0.0,
    rec_php : 0.0,

    date_type : "r",

    edit_once_kilo : false,
    edit_once_currency : false,
    edit_once_mode : false,

    space : '',

    payment: [],
    // export
    detail_id:0,

    exp_dr:"",
    exp_date:"",
    exp_sold_to:"",
    exp_quantity:"",
    exp_unit:"",
    exp_discription:"",
    exp_amount:"",

    adv:"",

    assist_by:"",

    export_record: {},

    export_discription_org: "",
    exp_amount_org: "",
    payment_record: [],

    org_item : {},

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

    kilo: function() {
      if(this.edit_once_kilo == true)
      {
        this.edit_once_kilo = false;
        return;
      }
      this.refresh_kilo();
    },

    ratio: function() {
 
      if(this.kilo != ""){
        if(this.currency == 'NTD')
        {
          this.total = this.kilo * this.ratio;
        }
          

        if(this.currency == 'PHP')
        {
          this.total = this.kilo * this.ratio;
        }
          
      }
    },

    currency: function() {
      if(this.edit_once_currency == true)
      {
        this.edit_once_currency = false;
        return;
      }
      if(this.kilo != ""){
        if(this.currency == 'NTD')
        {
          //if(this.ratio == 0.0)
            this.ratio = 395;
          this.total = this.kilo * this.ratio;
        }
          

        if(this.currency == 'PHP')
        {
          //if(this.ratio == 0.0)
            this.ratio = 750;
          this.total = this.kilo * this.ratio;
        }
          
      }
    },

    mode: function() {
      if(this.edit_once_mode == true)
      {
        this.edit_once_mode = false;
        return;
      }

      if(this.mode == '')
      {
        this.details = [];

        obj = {
          "id" : 1,
          "title" : "空運費",
          "qty" : "",
          "price": 0,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 2,
          "title" : "過檢費",
          "qty" : "",
          "price": 2,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 3,
          "title" : "倉租費",
          "qty" : "",
          "price": 6,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 4,
          "title" : "文件費",
          "qty" : "",
          "price": 500,
        }, 
        this.details.push(obj);

        obj = {
          "id" : 5,
          "title" : "連線費",
          "qty" : "",
          "price": 200,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 6,
          "title" : "空運傳輸費",
          "qty" : "",
          "price": 150,
        }, 
        this.details.push(obj);
  
        this.calculate_total();
      }
      else if(this.mode == 'exp')
      {
        this.details = [];
        
        obj = {
          "id" : 1,
          "title" : "空運費",
          "qty" : "",
          "price": 131,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 2,
          "title" : "卡車費",
          "qty" : "",
          "price": 500,
        }, 
        this.details.push(obj);
  
        obj = {
          "id" : 3,
          "title" : "套袋費",
          "qty" : "",
          "price": 50,
        }, 
        this.details.push(obj);

        this.calculate_total();
      }

    },

  },
  component: {},

  methods: {

    setupRecord() {
      this.id = 0;

      this.details = [];
      this.details_php = [];

      this.date_receive = "";
      this.customer = "";
      this.address = "";
      this.description = "";
      this.quantity = "";
      this.kilo = "";
      this.supplier = "";
      this.flight = "";
      this.flight_date = "";
      this.currency = "";
      this.mode = "";
      this.total = "";
      this.total_php = "";
      this.pay_date = "";
      this.pay_status = "";
      this.payee = "";
      this.date_arrive = "";
      this.receiver = "";
      this.remark = "";
      this.amount = "";
      this.amount_php = "";

      obj = {
        "id" : 1,
        "title" : "空運費",
        "qty" : "",
        "price": 0,
      }, 
      this.details.push(obj);

      obj = {
        "id" : 2,
        "title" : "過檢費",
        "qty" : "",
        "price": 2,
      }, 
      this.details.push(obj);

      obj = {
        "id" : 3,
        "title" : "倉租費",
        "qty" : "",
        "price": 6,
      }, 
      this.details.push(obj);

      obj = {
        "id" : 4,
        "title" : "文件費",
        "qty" : "",
        "price": 500,
      }, 
      this.details.push(obj);

      obj = {
        "id" : 5,
        "title" : "連線費",
        "qty" : "",
        "price": 200,
      }, 
      this.details.push(obj);

      obj = {
        "id" : 6,
        "title" : "空運傳輸費",
        "qty" : "",
        "price": 150,
      }, 
      this.details.push(obj);


      this.calculate_total();

      obj = {
        "id" : 1,
        "title" : "Broker Charge",
        "qty" : "",
        "price": "",
      }, 
      this.details_php.push(obj);

      this.calculate_total_php();
    },

    calculate_total_amount: function() {

      this.record.overpayment = (Number(this.record.total_receive) - (this.record.amount_php == '' ? 0.0 : Number(this.record.amount_php))).toFixed(2);

      if(this.record.overpayment <= 0.0) {
        this.record.overpayment = '';
      }
    },

    refresh_kilo: function() {
      //var element = this.details.find(({ title }) => title === '空運費');
      //element.qty = this.kilo;
      //var element = this.details.find(({ title }) => title === '過檢費');
      //element.qty = this.kilo;

      //var element = this.details_php.find(({ title }) => title === 'Broker Charge');
      //element.qty = this.kilo;

      if(this.currency == 'NTD')
      {
        if(this.ratio == 0.0)
          this.ratio = 395;
        this.total = this.kilo * this.ratio;
      }

      if(this.currency == 'PHP')
      {
        if(this.ratio == 0.0)
          this.ratio = 750;
        this.total = this.kilo * this.ratio;
      }
        
      this.calculate_total();
      this.calculate_total_php();
    },

    calculate_total: function() {
      let amount = 0.0;
      for (i = 0; i < this.details.length; i++) {
          amount += (this.details[i].qty == '' ? 0.0 : Number(this.details[i].qty)) * (this.details[i].price == '' ? 0.0 : Number(this.details[i].price));
      }

      this.amount = amount.toFixed(2);

    },

    calculate_total_php: function() {
      let amount = 0.0;
      for (i = 0; i < this.details_php.length; i++) {
          amount += (this.details_php[i].qty == '' ? 0.0 : Number(this.details_php[i].qty)) * (this.details_php[i].price == '' ? 0.0 : Number(this.details_php[i].price));
      }

      this.amount_php = amount.toFixed(2);

    },

    save_item: function() {
      var element = this.details.find(({ id }) => id === this.e_id);

      element.title = this.title_ntd;
      element.qty = this.qty_ntd;
      element.price = this.price_ntd;
   
      this.clear_payment()

      this.editing = false;
      this.e_id = 0;
    },

    save_item_php: function() {
      var element = this.details_php.find(({ id }) => id === this.e_id_php);

      element.title = this.title_php;
      element.qty = this.qty_php;
      element.price = this.price_php;
   
      this.clear_payment_php()

      this.editing_php = false;
      this.e_id_php = 0;
    },

    clear_item: function() {
      this.title_ntd = '';
      this.qty_ntd = '';
      this.price_ntd = '';
     
      this.editing = false;
      this.e_id = 0;
    },

    clear_item_php: function() {
      this.title_php = '';
      this.qty_php = '';
      this.price_php = '';
     
      this.editing_php = false;
      this.e_id_php = 0;
    },

    del_plus_detail : function(item) {
      var index = this.details.findIndex(x => x.id ===item.id);
      if (index > -1) {
        this.details.splice(index, 1);
      }

      this.calculate_total();
    },

    del_plus_detail_php : function(item) {
      var index = this.details_php.findIndex(x => x.id ===item.id);
      if (index > -1) {
        this.details_php.splice(index, 1);
      }

      this.calculate_total_php();
    },

    edit_plus_detail : function(item) {
     
      this.title_ntd = item.title;
      this.qty_ntd = item.qty;
      this.price_ntd = item.price;

      this.editing = true;
      this.e_id = item.id;
    },

    edit_plus_detail_php : function(item) {
     
      this.title_php = item.title;
      this.qty_php = item.qty;
      this.price_php = item.price;

      this.editing_php = true;
      this.e_id_php = item.id;
    },

    del_plus_payment_detail : function(id) {
      var index = this.payment.findIndex(x => x.id ===id);
      if (index > -1) {
        this.payment.splice(index, 1);
      }
    },

    add_plus_payment_detail: function() {
      let order = 1;
      if(this.payment.length != 0)
      {
        let max = 0;
        for(let i = 0; i < this.payment.length; i++)
        {
          if(this.payment[i].id > max)
            max = this.payment[i].id;

        }
        order = max + 1;
      }
        
      
      obj = {
        "id" : 0,
        "detail_id" : this.detail_id,
        "type" : 1,
        "issue_date" : '',
        "payment_date" : '',
        "person": '',
        "amount": '',
        "change": '',
        "courier" : '',
        "remark": '',
        "is_selected": 1,
      }, 

      this.payment.push(obj);
    },
  
    chang_remark: function(row) {
      if(row.amount == '')
        return;
      // let charge = this.payment_record.charge;
      let charge = this.ar;
      let pay = 0;
      for(let i = 0; i < this.payment.length; i++)
        pay += (this.payment[i].amount == "" ? 0 : Number(this.payment[i].amount)) - (this.payment[i].courier == "" ? 0 : Number(this.payment[i].courier));
      if(charge - pay < 0 && row.type == 1)
      {
        row.remark = "Cash " + row.amount + " - " + (Number(charge) - Number(pay) + Number(row.amount)).toFixed(2) + " = P" + Math.abs(charge - pay).toFixed(2) + " (Change)";
        row.change = Math.abs(charge - pay).toFixed(2);
      }
      else
      {
        row.remark = '';
        row.change = '';
      }
    },

    add_plus_detail: function() {
      let order = 1;
      var details = this.details;

      if(this.title_ntd == '')
        return;

      if(this.qty_ntd == '')
        return;

      if(this.price_ntd == '')
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
        "title" : this.title_ntd,
        "qty" : this.qty_ntd,
        "price": this.price_ntd,
        
      }, 

      this.details.push(obj);

      this.clear_payment();
    },

    add_plus_detail_php: function() {
      let order = 1;
      var details = this.details_php;

      if(this.title_php == '')
        return;

      if(this.qty_php == '')
        return;

      if(this.price_php == '')
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
        "title" : this.title_php,
        "qty" : this.qty_php,
        "price": this.price_php,
        
      }, 

      this.details_php.push(obj);

      this.clear_payment_php();
    },

    clear_payment: function() {
      this.title_ntd = "";
      this.qty_ntd = "";
      this.price_ntd = "";
    
      this.calculate_total();
    },

    clear_payment_php: function() {
      this.title_php = "";
      this.qty_php = "";
      this.price_php = "";
    
      this.calculate_total_php();
    },

    apply: function() {

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("id", this.id);
      form_Data.append("date_receive", this.date_receive);
      form_Data.append("mode", this.mode);
      form_Data.append("customer", this.customer);
      form_Data.append("address", this.address);
      form_Data.append("description", this.description);
      form_Data.append("quantity", this.quantity);
      form_Data.append("kilo", this.kilo);
      form_Data.append("supplier", this.supplier);
      form_Data.append("flight", this.flight);
      form_Data.append("flight_date", this.flight_date);
      form_Data.append("currency", this.currency);
      form_Data.append("total", this.total);
      form_Data.append("ratio", this.ratio);
   
      form_Data.append("pay_date", this.pay_date);
      form_Data.append("pay_status", this.pay_status);
      form_Data.append("payee", this.payee);
      form_Data.append("date_arrive", this.date_arrive);
      form_Data.append("receiver", this.receiver);
      form_Data.append("remark", this.remark);
      form_Data.append("amount", this.amount);
      form_Data.append("amount_php", this.amount_php);

      form_Data.append("details", JSON.stringify(this.details));
      form_Data.append("details_php", JSON.stringify(this.details_php));
    

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/airship_record_add.php",
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

          // _this.reload();
        });

    },

    show_ntd: function(item) {
      this.record = item.items;
      $('#details_NTD').modal('show');
    },

    show_php: function(item) {
      this.record = item.items_php;
      $('#details_PHP').modal('show');
    },
    
    selectByDate: function() {
      this.action = 4; //select by date
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("action", this.action);
      form_Data.append("start_date", this.start_date);
      form_Data.append("date_type", this.date_type);
      form_Data.append("end_date", this.end_date);
      form_Data.append("category", this.category);
      form_Data.append("sub_category", this.sub_category);
      form_Data.append("project_name", this.project_name);
      form_Data.append("space", this.space);
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
    
    deleteRecord: function(item) {
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
          form_Data.append("id", item.id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/airship_record_del.php",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              //_this.items = response.data
              _this.reset_no_toggle();
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: JSON.stringify(response),
                icon: "error",
                confirmButtonText: "OK",
              });
              _this.reset_no_toggle();
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
      form_Data.append("date_type", this.date_type);
      form_Data.append("end_date", this.end_date);
      form_Data.append("page", this.page);

      axios({
        method: "post",
        url: "api/airship_record_print.php",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "airship_record.xlsx");

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

      if(this.currency !== row.currency)
        this.edit_once_currency = true;
      else
        this.edit_once_currency = false;


      if(this.kilo !== row.kilo)
        this.edit_once_kilo = true;
      else
        this.edit_once_kilo = false;

      if(this.mode !== row.mode)
        this.edit_once_mode = true;
      else
        this.edit_once_mode = false;
      
      this.id = row.id;
      
      this.date_receive = row.date_receive;
      this.mode = row.mode;
      this.customer = row.customer;
      this.address = row.address;
      this.description = row.description;
      this.quantity = row.quantity;
      this.kilo = row.kilo;
      this.supplier = row.supplier;
      this.flight = row.flight;
      this.flight_date = row.flight_date;
      this.currency = row.currency;
      this.total = row.total;
      this.ratio = row.ratio;
      this.total_php = row.total_php;
      this.pay_date = row.pay_date;
      this.pay_status = row.pay_status;
      this.payee = row.payee;
      this.date_arrive = row.date_arrive;
      this.receiver = row.receiver;
      this.remark = row.remark;
      this.amount = row.amount;
      this.amount_php = row.amount_php;

      this.details = row.items;
      this.details_php = row.items_php;

      this.is_edited = true;
      row.is_edited = 0;

      $('#collapseOne').toggle();
     
    },

    del(item) {

    },
 
 
    setPages: function() {
      //console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.cnt / this.perPage);

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
      _this.rec_total = 0.0;
      _this.rec_amount = 0.0;
      _this.rec_amount_php = 0.0;
      _this.rec_total_php = 0.0;
      _this.rec_kilo = 0.0;
      _this.rec_ntd = 0.0;
      _this.rec_php = 0.0;

 

      const params = {
        start_date: _this.start_date,
        end_date: _this.end_date,
        date_type: _this.date_type,
        keyword: _this.keyword,
        page: _this.page,
        space: _this.space,
      };

      let token = localStorage.getItem("accessToken");

      this.cnt = 0;

      axios
        .get("api/airship_record.php", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
       
            _this.items = res.data;
    
            _this.items.forEach((element, index) => {
              if(element.status != "-1") {
                _this.rec_total += parseFloat(element.total == '' ? 0 : element.total);
                _this.rec_amount += parseFloat(element.amount == '' ? 0 : element.amount);
                _this.rec_amount_php += parseFloat(element.amount_php == '' ? 0 : element.amount_php);
                _this.rec_kilo += parseFloat(element.kilo == '' ? 0 : element.kilo);

                if(element.currency == 'NTD')
                  _this.rec_ntd += parseFloat(element.total == '' ? 0 : element.total);
                if(element.currency == 'PHP')
                  _this.rec_php += parseFloat(element.total == '' ? 0 : element.total);
              }
            });

            if(_this.items.length > 0) 
              _this.cnt = _this.items[0].cnt;
            else
              _this.cnt = 0;
     
            _this.displayedPosts();
          },
          (err) => {
            alert(err.res);
          }
        )
        .finally(() => {});
    },

    displayedPosts: function() {
      this.setPages();
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
      $('#collapseOne').toggle();
    },

    reset_no_toggle: function() {
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
      var d = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      var yyyy = today.getFullYear();
      today = yyyy + "-" + mm + "-" + dd;
      first = yyyy + "-" + mm + "-01";
      end = yyyy + "-" + mm + "-" + d.getDate();
      _this.file_day = yyyy + mm + dd;
      _this.start_date = first;
      _this.end_date = end;
      this.space = "";
    },
    
    getPeriod: function(month) {
      let _this = this;
      var today = new Date();
      var first = new Date();
      var dd = ("01").slice(-2);
      var mm = (month).slice(-2);
      var d = new Date(today.getFullYear(), parseInt(month), 0);
      var yyyy = today.getFullYear();
      today = yyyy + "-" + mm + "-" + dd;
      first = yyyy + "-" + mm + "-01";
      end = yyyy + "-" + mm + "-" + d.getDate();
      _this.file_day = yyyy + mm + dd;
      _this.start_date = first;
      _this.end_date = end;
      this.space = "";

      _this.reset_no_toggle();
    },

    getSpace: function(space) {
      this.space = space

      this.reset_no_toggle();
    },


    scrollMeTo(refName) {
        var element = this.$refs[refName];
        element.scrollIntoView({ behavior: 'smooth' });
    },

    async get_export(detail_id) {
      let _this = this;

      const params = {
        measure_id: detail_id,
      
      };

      let token = localStorage.getItem("accessToken");

      try {
        let res = await axios.get("api/airship_get_export.php", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.export_record = res.data;
      } catch (err) {
        console.log(err)
        alert('error')
      }
    },

    export_save: async function(print) {
      let _this = this;

      var form_Data = new FormData();

      form_Data.append('id', this.detail_id);
      form_Data.append('exp_dr', this.exp_dr)
      form_Data.append('assist_by', this.assist_by)
      form_Data.append('exp_date', this.exp_date)
      form_Data.append('exp_sold_to', this.exp_sold_to)
      form_Data.append('exp_quantity', this.exp_quantity)
      form_Data.append('exp_unit', this.exp_unit)
      form_Data.append('exp_discription', this.exp_discription)
      form_Data.append('exp_amount', this.exp_amount)
      form_Data.append('payment', JSON.stringify(this.payment))
      form_Data.append('record', JSON.stringify(this.payment_record))

      form_Data.append('adv', this.adv)
      form_Data.append('print', print)
    
      if(this.submit == true)
            return;

          this.submit = true;
    
          try {
            if(print == '')
            {
              let res = await axios({
                method: 'post',
                url: 'api/airship_set_payment.php',
                data: form_Data,
                
                headers: {
                  "Content-Type": "multipart/form-data",
                },
            });
            }

            if(print == 'Y')
            {
              let response = await axios({
                method: 'post',
                url: 'api/airship_set_payment.php',
                data: form_Data,
                responseType: 'blob', // important
                headers: {
                  "Content-Type": "multipart/form-data",
                },
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
          
              link.setAttribute('download', 'Airship_Payment_Receipt' + (_this.exp_dr !== '' ? '_' + _this.exp_dr : '') + '.docx');
          
            document.body.appendChild(link);
            link.click();

            _this.reset_no_toggle();
            }
            
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.submit = false;
  },



    item_export: async function(item) {

      let _this = this;

      this.org_item = JSON.parse(JSON.stringify(item));

      let detail_id = item.id;

      this.export_record = {};
      this.exp_date = '';
      this.exp_quantity = '';
      this.exp_unit = '';
      this.adv = '';

      await this.get_export(detail_id);

      this.payment = [];
      this.payment_record = [];

      let rec = {
        id: 0,
        is_selected: 1,
        date_receive: item.date_receive,
        customer: item.customer,
        description: item.description,
        quantity: item.quantity,
        supplier: item.supplier,
      }

      if(this.export_record.length > 0) {
        if(this.export_record[0].record !== undefined)
          this.payment_record = JSON.parse(this.export_record[0].record);

        //this.payment = [].concat(record);
        if(this.export_record[0].payment !== undefined)
          this.payment = JSON.parse(this.export_record[0].payment);
        for(const element of this.payment) {
          element.is_selected = 1;
        }
      }
      else
        this.payment_record.push(rec);

      this.detail_id = detail_id;

      this.exp_dr = "";
      this.exp_date = "";
      this.exp_sold_to = item.customer;
      this.assist_by = "";

      this.exp_quantity = "";
      this.exp_unit = "";
      if(item.currency == 'NTD')
        this.exp_discription = item.kilo + " kilo @ NT " + item.ratio;
      if(item.currency == 'PHP')
        this.exp_discription = item.kilo + " kilo @ P " + item.ratio;

      kilo_price = 0;
      item.kilo !== "" ? kilo_price = item.kilo * item.ratio : kilo_price = 0;

      

      if(item.currency == 'NTD')
        this.exp_amount = 'NT ' + Number(kilo_price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      if(item.currency == 'PHP')
        this.exp_amount = '₱ ' + Number(kilo_price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

      this.exp_amount_org = this.exp_amount;

      this.ar = kilo_price;

      // // warehouse fee
      // if(item.warehouse_fee !== '')
      // {
      //   this.exp_amount = this.exp_amount + '\n' + '₱ ' + Number(item.warehouse_fee).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      // }
      
      // kilo_price = 0;
      // cuft_price = 0;

      // item.kilo_price !== "" ? kilo_price = item.kilo_price : kilo_price = (item.kilo < 3000 ? 36.5 : 34.5);
      // item.cuft_price !== "" ? cuft_price = item.cuft_price : cuft_price = (item.cuft < 300 ? 385 : 365);

      // nkilo = kilo_price * (item.kilo == "" ? 0 : item.kilo);
      // ncuft = cuft_price * (item.cuft == "" ? 0 : item.cuft);
      // charge = (ncuft > nkilo) ? Number(item.cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_price).toFixed(2) : Number(item.kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_price).toFixed(2);
      // this.exp_discription = charge;

      // this.exp_discription_org = charge;

      // // warehouse charge
      // warehouse_charge = "";
      // if(item.warehouse_fee !== '')
      // {
      //     if(item.way == "kilo")
      //     {
      //       warehouse_charge = Number(item.kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_unit).toFixed(2);
      //     }

      //     if(item.way == "cuft")
      //     {
      //       warehouse_charge = Number(item.cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_unit).toFixed(2);
      //     }
      // }

      // if(warehouse_charge !== "")
      // {
      //   this.exp_discription = this.exp_discription + '\n' + warehouse_charge + ' Warehouse Fee';
      // }

      if(this.export_record.length > 0)
      {
        this.exp_dr = this.export_record[0].exp_dr;
        this.exp_date = this.export_record[0].exp_date;
        this.exp_sold_to = this.export_record[0].exp_sold_to;
        this.exp_quantity = this.export_record[0].exp_quantity;
        this.exp_unit = this.export_record[0].exp_unit;
        //this.exp_discription = this.export_record[0].exp_discription;
        //this.exp_amount = this.export_record[0].exp_amount;

        this.assist_by = this.export_record[0].assist_by;

        this.adv = this.export_record[0].adv;

        // this.payment = this.export_record[0].payment;
        // this.payment_record = this.export_record[0].record;

        // for(const element of JSON.parse(this.export_record[0].payment)) {
        //   var result  = this.payment.filter(function(o){return o.id == element.id;} );
        //   if(result.length > 0)
        //     result[0].is_selected = element.is_selected;
        // }

        // for(const element of JSON.parse(this.export_record[0].record)) {
        //   var result  = this.payment_record.filter(function(o){return o.id == element.id;} );
        //   if(result.length > 0)
        //     result[0].is_selected = element.is_selected;
        // }
      }

      if(this.org_item.customer != this.exp_sold_to) {

       let result = await Swal.fire({
          title: "Check",
          text: "Value in Column “Customer” is different from the value in Column “SOLD TO”. Would you want to replace “SOLD TO” by the value in Column “Customer”?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        });

          if (result.value) {
            this.exp_sold_to = this.org_item.customer;
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
    

      }

      if(this.org_item.kilo.trim() != this.exp_discription.split(' kilo @')[0].trim()) {
         result = await Swal.fire({
          title: "Check",
          text: "Value in Column “Kilo” is different from the weight in Column “Description”. Would you want to update “Description” and “Amount” by the value in Column “Kilo”?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        });

          if (result.value) {
            if(item.currency == 'NTD')
              this.exp_discription = item.kilo + " kilo @ NT " + item.ratio;
            if(item.currency == 'PHP')
              this.exp_discription = item.kilo + " kilo @ P " + item.ratio;
            kilo_price = 0;
            item.kilo !== "" ? kilo_price = item.kilo * item.ratio : kilo_price = 0;
            //this.exp_amount = '₱ ' + Number(kilo_price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            if(item.currency == 'NTD')
              this.exp_amount = 'NT ' + Number(kilo_price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            if(item.currency == 'PHP')
              this.exp_amount = '₱ ' + Number(kilo_price).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
      

      }
    },
  },
});
