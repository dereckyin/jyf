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

let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : false,

    // data
    date_receive: '',
    customer: '',
    email: '',
    description: '',
    quantity: 0,
    supplier: '',
    kilo: 0.0,
    cuft: 0.0,
    taiwan_pay: 0,
    courier_pay: 0,
    courier_money: 0,
    remark: '',
    receive_records: [],
    record: {},
    file: '',

    // error
    error_date_receive:'',
    error_customer: '',
    error_email: '',

    // paging
    page: 1,
    perPage: 10,
    pages: [],

    // searching
    keyword: ''
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

    updated: function() {
        console.log('Vue updated')

        if (this.isEditing) {
            var dialog;

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

            $("#create-user1").button().on("click", function() {
                dialog.dialog("open");
            });
        }
        else
        {
          var dialog;

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

            $("#create-user").button().on("click", function() {
                dialog.dialog("open");
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
     geteRecord: function() {
      console.log("getReceiveRecords");
        axios.get('http://192.168.3.142:8081/hello', { crossdomain: true })
            .then(function(response) {
                console.log(response.data);
                //app.receive_records = response.data;
                
                //this.$refs.showUser1.$el.DataTable();
                console.log("getReceiveRecords");

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
                    
                    //this.$refs.showUser1.$el.DataTable();
                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },



        paginate: function (posts) {
          console.log('paginate');
          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.receive_records.slice(from, to);
        },

        createReceiveRecord: function() {
            console.log("createReceiveRecord");

            if (this.validateForm()) {

                let formData = new FormData();
                //console.log("datepicker:", this.date_receive)
                //console.log(document.querySelector("input[name=datepicker]").value)
                formData.append('date_receive', this.date_receive)
                formData.append('customer', this.customer)
                formData.append('email', this.email)
                formData.append('description', this.description)
                formData.append('quantity', this.quantity)
                formData.append('supplier', this.supplier)
                formData.append('kilo', this.kilo)
                formData.append('cuft', this.cuft)
                formData.append('taiwan_pay', this.taiwan_pay)
                formData.append('courier_pay', this.courier_pay)
                formData.append('courier_money', this.courier_money)
                formData.append('remark', this.remark)
                formData.append('file', this.file);
                formData.append('crud', "insert");
                formData.append('id', '');

                var receive_record = {};
                formData.forEach(function(value, key) {
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
                        data: formData
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

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();
            console.log("datepicker:", this.record.date_receive)
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('date_receive', this.record.date_receive)
            formData.append('customer', this.record.customer)
            formData.append('email', this.record.email)
            formData.append('description', this.record.description)
            formData.append('quantity', this.record.quantity)
            formData.append('supplier', this.record.supplier)
            formData.append('kilo', this.record.kilo)
            formData.append('cuft', this.record.cuft)
            formData.append('taiwan_pay', this.record.taiwan_pay)
            formData.append('courier_pay', this.record.courier_pay)
            formData.append('courier_money', this.record.courier_money)
            formData.append('remark', this.record.remark)
            formData.append('file', this.record.file);
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/receive_record.php',
                    data: formData
                    
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
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('date_receive', "")
            formData.append('customer', "")
            formData.append('email', "")
            formData.append('description', "")
            formData.append('quantity', "")
            formData.append('supplier', "")
            formData.append('kilo', "")
            formData.append('cuft', "")
            formData.append('taiwan_pay', "")
            formData.append('courier_pay', "")
            formData.append('courier_money', "")
            formData.append('remark', "")
            formData.append('file', "");
            formData.append('crud', "del");
            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/receive_record.php',
                    data: formData
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                        console.log(response.data);
                    //this.$forceUpdate();
                    //app.resetForm();
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

            $('#adddate').datepicker('setDate', null);

            this.error_date_receive = '';
            this.error_customer = '';
            this.error_email = '';

            this.resetError();
            this.resetFile();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },

        resetError: function() {
          console.log("resetError");
            this.error_date_receive = '';
            this.error_customer = '';
            this.error_email = '';
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

            $.each($("input[name='record_id']:checked"), function() {
                favorite.push($(this).val());
            });
            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
                for( var j = 0; j <  favorite.length; j++)
                    this.delReceiveRecord(favorite[j]);

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

        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            $.each($("input[name='record_id']:checked"), function() {
                favorite.push($(this).val());
            });
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                $(window).scrollTop(0);
                return;
            }
            this.record = this.shallowCopy(app.receive_records.find(element => element.id == favorite));
            this.isEditing = true;

          
            $('.block.record').toggleClass('show');

            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            $(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        isNumeric: function (n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        },

        isDate: function (value) {
            switch (typeof value) {
                case 'string':
                    return !isNaN(Date.parse(value));
                case 'object':
                    if (value instanceof Date) {
                        return !isNaN(value.getTime());
                    }
                default:
                    return false;
            }
        },

        isEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },

        unCheckCheckbox()
        {
          $(".alone").prop("checked", false);
          this.clicked = false;
        },

        toggleCheckbox()
        {
          $(".alone").prop("checked", !this.clicked);
          this.clicked = !this.clicked;
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

              if (this.record.customer == "") 
              {
                  this.error_customer = '必須輸入收件人 (customer required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (!this.isEmail(this.record.email)) 
              {
                  this.error_email = '必須是email (email required)';
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

              if (this.customer == "") 
              {
                  this.error_customer = '必須輸入收件人 (customer required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (!this.isEmail(this.email)) 
              {
                  this.error_email = '必須是email (email required)';
                  $(window).scrollTop(0);
                  return false;
              } 
            }

            return true;
          
        },
    },
})