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

Vue.config.ignoredElements = ['eng', 'cht']


var app = new Vue({

    el: '#receive_record',

    data: {

        container_number:'',

        receive_records: [],

        courier_query:false,

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

        c_keyword:'',
        s_keyword:'',

        customer:'',
        supplier:'',

        c_options : [],
        s_options : [],

        c_filter    : [],
        s_filter    : [],

        url_ip: "https://storage.googleapis.com/feliiximg/",

        pic_preview: [],
        selectedImage: null,

        items: [],
        record: {},

        _date_start: '',
        _date_end: '',
        _pay_start : '',
        _pay_end : '',
        _flight_start : '',
        _flight_end : '',
        _arrive_start : '',
        _arrive_end : '',

    },

    created () {
      console.log('Vue created');
      //this.getReceiveRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);

      this.getCustomers();
      this.getSuppliers();
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },


        c_keyword: function (value) {
            //console.log(c_keyword);
            this.c_filter = [];

            if (value != '') {
                this.c_filter = this.c_options.filter(option => {
                    return option.name.toLowerCase().indexOf(value.toLowerCase()) > -1;
                });
            }
          
        },

        s_keyword: function (value) {
            //console.log(c_keyword);

            this.s_filter = [];

            if (value != '') {
                this.s_filter = this.s_options.filter(option => {
                    return option.name.toLowerCase().indexOf(value.toLowerCase()) > -1;
                });
            }
          
        }

    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');


        this.setPages();
        return this.paginate(this.receive_records);
      },

    },

    mounted: function() {
        console.log('Vue updated')

        var supdialog;
        var cusdialog;

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


        supdialog = $("#supModal").dialog({
            autoOpen: false,
            height: 640,
            width: 720,
            modal: true,
        });

        cusdialog = $("#cusModal").dialog({
            autoOpen: false,
            height: 640,
            width: 720,
            modal: true,
        });


        $("#create-supplier").button().unbind('click').on("click", function() {

   

            supdialog.dialog("open");
        });

        $("#create-customer").button().unbind('click').on("click", function() {


            cusdialog.dialog("open");
        });


        
    },

    methods: {

        getCustomers: function() {
            axios.get('api/query_receive_customer.php')
            .then(function(response) {
                console.log(response.data);
                app.c_options = response.data;
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        getSuppliers: function() {
            axios.get('api/query_receive_supplier.php')
            .then(function(response) {
                console.log(response.data);
                app.s_options = response.data;
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

        
        zoom_rec(id) {
            this.selectedImage = "true";
            this.pic_preview = this.shallowCopy(this.receive_records.find(element => element.id == id)['pic']);
  
            let imgdialog = $("#imgModal").dialog({
                  autoOpen: false,
                  height: 720,
                  width: 640,
                  modal: true,
              });
  
           imgdialog.dialog("open");
            console.log("Zoom", this.selectedImage);
      
            //this.$forceUpdate();
          },

          shallowCopy(obj) {
            console.log("shallowCopy");
              var result = {};
              for (var i in obj) {
                  result[i] = obj[i];
              }
              return result;
          },

        query: function() {
            var date_start = window.document.getElementById('date_start').value;
            var date_end = window.document.getElementById('date_end').value;

            var pay_start = window.document.getElementById('pay_start').value;
            var pay_end = window.document.getElementById('pay_end').value;

            var flight_start = window.document.getElementById('flight_start').value;
            var flight_end = window.document.getElementById('flight_end').value;

            var arrive_start = window.document.getElementById('arrive_start').value;
            var arrive_end = window.document.getElementById('arrive_end').value;

            var customer = window.document.getElementById('customer').value;
            var supplier = window.document.getElementById('supplier').value;

            var description = window.document.getElementById('description').value;
            // var remark = window.document.getElementById('remark').value;
            var sort = window.document.getElementById('sort').value;
            
            var form_Data = new FormData();

            form_Data.append('date_start', this.formatDate(date_start))
            form_Data.append('date_end', this.formatDate(date_end))
            form_Data.append('pay_start', this.formatDate(pay_start))
            form_Data.append('pay_end', this.formatDate(pay_end))
            form_Data.append('flight_start', this.formatDate(flight_start))
            form_Data.append('flight_end', this.formatDate(flight_end))
            form_Data.append('arrive_start', this.formatDate(arrive_start))
            form_Data.append('arrive_end', this.formatDate(arrive_end))

            form_Data.append('customer', customer)
            form_Data.append('supplier', supplier)

            form_Data.append('description', description)
            form_Data.append('remark', '')
            form_Data.append('sort', sort)

            const token = sessionStorage.getItem('token');


            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/query_airship_records.php',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    app.receive_records = response.data;

                    app.c_filter = '';
                    app.s_filter = '';
                    app.s_keyword = '';
                    app.c_keyword = '';

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });

        },

        print: function() {
            var token = localStorage.getItem("token");

            var date_start = window.document.getElementById('date_start').value;
            var date_end = window.document.getElementById('date_end').value;

            var pay_start = window.document.getElementById('pay_start').value;
            var pay_end = window.document.getElementById('pay_end').value;

            var flight_start = window.document.getElementById('flight_start').value;
            var flight_end = window.document.getElementById('flight_end').value;

            var arrive_start = window.document.getElementById('arrive_start').value;
            var arrive_end = window.document.getElementById('arrive_end').value;

            var customer = window.document.getElementById('customer').value;
            var supplier = window.document.getElementById('supplier').value;

            var description = window.document.getElementById('description').value;
            // var remark = window.document.getElementById('remark').value;
            var sort = window.document.getElementById('sort').value;
            
            var form_Data = new FormData();

            form_Data.append("jwt", token);
            form_Data.append('date_start', this.formatDate(date_start))
            form_Data.append('date_end', this.formatDate(date_end))
            form_Data.append('pay_start', this.formatDate(pay_start))
            form_Data.append('pay_end', this.formatDate(pay_end))
            form_Data.append('flight_start', this.formatDate(flight_start))
            form_Data.append('flight_end', this.formatDate(flight_end))
            form_Data.append('arrive_start', this.formatDate(arrive_start))
            form_Data.append('arrive_end', this.formatDate(arrive_end))

            form_Data.append('customer', customer)
            form_Data.append('supplier', supplier)

            form_Data.append('description', description)
            form_Data.append('remark', '')
            form_Data.append('sort', sort)

            axios({
                    method: 'post',
                    url: 'api/query_airship_records_print.php',
                    data: form_Data,
                    responseType: 'blob', // important
                })
                .then(function(response) {
                      const url = window.URL.createObjectURL(new Blob([response.data]));
                      const link = document.createElement('a');
                      link.href = url;
   
                      link.setAttribute('download', '空運記錄.xlsx');
      
                      document.body.appendChild(link);
                      link.click();

                      app.c_filter = '';
                    app.s_filter = '';
                    app.s_keyword = '';
                    app.c_keyword = '';

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
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

        show_ntd: function(item) {
            this.record = item.items;
            //$('#details_NTD').modal('show');
            let ntd = $("#details_NTD").dialog({
                autoOpen: false,
                height: 640,
                width: 720,
                modal: true,
            });

            ntd.dialog("open");
          },
      
          show_php: function(item) {
            this.record = item.items_php;
            //$('#details_PHP').modal('show');

            let ntd = $("#details_PHP").dialog({
                autoOpen: false,
                height: 640,
                width: 720,
                modal: true,
            });

            ntd.dialog("open");
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


        date_start: function(date) {
          console.log("date_start");
            if (this.isEditing)
                this.record.date_start = date;
            else
                this._date_start = date;
        },

        date_end: function(date) {
            console.log("date_end");
            if (this.isEditing)
                this.record.date_end = date;
            else
                this._date_end = date;
        },

        pay_start: function(date) {
            console.log("pay_start");
            if (this.isEditing)
                this.record.pay_start = date;
            else
                this._pay_start = date;
        },

        pay_end: function(date) {
            console.log("pay_end");
            if (this.isEditing)
                this.record.pay_end = date;
            else
                this._pay_end = date;
        },

        flight_start: function(date) {
            console.log("flight_start");
            if (this.isEditing)
                this.record.flight_start = date;
            else
                this._flight_start = date;
        },

        flight_end: function(date) {
            console.log("flight_end");
            if (this.isEditing)
                this.record.flight_end = date;
            else
                this._flight_end = date;
        },  

        arrive_start: function(date) {
            console.log("arrive_start");
            if (this.isEditing)
                this.record.arrive_start = date;
            else
                this._arrive_start = date;
        }, 

        arrive_end: function(date) {
            console.log("arrive_end");
            if (this.isEditing)
                this.record.arrive_end = date;
            else
                this._arrive_end = date;
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
            this.sort = '';
            this.file = '';
            this.isEditing = false;
            this.record = {};

            this.c_filter = [];
            this.s_filter = [];

            this.s_keyword = '';
            this.c_keyword = '';

            $('#adddate').datepicker('setDate', "");
            $('#adddate1').datepicker('setDate', "");

            this.resetError();
            this.resetFile();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },

    },
})
