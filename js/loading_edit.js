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

Vue.component('date_arrive-picker', {
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
    shipper: 0,
    date_sent: '',
    etd_date: '',
    ob_date: '',
    eta_date: '',
    date_arrive: '',
    date_send_his: '',
    etd_date_his: '',
    ob_date_his: '',
    eta_date_his: '',
    date_arrive_his: '',
    broker: '',
    remark: '',
    receive_records: [],
    loading_records: [],

    // error
    error_date_send:'',
    error_etd_date:'',
    error_ob_date:'',
    error_eta_date:'',
    error_date_arrive:'',

    record: {},
    record_tofix: {},
    // paging
    page: 1,
    page_loading: 1,
    //perPage: 10,
    pages: [],
    pages_loading: [],

    name: [
      {name: 'Lyn', id: 'Lyn'},
      {name: 'Roger', id: 'Roger'},
    ],

    inventory: [
        {name: '12', id: 12},
        {name: '25', id: 25},
        {name: '50', id: 50},
        {name: '100', id: 100},
        {name: 'All', id: 10000}
      ],

    perPage: 10000,
    perPage_loading: 12,

    // searching
    keyword: '',
    // image
    selectedImage: null,

        // for edit
    is_modifying : false,
    date_receive: '',
    customer: '',
    description:'',
    quantity: '',
    supplier: '',
    kilo : 0,
    cuft : 0,
    taiwan_pay : 0,
    courier_money : 0,
    e_remark:'',

    // compute
    r_kilo : 0.0,
    n_kilo : 0.0,
    r_cuft : 0.0,
    n_cuft : 0.0

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
      //this.getReceiveRecords();
      this.getLoadingRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);
      this.perPage_loading = this.inventory.find(i => i.id === this.perPage_loading);
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },

      loading_records () {
        console.log('Vue watch receive_records');
        this.setLoadingPages();
      },

      keyword () {
        console.log('Vue watch keyword');
        this.getReceiveRecords();
      }
    },

    computed: {
      displayedPosts () {
        console.log('displayedPosts');

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

        this.setPages();
        return this.paginate(this.receive_records);
      },

      displayedLoading () {
        console.log('displayedLoading');

        this.setLoadingPages();
        return this.paginate_loading(this.loading_records);

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
        getReceiveRecords: function(id) {
          console.log("getReceiveRecords");
          if(id == "")
            return;
            axios.get('api/loading.php?record=' + id)
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
                    //console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getLoadingRecords: function(keyword) {
          console.log("getLoadingRecords");
            axios.get('api/loading.php?loading=1')
                .then(function(response) {
                    console.log(response.data);
                    app.loading_records = response.data;

                    //this.r_kilo = 0.0;
                    //this.n_kilo = 0.0;
                    //this.r_cuft = 0.0;
                    //this.n_cuft = 0.0;
                    
                    

                    //console.log(this.n_kilo);
                    //console.log(this.n_cuft);
                    //console.log(this.r_kilo);
                    //console.log(this.r_cuft);

                    //this.$refs.showUser1.$el.DataTable();
                    //console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        confirmRow: function(item){
            // Ajax request to write changes back to db
            if (!this.isDate(this.date_receive) && !this.date_receive == "") 
            {
                  alert('必須是日期 (date required)');
                  return false;
            } 

            if (!this.isNumeric(this.kilo) && !this.kilo == "")
            {
                  alert('必須是數字 (numeric required)');
                  return false;
            }

            if (!this.isNumeric(this.cuft) && !this.cuft == "")
            {
                  alert('必須是數字 (numeric required)');
                  return false;
            }

            if (!this.isNumeric(this.courier_money) && !this.courier_money == "")
            {
                  alert('必須是數字 (numeric required)');
                  return false;
            }
            
            let formData = new FormData();

            this.taiwan_pay = window.document.getElementById('taiwan_pay'+item['id']).value;

            const date_receive = this.formatDate(this.date_receive);
            const customer = this.customer.trim();
            const description = this.description.trim();
            const quantity = this.quantity.trim();
            const supplier = this.supplier.trim();
            const kilo = this.kilo;
            const cuft = this.cuft;
            const taiwan_pay = this.taiwan_pay;
            const courier_money = this.courier_money;
            const remark = this.e_remark.trim();

            formData.append('date_receive', this.formatDate(this.date_receive))
            formData.append('customer', customer)
            formData.append('description', description)
            formData.append('quantity', quantity)
            formData.append('supplier', supplier)
            formData.append('kilo', this.kilo)
            formData.append('cuft', this.cuft)
            formData.append('taiwan_pay', this.taiwan_pay)
            formData.append('courier_money', this.courier_money)
            formData.append('remark', remark)
            formData.append('id', item['id']);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/loading_edit_row.php',
                    data: formData
                })
                .then(function(response) {
                    //handle success
                    console.log(response)

                    item['date_receive'] = date_receive;
                    item['customer'] = customer;
                    item['description'] = description;
                    item['quantity'] = quantity;
                    item['supplier'] = supplier;
                    item['kilo'] = kilo;
                    item['cuft'] = cuft;
                    item['taiwan_pay'] = taiwan_pay;
                    item['courier_money'] = courier_money;
                    item['remark'] = remark;

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });

            item['is_edited'] = 1; 
            this.is_modifying = false;
        },

        cancelRow: function(item){
            this.date_receive = '';
            this.customer = '';
            this.description = '';
            this.quantity = '';
            this.supplier = '';
            this.kilo = 0;
            this.cuft = 0;
            this.taiwan_pay = 0;
            this.courier_money = 0;
            this.e_remark = '';

            item['is_edited'] = 1; 
            this.is_modifying = false;
        },

        editRow:function(item){
            if(this.is_modifying)
                return;
            else
                this.is_modifying = true;

            item['is_edited'] = 0;

            this.date_receive = item['date_receive'];
            this.customer = item['customer'];
            this.description = item['description'];
            this.quantity = item['quantity'];
            this.supplier = item['supplier'];
            this.kilo = item['kilo'];
            this.cuft = item['cuft'];
            this.taiwan_pay = item['taiwan_pay'];
            this.courier_money = item['courier_money'];
            this.e_remark = item['remark'];

            console.log(item);
        },

        getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

        getIndex_loading(index) {
            return ((this.page_loading - 1) * this.perPage_loading.id) + index
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

        setLoadingPages () {
          console.log('setLoadingPages');
          this.pages_loading = [];
          let numberOfPages = Math.ceil(this.loading_records.length / this.perPage_loading.id);
          if(numberOfPages == 1)
            this.page_loading = 1;
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages_loading.push(index);
          }
        },

        paginate_loading: function (posts) {
          console.log('paginate_loading');
          if(this.page_loading < 1)
            this.page_loading = 1;
          if(this.page_loading > this.pages_loading.length)
            this.page_loading = this.pages_loading.length;

          let page = this.page_loading;
          let perPage = this.perPage_loading.id;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.loading_records.slice(from, to);
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
                formData.append('date_sent', this.date_sent)
                formData.append('etd_date', this.etd_date)
                formData.append('ob_date', this.ob_date)
                formData.append('eta_date', this.eta_date);
                formData.append('date_arrive', this.date_arrive);
                formData.append('broker', this.broker.name)
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
          console.log("updat_eta_date");
            this.date_arrive = date;
        },

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            if(!this.isEditing)
                return;

            this.record.date_sent = document.querySelector("input[id=date_sent]").value;
            this.record.etd_date = document.querySelector("input[id=etd_date]").value;
            this.record.ob_date = document.querySelector("input[id=ob_date]").value;
            this.record.eta_date = document.querySelector("input[id=eta_date]").value;
            this.record.date_arrive = document.querySelector("input[id=date_arrive]").value;


            if (this.validateForm()) {
                targetId = this.record.id;
                let formData = new FormData();

                var favorite = [];

                for (i = 0; i < this.receive_records.length; i++) 
                {
                  if(this.receive_records[i].is_checked == 1)
                    favorite.push(this.receive_records[i].id);
                }

                if (favorite.length < 1) {
                    alert("請選一筆資料進行裝櫃 (Please select one row to load!)");
                    //$(window).scrollTop(0);
                    return;
                }

                //if (!this.isDate(this.record.date_receive) && !this.record.date_receive == "") 
                //{
                //      this.error_date_receive = '必須是日期 (date required)';
                //      $(window).scrollTop(0);
                //      return false;
                    
                //}
                formData.append('shipping_mark', this.record.shipping_mark)
                formData.append('estimate_weight', this.record.estimate_weight)
                formData.append('actual_weight', this.record.actual_weight)
                formData.append('container_number', this.record.container_number)
                formData.append('seal', this.record.seal)
                formData.append('so', this.record.so)
                formData.append('ship_company', this.record.ship_company)
                formData.append('ship_boat', this.record.ship_boat)
                formData.append('neck_cabinet', this.record.neck_cabinet)
                formData.append('shipper', this.record.shipper)
                formData.append('date_sent', this.formatDate(this.record.date_sent))
                formData.append('etd_date', this.formatDate(this.record.etd_date))
                formData.append('ob_date', this.formatDate(this.record.ob_date))
                formData.append('eta_date', this.formatDate(this.record.eta_date));
                formData.append('date_arrive', this.formatDate(this.record.date_arrive));
                formData.append('broker', this.record.broker)
                formData.append('remark', this.record.remark);
                formData.append('record', favorite.toString());
                formData.append('crud', "update");
                formData.append('id', this.record.id);

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
                        if (response.data !== "")
                            console.log(response.data);
                            //const index = app.receive_records.findIndex((e) => e.id === this.record.id);
                            //if (index !== -1) 
                            //    app.receive_records[index] = this.record;
                            
                            //app.resetForm();

                            app.record_tofix['container_number'] = app.record.container_number;
                            app.record_tofix['so'] = app.record.so;
                            app.record_tofix['ship_company'] = app.record.ship_company;
                            app.record_tofix['date_sent'] = app.record.date_sent;
                            app.record_tofix['eta_date'] = app.record.eta_date;
                            app.record_tofix['date_arrive'] = app.record.date_arrive;

                            //if(app.record.date_sent != '')
                            //    app.record_tofix['date_sent_his'] += ',' + app.record.date_sent; 

                            //if(app.record.etd_date != '')
                            //    app.record_tofix['etd_date_his'] += ',' + app.record.etd_date; 

                            //if(app.record.ob_date != '')
                            //    app.record_tofix['ob_date_his'] += ',' + app.record.ob_date; 

                            //if(app.record.eta_date != '')
                            //    app.record_tofix['eta_date_his'] += ',' + app.record.eta_date; 

                            app.isEditing = false;
                    
                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
            }
        },

        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('shipping_mark', "")
            formData.append('estimate_weight', "")
            formData.append('actual_weight', "")
            formData.append('container_number', "")
            formData.append('seal', "")
            formData.append('so', "")
            formData.append('ship_company', "")
            formData.append('ship_boat', "")
            formData.append('neck_cabinet', "")
            formData.append('shipper', 0)
            formData.append('date_sent', "")
            formData.append('etd_date', "")
            formData.append('ob_date', "")
            formData.append('eta_date', "");
            formData.append('date_arrive', "");
            formData.append('broker', "")
            formData.append('remark', "");
            formData.append('record', "");
            formData.append('crud', "del");
            formData.append('id', id);

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

        delAllReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('shipping_mark', "")
            formData.append('estimate_weight', "")
            formData.append('actual_weight', "")
            formData.append('container_number', "")
            formData.append('seal', "")
            formData.append('so', "")
            formData.append('ship_company', "")
            formData.append('ship_boat', "")
            formData.append('neck_cabinet', "")
            formData.append('shipper', 0)
            formData.append('date_sent', "")
            formData.append('etd_date', "")
            formData.append('ob_date', "")
            formData.append('eta_date', "");
            formData.append('date_arrive', "");
            formData.append('broker', "")
            formData.append('remark', "");
            formData.append('record', "");
            formData.append('crud', "del_all");
            formData.append('id', id);

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
            this.shipping_mark = '';
            this.estimate_weight = 0.0;
            this.actual_weight = 0.0;
            this.container_number = '';
            this.seal = '';
            this.so = '';
            this.ship_company = '';
            this.ship_boat = '';
            this.neck_cabinet = '';
            this.shipper = 1;
            this.date_sent = '';
            this.etd_date = '';
            this.ob_date = '';
            this.eta_date = '';
            this.date_arrive = '';
            this.broker.name = '';
            this.remark = '';
            this.isEditing = false;
            this.record = {};

            $('#date_sent').datepicker('setDate', "");
            $('#etd_date').datepicker('setDate', "");
            $('#ob_date').datepicker('setDate', "");
            $('#eta_date').datepicker('setDate', "");
            $('#date_arrive').datepicker('setDate', "");

            this.resetError();

            this.getLoadingRecords();
            this.receive_records = [];
        },

        resetError: function() {
          console.log("resetError");
            this.error_date_send = '';
            this.error_etd_date = '';
            this.error_ob_date = '';
            this.error_date_arrive = '';
            this.error_eta_date = '';

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

        deleteRecord() {
          console.log("deleteRecord");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
              this.delReceiveRecord(favorite.join(", "));
            }

            app.resetForm();
            this.unCheckCheckbox();

            $(window).scrollTop(0);
        },

        deleteAllRecord() {
          console.log("deleteRecord");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

            if (favorite.length < 1) {
                alert("請選一筆資料進行修改刪除 (Please select rows to delete!)");
                $(window).scrollTop(0);
                return;
            }

            var r = confirm("是否確定刪除? (Are you sure to delete?)");
            if (r == true) {
              this.delAllReceiveRecord(favorite.join(", "));
            }

            app.resetForm();
            this.unCheckCheckbox();

            $(window).scrollTop(0);
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

            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                return;
            }
            this.record_tofix = app.loading_records.find(element => element.id == favorite);
            this.record = this.shallowCopy(app.loading_records.find(element => element.id == favorite));
            this.isEditing = true;

            if(this.record.date_sent != "")
            {
                $('#date_sent').datepicker();
                $('#date_sent').datepicker('setDate', this.record.date_sent);
            }
            else
            {
                $('#date_sent').datepicker();
                $('#date_sent').datepicker('setDate', null);
            }

            if(this.record.etd_date != "")
            {
                $('#etd_date').datepicker();
                $('#etd_date').datepicker('setDate', this.record.etd_date);
            }
            else
            {
                $('#etd_date').datepicker();
                $('#etd_date').datepicker('setDate', null);
            }

            if(this.record.ob_date != "")
            {
                $('#ob_date').datepicker();
                $('#ob_date').datepicker('setDate', this.record.ob_date);
            }
            else
            {
                $('#ob_date').datepicker();
                $('#ob_date').datepicker('setDate', null);
            }

            if(this.record.eta_date != "")
            {
                $('#eta_date').datepicker();
                $('#eta_date').datepicker('setDate', this.record.eta_date);
            }
            else
            {
                $('#eta_date').datepicker();
                $('#eta_date').datepicker('setDate', null);
            }

            if(this.record.date_arrive != "")
            {
                $('#date_arrive').datepicker();
                $('#date_arrive').datepicker('setDate', this.record.date_arrive);
            }
            else
            {
                $('#date_arrive').datepicker();
                $('#date_arrive').datepicker('setDate', null);
            }

            //console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;
            this.getReceiveRecords(this.record.id);

            this.unCheckCheckbox();

            this.updateWeightAndCult();

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
            for (i = 0; i < this.loading_records.length; i++) 
            {
              this.loading_records[i].is_checked = false;
            }
          //$(".alone").prop("checked", false);
          //this.clicked = false;
        },

        toggleCheckbox()
        {
            if(!this.isEditing)
                return;

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

          if (!this.isDate(this.record.date_sent) && !this.record.date_sent == "") 
          {
              this.error_date_send = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.record.etd_date) && !this.record.etd_date == "") 
          {
              this.error_etd_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.record.ob_date) && !this.record.ob_date == "") 
          {
              this.error_ob_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.record.eta_date) && !this.record.eta_date == "") 
          {
              this.error_eta_date = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

          if (!this.isDate(this.record.date_arrive) && !this.record.date_arrive == "") 
          {
              this.error_date_arrive = '必須是日期 (date required)';
              $(window).scrollTop(0);
              return false;
          } 

            return true;
          
        },
    },
})