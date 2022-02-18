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

Vue.component('etd-date-picker', {
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

Vue.component('ob-date-picker', {
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

Vue.component('eta-date-picker', {
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

Vue.component('date-arrive-picker', {
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

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    shipping_mark: '',
    estimate_weight: 0.0,
    actual_weight: 0.0,
    container_number: '',
    seal: '',
    so: '',
    ship_company: '',
    ship_boat: '',
    neck_cabinet: '',
    shipper : 0,
    date_sent: '',
    etd_date: '',
    ob_date: '',
    eta_date: '',
    date_arrive: '',
    broker: '',
    remark: '',
    receive_records: [],

    // error
    error_date_send:'',
    error_etd_date:'',
    error_ob_date:'',
    error_eta_date:'',
    error_date_arrive: '',


    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    name: [
      {name: 'Lyn', id: 'Lyn'},
      {name: 'Roger', id: 'Roger'},
    ],

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
    // image
    selectedImage: null,

    // compute
    r_kilo : 0.0,
    n_kilo : 0.0,
    r_cuft : 0.0,
    n_cuft : 0.0,

    // don't repeat submit
    submit : false,

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
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },

      keyword () {
        console.log('Vue watch keyword');
        this.getReceiveRecords();
      }
    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');

        this.setPages();
        return this.paginate(this.receive_records);
      }
    },

    updated: function() {
        console.log('Vue updated')
        /*
        var table = $('#showUser1').DataTable();
        $('#showUser1 tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
        } );
        */
        //document.querySelector("input[name=showUser1]").DataTable();
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

        createLoadingRecord: function() {
            console.log("createLoadingRecord");

            this.date_sent = document.querySelector("input[id=date_sent]").value;
            this.etd_date = document.querySelector("input[id=etd_date]").value;
            this.ob_date = document.querySelector("input[id=ob_date]").value;
            this.eta_date = document.querySelector("input[id=eta_date]").value;
            this.date_arrive = document.querySelector("input[id=date_arrive]").value;

            if (this.validateForm()) {

                let formData = new FormData();

                var favorite = [];

                for (i = 0; i < this.receive_records.length; i++) 
                {
                  if(this.receive_records[i].is_checked == 1)
                    favorite.push(this.receive_records[i].id);
                }

                //$.each($("input[name='record_id']:checked"), function() {
                //    favorite.push($(this).val());
                //});
                if (favorite.length < 1) {
                    alert("請選一筆資料進行裝櫃 (Please select one row to load!)");
                    //$(window).scrollTop(0);
                    return;
                }

                let _this = this;
                if(this.submit == true)
                    return;

                this.submit = true;

                formData.append('shipping_mark', this.shipping_mark)
                formData.append('estimate_weight', this.estimate_weight)
                formData.append('actual_weight', this.actual_weight)
                formData.append('container_number', this.container_number)
                formData.append('seal', this.seal)
                formData.append('so', this.so)
                formData.append('ship_company', this.ship_company)
                formData.append('ship_boat', this.ship_boat)
                formData.append('neck_cabinet', this.neck_cabinet)
                formData.append('shipper', this.shipper)
                formData.append('date_sent', this.formatDate(this.date_sent))
                formData.append('etd_date', this.formatDate(this.etd_date))
                formData.append('ob_date', this.formatDate(this.ob_date))
                formData.append('eta_date', this.formatDate(this.eta_date));
                formData.append('date_arrive', this.formatDate(this.date_arrive));
                formData.append('broker', this.broker)
                formData.append('remark', this.remark);
                formData.append('record', favorite.toString());
                formData.append('crud', "insert");
                formData.append('id', '');

                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/loading.php',
                        data: formData
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)
                        _this.submit = false;
                        app.resetForm();

                    })
                    .catch(function(response) {
                        //handle error
                        _this.submit = false;
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

        updateWeightAndCult: function(event) {
          console.log("updateWeightAndCult");
            this.n_kilo = 0.0;
            this.n_cuft = 0.0;
            this.r_kilo = 0.0;
            this.r_cuft = 0.0;

            for(let i=0; i<this.receive_records.length; i++)
            {   
                if(this.receive_records[i].is_checked == 1)
                {
                    this.n_kilo += parseFloat(this.receive_records[i].kilo);
                    this.n_cuft += parseFloat(this.receive_records[i].cuft);
                }
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

        update_date_sent: function(date) {
          console.log("update_date_sent");
            this.date_sent = date;
        },

        update_etd_date: function(date) {
          console.log("update_etd_date");
            this.etd_adddate = date;
        },

        update_ob_date: function(date) {
          console.log("update_ob_date");
            this.ob_adddate = date;
        },

        updat_eta_date: function(date) {
          console.log("updat_eta_date");
            this.eta_date = date;
        },

        updat_date_arrive: function(date) {
          console.log("updat_date_arrive");
            this.date_arrive = date;
        },

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();
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

            let _this = this;

            if(this.submit == true)
                    return;

            this.submit = true;

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
            formData.append('file', this.file);
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
                        _this.submit = false;
                        app.resetForm();
                      
                  }
                })
                .catch(function(response) {
                    //handle error
                    _this.submit = false;
                    console.log(response)
                });
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

        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            let _this = this;
            if(this.submit == true)
                    return;

            this.submit = true;

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
                    _this.submit = false;
                    if (response.data !== "")
                        console.log(response.data);
                    //this.$forceUpdate();
                    app.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    _this.submit = false;
                    console.log(response)
                });
        },

        resetForm: function() {
          console.log("resetForm");
            this.shipping_mark = '';
            this.estimate_weight = 0.0;
            this.actual_weight = 0.0;
            this.container_number = '';
            this.seal = '';
            this.so = '';
            this.ship_company = '';
            this.ship_boat = '';
            this.neck_cabinet = '';
            this.shipper = 0;
            this.date_sent = '';
            this.etd_date = '';
            this.ob_date = '';
            this.eta_date = '';
            this.date_arrive = '';
            this.broker.name = '';
            this.remark = '';
            this.isEditing = false;
            this.record = {};

            this.submit = false;

            $('#date_sent').datepicker('setDate', "");
            $('#etd_date').datepicker('setDate', "");
            $('#ob_date').datepicker('setDate', "");
            $('#eta_date').datepicker('setDate', "");
            $('#date_arrive').datepicker('setDate', "");

            this.resetError();

            this.getReceiveRecords();

            toggleme($('a.btn.detail'), $('.block.record'), 'show');
        },

        resetError: function() {
          console.log("resetError");
            this.error_date_send = '';
            this.error_etd_date = '';
            this.error_ob_date = '';
            this.error_eta_date = '';
            this.error_date_arrive = '';

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

        zoom(url) {
          this.selectedImage = "img/" + url;

          let imgdialog = $("#imgModal").dialog({
                autoOpen: false,
                height: 720,
                width: 640,
                modal: true,
            });

          $("#img_pre").attr('src', this.selectedImage);

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
            this.updateWeightAndCult();
        },

        validateForm() {
            console.log("validateForm");
            this.resetError();

          if (!this.isDate(this.date_sent) && !this.date_sent == "") 
          {
              this.error_date_send = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.etd_date) && !this.etd_date == "") 
          {
              this.error_etd_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.ob_date) && !this.ob_date == "") 
          {
              this.error_ob_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.eta_date) && !this.eta_date == "") 
          {
              this.error_eta_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.date_arrive) && !this.date_arrive == "") 
          {
              this.error_date_arrive = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

            return true;
          
        },
    },
})