Vue.component('date-picker', {
    template: '<input/>',
    props: ['dateFormat'],
    mounted: function() {
        var self = this;
        $(this.$el).datepicker({
            dateFormat: "yy/mm/dd",
            showOn: "button",
            buttonImage: "images/calendar.png",
            buttonImageOnly: true,
            buttonText: "",
            onSelect: function(date) {
                self.$emit('update-date', date);
            }
        });
    },
    beforeDestroy: function() {
        $(this.$el).datepicker('hide').datepicker('destroy');
    }
});

Vue.component('edit-date-picker', {
    template: '<input/>',
    props: ['dateFormat'],
    mounted: function() {
        var self = this;
        $(this.$el).datepicker({
            dateFormat: "yy/mm/dd",
            showOn: "button",
            buttonImage: "images/calendar.png",
            buttonImageOnly: true,
            buttonText: "",
            onSelect: function(date) {
                self.$emit('update-date', date);
            }
        });
    },
    beforeDestroy: function() {
        $(this.$el).datepicker('hide').datepicker('destroy');
    }
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


Vue.config.ignoredElements = ['eng']

let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    date_receive: '',
    customer: '',
    email: '',
    description: '',
    quantity: '',
    supplier: '',
    kilo: 0.0,
    cuft: 0.0,
    taiwan_pay: 0,
    courier_pay: 0,
    courier_money: 0,
    remark: '',
    picname: '',
    receive_records: [],
    record: {},
    file: '',

    // error
    error_date_receive:'',
    error_customer: '',
    error_email: '',
    error_cuft:'',
    error_courier_money: '',
    error_kilo: '',
    error_quantity : '',

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],
    perPage: 10000,

    // searching
    keyword: '',

    s_keyword:'',
    c_keyword:'',
    // image
    selectedImage: null,

    // compute
    r_kilo : 0.0,
    n_kilo : 0.0,
    r_cuft : 0.0,
    n_cuft : 0.0,

    // show photo
    pic_lib : [],
    pic_receive: [],
    url_ip: "https://storage.googleapis.com/feliiximg/",

    pic_preview: [],

};

