Vue.component('date-encode', {
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


Vue.component('date-cr', {
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
    isAdding: false,
    isEditing: false,

    // table
    clicked : 0,

    // measure
    measure_id: 0,
    date_encode:'',
    date_cr:'',
    measure_qty: 0,
    measure_container: '',
    currency_rate: 0.0,
    remark : '',

    error_date_encode:'',
    error_date_cr:'',

    edit_measure:{},
    edit_receive:{},

    loading_records: [],
    measure_records: [],
    receive_records: []


};

var app = new Vue({
    el: '#measure',

    data: mainState,



    created () {
      console.log('Vue created');
      this.getLoadingRecords();
      this.getMeasureRecords();
    },


    updated: function() {
        console.log('Vue updated')

    },

    computed: {
      displayedLoading () {
        console.log('displayedLoading');

        return this.loading_records;
      },

      displayedMeasure () {
        console.log('displayedMeasure');

        return this.measure_records;
      },

      displayedReceive () {
        console.log('displayedReceive');

        return this.receive_records;
      },

      pageUrl() {
          var favorite = [];

          for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

          return 'measure_loading_excel.php?id=' + favorite.join(",");
        }
    },

    methods: {
        getLoadingRecords: function(keyword) {
          console.log("getLoadingRecords");
            axios.get('api/measure_get_loading.php')
                .then(function(response) {
                    console.log(response.data);
                    app.loading_records = response.data;

                    console.log("getLoadingRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getMeasureRecordsById: function(keyword) {
          console.log("getMeasureRecordsById");
            axios.get('api/measure_get_measure_by_id.php?id=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    app.edit_measure = response.data;

                    app.date_encode = app.edit_measure[0]['date_encode'];
                    if(app.date_encode != "")
                    {
                        $('#date_encode').datepicker();
                        $('#date_encode').datepicker('setDate', app.date_encode);
                    }
                    else
                    {
                        $('#date_encode').datepicker();
                        $('#date_encode').datepicker('setDate', null);
                    }
                    app.date_cr = app.edit_measure[0]['date_arrive'];
                    if(app.date_cr != "")
                    {
                        $('#date_cr').datepicker();
                        $('#date_cr').datepicker('setDate', app.date_cr);
                    }
                    else
                    {
                        $('#date_cr').datepicker();
                        $('#date_cr').datepicker('setDate', null);
                    }
                    app.measure_id = app.edit_measure[0]['id'];
                    app.measure_qty = app.edit_measure[0]['qty'];
                    app.measure_container = app.edit_measure[0]['container'];
                    app.currency_rate = app.edit_measure[0]['currency_rate'];
                    app.remark  = app.edit_measure[0]['remark'];

                    app.getMeasureReceiveRecordsByNumber(app.edit_measure[0]['id'], app.edit_measure[0]['batch_num']);

                    console.log("getMeasureRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getMeasureReceiveRecordsByNumber: function(id, keyword) {
            console.log("getMeasureRecords");
            axios.get('api/measure_get_receive_records_by_number.php?id=' + id + '&ids=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    app.receive_records = response.data;

                    console.log("getMeasureRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getMeasureRecords: function(keyword) {
          console.log("getMeasureRecords");
            axios.get('api/measure_get_measure.php')
                .then(function(response) {
                    console.log(response.data);
                    app.measure_records = response.data;

                    console.log("getMeasureRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getReceiveRecords: function(keyword) {
          console.log("getReceiveRecords");
            axios.get('api/measure_get_receive_records.php?ids=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    app.receive_records = response.data;

                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        showReceiveRecords: function() {
            var favorite = [];
            var favorite_container = [];
         
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
              {
                favorite.push(this.loading_records[i].id);
                favorite_container.push(this.loading_records[i].container_number);
                }
            }

            this.measure_qty = favorite.length;
            this.measure_container = favorite_container.join(",");

            this.getReceiveRecords(favorite.join(","));
        },

        addReceiveRecords: function() {
            var favorite = [];
            var favorite_container = [];
         
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
              {
                favorite.push(this.loading_records[i].id);
                favorite_container.push(this.loading_records[i].container_number);
                }
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行丈量 (Please select one row to measure!)");
                //$(window).scrollTop(0);
                return;
            }

            this.isAdding = true;

            this.measure_qty = favorite.length;
            this.measure_container = favorite_container.join(",");

            this.getReceiveRecords(favorite.join(","));
        },

        preventAddRecord: function() {
            if(this.isAdding)
                alert("請先取消丈量資料 (Please cancel before checked!)");
        },

        cancelReceiveRecords: function() {
            this.resetForm();
        },

        update_date_encode: function(date) {
          console.log("update_date_encode");
            this.date_encode = date;
        },

        update_date_cr: function(date) {
          console.log("update_date_cr");
            this.date_cr = date;
        },

        updateMeasureRecord: function() {
            if (this.validateMeasure()) {

                var form_Data = new FormData();

                this.date_encode = document.querySelector("input[id=date_encode]").value;
                this.date_cr = document.querySelector("input[id=date_cr]").value;



                var customer = [];
                for (i = 0; i < this.receive_records.length; i++) 
                {
                    customer.push(this.receive_records[i].customer + '|' + this.receive_records[i].kilo + '|' + this.receive_records[i].cuft + '|' + this.receive_records[i].price_kilo + '|' + this.receive_records[i].price_cuft);
                }

                if(this.measure_id < 1 || customer.length < 1)
                {
                    return;
                }

                form_Data.append('id', this.measure_id)
                form_Data.append('date_encode', this.formatDate(this.date_encode))
                form_Data.append('date_cr', this.formatDate(this.date_cr))
                form_Data.append('currency_rate', this.currency_rate)
                form_Data.append('remark', this.remark)
                form_Data.append('customer', customer.join(","))

                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/measure_update.php',
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

        save_measurement: function() {
            if (this.validateMeasure()) {

                var form_Data = new FormData();

                this.date_encode = document.querySelector("input[id=date_encode]").value;
                this.date_cr = document.querySelector("input[id=date_cr]").value;


                var favorite = [];

                for (i = 0; i < this.loading_records.length; i++) 
                {
                  if(this.loading_records[i].is_checked == 1)
                  {
                    favorite.push(this.loading_records[i].id);
                    }
                }

                var customer = [];
                for (i = 0; i < this.receive_records.length; i++) 
                {
                    customer.push(this.receive_records[i].customer + '|' + this.receive_records[i].kilo + '|' + this.receive_records[i].cuft + '|' + this.receive_records[i].price_kilo + '|' + this.receive_records[i].price_cuft);
                }

                if(favorite.length < 1 || customer.length < 1)
                {
                    return;
                }

                form_Data.append('date_encode', this.formatDate(this.date_encode))
                form_Data.append('date_cr', this.formatDate(this.date_cr))
                form_Data.append('loading_id', favorite.join(","))
                form_Data.append('currency_rate', this.currency_rate)
                form_Data.append('remark', this.remark)
                form_Data.append('customer', customer.join(","))

                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/measure_save.php',
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

        createMeasureRecord: function() {
            console.log("createMeasureRecord");

            if (this.validateForm()) {

                var form_Data = new FormData();

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

        delMeasureRecord: function(ids) {
            console.log("delMeasureRecord")

            //targetId = this.record.id;
            var form_Data = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            form_Data.append('ids', ids);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/measure_delete.php',
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
            this.date_encode = '';
            this.date_cr = '';
            this.currency_rate = 0;
            this.measure_qty = 0;
            this.measure_container = '';
            this.remark = '';

            this.isAdding = false;
            this.isEditing = false;
           
            $('#date_encode').datepicker('setDate', "");
            $('#date_cr').datepicker('setDate', "");

            this.resetError();

            this.getLoadingRecords();
            this.getMeasureRecords();

            app.receive_records = {};
        },

        resetError: function() {
          console.log("resetError");
            this.error_date_encode = '';
            this.error_date_cr = '';
        },

        deleteMeasure() {
          console.log("deleteMeasure");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.measure_records.length; i++) 
            {
              if(this.measure_records[i].is_checked == 1)
                favorite.push(this.measure_records[i].id);
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
              this.delMeasureRecord(favorite.join(", "));

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


        editMeasureRecord() {
          console.log("editMeasureRecord");
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.measure_records.length; i++) 
            {
              if(this.measure_records[i].is_checked == 1)
                favorite.push(this.measure_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                return;
            }

            this.isEditing = true;
            this.isAdding = true;

            this.getMeasureRecordsById(favorite[0]);


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

        validateMeasure() {
            this.resetError();

            if (!this.isDate(this.date_encode) && !this.date_encode == "") 
            {
                this.error_date_encode = '必須是日期 (date required)';
                return false;
            }

            if (!this.isDate(this.date_cr) && !this.date_cr == "") 
            {
                this.error_date_cr = '必須是日期 (date required)';
                return false;
            }

           return true;
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