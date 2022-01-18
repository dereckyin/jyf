
Vue.config.ignoredElements = ['eng']
Vue.config.ignoredElements = ['cht']

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
    measure_container_id:'',
    currency_rate: 0.0,
    remark : '',

    error_mesasge:'',

    filter : 'F',

    edit_measure:{},
    edit_receive:{},

    loading_records: [],
    measure_records: [],
    receive_records: [],

    record : [],
    item:[],
    payment: [],
    payment_record: [],

    contactor: [],

    perPage_loading:0,
    inventory:0,
    page_loading:0,
    pages_loading:0,

    show_detail:false,
    show_record:false,
    need_to_update: false,

    ar: 0,
    detail_id:0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
        {name: '12', id: 12},
        {name: '25', id: 25},
        {name: '50', id: 50},
      ],

    perPage: 12,

};

var app = new Vue({
    el: '#measure',

    data: mainState,

    created () {
      console.log('Vue created');
      this.perPage = this.inventory.find(i => i.id === this.perPage);
      this.getContactors();
      this.load_measurement();
      this.getMeasures();
    },


    updated: function() {
   
    },

    watch: {

    },

    computed: {
      displayedLoading () {
        console.log('displayedLoading');

        this.setPages();
        return this.paginate(this.loading_records);

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

        delReceiveRecords() {
            let _this = this;

            var favorite = [];
            var favorite_container = [];
             
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
                favorite_container.push(this.loading_records[i].container_number);
            }

            if(favorite.length != 1)
            {
                Swal.fire({
                    title: 'Warning',
                    text: 'Please select one record only',
                    type: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

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
            
                    _this.date_encode = document.querySelector("input[id=date_encode]").value;
                    _this.date_cr = document.querySelector("input[id=date_cr]").value;

                    form_Data.append("jwt", token);
                    form_Data.append("id", favorite.join(","));
                         
            
                    axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/measure_delete_save.php",
                    data: form_Data,
                    })
                    .then(function(response) {
                        //handle success
                        Swal.fire({
                        html: response.data.message,
                        icon: "info",
                        confirmButtonText: "OK",
                        });
            
                        _this.resetForm();
                    
            
                    })
                    .catch(function(error) {
                        //handle error
                        Swal.fire({
                        text: JSON.stringify(error),
                        icon: "info",
                        confirmButtonText: "OK",
                        });
            
                    });
                } else {
                  return;
                }
              });

      
          },

        save_measure() {
            let _this = this;
            if (!this.validateMeasure()) {
                Swal.fire({
                    text: _this.error_message,
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  return;
            }

      
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
      
            this.date_encode = document.querySelector("input[id=date_encode]").value;
            this.date_cr = document.querySelector("input[id=date_cr]").value;

            form_Data.append("jwt", token);
            form_Data.append("id", this.measure_id);
            form_Data.append('date_encode', this.formatDate(this.date_encode))
            form_Data.append('date_cr', this.formatDate(this.date_cr))
            form_Data.append('measure_container_id', this.measure_container_id)
            form_Data.append('currency_rate', this.currency_rate)
            form_Data.append('remark', this.remark)
      
            form_Data.append("detail", JSON.stringify(this.receive_records));
        
      
            axios({
              method: "post",
              headers: {
                "Content-Type": "multipart/form-data",
              },
              url: "api/measure_update_save.php",
              data: form_Data,
            })
              .then(function(response) {
                //handle success
                Swal.fire({
                  html: response.data.message,
                  icon: "info",
                  confirmButtonText: "OK",
                });
      
                _this.resetForm();
             
      
              })
              .catch(function(error) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(error),
                  icon: "info",
                  confirmButtonText: "OK",
                });
      
              });
          },

        refresh_kilo() {
            for(var i = 0; i < this.receive_records.length; i++) {
                var num = "";
               
                if(this.receive_records[i]['kilo'] == "")
                {
                  this.receive_records[i]['kilo_price'] = "";
                }
                else
                {
                  num = parseFloat(this.receive_records[i]['kilo'] == "" ? 0 : this.receive_records[i]['kilo']);
                  this.receive_records[i]['kilo_price'] = (num < 3000 ? 36.5 : 34.5).toLocaleString('en-US', {maximumFractionDigits:2});  
                }
              }
        },

        refresh_cuft() {
            for(var i = 0; i < this.receive_records.length; i++) {
                var num = "";
               
                if(this.receive_records[i]['cuft'] == "")
                {
                  this.receive_records[i]['cuft_price'] = "";
                }
                else
                {
                  num = parseFloat(this.receive_records[i]['cuft'] == "" ? 0 : this.receive_records[i]['cuft']);
                  this.receive_records[i]['cuft_price'] = (num < 300 ? 365 : 345).toLocaleString('en-US', {maximumFractionDigits:2});  
                }
              }
        },

        decompose_item: async function () {
          let favorite = [];

          for (i = 0; i < this.receive_records.length; i++) {
              if(this.receive_records[i].is_checked == 1)
              {
                let measure = this.receive_records[i].measure;
                    for (j = 0; j < measure.length; j++) {
                      if(measure[j].payment_status != "")
                      {
                        alert('Item that already completed the total payment cannot be decomposed.');
                        this.getMeasures();
                        return;
                      }
                    }
                  favorite.push(this.receive_records[i].group_id);
              }
            }

            let _this = this;

        var form_data = new FormData();
        form_data.append('id', favorite.join(","));
 
        let token = localStorage.getItem("accessToken");
  
        try {
          let res = await axios({
            method: 'post',
            url: 'api/pickup_set_decompose.php',
            data: form_data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
        });
          
        } catch (err) {
          console.log(err)
          alert('error')
        }

        this.getMeasures();

        },

        sortedIndex(array, value) {
            var low = 0,
                high = array.length;
        
            while (low < high) {
                var mid = (low + high) >>> 1;
                if (array[mid]['record'][0]['customer'] + array[mid]['record'][0]['date_receive'] < value) low = mid + 1;
                else high = mid;
            }
            return low;
        },

        merge_item: async function () {

            let favorite = [];

            for (i = 0; i < this.receive_records.length; i++) {
                if(this.receive_records[i].is_checked == 1)
                {
                    let measure = this.receive_records[i].measure;
                    for (j = 0; j < measure.length; j++) {
                      if(measure[j].payment_status != "")
                      {
                          alert('Item that already completed the total payment cannot merge with other items.');
                          this.getMeasures();
                        return;
                      }
                      favorite.push(measure[j].id);
                    }
                    
                }
              }

              let _this = this;

          var form_data = new FormData();
          form_data.append('id', favorite.join(","));
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_merge.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();

        },

        getContactors: function(keyword) {
            console.log("getContactors");
            let _this = this;

            const params = {
                keyword: keyword,
              
              };

              let token = localStorage.getItem("accessToken");

            axios
                .get("api/measure_get_contactor.php", {
                params,
                headers: { Authorization: `Bearer ${token}` },
                })
                .then((response) => {
                    console.log(response.data);
                    _this.contactor = response.data;

                    console.log("getContactors");

                })
                .catch(function(error) {
                    console.log(error);
                });
          
        },


        refresh_select () {
            for (i = 0; i < this.receive_records.length; i++) {
                for (j = 0; j < this.receive_records[i].record.length; j++) {
                let select_id = 'client_' + this.receive_records[i].record[j].id;
                $("#" + select_id).selectpicker('refresh');
                }
              }
        },

        load_measurement:function() {
            this.show_detail = true;
            this.getRecords();
        },

        setPages () {
            console.log('setPages');
            this.pages = [];
            let numberOfPages = Math.ceil(this.loading_records.length / this.perPage.id);
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
            return  this.loading_records.slice(from, to);
          },

        del_plus_detail : function(id) {
          var index = this.payment.findIndex(x => x.id ===id);
          if (index > -1) {
            this.payment.splice(index, 1);
          }
        },

        add_plus_detail: function() {
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
            "remark": '',
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
            pay += this.payment[i].amount == "" ? 0 : Number(this.payment[i].amount);
          if(charge - pay < 0 && row.type == 1)
            row.remark = "Cash " + row.amount + " - " + (Number(charge) - Number(pay) + Number(row.amount)) + " = P" + Math.abs(charge - pay);
        },

        getRecords: function(keyword) {
      
            axios.get('api/pickup_get_measure_ph.php')
                .then(function(response) {
                    console.log(response.data);
                    app.loading_records = response.data;

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        item_record: function(item) {
          this.record = this.shallowCopy(item);

        },

        item_encode: function(item) {
          this.item = this.shallowCopy(item);

        },

        item_payment: function(record, ar, detail_id) {
          this.payment = [];
          this.payment_record = [];

          //this.payment = [].concat(record);
          this.payment = JSON.parse(JSON.stringify(record));
          this.ar = ar;

          this.detail_id = detail_id;

          this.payment_record = this.shallowCopy(record);
        },

        encode_save: async function() {
          let _this = this;

          var form_data = new FormData();
          form_data.append('id', this.item.id);
          form_data.append('encode', this.item.encode);
          form_data.append('encode_status', '');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_encode.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        record_cancel: function() {
          for (let obj in this.record) {
            this.record[obj].org_pick_date = this.record[obj].pick_date;
          }
        },

        record_save: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('record', JSON.stringify(this.record));
           form_data.append('encode_status', '');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_record.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        payment_save: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('id', this.payment_record.id);
          form_data.append('record', JSON.stringify(this.payment));
           form_data.append('encode_status', '');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_payment.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        payment_save_complete: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('id', this.payment_record.id);
          form_data.append('record', JSON.stringify(this.payment));
           form_data.append('encode_status', 'C');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_payment.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        encode_save_complete: async function() {
          let _this = this;

          var form_data = new FormData();
          form_data.append('id', this.item.id);
          form_data.append('encode', this.item.encode);
          form_data.append('encode_status', 'C');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_encode.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        record_save_complete: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('record', JSON.stringify(this.record));
           form_data.append('encode_status', 'C');
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_record.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }

          this.getMeasures();
        },

        pickup: async function(keyword) {
          var favorite = [];

            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

            await this.add_pickup(favorite.join(","));

            this.load_measurement();
            this.getMeasures();
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

        getMeasures: function() {
            let _this = this;

            axios.get('api/pickup_get_records.php?keyword=' + this.filter)
                .then(function(response) {
                    console.log(response.data);
                    _this.receive_records = response.data;

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

        async add_pickup(keyword) {
          let _this = this;

          var form_data = new FormData();
          form_data.append('ids', keyword);
          form_data.append('remark', '');

          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_measure.php',
              data: form_data,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }
    
        },

        async getReceivedData(keyword) {
            let _this = this;
      
            const params = {
                ids: keyword,
            
            };
      
            let token = localStorage.getItem("accessToken");
      
            try {
              let res = await axios.get("api/measure_get_measure_records_ph.php", {
                params,
                headers: { Authorization: `Bearer ${token}` },
              });
              
              this.receive_records = res.data;
            } catch (err) {
              console.log(err)
              alert('error')
            }
      
          },

        getReceiveRecords: function(keyword) {
            let _this = this;
          console.log("getReceiveRecords");
            axios.get('api/measure_get_receive_records.php?ids=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    _this.receive_records = response.data;

                    _this.refresh_select();
                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        cancelRecord () {
            for (i = 0; i < this.loading_records.length; i++) 
            {
              this.loading_records[i].is_checked = 0;
            }

            this.show_record = false;
            this.receive_records = [];

        },

        exportReceiveRecords () {

            var favorite = [];

            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }
            
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("id", favorite.join(","));
           

            axios({
                method: "post",
                url: "create_measure_excel.php",
                data: form_Data,
                responseType: "blob",
            })
                .then(function(response) {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement("a");
                link.href = url;

                link.setAttribute("download", "create_measure_excel.xlsx");

                document.body.appendChild(link);
                link.click();
                })
                .catch(function(response) {
                console.log(response);
                });

        },

        async showReceiveRecords () {
            
            var favorite = [];
            var favorite_container = [];
         
            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
              {
                favorite.push(this.loading_records[i].id);
                favorite_container.push(this.loading_records[i].container_number);
                this.measure_container = this.loading_records[i].container;
                this.measure_qty = this.loading_records[i].qty;
                this.date_encode = this.loading_records[i].date_encode;
                this.date_cr = this.loading_records[i].date_arrive;
                this.measure_id = this.loading_records[i].id;
                this.currency_rate = this.loading_records[i].currency_rate;
                this.remark = this.loading_records[i].remark;

                $('#date_encode').datepicker();
                $('#date_encode').datepicker('setDate', this.date_encode);

                $('#date_cr').datepicker();
                $('#date_cr').datepicker('setDate', this.date_cr);
                }
            }

            if(favorite.length != 1)
            { 
                Swal.fire({
                    title: 'Warning',
                    text: 'Please select one record only',
                    type: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            this.measure_container_id = favorite.join(",");

            await this.getReceivedData(favorite.join(","));

            this.show_record = true;

           this.refresh_select();
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
            this.date_encode = '';
            this.date_cr = '';
            this.currency_rate = 0;
            this.measure_qty = 0;
            this.measure_container = '';
            this.remark = '';

            this.isAdding = false;
            this.isEditing = false;

            this.show_record = false;
            this.show_detail = true;

            this.measure_id = 0;
            this.record = [];
            this.item = [];
           
            $('#date_encode').datepicker('setDate', "");
            $('#date_cr').datepicker('setDate', "");

            this.resetError();

            this.getLoadingRecords();
  
            app.receive_records = {};
        },

        resetError: function() {
          console.log("resetError");

            this.error_mesage = '';
        },


        shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
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

    },
})