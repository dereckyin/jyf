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


Vue.config.ignoredElements = ['eng']

let mainState = {

    date_start: '',
    date_end: '',
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
    // image

    ar_php: '',
    ar: '',
    amount: '',
    payment_date : '',
    note: '',
    status: '',
    rate: '',

    cuft: '',
    kilo: '',

};

var app = new Vue({
    el: '#receive_record',

    data: mainState,


    created () {
      console.log('Vue created');
      //this.getReceiveRecords();
      this.query();
      this.perPage = this.inventory.find(i => i.id === this.perPage);
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },


        c_keyword: function (value) {
            //console.log(c_keyword);

            $.ajax({
            url: 'api/taiwanpay_get_container_number.php?c_keyword=' + value,
            type: 'GET',
            data: '',
            dataType: "json",
            async: true,
            success: function(json) {
              // Add response in Modal body
                var html = "";

                var contentJson = eval(json);
                var container;
                for (var i = 0; i < contentJson.length; i++) {
                    container = contentJson[i].container_number;

                    html += "<tr><td onclick='data(this)'>" + "<input type='checkbox' class='form-check-input' value='" + container + "'><label class='form-check-label'>&nbsp</label>" + "</td><td>" + container + "</td></tr>";
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


        this.setPages();
        return this.paginate(this.receive_records);
      },

    },

    updated: function() {
        console.log('Vue updated')

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
            height: 640,
            width: 720,
            modal: true,
        });


        $("#create-supplier").button().unbind('click').on("click", function() {

            $.ajax({
            url: 'api/taiwanpay_get_container_number.php?c_keyword=',
            type: 'GET',
            data: '',
            dataType: "json",
            async: true,
            success: function(json) {
              // Add response in Modal body
                var html = "";

                var contentJson = eval(json);
                var container;
                for (var i = 0; i < contentJson.length; i++) {
                    container = contentJson[i].container_number;

                    html += "<tr><td onclick='data(this)'>" + "<input type='checkbox' class='form-check-input' value='" + container + "'><label class='form-check-label'>&nbsp</label>" + "</td><td>" + container + "</td></tr>";
                    //$("#contact").append("<tr onclick='data(this)'><td>" + customer + "</td><td>" + c_phone + "</td><td>" + c_fax + "</td><td>" + c_email + "</td></tr>");
                }
                
                $('#contact').html(html);
            }
          });

            dialog.dialog("open");
        });


        
    },

    methods: {
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

        query: function() {
            var date_start = window.document.getElementById('date_start').value;
            var date_end = window.document.getElementById('date_end').value;
            var container_number = window.document.getElementById('container_number').value;
            
            var form_Data = new FormData();

            form_Data.append('date_start', this.formatDate(date_start))
            form_Data.append('date_end', this.formatDate(date_end))

            form_Data.append('container_number', container_number)

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/detail_taiwanpay_query.php',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    app.receive_records = response.data;

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });

        },

        print: function() {
     
            var date_start = window.document.getElementById('date_start').value;
            var date_end = window.document.getElementById('date_end').value;
            var container_number = window.document.getElementById('container_number').value;
            
            var form_Data = new FormData();

            form_Data.append('date_start', this.formatDate(date_start))
            form_Data.append('date_end', this.formatDate(date_end))
   
            form_Data.append('container_number', container_number)

     

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    url: 'api/detail_taiwanpay_print.php',
                    data: form_Data,
                    responseType: 'blob', // important
                })
                .then(function(response) {
                      const url = window.URL.createObjectURL(new Blob([response.data]));
                      const link = document.createElement('a');
                      link.href = url;
             
                        link.setAttribute('download', '台灣付費明細.xlsx');
                
                      document.body.appendChild(link);
                      link.click();

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
          if(date === undefined)
            return '';
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

        cancelRow: function(item){
          this.ar_php = '';
          this.ar = '';
          this.amount = '';
          this.payment_date = '';
          this.note = '';
          this.status = '';
          this.rate = '';

          this.cuft = '';
          this.kilo = '';

          item['is_edited'] = 1; 
          this.is_modifying = false;

          this.query();
      },

        editRow:function(item){
          if(this.is_modifying)
              return;
          else
              this.is_modifying = true;

          item['is_edited'] = 0;

          this.ar_php = item['ar_php'];
          this.ar = item['ar'];
          this.amount = item['amount'];
          this.payment_date = item['payment_date'];
          this.note = item['note'];
          this.status = item['status'];
          this.rate = item['rate'];
      
          this.cuft = item['cuft'];
          this.kilo = item['kilo'];

          console.log(item);
      },


      completeRow: function(item){
        let _this = this;
        
        let formData = new FormData();

        if(item.payment_date == '')
        {
          alert('請輸入付款日期');
          return;
        }

        // if(item.ar_php == '')
        // {
        //   alert('請輸入請款金額(菲幣)');
        //   return;
        // }

        // alert to confirm execute
        if(!confirm('確定要完成此筆資料?'))
          return;

        formData.append('record_id', item.id)
        formData.append('ar_php', item.ar_php)
        formData.append('ar', item.ar)
        formData.append('amount', item.amount)
        formData.append('payment_date', item.payment_date)
        formData.append('note', item.note)
        formData.append('status', '1')
        formData.append('rate', item.rate)

        formData.append('cuft', item.cuft)
        formData.append('kilo', item.kilo)

        const token = sessionStorage.getItem('token');

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/taiwan_pay_edit_row.php',
                data: formData
            })
            .then(function(response) {
                //handle success
                console.log(response)
                _this.query()

            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });

        item['is_edited'] = 1; 
        this.is_modifying = false;
    },

      confirmRow: function(item){
        let _this = this;
        
        let formData = new FormData();

        formData.append('record_id', item.id)
        formData.append('ar_php', item.ar_php)
        formData.append('ar', item.ar)
        formData.append('amount', item.amount)
        formData.append('payment_date', item.payment_date)
        formData.append('kilo', item.kilo)
        formData.append('cuft', item.cuft)
        formData.append('note', item.note)
        formData.append('status', item.status)
        formData.append('rate', item.rate)

        const token = sessionStorage.getItem('token');

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/taiwan_pay_edit_row.php',
                data: formData
            })
            .then(function(response) {
                //handle success
                console.log(response)
                _this.query()

            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });

        item['is_edited'] = 1; 
        this.is_modifying = false;
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
     
            this.courier_pay = '';
            this.courier_money = '';
            this.remark = '';
            this.file = '';
            this.isEditing = false;
            this.record = {};

            this.ar_php = '';
            this.ar = '';
            this.amount = '';
            this.payment_date = '';
            this.note = '';
            this.status = '';
            this.rate = '';

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