var app = new Vue({
    el: '#receive_record',

    data: mainState,

    //mounted: function() {
    //    console.log('Vue mounted');
    //    this.getReceiveRecords();
    //    //$('#showUser1').DataTable();
    //    //document.querySelector("input[name=showUser]").DataTable();
    //    
    //},

    created () {
      console.log('Vue created');
      this.getReceiveRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);
      if(this.kilo == 0)
        this.kilo = '';
      if(this.cuft == 0)
        this.cuft = '';
      if(this.courier_money == 0)
        this.courier_money = '';
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },

      keyword () {
        console.log('Vue watch keyword');
        this.getReceiveRecords();
      },


      s_keyword: function (value) {
            //console.log(s_keyword);

            $.ajax({
            url: 'api/contactor.php?s_keyword=' + value,
            type: 'GET',
            data: '',
            dataType: "json",
            async: true,
            success: function(json) {
              // Add response in Modal body
                var html = "";

                var contentJson = eval(json);
                //contentJson = contentJson.Replace("<", "\u003c").Replace(">", "\u003e");
                //console.log(contentJson);
               // contentJson = contentJson.Replace("\u003c", "<").Replace("\u003e", ">");
                var spNo, deliverTitle, status;
                for (var i = 0; i < contentJson.length; i++) {
                    customer = contentJson[i].supplier;
                    c_phone = contentJson[i].s_phone;
                    c_fax = contentJson[i].s_fax;
                    c_email = contentJson[i].company_title;
                    
                    html += "<tr onclick='data1(this)'><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                    //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                }
                
                $('#supplier').html(html);
            }
          });
          
        },

        c_keyword: function (value) {
            //console.log(c_keyword);

            $.ajax({
            url: 'api/contactor.php?c_keyword=' + value,
            type: 'GET',
            data: '',
            dataType: "json",
            async: true,
            success: function(json) {
              // Add response in Modal body
                var html = "";

                var contentJson = eval(json);
                var spNo, deliverTitle, status;
                for (var i = 0; i < contentJson.length; i++) {
                    shipping_mark = contentJson[i].shipping_mark;
                    customer = contentJson[i].customer;
                    c_phone = contentJson[i].c_phone;
                    c_fax = contentJson[i].c_fax;
                    c_email = contentJson[i].c_email;
                    
                    html += "<tr onclick='data(this)'><td>" + shipping_mark.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                    //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                }
                
                $('#contact').html(html);
            }
          });
          
        }

    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');

        this.n_kilo = 0.0;
        this.n_cuft = 0.0;
        this.r_kilo = 0.0;
        this.r_cuft = 0.0;

        for(let i=0; i<this.receive_records.length; i++)
        {   

            if(this.receive_records[i].date_receive=="")
            {
                this.n_kilo += parseFloat(this.receive_records[i].kilo);
                this.n_cuft += parseFloat(this.receive_records[i].cuft);
            }
            else
            {
                this.r_kilo += parseFloat(this.receive_records[i].kilo);
                this.r_cuft += parseFloat(this.receive_records[i].cuft);
            }
        }

        this.setPages();
        return this.paginate(this.receive_records);
      },

      pageUrl() {
          var favorite = [];

          for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
                favorite.push(this.receive_records[i].id);
            }

          return 'receive_data_excel.php?id=' + favorite.join(",");
        }
    },

    updated: function() {
        console.log('Vue updated')

        if (this.isEditing) {
            var dialog;
            var supdialog;

            var photoModal;

            //    $('header').load('Include/header.htm');
            toggleme($('a.btn.detail'), $('.block.record'), 'show');

            $.widget("ui.dialog", $.ui.dialog, {
                // customize open method to register the click
                open: function() {
                    var me = this;
                    $(document).on('click', ".ui-widget-overlay", function(e) {
                        //call dialog close function

                        me.close();
                    });

                    // Invoke parent open method
                    this._super();
                },
                close: function() {
                    // Remove click handler for the current .ui-widget-overlay
                    $(document).off("click", ".ui-widget-overlay");
                    // Invoke parent close method
                    this._super();
                }
            });

            dialog = $("#myModal").dialog({
                autoOpen: false,
                height: 540,
                width: 720,
                modal: true,
            });

            supdialog = $("#supModal").dialog({
                autoOpen: false,
                height: 540,
                width: 720,
                modal: true,
            });

            photoModal = $("#photoModal").dialog({
                autoOpen: false,
                height: 540,
                width: 720,
                modal: true,
            });

            $("#get_photo_library_1").button().unbind('click').on("click", function() {
                app.getPicLibrary();
                photoModal.dialog("open");
            });

            $("#create-user1").button().unbind('click').on("click", function() {

                $.ajax({
                url: 'api/contactor.php',
                type: 'GET',
                data: '',
                dataType: "json",
                async: true,
                success: function(json) {
                  // Add response in Modal body
                    var html = "";

                    var contentJson = eval(json);
                    var spNo, deliverTitle, status;
                    for (var i = 0; i < contentJson.length; i++) {
                        shipping_mark = contentJson[i].shipping_mark;
                        customer = contentJson[i].customer;
                        c_phone = contentJson[i].c_phone;
                        c_fax = contentJson[i].c_fax;
                        c_email = contentJson[i].c_email;
                        
                        html += "<tr onclick='data(this)'><td>" + shipping_mark.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                        //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                    }
                    
                    $('#contact').html(html);
                }
              });

                dialog.dialog("open");
            });

            $("#create-supplier1").button().unbind('click').on("click", function() {

                $.ajax({
                url: 'api/contactor.php',
                type: 'GET',
                data: '',
                dataType: "json",
                async: true,
                success: function(json) {
                  // Add response in Modal body
                    var html = "";

                    var contentJson = eval(json);
                    var spNo, deliverTitle, status;
                    for (var i = 0; i < contentJson.length; i++) {
                        customer = contentJson[i].supplier;
                        c_phone = contentJson[i].s_phone;
                        c_fax = contentJson[i].s_fax;
                        c_email = contentJson[i].company_title;
                        
                        html += "<tr onclick='data1(this)'><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                        //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                    }
                    
                    $('#supplier').html(html);
                }
              });

                supdialog.dialog("open");
            });
        }
        else
        {
          var dialog;
          var supdialog;

          var photoModal;

            //    $('header').load('Include/header.htm');
            toggleme($('a.btn.detail'), $('.block.record'), 'show');

            $.widget("ui.dialog", $.ui.dialog, {
                // customize open method to register the click
                open: function() {
                    var me = this;

                    $(document).on('click', ".ui-widget-overlay", function(e) {
                        //call dialog close function
                        me.close();
                    });

                    // Invoke parent open method
                    this._super();
                },

                close: function() {
                    // Remove click handler for the current .ui-widget-overlay
                    $(document).off("click", ".ui-widget-overlay");
                    // Invoke parent close method
                    this._super();
                }
            });


            dialog = $("#myModal").dialog({
                autoOpen: false,
                height: 520,
                width: 800,
                modal: true,
            });

            supdialog = $("#supModal").dialog({
                autoOpen: false,
                height: 540,
                width: 720,
                modal: true,
            });

            photoModal = $("#photoModal").dialog({
                autoOpen: false,
                height: 540,
                width: 720,
                modal: true,
            });

            $("#create-user").button().unbind('click').on("click", function() {

                $.ajax({
                url: 'api/contactor.php',
                type: 'GET',
                data: '',
                dataType: "json",
                async: true,
                success: function(json) {
                  // Add response in Modal body
                    var html = "";

                    var contentJson = eval(json);
                    var spNo, deliverTitle, status;
                    for (var i = 0; i < contentJson.length; i++) {
                        shipping_mark = contentJson[i].shipping_mark;
                        customer = contentJson[i].customer;
                        c_phone = contentJson[i].c_phone;
                        c_fax = contentJson[i].c_fax;
                        c_email = contentJson[i].c_email;
                        
                        html += "<tr onclick='data(this)'><td>" + shipping_mark.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                        //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                    }
                    
                    $('#contact').html(html);
                }
              });

                dialog.dialog("open");
            });

            $("#get_photo_library").button().unbind('click').on("click", function() {
                app.getPicLibrary();
                photoModal.dialog("open");
            });

            $("#create-supplier").button().unbind('click').on("click", function() {



                $.ajax({
                url: 'api/contactor.php',
                type: 'GET',
                data: '',
                dataType: "json",
                async: true,
                success: function(json) {
                  // Add response in Modal body
                    var html = "";

                    var contentJson = eval(json);
                    var spNo, deliverTitle, status;
                    for (var i = 0; i < contentJson.length; i++) {
                        customer = contentJson[i].supplier;
                        c_phone = contentJson[i].s_phone;
                        c_fax = contentJson[i].s_fax;
                        c_email = contentJson[i].company_title;
                        
                        html += "<tr onclick='data1(this)'><td>" + customer.replace(/</g, '&lt;').replace(/>/g, '&gt') + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>";
                        //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                    }
                    
                    $('#supplier').html(html);
                }
              });

                supdialog.dialog("open");
            });
        }
        /*
        var table = $('#showUser1').DataTable();
        $('#showUser1 tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
        } );
        */
        //document.querySelector("input[name=showUser1]").DataTable();
    },

    methods: {
        delete_library : function () {
            let _this = this;
            let delete_me = [];
            for(var i = 0; i < this.pic_lib.length; i++) {
                if(this.pic_lib[i].is_checked == true) {
                    delete_me.push(this.pic_lib[i].pid);
                }
            }

            if(delete_me.length > 0)
            {
                Swal.fire({
                    title: "Submit",
                    text: "確定要刪除? Are you sure to delete?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                  }).then((result) => {
                    if (result.value) {
                        var token = localStorage.getItem("token");
                        var form_Data = new FormData();
                        form_Data.append("jwt", token);
                        form_Data.append("ids", delete_me.join());
                        form_Data.append("crud", "del");

                        axios({
                            method: "post",
                            headers: {
                            "Content-Type": "multipart/form-data",
                            },
                            url: "api/receive_library_delete.php",
                            data: form_Data,
                        })
                        .then(function(response) {
                        //handle success
                            Swal.fire({
                                html: response.data.message,
                                icon: "info",
                                confirmButtonText: "OK",
                            });

                            //window.jQuery(".mask").toggle();
                            //window.jQuery("#photoModal").toggle();
                            $( "#photoModal" ).dialog('close');
                            _this.pic_lib = [];
                            _this.getPicLibrary();
                        })
                        .catch(function(error) {
                            //handle error
                            Swal.fire({
                                text: JSON.stringify(error),
                                icon: "info",
                                confirmButtonText: "OK",
                            });

                            //window.jQuery(".mask").toggle();
                            //window.jQuery("#photoModal").toggle();
                            $( "#photoModal" ).dialog('close');
                            
                        });
                    } else {
                        return;
                    }
                });
            }
        },
    

        bulk_toggle_library: function(){
            let toogle = document.getElementById('bulk_select_all_library').checked;
            for(var i = 0; i < this.pic_lib.length; i++) {
              this.pic_lib[i].is_checked = toogle;
          }
        },

        choose_library: function (){
            if(this.isEditing == true)
            {
                for (var i = 0; i < this.pic_lib.length; i++) {
                    if(this.pic_lib[i].is_checked == true) {
                        let pid = this.pic_lib[i].pid;
                        var found = false;
                        for(var j = 0; j < this.record.pic.length; j++) {
                            if (this.record.pic[j].pid == pid) {
                                found = true;
                                break;
                            }
                        }
                        if(found == false) {
                            this.record.pic.push(this.shallowCopy(
                                this.pic_lib.find((element) => element.pid == pid)
                            ));
                        }

                    }
                }
            }
            else
            {
                this.pic_receive = [];
                for (var i = 0; i < this.pic_lib.length; i++) {
                    if(this.pic_lib[i].is_checked == true) {
                        let pid = this.pic_lib[i].pid;
                        this.pic_receive.push(this.shallowCopy(
                            this.pic_lib.find((element) => element.pid == pid)
                        ));

                        //this.customer = this.pic_lib[i].customer;
                        //this.supplier = this.pic_lib[i].supplier;
                        //this.date_receive = this.pic_lib[i].date_receive;
                        //$('#adddate').datepicker('setDate', this.date_receive);
                        //this.quantity = this.pic_lib[i].quantity;
                        //this.remark = this.pic_lib[i].remark;
                    }
                }
            }
            

              //window.jQuery(".mask").toggle();
              //window.jQuery("#photoModal").toggle();
              $( "#photoModal" ).dialog('close');

        },

        getPicLibrary: function(keyword) {
            let _this = this;
            if(this.pic_lib.length > 0) {
                return;
            }
            console.log("getPicLibrary");
              axios.get('api/get_pic_library.php')
                  .then(function(response) {
                      console.log(response.data);
                      _this.pic_lib = response.data;

                  })
                  .catch(function(error) {
                      console.log(error);
                  });
          },

        getReceiveRecords: function(keyword) {
          console.log("getReceiveRecords");
            axios.get('api/receive_record.php')
                .then(function(response) {
                    console.log(response.data);
                    app.receive_records = response.data;

                    //this.r_kilo = 0.0;
                    //this.n_kilo = 0.0;
                    //this.r_cuft = 0.0;
                    //this.n_cuft = 0.0;
                    
                    

                    //console.log(this.n_kilo);
                    //console.log(this.n_cuft);
                    //console.log(this.r_kilo);
                    //console.log(this.r_cuft);

                    //this.$refs.showUser1.$el.DataTable();
                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

        setPages () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.receive_records.length / this.perPage.id);

          if(numberOfPages == 1)
            this.page = 1;
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages.push(index);
          }
        },

        paginate: function (posts) {
          console.log('paginate');
          if(this.page < 1)
            this.page = 1;
          if(this.page > this.pages.length)
            this.page = this.pages.length;

          let page = this.page;
          let perPage = this.perPage.id;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.receive_records.slice(from, to);
        },

        get_photo_library: function () {
            this.getPicLibrary();
            //window.jQuery(".mask").toggle();
            //window.jQuery("#photoModal").toggle();
            
        },

        createReceiveRecord: function() {
            console.log("createReceiveRecord");

            if (this.validateForm()) {

                var form_Data = new FormData();
                //console.log("datepicker:", this.date_receive)
                //console.log(document.querySelector("input[name=datepicker]").value)
                //this.date_receive = document.querySelector("input[name=datepicker]").value;
                //console.log(document.querySelector("input[id=adddate]").value)

                this.date_receive = document.querySelector("input[id=adddate]").value;

                if (!this.isDate(this.date_receive) && !this.date_receive == "") 
                {
                  this.error_date_receive = '必須是日期 (date required)';
                  $(window).scrollTop(0);
                  return false;
                } 

                form_Data.append('date_receive', this.formatDate(this.date_receive))
                form_Data.append('customer', this.customer)
                form_Data.append('email', this.email)
                form_Data.append('description', this.description)
                form_Data.append('quantity', this.quantity)
                form_Data.append('supplier', this.supplier)
                form_Data.append('kilo', this.kilo)
                form_Data.append('cuft', this.cuft)
                form_Data.append('taiwan_pay', this.taiwan_pay)
                form_Data.append('courier_pay', this.courier_pay)
                form_Data.append('courier_money', this.courier_money)
                form_Data.append('remark', this.remark)
                form_Data.append('file', this.file);
                form_Data.append('crud', "insert");
                form_Data.append('id', '');

                let delete_me = [];
                for(var i = 0; i < this.pic_receive.length; i++) {
                    if(this.pic_receive[i].is_checked == true) {
                        delete_me.push(this.pic_receive[i].pid);
                    }
                }

                form_Data.append("photo", delete_me.join());
           
                var receive_record = {};
                form_Data.forEach(function(value, key) {
                    receive_record[key] = value;
                });

                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/receive_record.php',
                        data: form_Data
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)

                        app.resetForm();

                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
            }
        },

        createReceiveRecordMail: function() {
            console.log("createReceiveRecord");

            if (this.validateForm()) {

                var form_Data = new FormData();
                //console.log("datepicker:", this.date_receive)
                //console.log(document.querySelector("input[name=datepicker]").value)
                //this.date_receive = document.querySelector("input[name=datepicker]").value;
                //console.log(document.querySelector("input[id=adddate]").value)

                this.date_receive = document.querySelector("input[id=adddate]").value;

                if (!this.isDate(this.date_receive) && !this.date_receive == "") 
                {
                  this.error_date_receive = '必須是日期 (date required)';
                  $(window).scrollTop(0);
                  return false;
                } 

                form_Data.append('date_receive', this.formatDate(this.date_receive))
                form_Data.append('customer', this.customer)
                form_Data.append('email', this.email)
                form_Data.append('description', this.description)
                form_Data.append('quantity', this.quantity)
                form_Data.append('supplier', this.supplier)
                form_Data.append('kilo', this.kilo)
                form_Data.append('cuft', this.cuft)
                form_Data.append('taiwan_pay', this.taiwan_pay)
                form_Data.append('courier_pay', this.courier_pay)
                form_Data.append('courier_money', this.courier_money)
                form_Data.append('remark', this.remark)
                form_Data.append('file', this.file);
                form_Data.append('crud', "insert_mail");
                form_Data.append('id', '');

                let delete_me = [];
                for(var i = 0; i < this.pic_receive.length; i++) {
                    if(this.pic_receive[i].is_checked == true) {
                        delete_me.push(this.pic_receive[i].pid);
                    }
                }

                form_Data.append("photo", delete_me.join());

                var receive_record = {};
                form_Data.forEach(function(value, key) {
                    receive_record[key] = value;
                });

                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/receive_record.php',
                        data: form_Data
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)

                        app.resetForm();

                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
            }
        },

        updateTaiwanPay: function(event) {
          console.log("updateTaiwanPay");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.taiwan_pay = 1;
            } else {
                this.taiwan_pay = 0;
            }
        },

        updateEditTaiwanPay: function(event) {
          console.log("updateEditTaiwanPay");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.taiwan_pay = 1;
            } else {
                this.record.taiwan_pay = 0;
            }
        },

        updateDate: function(date) {
          console.log("updateDate");
            if (this.isEditing)
                this.record.date_receive = date;
            else
                this.date_receive = date;
        },

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
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

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            var form_Data = new FormData();
            //console.log("datepicker:", this.record.date_receive)
            console.log(document.querySelector("input[id=adddate1]").value)
            //this.record.date_receive = document.querySelector("input[name=datepicker1]").value;

            //console.log(document.querySelector("input[id=adddate]").value)

            this.record.date_receive = document.querySelector("input[id=adddate1]").value;

            if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
            {
              this.error_date_receive = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
            } 

            //if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
            //{
            //      this.error_date_receive = '必須是日期 (date required)';
            //      $(window).scrollTop(0);
            //      return false;
                
            //}


            form_Data.append('date_receive', this.formatDate(this.record.date_receive))
            form_Data.append('customer', this.record.customer)
            form_Data.append('email', this.record.email)
            form_Data.append('description', this.record.description)
            form_Data.append('quantity', this.record.quantity)
            form_Data.append('supplier', this.record.supplier)
            form_Data.append('kilo', this.record.kilo)
            form_Data.append('cuft', this.record.cuft)
            form_Data.append('taiwan_pay', this.record.taiwan_pay)
            form_Data.append('courier_pay', this.record.courier_pay)
            form_Data.append('courier_money', this.record.courier_money)
            form_Data.append('remark', this.record.remark)
            form_Data.append('file', this.file);
            form_Data.append('crud', "update");
            form_Data.append('id', this.record.id);

            form_Data.append('pic', JSON.stringify(this.record.pic))

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/receive_record.php',
                    data: form_Data
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                    {
                        //const index = app.receive_records.findIndex((e) => e.id === this.record.id);
                        //if (index !== -1) 
                        //    app.receive_records[index] = this.record;
                        
                        app.resetForm();
                      
                  }
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        editReceiveRecordMail: function(event) {
            console.log("editReceiveRecordMail")

            targetId = this.record.id;
            var form_Data = new FormData();
            //console.log("datepicker:", this.record.date_receive)
            console.log(document.querySelector("input[id=adddate1]").value)
            //this.record.date_receive = document.querySelector("input[name=datepicker1]").value;

            //console.log(document.querySelector("input[id=adddate]").value)

            this.record.date_receive = document.querySelector("input[id=adddate1]").value;

            if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
            {
              this.error_date_receive = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
            } 

            //if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
            //{
            //      this.error_date_receive = '必須是日期 (date required)';
            //      $(window).scrollTop(0);
            //      return false;
                
            //}


            form_Data.append('date_receive', this.formatDate(this.record.date_receive))
            form_Data.append('customer', this.record.customer)
            form_Data.append('email', this.record.email)
            form_Data.append('description', this.record.description)
            form_Data.append('quantity', this.record.quantity)
            form_Data.append('supplier', this.record.supplier)
            form_Data.append('kilo', this.record.kilo)
            form_Data.append('cuft', this.record.cuft)
            form_Data.append('taiwan_pay', this.record.taiwan_pay)
            form_Data.append('courier_pay', this.record.courier_pay)
            form_Data.append('courier_money', this.record.courier_money)
            form_Data.append('remark', this.record.remark)
            form_Data.append('file', this.file);
            form_Data.append('crud', "update_mail");
            form_Data.append('id', this.record.id);

            form_Data.append('pic', JSON.stringify(this.record.pic))

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/receive_record.php',
                    data: form_Data
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                    {
                        //const index = app.receive_records.findIndex((e) => e.id === this.record.id);
                        //if (index !== -1) 
                        //    app.receive_records[index] = this.record;
                        
                        app.resetForm();
                      
                  }
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            var form_Data = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            form_Data.append('date_receive', "")
            form_Data.append('customer', "")
            form_Data.append('email', "")
            form_Data.append('description', "")
            form_Data.append('quantity', "")
            form_Data.append('supplier', "")
            form_Data.append('kilo', "")
            form_Data.append('cuft', "")
            form_Data.append('taiwan_pay', "")
            form_Data.append('courier_pay', "")
            form_Data.append('courier_money', "")
            form_Data.append('remark', "")
            form_Data.append('file', "");
            form_Data.append('crud', "del");
            form_Data.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/receive_record.php',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                        console.log(response.data);
                    //this.$forceUpdate();
                    app.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        resetForm: function() {
          console.log("resetForm");
            this.date_receive = '';
            this.customer = '';
            this.description = '';
            this.quantity = '';
            this.email = '';
            this.supplier = '';
            this.kilo = '';
            this.cuft = '';
            this.taiwan_pay = '';
            this.courier_pay = '';
            this.courier_money = '';
            this.remark = '';
            this.file = '';
            this.isEditing = false;
            this.record = {};

            this.pic_lib = [];
            this.pic_receive = [];
            this.pic_preview = [];

            $('#adddate').datepicker('setDate', "");
            $('#adddate1').datepicker('setDate', "");

            this.resetError();
            // this.resetFile();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },

        resetError: function() {
          console.log("resetError");
            this.error_date_receive = '';
            this.error_customer = '';
            this.error_email = '';
            this.error_courier_money = '';
            this.error_kilo = '';
            this.error_quantity = '';
        },

        resetFile: function () {
          console.log("resetFile")
          const input = this.$refs.file;
          input.type = 'text';
          input.type = 'file';
        },

        onChangeFileUpload() {
          console.log("onChangeFileUpload");
            this.file = this.$refs.file.files[0];
            console.log(this.$refs.file.files[0]);
        },

        deleteRecord() {
          console.log("deleteRecord");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
                favorite.push(this.receive_records[i].id);
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
              this.delReceiveRecord(favorite.join(", "));

              app.resetForm();
              this.unCheckCheckbox();

              $(window).scrollTop(0);
            }
        },


        shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },

        zoom(id) {
          this.selectedImage = "true";
          this.pic_preview = this.shallowCopy(app.receive_records.find(element => element.id == id)['pic']);

          let imgdialog = $("#imgModal").dialog({
                autoOpen: false,
                height: 720,
                width: 640,
                modal: true,
            });

         imgdialog.dialog("open");
          console.log("Zoom", this.selectedImage);
        },

        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
                favorite.push(this.receive_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                return;
            }
            this.record = this.shallowCopy(app.receive_records.find(element => element.id == favorite));
            this.isEditing = true;

            $('.block.record').toggleClass('show');

            if(this.record.date_receive != "")
            {
                $('#adddate1').datepicker();
                $('#adddate1').datepicker('setDate', this.record.date_receive);
            }
            else
            {
                $('#adddate1').datepicker();
                $('#adddate1').datepicker('setDate', null);
            }

            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            if(this.record.kilo == 0)
                this.record.kilo = '';
            if(this.record.cuft == 0)
                this.record.cuft = '';
            if(this.record.courier_money == 0)
                this.record.courier_money = '';

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        isNumeric: function (n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        },

        isDate: function(txtDate, separator) {
            var aoDate,           // needed for creating array and object
                ms,               // date in milliseconds
                month, day, year; // (integer) month, day and year
            // if separator is not defined then set '/'
            if (separator === undefined) {
                separator = '/';
            }
            // split input date to month, day and year
            aoDate = txtDate.split(separator);
            // array length should be exactly 3 (no more no less)
            if (aoDate.length !== 3) {
                return false;
            }
            // define month, day and year from array (expected format is m/d/yyyy)
            // subtraction will cast variables to integer implicitly
            month = aoDate[1] - 1; // because months in JS start from 0
            day = aoDate[2] - 0;
            year = aoDate[0] - 0;
            // test year range
            if (year < 1000 || year > 3000) {
                return false;
            }
            // convert input date to milliseconds
            ms = (new Date(year, month, day)).getTime();
            // initialize Date() object from milliseconds (reuse aoDate variable)
            aoDate = new Date();
            aoDate.setTime(ms);
            // compare input date and parts from Date() object
            // if difference exists then input date is not valid
            if (aoDate.getFullYear() !== year ||
                aoDate.getMonth() !== month ||
                aoDate.getDate() !== day) {
                return false;
            }
            // date is OK, return true
            return true;
        },

        isEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },

        unCheckCheckbox()
        {
            for (i = 0; i < this.receive_records.length; i++) 
            {
              this.receive_records[i].is_checked = false;
            }
          //$(".alone").prop("checked", false);
          //this.clicked = false;
        },

        toggleCheckbox()
        {
            var i;
            for (i = 0; i < this.receive_records.length; i++) 
            {
              this.receive_records[i].is_checked = (this.clicked == 1 ? 0 : 1);
            }

            this.clicked = (this.clicked == 1 ? 0 : 1);
          //$(".alone").prop("checked", !this.clicked);
          //this.clicked = !this.clicked;
        },

        validateForm() {
            console.log("validateForm");
            this.resetError();

            if (this.isEditing) 
            {
              if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
              {
                  this.error_date_receive = '必須是日期 (date required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.record.supplier == "") 
              {
                  this.error_customer = '必須輸入寄件人 (supplier required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (!this.isEmail(this.record.email) && !this.record.email == "") 
              {
                  this.error_email = '必須是email (email required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (!this.isNumeric(this.record.kilo) && !this.record.kilo == "")
              {
                  this.error_kilo = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }

              if (!this.isNumeric(this.record.cuft) && !this.record.cuft == "")
              {
                  this.error_cuft = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }

              if (!this.isNumeric(this.record.courier_money) && !this.record.courier_money == "")
              {
                  this.error_courier_money = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }
            }
            else
            {
              if (!this.isDate(this.date_receive) && !this.date_receive == "") 
              {
                  this.error_date_receive = '必須是日期 (date required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.supplier == "") 
              {
                  this.error_customer = '必須輸入寄件人 (supplier required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (!this.isEmail(this.email) && !this.email == "") 
              {
                  this.error_email = '必須是email (email required)';
                  $(window).scrollTop(0);
                  return false;
              } 


              if (!this.isNumeric(this.kilo) && !this.kilo == "")
              {
                  this.error_kilo = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }

              if (!this.isNumeric(this.cuft) && !this.cuft == "")
              {
                  this.error_cuft = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }

              if (!this.isNumeric(this.courier_money) && !this.courier_money == "")
              {
                  this.error_courier_money = '必須是數字 (numeric required)';
                  $(window).scrollTop(0);
                  return false;
              }
            }

            return true;
          
        },
    },
})