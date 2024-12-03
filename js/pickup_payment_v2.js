
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

    checker: "",

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
        {name: '10', id: 10},
        {name: '25', id: 25},
        {name: '50', id: 50},
      ],

    perPage: 10,


    pick_id: 0,
    measure_to_edit : [],
    measure_to_seperate : [],
    group_a : [],
    group_b : [],

    // don't repeat submit
    submit : false,

    search: "",
    search_date: "",

    payment_measure : [],

    // export
    exp_dr:"",
    exp_date:"",
    exp_sold_to:"",
    exp_quantity:"",
    exp_unit:"",
    exp_discription:"",
    exp_amount:"",

    adv:"",

    assist_by:"",

    export_record: {},

    export_discription_org: "",
    exp_amount_org: "",

    container_numbers: [],
    container: "",

    editable: false,
    measure: {},

};

var app = new Vue({
    el: '#measure',

    data: mainState,

    created () {
      console.log('Vue created');
      this.perPage = this.inventory.find(i => i.id === this.perPage);
      //this.getContactors();
      this.load_measurement();
      this.getMeasures();
      this.getAccess();
    },


    updated: function() {
   
    },

    watch: {

      filter(value) {
        if(value == '' || value == 'D')
          this.page = 1;
      },

      page() {
        if(this.filter == 'D' || this.filter == ''){
          this.getMeasures('search');
        }
      }
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

      getAccess: function() {
        let _this = this;


        axios({
            method: 'get',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/editable_access_control.php',
            data: {}
        })
        .then(function(response) {
            //handle success
            _this.editable = response.data.editable;

        })
        .catch(function(response) {
            //handle error
            Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
            })
        });
    },

      B_change_C: function(){
        let row = this.group_b;
        
        if(row.kilo_price == "" && row.kilo <= 45)
            nkilo = 2000;
        else
            nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

        if(row.cuft_price == "" && row.cuft <= 4.5)
            ncuft = 2000;
        else
            ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

    B_change_D: function(){
      let row = this.group_b;
      if(row.kilo_price == "" && row.kilo <= 45)
          nkilo = 2000;
      else
          nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

      if(row.cuft_price == "" && row.cuft <= 4.5)
          ncuft = 2000;
      else
          ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

      B_change_A: function(){
        let row = this.group_b;

        // kilo <= 45 nkilo = 2000, kilo > 45 nkilo = kilo * 45, kilo > 300 nkilo = kilo * 42, kilo > 1000 nkilo = kilo * 40, kilo > 3000 nkilo = kilo * 38.5
        nkilo = 0;
        if(row.kilo != "")
        {
          if(row.kilo <= 45)
              nkilo = 2000;
          if(row.kilo > 45)
              nkilo = 45 * row.kilo;
          if(row.kilo >= 300)
              nkilo = 42 * row.kilo;
          if(row.kilo >= 1000)
              nkilo = 40 * row.kilo;
          if(row.kilo >= 3000)
              nkilo = 38.5 * row.kilo;
        }
        ncuft = (row.cuft == "" ? 0 : row.cuft)  * (row.cuft_price == "" ? 0 : row.cuft_price);
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        kilo_price = "";
        if(row.kilo > 45)
            kilo_price = 45;
        if(row.kilo >= 300)
            kilo_price = 42;
        if(row.kilo >= 1000)
            kilo_price = 40;
        if(row.kilo >= 3000)
            kilo_price = 38.5;

        if(kilo_price != "")
            row.kilo_price = kilo_price.toLocaleString('en-US', {maximumFractionDigits:2});  
        else
            row.kilo_price = "";

        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

    B_change_B: function(){
      let row = this.group_b;
        nkilo = (row.kilo == "" ? 0 : row.kilo)  * (row.kilo_price == "" ? 0 : row.kilo_price);
  
        ncuft = 0;
        if(row.cuft != "")
        {
          if(row.cuft <= 4.5)
              ncuft = 2000;
          if(row.cuft > 4.5)
              ncuft = 450 * row.cuft;
          if(row.cuft >= 30)
              ncuft = 430 * row.cuft;
          if(row.cuft >= 100)
              ncuft = 410 * row.cuft;
          if(row.cuft >= 300)
              ncuft = 395 * row.cuft;
        }
      
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        cuft_price = "";
        if(row.cuft > 4.5)
            cuft_price = 450;
        if(row.cuft >= 30)
            cuft_price = 430;
        if(row.cuft >= 100)
            cuft_price = 410;
        if(row.cuft >= 300)
            cuft_price = 395;

        if(cuft_price != "")
            row.cuft_price = cuft_price.toLocaleString('en-US', {maximumFractionDigits:2});
        else
            row.cuft_price = "";


        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

      A_change_C: function(){
        let row = this.group_a;
        if(row.kilo_price == "" && row.kilo <= 45)
            nkilo = 2000;
        else
            nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

        if(row.cuft_price == "" && row.cuft <= 4.5)
            ncuft = 2000;
        else
            ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

    A_change_D: function(){
      let row = this.group_a;
      if(row.kilo_price == "" && row.kilo <= 45)
          nkilo = 2000;
      else
          nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

      if(row.cuft_price == "" && row.cuft <= 4.5)
          ncuft = 2000;
      else
          ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

      A_change_A: function(){
        let row = this.group_a;
        // kilo <= 45 nkilo = 2000, kilo > 45 nkilo = kilo * 45, kilo > 300 nkilo = kilo * 42, kilo > 1000 nkilo = kilo * 40, kilo > 3000 nkilo = kilo * 38.5
        nkilo = 0;
        if(row.kilo != "")
        {
          if(row.kilo <= 45)
              nkilo = 2000;
          if(row.kilo > 45)
              nkilo = 45 * row.kilo;
          if(row.kilo >= 300)
              nkilo = 42 * row.kilo;
          if(row.kilo >= 1000)
              nkilo = 40 * row.kilo;
          if(row.kilo >= 3000)
              nkilo = 38.5 * row.kilo;
        }

        ncuft = (row.cuft == "" ? 0 : row.cuft)  * (row.cuft_price == "" ? 0 : row.cuft_price);
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        kilo_price = "";
        if(row.kilo > 45)
            kilo_price = 45;
        if(row.kilo >= 300)
            kilo_price = 42;
        if(row.kilo >= 1000)
            kilo_price = 40;
        if(row.kilo >= 3000)
            kilo_price = 38.5;

        if(kilo_price != "")
            row.kilo_price = kilo_price.toLocaleString('en-US', {maximumFractionDigits:2});  
        else
            row.kilo_price = "";

        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

    A_change_B: function(){
      let row = this.group_a;
        nkilo = (row.kilo == "" ? 0 : row.kilo)  * (row.kilo_price == "" ? 0 : row.kilo_price);

        ncuft = 0;
        if(row.cuft != "")
        {
          if(row.cuft <= 4.5)
              ncuft = 2000;
          if(row.cuft > 4.5)
              ncuft = 450 * row.cuft;
          if(row.cuft >= 30)
              ncuft = 430 * row.cuft;
          if(row.cuft >= 100)
              ncuft = 410 * row.cuft;
          if(row.cuft >= 300)
              ncuft = 395 * row.cuft;
        }

        cuft_price = "";
        if(row.cuft > 4.5)
            cuft_price = 450;
        if(row.cuft >= 30)
            cuft_price = 430;
        if(row.cuft >= 100)
            cuft_price = 410;
        if(row.cuft >= 300)
            cuft_price = 395;
      
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        if(cuft_price != "")
            row.cuft_price = cuft_price.toLocaleString('en-US', {maximumFractionDigits:2});
        else
            row.cuft_price = "";


        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

      change_C: function(){
        let row = this.measure_to_edit;
        
        if(row.kilo_price == "" && row.kilo <= 45)
            nkilo = 2000;
        else
            nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

        if(row.cuft_price == "" && row.cuft <= 4.5)
            ncuft = 2000;
        else
            ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

    change_D: function(){
      let row = this.measure_to_edit;
        
      if(row.kilo_price == "" && row.kilo <= 45)
            nkilo = 2000;
        else
            nkilo = (row.kilo_price == "" ? 0 : row.kilo_price) * (row.kilo == "" ? 0 : row.kilo);

        if(row.cuft_price == "" && row.cuft <= 4.5)
            ncuft = 2000;
        else
            ncuft = (row.cuft_price == "" ? 0 : row.cuft_price) * (row.cuft == "" ? 0 : row.cuft);
        
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        app.$forceUpdate();
    },

      change_A: function(){
        let row = this.measure_to_edit;
        nkilo = 0;
        if(row.kilo != "")
        {
          if(row.kilo <= 45)
              nkilo = 2000;
          if(row.kilo > 45)
              nkilo = 45 * row.kilo;
          if(row.kilo >= 300)
              nkilo = 42 * row.kilo;
          if(row.kilo >= 1000)
              nkilo = 40 * row.kilo;
          if(row.kilo >= 3000)
              nkilo = 38.5 * row.kilo;
        }
        ncuft = (row.cuft == "" ? 0 : row.cuft)  * (row.cuft_price == "" ? 0 : row.cuft_price);
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        kilo_price = "";
        if(row.kilo > 45)
            kilo_price = 45;
        if(row.kilo >= 300)
            kilo_price = 42;
        if(row.kilo >= 1000)
            kilo_price = 40;
        if(row.kilo >= 3000)
            kilo_price = 38.5;

        if(kilo_price != "")
            row.kilo_price = kilo_price.toLocaleString('en-US', {maximumFractionDigits:2});  
        else
            row.kilo_price = "";

        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

    change_B: function(){
      let row = this.measure_to_edit;
        nkilo = (row.kilo == "" ? 0 : row.kilo)  * (row.kilo_price == "" ? 0 : row.kilo_price);
        ncuft = 0;
        if(row.cuft != "")
        {
          if(row.cuft <= 4.5)
              ncuft = 2000;
          if(row.cuft > 4.5)
              ncuft = 450 * row.cuft;
          if(row.cuft >= 30)
              ncuft = 430 * row.cuft;
          if(row.cuft >= 100)
              ncuft = 410 * row.cuft;
          if(row.cuft >= 300)
              ncuft = 395 * row.cuft;
        }
      
        row.charge = (ncuft > nkilo) ? ncuft.toFixed(2) : nkilo.toFixed(2);

        cuft_price = "";
        if(row.cuft > 4.5)
            cuft_price = 450;
        if(row.cuft >= 30)
            cuft_price = 430;
        if(row.cuft >= 100)
            cuft_price = 410;
        if(row.cuft >= 300)
            cuft_price = 395;

        if(cuft_price != "")
            row.cuft_price = cuft_price.toLocaleString('en-US', {maximumFractionDigits:2});
        else
            row.cuft_price = "";

        if(row.cuft == "" && row.cuft_price == "" && row.kilo == "" && row.kilo_price == "")
            row.charge = "";

        app.$forceUpdate();
    },

      edit_measurement: function(){

        var selected_cnt = 0;

        for (i = 0; i < this.receive_records.length; i++) {
            if(this.receive_records[i].is_checked == 1)
            {

              selected_cnt++;

              this.measure_to_edit = [];

              let measure = this.receive_records[i].measure;
              if(measure.length > 1)
              {
                alert('Editing the measurement data for a merged item is not allowed.');
                this.getMeasures();
                this.measure_to_edit = [];
                return;
              }
                  
              this.measure_to_edit = JSON.parse(JSON.stringify(measure[0]));
              this.measure_to_edit.customer = this.measure_to_edit.record_cust.join(", ");
           
            }

            
          }

          if(selected_cnt > 1)
          {
            alert('Please select one measurement to edit.');
            this.getMeasures();
            this.measure_to_edit = [];
            return;
          }

          if(selected_cnt == 1)
            $('#edit_record_modal').modal('show');
          
            
      },

      edit_measurement_cancel: function()
      {
        this.measure_to_edit = [];
        $('#edit_record_modal').modal('hide');
        this.getMeasures();
      },

      checker_confirm: async function()
      {
        let _this = this;

        var form_data = new FormData();
  
        form_data.append('record', JSON.stringify(this.record));
         form_data.append('encode_status', '');
 
        let token = localStorage.getItem("accessToken");

        if(this.submit == true)
          return;

        this.submit = true;
  
        try {
          let res = await axios({
            method: 'post',
            url: 'api/pickup_set_record_checker.php',
            data: form_data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
        });
          
        } catch (err) {
          console.log(err)
          alert('error')
        }

        this.submit = false;

        this.getMeasures();
      },

      
      editable_record: async function()
      {
        let _this = this;

        var form_data = new FormData();
  
        form_data.append('id', this.measure.id);
 
        let token = localStorage.getItem("accessToken");

        if(this.submit == true)
          return;

        this.submit = true;
  
        try {
          let res = await axios({
            method: 'post',
            url: 'api/pickup_set_record_editable.php',
            data: form_data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
        });
          
        } catch (err) {
          console.log(err)
          alert('error')
        }

        this.submit = false;

        this.getMeasures();
      },

      
      editable_payment: async function()
      {
        let _this = this;

        var form_data = new FormData();
  
        form_data.append('id', this.detail_id);
 
        let token = localStorage.getItem("accessToken");

        if(this.submit == true)
          return;

        this.submit = true;
  
        try {
          let res = await axios({
            method: 'post',
            url: 'api/pickup_set_payment_editable.php',
            data: form_data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
        });
          
        } catch (err) {
          console.log(err)
          alert('error')
        }

        this.submit = false;

        this.getMeasures();
      },

      seperate_record: function(){

        var selected_cnt = 0;
        var record_cnt = 0;

        for (i = 0; i < this.receive_records.length; i++) {
            if(this.receive_records[i].is_checked == 1)
            {

              selected_cnt++;

              this.measure_to_seperate = [];
              this.group_a = [];
              this.group_b = [];

              let measure = this.receive_records[i].measure;
              if(measure.length > 1)
              {
                alert('Decomposing the measurement data for a merged item is not allowed.');
                this.getMeasures();
                this.measure_to_seperate = [];
                this.group_a = [];
                this.group_b = [];
                return;
              }
                  
              this.measure_to_seperate = JSON.parse(JSON.stringify(measure[0]));
              this.pick_id = this.receive_records[i].id;
              for(var j = 0; j < this.measure_to_seperate.record.length; j++){
                this.measure_to_seperate.record[j].group = 'A';
                record_cnt += this.measure_to_seperate.record.length;
              }
            }
            
          }

          if(selected_cnt > 1)
          {
            alert('Please select one measurement to edit.');
            this.getMeasures();
            this.measure_to_seperate = [];
            this.pick_id = 0;
            this.group_a = [];
            this.group_b = [];
            return;
          }

          this.group_a.customer = this.measure_to_seperate.record_cust.join(", ");
          this.group_a.kilo = this.measure_to_seperate.kilo;
          this.group_a.cuft = this.measure_to_seperate.cuft;
          this.group_a.kilo_price = this.measure_to_seperate.kilo_price;
          this.group_a.cuft_price = this.measure_to_seperate.cuft_price;
          this.group_a.charge = this.measure_to_seperate.charge;

          this.group_b.customer = '';
          this.group_b.kilo = '';
          this.group_b.cuft = '';
          this.group_b.kilo_price = '';
          this.group_b.cuft_price = '';
          this.group_b.charge = '';


          if(selected_cnt == 1 && record_cnt > 1)
            $('#seperate_record_modal').modal('show');
          else
            this.seperate_record_cancel();
            
      },

      seperate_record_cancel: function()
      {
        this.measure_to_seperate = [];
        this.group_a = [];
        this.group_b = [];
        this.pick_id = 0;
        $('#seperate_record_modal').modal('hide');
        this.getMeasures();
      },

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

          save_seperate_data:async function() {
            let _this = this;

            var group_a_cnt = 0;
            var group_b_cnt = 0;

            for(var i=0; i < this.measure_to_seperate.record.length; i++)
            {
              if(this.measure_to_seperate.record[i].group == 'A')
                group_a_cnt++;

              if(this.measure_to_seperate.record[i].group == 'B')
                group_b_cnt++;
            }

            if(group_a_cnt == 0 || group_b_cnt == 0) 
            {
              alert('Please seperate measurement data into both groups.');
              this.seperate_record_cancel();
              return;
            }

            if(this.submit == true)
              return;
            this.submit = true;
              

            obj_a = {
              "customer" : this.group_a.customer,
              "kilo" : this.group_a.kilo,
              "cuft" : this.group_a.cuft,
              "kilo_price" : this.group_a.kilo_price,
              "cuft_price" : this.group_a.cuft_price,
              "charge": this.group_a.charge,
           
            }

            obj_b = {
              "customer" : this.group_b.customer,
              "kilo" : this.group_b.kilo,
              "cuft" : this.group_b.cuft,
              "kilo_price" : this.group_b.kilo_price,
              "cuft_price" : this.group_b.cuft_price,
              "charge": this.group_b.charge,
           
            }

            var token = localStorage.getItem("token");
            var form_Data = new FormData();
      
            form_Data.append("jwt", token);
      
            form_Data.append("pick_id", this.pick_id);
            form_Data.append("measure", JSON.stringify(this.measure_to_seperate));
            form_Data.append("group_a", JSON.stringify(obj_a));
            form_Data.append("group_b", JSON.stringify(obj_b));
      
            axios({
              method: "post",
              headers: {
                "Content-Type": "multipart/form-data",
              },
              url: "api/measure_to_seperate_save.php",
              data: form_Data,
            })
              .then(function(response) {
                //handle success
                Swal.fire({
                  html: response.data.message,
                  icon: "info",
                  confirmButtonText: "OK",
                });
                _this.submit = false;
                _this.seperate_record_cancel();
      
              })
              .catch(function(error) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(error),
                  icon: "info",
                  confirmButtonText: "OK",
                });
                _this.submit = false;
                _this.seperate_record_cancel();
              });
          },

          save_measurement_data:async function() {
            let _this = this;
            
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
      
            form_Data.append("jwt", token);
      
            form_Data.append("detail", JSON.stringify(this.measure_to_edit));
        
            if(this.submit == true)
                    return;
            this.submit = true;

            axios({
              method: "post",
              headers: {
                "Content-Type": "multipart/form-data",
              },
              url: "api/measure_to_edit_save.php",
              data: form_Data,
            })
              .then(function(response) {
                //handle success
                Swal.fire({
                  html: response.data.message,
                  icon: "info",
                  confirmButtonText: "OK",
                });
                _this.submit = false;
                _this.edit_measurement_cancel();
      
              })
              .catch(function(error) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(error),
                  icon: "info",
                  confirmButtonText: "OK",
                });
                _this.submit = false;
                _this.edit_measurement_cancel();
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

                  kilo_price = "";
                if(num > 45)
                    kilo_price = 45;
                if(num >= 300)
                    kilo_price = 42;
                if(num >= 1000)
                    kilo_price = 40;
                if(num >= 3000)
                    kilo_price = 38.5;
                  
                  this.receive_records[i]['kilo_price'] = (kilo_price == "" ? "" : kilo_price.toLocaleString('en-US', {maximumFractionDigits:2}));  
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

                  cuft_price = "";
                  if(num > 4.5)
                      cuft_price = 450;
                  if(num >= 30)
                      cuft_price = 430;
                  if(num >= 100)
                      cuft_price = 410;
                  if(num >= 300)
                      cuft_price = 395;

                      
                  this.receive_records[i]['cuft_price'] = (cuft_price == "" ? "" : cuft_price.toLocaleString('en-US', {maximumFractionDigits:2}));  
                }
              }
        },

        decompose_item: async function () {
          let favorite = [];
      
          row_selected = 0;

          for (i = 0; i < this.receive_records.length; i++) {
              if(this.receive_records[i].is_checked == 1)
              {
                  let measure = this.receive_records[i].measure;
                  row_selected = 0
                  
                    for (j = 0; j < measure.length; j++) {
                      if(measure[j].payment_status != "")
                      {
                        alert('Item that already completed the total payment cannot be decomposed.');
                        this.getMeasures();
                        return;
                      }
                      else
                      {
                        row_selected++;
                      }
                    }

                    if(row_selected < 2)
                    {
                      Swal.fire({
                        title: 'Info',
                        text: 'This item cannot be decomposed.',
                        type: 'Info',
                        confirmButtonText: 'OK'
                      });
                      return;
                    }
                    
                    favorite.push(this.receive_records[i].group_id);
              }
            }

            

            if(favorite.length == 0) {
              Swal.fire({
                  title: 'Info',
                  text: 'Please select records to decompose',
                  type: 'Info',
                  confirmButtonText: 'OK'
              });
                return;
            }

            let _this = this;

        var form_data = new FormData();
        form_data.append('id', favorite.join(","));
 
        let token = localStorage.getItem("accessToken");

        if(this.submit == true)
            return;
        this.submit = true;

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
          _this.submit = false;
            _this.getMeasures();
            return;
        }

        this.submit = false;

        Swal.fire({
          title: 'Info',
          text: 'Decompose Successfully',
          type: 'Info',
          confirmButtonText: 'OK'
      });

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

        archive_record: async function () {

          let favorite = [];
          let gavorite = [];

          for (i = 0; i < this.receive_records.length; i++) {
              if(this.receive_records[i].is_checked == 1)
              {
                  if(this.receive_records[i].group_id == 0)
                    favorite.push(this.receive_records[i].id);
                  else
                    gavorite.push(this.receive_records[i].group_id);
                
              }
            }


            let _this = this;

        var form_data = new FormData();
        form_data.append('id', favorite.join(","));
        form_data.append('gid', gavorite.join(","));
 
        let token = localStorage.getItem("accessToken");

        if(this.submit == true)
          return;
      this.submit = true;
  
        try {
          let res = await axios({
            method: 'post',
            url: 'api/pickup_set_archive.php',
            data: form_data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
        });
          
        } catch (err) {
          console.log(err)
          alert('error')
          _this.submit = false;
          _this.getMeasures();
          return;
        }

        this.submit = false;

      Swal.fire({
        title: 'Info',
        text: 'Archive Successfully',
        type: 'Info',
        confirmButtonText: 'OK'
    });

        this.getMeasures();

      },


        merge_item: async function () {

            let favorite = [];

            var row_selected = 0;

            for (i = 0; i < this.receive_records.length; i++) {
                if(this.receive_records[i].is_checked == 1)
                {
                  row_selected += 1;
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

              if(row_selected < 2) {
                Swal.fire({
                    title: 'Info',
                    text: 'Please select records to merge',
                    type: 'Info',
                    confirmButtonText: 'OK'
                });
                  return;
              }

              let _this = this;

          var form_data = new FormData();
          form_data.append('id', favorite.join(","));
   
          let token = localStorage.getItem("accessToken");

          if(this.submit == true)
            return;
        this.submit = true;
    
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
            _this.submit = false;
            _this.getMeasures();
            return;
          }

          this.submit = false;

        Swal.fire({
          title: 'Info',
          text: 'Merge Successfully',
          type: 'Info',
          confirmButtonText: 'OK'
      });

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

        setPages (data) {
            console.log('setPages');
            this.pages = [];
            let numberOfPages = Math.ceil(data / this.perPage.id);
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
            "change": '',
            "courier" : '',
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
            pay += (this.payment[i].amount == "" ? 0 : Number(this.payment[i].amount)) - (this.payment[i].courier == "" ? 0 : Number(this.payment[i].courier));
          if(charge - pay < 0 && row.type == 1)
          {
            row.remark = "Cash " + row.amount + " - " + (Number(charge) - Number(pay) + Number(row.amount)).toFixed(2) + " = P" + Math.abs(charge - pay).toFixed(2) + " (Change)";
            row.change = Math.abs(charge - pay).toFixed(2);
          }
          else
          {
            row.remark = '';
            row.change = '';

            if(row.type != 2)
            {
              row.remark = '';
              row.change = '';
            }
            else
              row.remark = 'Deposit to Feliix Inc Account';
          }

          
        },

        deposit_remark: function(row) {
          if(row.type != 2)
            row.remark = '';
          else
            row.remark = 'Deposit to Feliix Inc Account';
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

        item_record_checker: function(item, checker, measure) {
          this.record = this.shallowCopy(item);
          this.measure = this.shallowCopy(measure);
          this.checker = checker;
        },

        item_encode: function(item) {
          this.item = this.shallowCopy(item);

        },

        item_warehousefee: function(item) {
          this.item = this.shallowCopy(item);

        },


        change_days: function(row) {
            if(row.days == '')
              return;

            days = Math.floor(row.days);
           
          if(isNaN(days))
            return;

          // days between 1 and 15
          
            if(days >= 1 && days <= 15)
            {
              row.kilo_unit = 1;
            }

            // days between 16
            if(days >= 16)
            {
              row.kilo_unit = 2;
            }
          

            if(days >= 1 && days <= 15)
            {
              row.cuft_unit = 10;
            }

            // days between 16
            if(days >= 16)
            {
              row.cuft_unit = 20;
            }
          

            row.kilo_amount = isNaN((days * row.kilo_unit * row.warehouse_kilo).toFixed(2)) ? 0 : (days * row.kilo_unit * row.warehouse_kilo).toFixed(2);
            row.cuft_amount = isNaN((days * row.cuft_unit * row.warehouse_cuft).toFixed(2)) ? 0 : (days * row.cuft_unit * row.warehouse_cuft).toFixed(2);

            app.$forceUpdate();

      },

      change_kilo: function(row) {
        if(row.days == '')
          return;

        days = Math.floor(row.days);

        
        if(isNaN(days))
            return;

            row.kilo_amount = isNaN((days * row.kilo_unit * row.warehouse_kilo).toFixed(2)) ? 0 : (days * row.kilo_unit * row.warehouse_kilo).toFixed(2);

        app.$forceUpdate();

  },

  change_cuft: function(row) {
    if(row.days == '')
      return;

    days = Math.floor(row.days);

    
    if(isNaN(days))
        return;

        row.cuft_amount = isNaN((days * row.cuft_unit * row.warehouse_cuft).toFixed(2)) ? 0 : (days * row.cuft_unit * row.warehouse_cuft).toFixed(2);

    app.$forceUpdate();

},

      change_unit: function(row) {
        if(row.days == '')
          return;

        days = Math.floor(row.days);

        
        if(isNaN(days))
            return;

            row.kilo_amount = isNaN((days * row.kilo_unit * row.warehouse_kilo).toFixed(2)) ? 0 : (days * row.kilo_unit * row.warehouse_kilo).toFixed(2);
            row.cuft_amount = isNaN((days * row.cuft_unit * row.warehouse_cuft).toFixed(2)) ? 0 : (days * row.cuft_unit * row.warehouse_cuft).toFixed(2);

        app.$forceUpdate();

  },

  change_amount: function(row) {

    row.kilo_amount = isNaN(Number(row.kilo_amount)) ? 0.00 : Number(row.kilo_amount).toFixed(2);
    row.cuft_amount = isNaN(Number(row.cuft_amount)) ? 0.00 : Number(row.cuft_amount).toFixed(2);

app.$forceUpdate();

},



      async get_export(detail_id) {
        let _this = this;

        const params = {
          measure_id: detail_id,
        
        };
  
        let token = localStorage.getItem("accessToken");
  
        try {
          let res = await axios.get("api/pickup_get_export.php", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          this.export_record = res.data;
        } catch (err) {
          console.log(err)
          alert('error')
        }
      },

        item_export: async function(item, record, ar, detail_id, measure) {

          this.export_record = {};
          this.exp_date = '';
          this.exp_quantity = '';
          this.exp_unit = '';
          this.adv = '';

          await this.get_export(detail_id);

          this.payment = [];
          this.payment_record = [];

          //this.payment = [].concat(record);
          this.payment = JSON.parse(JSON.stringify(record));
          for(const element of this.payment) {
            element.is_selected = 1;
          }
          this.ar = ar;

          this.detail_id = detail_id;

          this.payment_record = this.shallowCopy(record);

          this.payment_measure = this.shallowCopy(measure);

          this.record = JSON.parse(JSON.stringify(item.record));
          if(this.record.constructor === Array)
          {
            for(const element of this.record) {
                element.is_selected = 1;
            }
          }

          this.exp_dr = item.encode;
          this.exp_sold_to = item.record_cust.join();
          this.exp_amount = (item.charge !== '' ? '₱ ' + Number(item.charge).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '');

          this.exp_amount_org = this.exp_amount;

          // warehouse fee
          if(item.warehouse_fee !== '')
          {
            this.exp_amount = this.exp_amount + '\n' + '₱ ' + Number(item.warehouse_fee).toFixed(2).toLocaleString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          }
          
          kilo_price = 0;
          cuft_price = 0;

          if(item.kilo_price !== "")
            kilo_price = item.kilo_price;
          else
          {
            if(item.kilo > 45)
                kilo_price = 45;
            if(item.kilo > 300)
                kilo_price = 42;
            if(item.kilo >= 1000)
                kilo_price = 40;
            if(item.kilo >= 3000)
                kilo_price = 38.5;
          }

          if(item.cuft_price !== "")
            cuft_price = item.cuft_price;
          else
          {
            if(item.cuft > 4.5)
                cuft_price = 450;
            if(item.cuft > 30)
                cuft_price = 430;
            if(item.cuft >= 100)
                cuft_price = 410;
            if(item.cuft >= 300)
                cuft_price = 395;
          }

          nkilo = 2000;
          if(item.kilo > 45)
              nkilo = 45 * item.kilo;
          if(item.kilo > 300)
              nkilo = 42 * item.kilo;
          if(item.kilo >= 1000)
              nkilo = 40 * item.kilo;
          if(item.kilo >= 3000)
              nkilo = 38.5 * item.kilo;

          ncuft = 2000;
            if(item.cuft > 4.5)
                ncuft = 450 * item.cuft;
            if(item.cuft > 30)
                ncuft = 430 * item.cuft;
            if(item.cuft >= 100)
                ncuft = 410 * item.cuft;
            if(item.cuft >= 300)
                ncuft = 395 * item.cuft;

                // charge = Number(item.cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_price).toFixed(2);

                // if(item.kilo * item.kilo_price == item.charge)
                //   charge = Number(item.kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_price).toFixed(2);

          if(item.kilo_price==0 && item.cuft_price==0){

            if(item.kilo==0 && item.cuft==0){
                charge = '';
            }
            else{

                if(item.kilo==0){
                    charge = Number(item.cuft).toFixed(2) + ' cuft';
               }
                else{

                    if(item.cuft==0){
                        charge = Number(item.kilo).toFixed(2) + ' kilo';
                    }
                    else{

                        if(item.kilo*45 >= item.cuft*450){
                            charge = Number(item.kilo).toFixed(2) + ' kilo';
                        }
                        else{
                            charge = Number(item.cuft).toFixed(2) + ' cuft';
                        }

                    }

                }

            }
            
          }
          else{

            if(Math.abs(item.charge - item.kilo * item.kilo_price) >= Math.abs(item.charge - item.cuft * item.cuft_price))
                charge = Number(item.cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_price).toFixed(2);
            else
                charge = Number(item.kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_price).toFixed(2);

         }

         // charge = (ncuft > nkilo) ? Number(item.cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_price).toFixed(2) : Number(item.kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_price).toFixed(2);
          this.exp_discription = charge;

          this.exp_discription_org = charge;

          // warehouse charge
          warehouse_charge = "";
          if(item.warehouse_fee !== '')
          {
              if(item.way == "kilo")
              {
                warehouse_charge = Number(item.warehouse_kilo).toFixed(2) + ' kilo @ ₱ ' + Number(item.kilo_unit).toFixed(2);
              }

              if(item.way == "cuft")
              {
                warehouse_charge = Number(item.warehouse_cuft).toFixed(2) + ' cuft @ ₱ ' + Number(item.cuft_unit).toFixed(2);
              }
          }

          if(warehouse_charge !== "")
          {
            this.exp_discription = this.exp_discription + '\n' + warehouse_charge + ' Warehouse Fee, ' + item.days + ' day(s)';
          }

          if(this.export_record.length > 0)
          {
            //this.exp_dr = this.export_record[0].exp_dr;
            this.exp_date = this.export_record[0].exp_date;
            //this.exp_sold_to = this.export_record[0].exp_sold_to;
            this.exp_quantity = this.export_record[0].exp_quantity;
            this.exp_unit = this.export_record[0].exp_unit;
            //this.exp_discription = this.export_record[0].exp_discription;
            //this.exp_amount = this.export_record[0].exp_amount;

            this.assist_by = this.export_record[0].assist_by;

            this.adv = this.export_record[0].adv;

            // for(const element of JSON.parse(this.export_record[0].payment)) {
            //   var result  = this.payment.filter(function(o){return o.id == element.id;} );
            //   if(result.length > 0)
            //     result[0].is_selected = element.is_selected;
            // }

            for(const element of JSON.parse(this.export_record[0].record)) {
              var result  = this.record.filter(function(o){return o.id == element.id;} );
              if(result.length > 0)
                result[0].is_selected = element.is_selected;
            }
          }
        },

        item_payment: function(record, ar, detail_id, measure) {
          this.payment = [];
          this.payment_record = [];

          //this.payment = [].concat(record);
          this.payment = JSON.parse(JSON.stringify(record));
          this.ar = ar;

          this.detail_id = detail_id;

          this.payment_record = this.shallowCopy(record);

          this.payment_measure = this.shallowCopy(measure);
        },

        encode_save: async function() {
          let _this = this;

          var form_data = new FormData();
          form_data.append('id', this.item.id);
          form_data.append('encode', this.item.encode);
          form_data.append('cust_type', this.item.cust_type);
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

        warehouse_save: async function() {
          let _this = this;

          var form_data = new FormData();
          form_data.append('id', this.item.id);
          form_data.append('days', this.item.days);
          form_data.append('way', this.item.way);
          form_data.append('kilo_unit', this.item.kilo_unit);
          form_data.append('cuft_unit', this.item.cuft_unit);
          form_data.append('kilo_amount', this.item.kilo_amount);
          form_data.append('cuft_amount', this.item.cuft_amount);
          form_data.append('kilo_remark', this.item.kilo_remark);
          form_data.append('cuft_remark', this.item.cuft_remark);
          form_data.append('cust_type', this.item.cust_type);
          form_data.append('warehouse_cuft', this.item.warehouse_cuft);
          form_data.append('warehouse_kilo', this.item.warehouse_kilo);
   
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios({
              method: 'post',
              url: 'api/pickup_set_warehouse.php',
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

        export_save: function() {
          let _this = this;
          var form_Data = new FormData();

          form_Data.append('id', this.detail_id);
          form_Data.append('exp_dr', this.exp_dr)
          form_Data.append('assist_by', this.assist_by)
          form_Data.append('exp_date', this.exp_date)
          form_Data.append('exp_sold_to', this.exp_sold_to)
          form_Data.append('exp_quantity', this.exp_quantity)
          form_Data.append('exp_unit', this.exp_unit)
          form_Data.append('exp_discription', this.exp_discription_org)
          form_Data.append('exp_amount', this.exp_amount_org)
          form_Data.append('exp_discription_ext', this.exp_discription)
          form_Data.append('exp_amount_ext', this.exp_amount)
          form_Data.append('payment', JSON.stringify(this.payment))
          form_Data.append('record', JSON.stringify(this.record))

          form_Data.append('adv', this.adv)
        
          const filename = "Format_of_Payment_Receipt";

          const token = sessionStorage.getItem('token');

          axios({
                  method: 'post',
                  url: 'api/pickup_payment_export.php',
                  data: form_Data,
                  responseType: 'blob', // important
              })
              .then(function(response) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                   
                      link.setAttribute('download', 'Payment_Receipt' + (_this.exp_dr !== '' ? '_' + _this.exp_dr : '') + '.docx');
                   
                    document.body.appendChild(link);
                    link.click();

                    _this.getMeasures();

              })
              .catch(function(response) {
                  //handle error
                  console.log(response)
              });
      },

        record_save: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('record', JSON.stringify(this.record));
           form_data.append('encode_status', '');
   
          let token = localStorage.getItem("accessToken");

          if(this.submit == true)
            return;

          this.submit = true;
    
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

          this.submit = false;

          this.getMeasures();
        },

        payment_save: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('id', this.detail_id);
          form_data.append('record', JSON.stringify(this.payment));
          form_data.append('pre_record', JSON.stringify(this.payment_record));
           form_data.append('encode_status', '');
   
          let token = localStorage.getItem("accessToken");

          if(this.submit == true)
            return;

          this.submit = true;
    
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

          this.submit = false;

          this.getMeasures();
        },

        payment_save_complete: async function() {
          let _this = this;

          var form_data = new FormData();
    
          form_data.append('id', this.detail_id);
          form_data.append('record', JSON.stringify(this.payment));
          form_data.append('pre_record', JSON.stringify(this.payment_record));
           form_data.append('encode_status', 'C');
   
          let token = localStorage.getItem("accessToken");

          if(this.submit == true)
            return;

          this.submit = true;
    
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

          this.submit = false;

          this.getMeasures();
        },

        encode_save_complete: async function() {
          let _this = this;

          var form_data = new FormData();
          form_data.append('id', this.item.id);
          form_data.append('encode', this.item.encode);
          form_data.append('cust_type', this.item.cust_type);
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

        async IsPicked(id)  {
          let _this = this;
    
          const params = {
              id: id,
          
          };
    
          let token = localStorage.getItem("accessToken");
    
          try {
            let res = await axios.get("api/measure_is_picked.php", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            });
            
              if(res.data.length > 0)
                  return true;
              else
                  return false;

          } catch (err) {
            console.log(err)
            alert('error')
          }
      },

        pickup: async function(keyword) {
          var favorite = [];

          if(this.submit == true)
                return;

            for (i = 0; i < this.loading_records.length; i++) 
            {
              if(this.loading_records[i].is_checked == 1)
                favorite.push(this.loading_records[i].id);
            }

            if(favorite.length == 0)
            {
              Swal.fire({
                title: 'Info',
                text: 'Please select records to pickup',
                type: 'Info',
                confirmButtonText: 'OK'
            });
            return;
            }
              

            for(i=0; i<favorite.length; i++)
            {
              let is_picked = await this.IsPicked(favorite[i]);
                if(is_picked)
                {
                    Swal.fire({
                        title: 'Warning',
                        text: 'The selected measurement record already generated its pickup/payment records, so this measurement record is not allowed to generate again. Please also refresh the webpage.',
                        type: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

              this.submit = true;

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

        getContanerNumbers: function() {
          let _this = this;
          axios.get('api/pickup_get_records_container_number.php?keyword=' + this.filter)
                .then(function(response) {
                    _this.container_numbers = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        getMeasures: function(search) {
            let _this = this;

            if(search !== 'search')
            {
              this.search = "";
              this.search_date = "";
            }

            if(this.filter == "D"  || this.filter == '')
            {

              if(this.search_date != "" && this.search != "")
              {
                  Swal.fire({
                    title: 'Warning',
                    text: 'User is only allowed to input either date or DR # to proceed the action of searching.',
                    type: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
              }
              
              axios.get('api/pickup_get_records_page.php?keyword=' + this.filter + '&page=' + this.page + '&size=' + this.perPage.id + '&search=' + this.search + '&search_date=' + this.search_date + '&container=' + this.container)
                .then(function(response) {
                    console.log(response.data);
                    _this.receive_records = response.data;

                    totalRows = _this.receive_records[0]['total'];
                    _this.setPages(totalRows);

                    _this.submit = false;

                })
                .catch(function(error) {
                    console.log(error);
                });
            }
            else
            {
            axios.get('api/pickup_get_records.php?keyword=' + this.filter + '&page=' + this.page + '&size=12' + '&container=' + this.container)
                .then(function(response) {
                    console.log(response.data);
                    _this.receive_records = response.data;

                    _this.submit = false;

                })
                .catch(function(error) {
                    console.log(error);
                });
              }

            this.getContanerNumbers();
        },

        getMeasureRecords: function(keyword) {
          console.log("getMeasureRecords");
            axios.get('api/measure_get_measure.php')
                .then(function(response) {
                    console.log(response.data);
                    app.measure_records = response.data;

                    console.log("getMeasureRecords");

                    _this.submit = false;

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