Vue.component("v-select", VueSelect.VueSelect);
Vue.filter("dateString", function(value, format = "YYYY-MM-DD HH:mm:ss") {
  return moment(value).format(format);
});

Vue.config.ignoredElements = ['eng']

var app = new Vue({
  el: "#app",
  data: {
    baseURL: "https://storage.cloud.google.com/feliiximg/",

    id: 0,
    account: 1,
    account_name: "",
    category: "",
    sub_category: "",
    project_name: "",
    related_account: 0,
    details: "",

    pic_url: "",
    file: "",
    payee: [],
    paid_date: "",
    amount: 0,
    operation_type: 0,
    cash_in: 0.0,
    cash_out: 0.0,
    remarks: "",
    keyword: "",
    select_date_type: 0,
    select_category: 'All',
    select_sub_category: "",

    is_locked: false,
    is_enabled: true,
    is_marked: "x",
    action: 0,
    items: [],
    payees: [],
    fileArray: [],
    filename: [],
    edd: 0,

    del_array: [],

    pic_url_array: [],
    pic_url_array1: [],
    pic_url_array2: [],
    pic_url_array3: [],
    pic_url_array4: [],
    pic_url_array5: [],

    start_date: "",
    end_date: "",

    name: "",
    is_viewer: 0,
    mail_ip: "https://storage.googleapis.com/feliiximg/",

    allCashIn: 0.0,
    allCashOut: 0.0,
    allBalance: 0.0,

    accountOneCashIn: 0.0,
    accountOneCashOut: 0.0,
    accountOneBalance: 0.0,

    accountTwoCashIn: 0.0,
    accountTwoCashOut: 0.0,
    accountTwoBalance: 0.0,

    accountThreeCashIn: 0.0,
    accountThreeCashOut: 0.0,
    accountThreeBalance: 0.0,

    myVar: null,
    lockVar: null,
    index: 0,
    spa: [],

    file_day: "",

    split1: {
      account: 0,
      category: "",
      sub_category: "",
      project_name: "",
      related_account: 0,
      details: "",
      pic_url: "",
      payee: [],
      paid_date: "",
      amount: 0.0,
      cash_in: 0.0,
      cash_out: 0.0,
      remarks: "",
      filename: [],

      is_locked: false,
      is_enabled: true,
      is_marked: "x",
    },
    split2: {
      account: 0,
      category: "",
      sub_category: "",
      project_name: "",
      related_account: 0,
      details: "",
      pic_url: "",
      payee: [],
      paid_date: "",
      amount: 0.0,
      cash_in: 0.0,
      cash_out: 0.0,
      remarks: "",
      filename: [],

      is_locked: false,
      is_enabled: true,
      is_marked: "x",
    },
    split3: {
      account: 0,
      category: "",
      sub_category: "",
      project_name: "",
      related_account: 0,
      details: "",
      pic_url: "",
      payee: [],
      paid_date: "",
      amount: 0.0,
      cash_in: 0.0,
      cash_out: 0.0,
      remarks: "",
      filename: [],

      is_locked: false,
      is_enabled: true,
      is_marked: "x",
    },
    split4: {
      account: 0,
      category: "",
      sub_category: "",
      project_name: "",
      related_account: 0,
      details: "",
      pic_url: "",
      payee: [],
      paid_date: "",
      amount: 0.0,
      cash_in: 0.0,
      cash_out: 0.0,
      remarks: "",
      filename: [],

      is_locked: false,
      is_enabled: true,
      is_marked: "x",
    },
    split5: {
      account: 0,
      category: "",
      sub_category: "",
      project_name: "",
      related_account: 0,
      details: "",
      pic_url: "",
      payee: [],
      paid_date: "",
      amount: 0.0,
      cash_in: 0.0,
      cash_out: 0.0,
      remarks: "",
      filename: [],

      is_locked: false,
      is_enabled: true,
      is_marked: "x",
    },
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
    dismiss: "",
  },

  created() {
    this.getMonthDay();
    this.getUserName();
    this.getRecords();
    this.getPayees();
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
    getAllPriceRecord: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      this.action = 1; //select
      form_Data.append("jwt", token);
      form_Data.append("action", this.action);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary_sea.php",
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
    add: function(range, edd) {
      this.action = 2; //add
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      var paidat = this.sliceDate(this.paid_date).replace(/-/g, "/");
      var payee = this.payee.toString();
      if (edd == 1) {
        this.update(this.id, 0);
      } else {
        if (range == 1) {
          if (this.operation_type == 1) {
            this.cash_in = this.amount;
          } else {
            this.cash_out = this.amount;
          }

          if (
            this.category != "Marketing" &&
            this.category != "Office Needs" &&
            this.category != "Others" &&
            this.category != "Projects" &&
            this.category != "Store"
          ) {
            this.sub_category = "";
          }

          form_Data.append("jwt", token);
          form_Data.append("account", this.account);
          form_Data.append("category", this.category);
          form_Data.append("sub_category", this.sub_category);
          form_Data.append("project_name", this.project_name);
          form_Data.append("related_account", this.related_account);

          this.details = this.$refs.detail.value;
          form_Data.append("details", this.details.replaceAll("\n", "<br>"));



          var final_id = [];
          var final_array = [];
          var finals = document.getElementsByName("file_elements");
          var myArr = this.fileArray;

          for (var i = 0; i < finals.length; i++) {

            if (finals[i].checked) {
              myArr.forEach((element, index) => {
               
                var data = myArr[index];
                if(_this.file_day + data.name === finals[i].value)
                {
                  final_array.push(data);
                }

              });

              final_id.push(finals[i].value);
            }
          }

          this.filename = final_id;
          this.fileArray = final_array;



          form_Data.append("pic_url", this.filename);
          form_Data.append("payee", payee);
          form_Data.append("paid_date", paidat);
          form_Data.append("cash_in", this.cash_in);
          form_Data.append("cash_out", this.cash_out);
          form_Data.append("remarks", this.remarks);
          form_Data.append("is_locked", this.is_locked);
          form_Data.append("is_enabled", this.is_enabled);

          if (this.is_marked == "x") form_Data.append("is_marked", "0");
          else form_Data.append("is_marked", this.is_marked);

          form_Data.append("action", this.action);
          form_Data.append("created_by", this.name);
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/add_or_edit_price_record_salary_sea.php",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.items = response.data;
              console.log(_this.items);
              Swal.fire({
                text: "Add Success.",
                icon: "success",
                confirmButtonText: "OK",
              });
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: "Upload Error, Please contact engineer.",
                icon: "error",
                confirmButtonText: "OK",
              });
            });
          this.upload();
          this.reload();
        } else {
          _this.spa.push(_this.split1);
          _this.spa.push(_this.split2);
          _this.spa.push(_this.split3);
          _this.spa.push(_this.split4);
          _this.spa.push(_this.split5);
          _this.spa_total_amount =
            parseFloat(_this.split1.amount) +
            parseFloat(_this.split2.amount) +
            parseFloat(_this.split3.amount) +
            parseFloat(_this.split4.amount) +
            parseFloat(_this.split5.amount);
          if (this.spa_total_amount != this.amount) {
            _this.dismiss = "";
            alert("Total amount is not correct.");
            _this.spa = [];
            //_this.reset();
          } else {
            _this.dismiss = "modal";
            _this.clear();
            for (var i = 0; i < this.spa.length; i++) {
              if (this.spa[i].amount != 0) {
                if (_this.operation_type == 1) {
                  this.spa[i].cash_in = this.spa[i].amount;
                } else {
                  this.spa[i].cash_out = this.spa[i].amount;
                }
                form_Data.append("jwt", token);
                form_Data.append("account", this.account);
                form_Data.append("category", this.spa[i].category);
                form_Data.append("sub_category", this.spa[i].sub_category);
                form_Data.append("project_name", this.spa[i].project_name);
                form_Data.append("related_account", this.related_account);

                if (i === 0) this.spa[i].details = this.$refs.detail1.value;
                if (i === 1) this.spa[i].details = this.$refs.detail2.value;
                if (i === 2) this.spa[i].details = this.$refs.detail3.value;
                if (i === 3) this.spa[i].details = this.$refs.detail4.value;
                if (i === 4) this.spa[i].details = this.$refs.detail5.value;
                form_Data.append(
                  "details",
                  this.spa[i].details.replaceAll("\n", "<br>")
                );

                var final_id = [];
                var final_array = [];
                var myArr = this.fileArray;
                if (i === 0) 
                {
                  var finals = document.getElementsByName("file_elements1");

                  for (var j = 0; j < finals.length; j++) {

                    if (finals[j].checked) {
                      myArr.forEach((element, index) => {
                       
                        var data = myArr[index];
                        if(_this.file_day + data.name === finals[j].value)
                        {
                          final_array.push(data);
                        }
        
                      });
        
                      final_id.push(finals[j].value);
                    }
                  }
        
                  this.spa[i].filename = final_id;
                  this.fileArray = final_array;
                }

                if (i === 1)
                {
                  var finals = document.getElementsByName("file_elements2");

                  for (var j = 0; j < finals.length; j++) {

                    if (finals[j].checked) {
                      myArr.forEach((element, index) => {
                       
                        var data = myArr[index];
                        if(_this.file_day + data.name === finals[j].value)
                        {
                          final_array.push(data);
                        }
        
                      });
        
                      final_id.push(finals[j].value);
                    }
                  }
        
                  this.spa[i].filename = final_id;
                  this.fileArray = final_array;
                }

                if (i === 2) 
                {
                  var finals = document.getElementsByName("file_elements3");

                  for (var j = 0; j < finals.length; j++) {

                    if (finals[j].checked) {
                      myArr.forEach((element, index) => {
                       
                        var data = myArr[index];
                        if(_this.file_day + data.name === finals[j].value)
                        {
                          final_array.push(data);
                        }
        
                      });
        
                      final_id.push(finals[j].value);
                    }
                  }
        
                  this.spa[i].filename = final_id;
                  this.fileArray = final_array;
                }

                if (i === 3) 
                {
                  var finals = document.getElementsByName("file_elements4");

                  for (var j = 0; j < finals.length; j++) {

                    if (finals[j].checked) {
                      myArr.forEach((element, index) => {
                       
                        var data = myArr[index];
                        if(_this.file_day + data.name === finals[j].value)
                        {
                          final_array.push(data);
                        }
        
                      });
        
                      final_id.push(finals[j].value);
                    }
                  }
        
                  this.spa[i].filename = final_id;
                  this.fileArray = final_array;
                }

                if (i === 4) 
                {
                  var finals = document.getElementsByName("file_elements5");

                  for (var j = 0; j < finals.length; j++) {

                    if (finals[j].checked) {
                      myArr.forEach((element, index) => {
                       
                        var data = myArr[index];
                        if(_this.file_day + data.name === finals[j].value)
                        {
                          final_array.push(data);
                        }
        
                      });
        
                      final_id.push(finals[j].value);
                    }
                  }
        
                  this.spa[i].filename = final_id;
                  this.fileArray = final_array;
                }


                form_Data.append("pic_url", this.spa[i].filename);
                form_Data.append("payee", this.spa[i].payee.toString());
                form_Data.append("paid_date", paidat);
                form_Data.append("cash_in", this.spa[i].cash_in);
                form_Data.append("cash_out", this.spa[i].cash_out);
                form_Data.append("remarks", this.spa[i].remarks);
                form_Data.append("is_locked", this.is_locked);
                form_Data.append("is_enabled", this.is_enabled);
                if (this.spa[i].is_marked == "x")
                  form_Data.append("is_marked", "0");
                else form_Data.append("is_marked", this.spa[i].is_marked);
                form_Data.append("action", this.action);
                form_Data.append("created_by", this.name);
                axios({
                  method: "post",
                  headers: {
                    "Content-Type": "multipart/form-data",
                  },
                  url: "api/add_or_edit_price_record_salary_sea.php",
                  data: form_Data,
                })
                  .then(function(response) {
                    //handle success
                    //_this.items = response.data
                    //console.log(_this.items)
                    //Swal.fire({
                    //    text: _this.items,
                    //    icon: 'success',
                    //    confirmButtonText: 'OK'
                    //});
                  })
                  .catch(function(response) {
                    //handle error
                    //Swal.fire({
                    //    text: JSON.stringify(response),
                    //    icon: 'error',
                    //    confirmButtonText: 'OK'
                    //})
                  });
              }
              form_Data = new FormData();
            }
            _this.upload();
            _this.deleteRecord(_this.id);
            _this.reload();
          }
        }
      }
    },
    update: function(id) {
      this.action = 3; //update
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      var paidat = this.sliceDate(this.paid_date).replace(/-/g, "/");
      var payee = this.payee.toString();

      if (this.operation_type == 1) {
        this.cash_in = this.amount;
        this.cash_out = 0;
      } else {
        this.cash_out = this.amount;
        this.cash_in = 0;
      }

      if (
        this.category != "Marketing" &&
        this.category != "Office Needs" &&
        this.category != "Others" &&
        this.category != "Projects" &&
        this.category != "Store"
      ) {
        this.sub_category = "";
      }

      form_Data.append("jwt", token);
      form_Data.append("id", id);
      form_Data.append("account", this.account);
      form_Data.append("category", this.category);
      form_Data.append("sub_category", this.sub_category);
      form_Data.append("project_name", this.project_name);
      form_Data.append("related_account", this.related_account);

      this.details = this.$refs.detail.value;
      form_Data.append("details", this.details.replaceAll("\n", "<br>"));




      var final_id = [];
      var final_array = [];
      var del_array = [];
      var finals = document.getElementsByName("file_elements");
      var myArr = this.fileArray;

      for (var i = 0; i < finals.length; i++) {

        if (finals[i].checked) {
          myArr.forEach((element, index) => {
            
            var data = myArr[index];
            if(_this.file_day + data.name === finals[i].value)
            {
              final_array.push(data);
            }

          });

          final_id.push(finals[i].value);
        }
        else
        { 
          var bool_add_new = false;
          myArr.forEach((element, index) => {
            
            var data = myArr[index];
            if(_this.file_day + data.name === finals[i].value)
            {
              bool_add_new = true;
            }

          });

          if(!bool_add_new)
            del_array.push(finals[i].value);
        }
      }

      this.filename = final_id;
      this.fileArray = final_array;

      this.del_array = del_array;


      form_Data.append("pic_url", this.filename);
      form_Data.append("del_url", JSON.stringify(this.del_array));

      //if (this.filename != "") {
      //  form_Data.append("pic_url", this.filename);
      //} else {
      //  form_Data.append("pic_url", this.pic_url);
      //}

      form_Data.append("payee", payee);
      form_Data.append("paid_date", paidat);
      form_Data.append("cash_in", this.cash_in);
      form_Data.append("cash_out", this.cash_out);
      form_Data.append("remarks", this.remarks);
      if (this.is_marked == "x") form_Data.append("is_marked", "0");
      else form_Data.append("is_marked", this.is_marked);
      form_Data.append("action", this.action);
      form_Data.append("updated_by", this.name);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary_sea.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.items = response.data;
          //console.log(_this.items)
          Swal.fire({
            text: "Update Success.",
            icon: "success",
            confirmButtonText: "OK",
          });
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: "Upload Error, Please contact engineer.",
            icon: "error",
            confirmButtonText: "OK",
          });
        });
      this.upload();
      this.reload();
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
        url: "api/add_or_edit_price_record_salary_sea.php",
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
        url: "api/add_or_edit_price_record_salary_sea.php",
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
    edit: function(id) {
      var form_Data = new FormData();
      let _this = this;
      _this.reset();
      this.action = 6; //select by id
      this.edd = 1;
      form_Data.append("action", this.action);
      form_Data.append("id", id);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary_sea.php",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.id = response.data[0].id;
          if (response.data[0].account == 1) {
            _this.account_name = "Office Petty Cash";
          } else if (response.data[0].account == 2) {
            _this.account_name = "Security Bank";
          }
          _this.account = response.data[0].account;
          _this.category = response.data[0].category;
          _this.sub_category = response.data[0].sub_category;
          _this.project_name = response.data[0].project_name;
          if (response.data[0].related_account != "0") {
            _this.related_account = response.data[0].related_account;
          }

          _this.details = response.data[0].details;
          _this.$refs.detail.value = response.data[0].details.replaceAll(
            "<br>",
            "\n"
          );

          _this.pic_url = response.data[0].pic_url;
          _this.payee = response.data[0].payee.split(",");
          _this.paid_date = response.data[0].paid_date;

          if (response.data[0].cash_in != 0) {
            _this.amount = response.data[0].cash_in;
            _this.operation_type = 1;
          } else {
            _this.amount = response.data[0].cash_out;
            _this.operation_type = 2;
          }

          _this.remarks = response.data[0].remarks;
          _this.is_locked = response.data[0].is_locked;
          _this.is_enabled = response.data[0].is_enabled;

          _this.pic_url_array = response.data[0].pic_array;

          //_this.is_marked = response.data[0].is_marked;
          if (response.data[0].is_marked == "0") {
            _this.is_marked = "x";
          } else {
            _this.is_marked = response.data[0].is_marked;
          }

          _this.scrollMeTo('collapseOne');

          console.log(response.data[0]);
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
          this.action = 7; //delete
          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          let _this = this;

          form_Data.append("jwt", token);
          form_Data.append("id", id);
          form_Data.append("action", _this.action);
          form_Data.append("deleted_by", _this.name);
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/add_or_edit_price_record_salary_sea.php",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              //_this.items = response.data
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: JSON.stringify(response),
                icon: "error",
                confirmButtonText: "OK",
              });
            });
          _this.reload();
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
          url: "api/add_or_edit_price_record_salary_sea.php",
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
      form_Data.append("category", this.category);
      form_Data.append("sub_category", this.sub_category);
      form_Data.append("project_name", this.project_name);
      form_Data.append("keyword", this.keyword);

      axios({
        method: "post",
        url: "api/price_record_print_salary_sea.php",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "price_record.xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
    },
    onChangeFileUpload: function(e, record) {
      for (i = 0; i < e.target.files.length; i++) {
        const image = e.target.files[i];
        if (record == 1) {
          this.split1.filename.push(this.file_day + e.target.files[i].name);

          this.pic_url_array1.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

        } else if (record == 2) {
          this.split2.filename.push(this.file_day + e.target.files[i].name);

          this.pic_url_array2.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

        } else if (record == 3) {
          this.split3.filename.push(this.file_day + e.target.files[i].name);

          this.pic_url_array3.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

        } else if (record == 4) {
          this.split4.filename.push(this.file_day + e.target.files[i].name);

          this.pic_url_array4.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

        } else if (record == 5) {
          this.split5.filename.push(this.file_day + e.target.files[i].name);

          this.pic_url_array5.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

        } else {

          this.pic_url_array.push({
            id: i,
            is_checked: true,
            pic_url: this.file_day + e.target.files[i].name,
          });

          this.filename.push(this.file_day + e.target.files[i].name);
        }

        this.fileArray.push(image);
      }
      //const reader = new FileReader();
      //reader.readAsDataURL(image);
      //reader.onload = e=>{
      //this.filename = e.target.result;
      //console.log(this.filename);
      //console.log(this.split1.filename);
      //console.log(this.split2.filename);
      //console.log(this.fileArray);
      //};
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
      _this.allCashIn = 0.0;
      _this.allCashOut = 0.0;
      _this.allBalance = 0.0;
      _this.accountOneCashIn = 0.0;
      _this.accountOneCashOut = 0.0;
      _this.accountOneBalance = 0.0;
      _this.accountTwoCashIn = 0.0;
      _this.accountTwoCashOut = 0.0;
      _this.accountTwoBalance = 0.0;
      _this.accountThreeCashIn = 0.0;
      _this.accountThreeCashOut = 0.0;
      _this.accountThreeBalance = 0.0;
      if (
        _this.select_category != "Marketing" &&
        _this.select_category != "Office Needs" &&
        _this.select_category != "Others" &&
        _this.select_category != "Projects" &&
        _this.select_category != "Store"
      ) {
        _this.select_sub_category = "";
      }

      if (_this.category != "Projects") {
        _this.project_name = "";
      }
      const params = {
        category: _this.select_category,
        sub_category: _this.select_sub_category,
        project_name: _this.project_name,
        start_date: _this.start_date,
        end_date: _this.end_date,
        page: _this.page,
        account: _this.account,
        keyword: _this.keyword,
        select_date_type: _this.select_date_type,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/price_record_page_salary_sea.php", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.items = res.data;
            _this.items.forEach((element) => {
              element.pic_url = element.pic_url.replaceAll(",", '","');
              if (element.pic_url.indexOf('"') == 0) {
                element.pic_url = "[" + element.pic_url + "]";
                element.pic_url = JSON.parse(element.pic_url);
              } else {
                element.pic_url = '["' + element.pic_url + '"]';
                element.pic_url = JSON.parse(element.pic_url);
              }
              // console.log(element.pic_url);
            });
            // console.log(_this.items);
            /*
            res.data.forEach((element, index) => {
              if (index < this.perPage) {
                if (element.is_enabled == 1) {
                  if (
                    element.account == 1 ||
                    element.account == 2 ||
                    element.account == 3
                  ) {
                    _this.allCashIn += parseFloat(element.cash_in);
                    _this.allCashOut += parseFloat(element.cash_out);
                  }
                  if (element.account == 1) {
                    _this.accountOneCashIn += parseFloat(element.cash_in);
                    _this.accountOneCashOut += parseFloat(element.cash_out);
                  } else if (element.account == 2) {
                    _this.accountTwoCashIn += parseFloat(element.cash_in);
                    _this.accountTwoCashOut += parseFloat(element.cash_out);
                  } else if (element.account == 3) {
                    _this.accountThreeCashIn += parseFloat(element.cash_in);
                    _this.accountThreeCashOut += parseFloat(element.cash_out);
                  }
                }
              }
            });
            */

            if(res.data.length > 0)
            {
              _this.allCashIn = parseFloat(res.data[0]['ai']);
              _this.allCashOut = parseFloat(res.data[0]['ao']);

              _this.accountOneCashIn = parseFloat(res.data[0]['i1']);
              _this.accountOneCashOut = parseFloat(res.data[0]['o1']);

              _this.accountTwoCashIn = parseFloat(res.data[0]['i2']);
              _this.accountTwoCashOut = parseFloat(res.data[0]['o2']);

              _this.accountThreeCashIn = parseFloat(res.data[0]['i3']);
              _this.accountThreeCashOut = parseFloat(res.data[0]['o3']);

              _this.allBalance = _this.allCashIn - _this.allCashOut;
              _this.accountOneBalance =
                _this.accountOneCashIn - _this.accountOneCashOut;
              _this.accountTwoBalance =
                _this.accountTwoCashIn - _this.accountTwoCashOut;
              _this.accountThreeBalance =
                _this.accountThreeCashIn - _this.accountThreeCashOut;
            }
            
            this.displayedPosts();
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },
    displayedPosts: function() {
      this.setPages();
      return this.paginate(this.items);
    },
    upload: function() {
      var myArr = this.fileArray;
      var vm = this;
      console.log(myArr);
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append("file", data);
        myForm.append("batch_type", "proof");
        myForm.append("batch_id", 0);
        myForm.append("today", vm.file_day);

        axios
          .post("api/upload_price_record_gcp.php", myForm, config)
          .then(function(res) {
            if (res.data.code == 0) {
              myArr[index].progress = 1;
              vm.$set(vm.fileArray, index, myArr[index]);
              console.log(vm.fileArray, index);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function(err) {
            console.log(err);
          });
      });
    },
    getUserName: function() {
  
      let _this = this;

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/on_duty_get_myname.php",
 
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
      this.id = 0;
      this.account = 1;
      this.category = "";
      this.sub_category = "";
      this.project_name = "";
      this.related_account = 0;
      this.details = "";
      this.pic_url = "";
      this.payee = "";
      this.paid_date = "";
      this.amount = 0.0;
      this.operation_type = 0;
      this.cash_in = 0.0;
      this.cash_out = 0.0;
      this.remarks = "";
      this.filename = [];

      this.select_category = "All";
      this.select_sub_category = "";

      this.is_locked = 0;
      this.is_enabled = 1;
      this.is_marked = "x";
      this.action = 0;
      this.edd = 0;

      this.spa = [];
      this.fileArray = [];
      this.del_array = [];

      this.pic_url_array = [];
      this.pic_url_array1 = [];
      this.pic_url_array2 = [];
      this.pic_url_array3 = [];
      this.pic_url_array4 = [];
      this.pic_url_array5 = [];

      this.split1.account = 0;
      this.split1.category = "";
      this.split1.sub_category = "";
      this.split1.project_name = "";
      this.split1.related_account = 0;
      this.split1.details = "";
      this.split1.pic_url = "";
      this.split1.payee = [];
      this.split1.paid_date = "";
      this.split1.amount = 0.0;
      this.split1.cash_in = 0.0;
      this.split1.cash_out = 0.0;
      this.split1.remarks = "";
      this.split1.filename = [];

      this.split1.is_locked = false;
      this.split1.is_enabled = true;
      this.split1.is_marked = "x";

      this.split2.account = 0;
      this.split2.category = "";
      this.split2.sub_category = "";
      this.split2.project_name = "";
      this.split2.related_account = 0;
      this.split2.details = "";
      this.split2.pic_url = "";
      this.split2.payee = [];
      this.split2.paid_date = "";
      this.split2.amount = 0.0;
      this.split2.cash_in = 0.0;
      this.split2.cash_out = 0.0;
      this.split2.remarks = "";
      this.split2.filename = [];

      this.split2.is_locked = false;
      this.split2.is_enabled = true;
      this.split2.is_marked = "x";

      this.split3.account = 0;
      this.split3.category = "";
      this.split3.sub_category = "";
      this.split3.project_name = "";
      this.split3.related_account = 0;
      this.split3.details = "";
      this.split3.pic_url = "";
      this.split3.payee = [];
      this.split3.paid_date = "";
      this.split3.amount = 0.0;
      this.split3.cash_in = 0.0;
      this.split3.cash_out = 0.0;
      this.split3.remarks = "";
      this.split3.filename = [];

      this.split3.is_locked = false;
      this.split3.is_enabled = true;
      this.split3.is_marked = "x";

      this.split4.account = 0;
      this.split4.category = "";
      this.split4.sub_category = "";
      this.split4.project_name = "";
      this.split4.related_account = 0;
      this.split4.details = "";
      this.split4.pic_url = "";
      this.split4.payee = [];
      this.split4.paid_date = "";
      this.split4.amount = 0.0;
      this.split4.cash_in = 0.0;
      this.split4.cash_out = 0.0;
      this.split4.remarks = "";
      this.split4.filename = [];

      this.split4.is_locked = false;
      this.split4.is_enabled = true;
      this.split4.is_marked = "x";

      this.split5.account = 0;
      this.split5.category = "";
      this.split5.sub_category = "";
      this.split5.project_name = "";
      this.split5.related_account = 0;
      this.split5.details = "";
      this.split5.pic_url = "";
      this.split5.payee = [];
      this.split5.paid_date = "";
      this.split5.amount = 0.0;
      this.split5.cash_in = 0.0;
      this.split5.cash_out = 0.0;
      this.split5.remarks = "";
      this.split5.filename = [];

      this.split5.is_locked = false;
      this.split5.is_enabled = true;
      this.split5.is_marked = "x";
      this.$refs.file0.value = "";
      this.$refs.file1.value = "";
      this.$refs.file2.value = "";
      this.$refs.file3.value = "";
      this.$refs.file4.value = "";
      this.$refs.file5.value = "";

      this.$refs.detail.value = "";
      this.$refs.detail1.value = "";
      this.$refs.detail2.value = "";
      this.$refs.detail3.value = "";
      this.$refs.detail4.value = "";
      this.$refs.detail5.value = "";

      this.get_today();
    },
    reload: function() {
      let _this = this;
      _this.myVar = setTimeout(function() {
        _this.reset();
        _this.getRecords();
      }, 1000);
    },
    clear: function() {
      let _this = this;
      clearTimeout(_this.myVar);
      clearTimeout(_this.lockVar);
    },
    formatOperationType: function(type) {
      if (type == 1) {
        return "Cash In";
      } else if (type == 2) {
        return "Cash Out";
      }
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
