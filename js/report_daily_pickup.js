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


Vue.config.ignoredElements = ['eng', 'cht'];


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

    // total
    container_total: 0,
    ar_total: 0,
    charge_total: 0,
    total_total: 0,

    // fil
    fil_start_date: "",
    fil_end_date: "",
    fil_creator: "",
    fil_category: "2",

    space: "s",

};

var app = new Vue({
    el: '#receive_record',

    data: mainState,


    created () {
      console.log('Vue created');

      let _this = this;
    let uri = window.location.href.split('?');

    let id = 0;
/*
    if (uri.length >= 2)
    {
      let vars = uri[1].split('&');
      
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        {
          switch (tmp[0]) {
            case "d":
              document.getElementById("start").value = tmp[1];
              _this.fil_start_date = tmp[1];
              break;
            case "e":
              document.getElementById("end").value = tmp[1];
              _this.fil_end_date = tmp[1];
              break;
            case "c":
              _this.fil_category = tmp[1];
              break;
            case "p":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "pg":
              _this.pg = tmp[1];
              break;
            case "page":
              _this.page = tmp[1];
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }
*/
      this.getFirst();
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

                    this.container_total = 0.0;
                    this.ar_total = 0.0;
                    this.charge_total = 0.0;
                    this.total_total = 0.0;

                    for (var i = 0; i < app.receive_records.length; i++) {
                        this.container_total += (app.receive_records[i].loading.length);
                        this.ar_total += parseFloat(app.receive_records[i].ar);
                        this.charge_total += parseFloat(app.receive_records[i].charge);

                        this.total_total += parseFloat(app.receive_records[i].charge_kilo) + parseFloat(app.receive_records[i].charge_cuft);
                    }
        
                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        print() {
          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          let _this = this;
          form_Data.append("jwt", token);
          form_Data.append('date_start', this.date_start);
          form_Data.append('date_end', this.date_end);
    
          axios({
            method: "post",
            url: "api/report_daily_pickup_print.php",
            data: form_Data,
            responseType: "blob",
          })
              .then(function(response) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                   
                      link.setAttribute('download', 'Report Daily Pickup.xlsx');
                   
                    document.body.appendChild(link);
                    link.click();
    
              })
              .catch(function(response) {
                  //handle error
                  console.log(response)
              });
        },

        update_remark: function(item) {
          let remark = item.notes;
          Swal.fire({
            title: "Remarks",
            text: "Input new remarks or edit existing remarks:",
            input: 'text',
            inputValue: remark,
            showCancelButton: true        
        }).then((result) => {
            if (result.isConfirmed) {
              
              this.update_remark_value(item, result.value);
            }
        });
        },

        update_remark_value: function(item, remark) {
          let id = item.id;
          let _this = this;
          let form_Data = new FormData();
          form_Data.append("id", item.id);
          form_Data.append("remark", remark);
          axios({
            method: "post",
            url: "api/update_measure_ph_remark.php",
            data: form_Data,
            responseType: "blob",
          })
              .then(function(response) {
                console.log(response)
                item.notes = remark;
                _this.$forceUpdate();
              })
              .catch(function(response) {
                  //handle error
                  console.log(response)
              });
        },

        
        getFirst: function() {
          let _this = this;
          var today = new Date();
          var first = new Date();
          var yyyy = today.getFullYear();
  
          first = yyyy + "-" + "01-01";
          end = yyyy + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + today.getDate();

          _this.date_start = first;
          _this.date_end = end;

          _this.ini_query();
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

          _this.date_start = first;
          _this.date_end = end;
          this.space = "";

          _this.query("");
        },

        getSpace: function(space) {
          this.space = space

          this.query("s");
        },

        ini_query: function() {
         
          var form_Data = new FormData();
          const token = sessionStorage.getItem('token');
          let _this = this;

          this.space = "i";

          form_Data.append('date_start', this.date_start);
          form_Data.append('date_end', this.date_end);
          form_Data.append('space', "i");
          form_Data.append('type', this.fil_category);

          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data',
                      Authorization: `Bearer ${token}`
                  },
                  url: 'api/report_daily_pickup.php',
                  data: form_Data
              })
              .then(function(response) {
                  //handle success
                  app.receive_records = response.data;

                  _this.container_total = 0.0;
                  _this.ar_total = 0.0;
                  _this.charge_total = 0.0;
                  _this.total_total = 0.0;

                  for (var i = 0; i < app.receive_records.length; i++) {
                      _this.container_total += (app.receive_records[i].loading.length);
                      _this.ar_total += parseFloat(app.receive_records[i].ar);
                      _this.charge_total += parseFloat(app.receive_records[i].charge);

                      _this.total_total += parseFloat(app.receive_records[i].charge_kilo) + parseFloat(app.receive_records[i].charge_cuft);
                  }

                  console.log(_this.ar_total)

              })
              .catch(function(response) {
                  //handle error
                  console.log(response)
              });

      },

        query: function(space) {
         
            var form_Data = new FormData();
            const token = sessionStorage.getItem('token');
            let _this = this;

            this.space = space;

            form_Data.append('date_start', this.date_start);
            form_Data.append('date_end', this.date_end);
            form_Data.append('space', this.space);
            form_Data.append('type', this.fil_category);

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/report_daily_pickup.php',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    _this.receive_records = response.data;

                    // _this.container_total = 0.0;
                    // _this.ar_total = 0.0;
                    // _this.charge_total = 0.0;
                    // _this.total_total = 0.0;

                    // for (var i = 0; i < app.receive_records.length; i++) {
                    //     _this.container_total += (app.receive_records[i].loading.length);
                    //     _this.ar_total += parseFloat(app.receive_records[i].ar);
                    //     _this.charge_total += parseFloat(app.receive_records[i].charge);

                    //     _this.total_total += parseFloat(app.receive_records[i].charge_kilo) + parseFloat(app.receive_records[i].charge_cuft);
                    // }

                    console.log(_this.receive_records)

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
            this.file = '';
            this.isEditing = false;
            this.record = {};

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