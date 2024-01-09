Vue.component('v-select', VueSelect.VueSelect)

var install = new Vue({
    el: '#install', 
    data: {
        installer:[],
    },created() {
    
        this.getLeadMan();
    },
    methods: {
        getLeadMan() {
  
            let _this = this;
      
            let token = localStorage.getItem('accessToken');
      
            axios
              .get('api/project02_user_leadman.php', { headers: { "Authorization": `Bearer ${token}` } })
              .then(
                (res) => {
                    var _users = res.data;
                    _this.installer = Object.keys(_users).map(function(k){return _users[k].username})
                    
                  
                },
                (err) => {
                  alert(err.response);
                },
              )
              .finally(() => {
      
              });
          }, }});

var project = new Vue({
    el: "#projects",
    data: {
        projects: [],
        project_id : 0,

        stages: [],
        stage_id : 0,

        add_project_id : 0,
        add_stage_id : 0,
  
    },
    created() {
        let _this = this;
        let uri = window.location.href.split("?");
        if (uri.length >= 2) {
            let vars = uri[1].split("&");

            let tmp = "";
            vars.forEach(async function(v) {
                tmp = v.split("=");
                if (tmp.length == 2) {
                switch (tmp[0]) {
                    case "project_id":
                        _this.project_id = tmp[1];
                        _this.add_project_id = tmp[1];
                    break;

                    case "stage_id":
                        _this.stage_id = tmp[1];
                        _this.add_stage_id = tmp[1];
                    break;
                
                    default:
                    console.log(`Too many args`);
                }
                }
            });
        }

        this.getProjects();
    },

    methods: {
        getProjects() {
            let _this = this;
            let token = localStorage.getItem('accessToken');
            axios
                .get('api/project02_get_project_name_by_keyword.php', { headers: { "Authorization": `Bearer ${token}` } })  
                .then(
                    (res) => {
                        _this.projects = res.data;
                    },
                    (err) => {
                        alert(err.response);
                    },
                )
                .finally(() => {
                });
        },

        async getStages(pid) {
            let _this = this;
            let token = localStorage.getItem('accessToken');

            // clear select sc_related_stage_id (add by edit)
            // var select = document.getElementById("sc_related_stage_id");
            // var length = select.options.length;
            // for (i = length-1; i >= 0; i--) {
            //     select.options[i] = null;
            // }

            if(pid != undefined) 
                _this.project_id = pid;

            let res = await axios.get('api/project02_stages.php', { headers: { "Authorization": `Bearer ${token}` }, params: { pid: _this.project_id } });
                _this.stages = res.data;

            console.log("getStages");
            },
        
            clear(){
                let _this = this;
                _this.project_id = 0;
                _this.stage_id = 0;
                _this.stages = [];
            },

    },
});


var service_feliix = new Vue({
    el: '#approval_section_feliix',
    data: {
        is_admin:false,

        schedule_Name: "",
        date_use: "",
        car_use: "",
        driver: "",
        helper: "",
        time_out: "",
        time_in: "",
        notes: "",
        creator: "",
        status: "",
        id: 0,

        item_schedule: "",
        item_company: "",
        item_address: "",
        item_purpose: "",
        items: [],
        editing : false,
        sn: 0,
        e_id: 0,

        org_schedule: {},
        org_schedule_check1: {},
        org_schedule_check2: {},

        edit_servie: false,

        access1 : false,
        access2 : false,

        showing : true,

        username: "",

        // for check
        check_showing: false,
        check_showing2: false,

        check_date_use: "",
        check_car_use: "",
        check_driver: "",
        check_time_out: "",
        check_time_in: "",

        access_check1: false,
        access_check2: false,

        edit_servie_check1: false,
        edit_servie_check2: false,

        is_submit: false,

    },

    created() {
        this.getAccess();
        this.getUserName();
    },

    watch: {
        id: function (val) {

            this.access_check1 = false;
            this.access_check2 = false;
            this.edit_servie_check1 = false;
            this.edit_servie_check2 = false;
            this.check_showing = false;
            this.check_showing2 = false;

            if((this.status == "1" || this.status == "2") && this.access1 == true)
            {
                this.access_check1 = true;
            }

            if(this.status == "2" && this.access2 == true && this.access1 == false)
            {
                this.access_check2 = true;
            }

            if(this.id == 0) 
            {
                this.showing = true;
                return;
            }
            else
               this.showing = false;

            // if (this.creator != this.username) {
            //     this.showing = false;
            //     return;
            // }

            if(this.status == "1" && this.access1 == true)
            {
                this.service_edit_check();
            }

            if(this.status == "2" && this.access1 == true && this.access2 == true)
            {
                this.service_edit_check();
            }
            else if(this.status == "2" && this.access1 == false && this.access2 == true)
            {
               // this.service_edit_check2();
            }
            
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
                url: 'api/car_calender_access_control.php',
                data: {}
            })
            .then(function(response) {
                //handle success
                _this.access1 = response.data.car_access1;
                _this.access2 = response.data.car_access2;

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

        getUserName: function() {
            var form_Data = new FormData();
            let _this = this;
    
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/on_duty_get_myname.php',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                _this.username = response.data.username;
    
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

        clear_service: function() {
            if(this.showing == false) return;

            this.schedule_Name = "";
            this.date_use = "";
            this.car_use = "";
            this.driver = "";
            this.helper = "";
            this.time_out = "";
            this.time_in = "";
            this.notes = "";
            this.creator = "";
            this.status = "";

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";
            this.items = [];
            this.id = 0;

            this.editing = false;
            this.edit_servie = false;
            this.sn = 0;
            this.showing = false;

            this.is_submit = false;
        },

        
        reset_service_check: function() {
            if(this.access_check1){
                if(this.check_showing == false) return;
    
                this.check_date_use = "";
                this.check_car_use = "";
                this.check_driver = "";
    
                this.check_time_out = "";
                this.check_time_in = "";

            }

            if(this.access_check2 && this.access_check1 == false){
                if(this.check_showing2 == false) return;

                this.check_driver = "";
            }
        
        },

        reset_service_check2: function() {
            
            if(this.access_check1){
                if(this.check_showing == false) return;
    
                this.check_date_use = "";
                this.check_car_use = "";
                this.check_driver = "";
    
                this.check_time_out = "";
                this.check_time_in = "";

            }

            if(this.access_check2 && this.access_check1 == false){
                if(this.check_showing2 == false) return;

                this.check_driver = "";
            }
        
        },

        service_click: function(sc_content) {
            this.org_schedule = JSON.parse(JSON.stringify(sc_content));
            this.org_schedule_check1 = JSON.parse(JSON.stringify(sc_content.check1));
            this.org_schedule_check2 = JSON.parse(JSON.stringify(sc_content.check2));

            this.schedule_Name = sc_content.Title;
            this.date_use = sc_content.Date;
            this.car_use = sc_content.Service;
            this.driver = sc_content.Driver_Text;
            this.helper = sc_content.helper;
            this.time_out = sc_content.Starttime;
            this.time_in = sc_content.Endtime;
            this.notes = sc_content.Notes;
            // this.items = JSON.parse(sc_content.items);
            this.creator = sc_content.Lasteditor;
            this.status = sc_content.status;

            var tout = sc_content.Starttime;
            var tin = sc_content.Endtime;

            if(sc_content.Allday == true)
            {
                tout = "00:00";
                tin = "23:59";
            }

            // for check
            this.check_date_use = sc_content.Date;
            this.check_car_use = sc_content.Service;
            this.check_driver = sc_content.Driver_Text;
            this.check_time_out = tout;
            this.check_time_in = tin;

            if(sc_content.check1.length > 0)
            {
                this.check_date_use = moment(sc_content.check1[0].date_use).format("YYYY-MM-DD");
                this.check_car_use = sc_content.check1[0].car_use;
                this.check_driver = sc_content.check1[0].driver;
                this.check_time_out = moment(sc_content.check1[0].time_out).format("HH:mm");
                this.check_time_in = moment(sc_content.check1[0].time_in).format("HH:mm");
            }

            if(sc_content.check2.length > 0)
            {
                this.check_driver = sc_content.check2[0].driver;
            }

            // if(this.status == "2" && sc_content.check1.length > 0)
            // {
            //     this.date_use = moment(sc_content.check1[0].date_use).format("YYYY-MM-DD");
            //     this.car_use = sc_content.check1[0].car_use;
            //     this.driver = sc_content.check1[0].driver;
            //     this.time_out = moment(sc_content.check1[0].time_out).format("HH:mm");
            //     this.time_in = moment(sc_content.check1[0].time_in).format("HH:mm");
            // }

            // if(this.status == "3" && sc_content.check2.length > 0)
            // {
            //     this.check_driver = sc_content.check2[0].driver;
            // }

            this.is_submit = false;
        },

        
    service_change_check: function(status) {
        let _this = this;

        Swal.fire({
            title: "Change To Under Review",
            text: "Reason",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                  },
                  showCancelButton: true,
                  confirmButtonText: 'OK',
          }).then((result) => {
            if (result.value) {

                if(result.isDismissed) return;

                console.log("Result: " + result.value);

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            let id = 0;
            let sid = 0;

            if(_this.org_schedule_check1.length > 0){
                id = _this.org_schedule_check1[0].id;
                sid = _this.id * -1;
                api_url = "api/car_calender_check_remove_feliix.php";
            }
            else{
                api_url = "api/car_calender_check_reject_feliix.php";
                id = _this.id * -1;
            }
        
            form_Data.append("jwt", token);
            form_Data.append("id", id);
            form_Data.append("sid", sid);
            form_Data.append("status", status);

            form_Data.append("reason", result.value);

            form_Data.append("date_use", this.check_date_use);
            form_Data.append("car_use", this.check_car_use);
            form_Data.append("driver", this.check_driver);
        
            form_Data.append("time_out", this.check_time_out);
            form_Data.append("time_in", this.check_time_in);

            form_Data.append("schedule_Name_f", this.schedule_Name);
            form_Data.append("date_use_f", this.date_use);
            form_Data.append("car_use_f", this.car_use);
            form_Data.append("driver_f", this.driver);
            form_Data.append("helper_f", this.helper);
            form_Data.append("time_out_f", this.time_out);
            form_Data.append("time_in_f", this.time_in);
            form_Data.append("notes_f", this.notes);
            form_Data.append("status_f", this.status);
            form_Data.append("items_f", JSON.stringify(this.items));
            form_Data.append("creator_f", this.creator.split(' at ')[0]);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let is_send = status;

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                    let gbcolor = "";

                    if(_this.car_use == "Alphard")
                    gbcolor = "#FECC28";
                    if(_this.car_use == "Avanza")
                    gbcolor = "#4EB5BB";
                    if(_this.car_use == "Traviz 1")
                    gbcolor = "#009858";
                    if(_this.car_use == "Traviz 2")
                    gbcolor = "#A671AD";
                    if(_this.car_use == "Traviz 3")
                    gbcolor = "#1A17E8";
                    if(_this.car_use == "Toyota Rush")
                    gbcolor = "#F19DB4";
                    if(_this.car_use == "")
                    gbcolor = "#141415";

                    if(_this.id != 0)
                    {
                        
                        var event = calendar.getEventById(_this.id);

                        event.color = gbcolor;
                        event.backgroundColor = gbcolor;
                        event.borderColor = gbcolor;
                        event.extendedProps.description.status = is_send;
                        event.extendedProps.description.icon = symbol;

                        _this.id = 0;
                    }

                    $("#exampleModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
            
                
                })
            }
            else{
                return;
            }
        });
    },

        
        service_save_check: function(check1, check2, is_send) {
            let _this = this;

            if(is_send == "2")
            {
                if(this.check_date_use == "" || this.check_time_out == "" || this.check_time_in == "" || this.check_car_use == "")
                {
                    Swal.fire({
                        text: "Columns of Date, Time, Assigned Car cannot be blank.",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
            }

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            let kind = "1";

            if(check2 == true) kind = "2";
        
            form_Data.append("jwt", token);
            form_Data.append("id", this.id * -1);
           
            form_Data.append("date_use", this.check_date_use);
            form_Data.append("car_use", this.check_car_use);
            form_Data.append("driver", this.check_driver);
        
            form_Data.append("time_out", this.check_time_out);
            form_Data.append("time_in", this.check_time_in);
            form_Data.append("status", is_send);

            form_Data.append("schedule_Name_f", this.schedule_Name);
            form_Data.append("date_use_f", this.date_use);
            form_Data.append("car_use_f", this.car_use);
            form_Data.append("driver_f", this.driver);
            form_Data.append("helper_f", this.helper);
            form_Data.append("time_out_f", this.time_out);
            form_Data.append("time_in_f", this.time_in);
            form_Data.append("notes_f", this.notes);
            form_Data.append("status_f", this.status);
            form_Data.append("items_f", JSON.stringify(this.items));
            form_Data.append("creator_f", this.creator.split(' at ')[0]);

            form_Data.append("kind", kind);

            api_url = "api/car_calender_check_save_feliix.php";

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                    let gbcolor = "";

                    if(_this.check_car_use == "Alphard")
                    gbcolor = "#FECC28";
                    if(_this.check_car_use == "Avanza")
                    gbcolor = "#4EB5BB";
                    if(_this.check_car_use == "Traviz 1")
                    gbcolor = "#009858";
                    if(_this.check_car_use == "Traviz 2")
                    gbcolor = "#A671AD";
                    if(_this.check_car_use == "Traviz 3")
                    gbcolor = "#1A17E8";
                    if(_this.check_car_use == "Toyota Rush")
                    gbcolor = "#F19DB4";
                    if(_this.check_car_use == "")
                    gbcolor = "#141415";
                    


                    if(_this.id != 0)
                    {
                        
                        var event = calendar.getEventById(_this.id);
                        event.title = _this.schedule_Name;
                        event.allDay = true;
                        event.Date = moment(_this.date_use).format("YYYY-MM-DD");
                        event.start = _this.date_use;
                        event.end = _this.date_use;

                        event.extendedProps.description.icon = symbol,

                        event.extendedProps.description.status = is_send;
        

                    }

                    _this.id = 0

                    $("#exampleModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
                })
        },

        
        service_reject_check: function(status) {
            let _this = this;

            Swal.fire({
                title: "Reject",
                text: "Reason",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                  },
                  showCancelButton: true,
                  confirmButtonText: 'OK',
              }).then((result) => {

                if (result.value) {
                
                    if(result.isDismissed) return;

                console.log("Result: " + result.value);

                var token = localStorage.getItem("token");
                var form_Data = new FormData();

                let id = 0;
                let sid = 0;

                // if(_this.org_schedule_check1.length > 0){
                //     id = _this.org_schedule_check1[0].id;
                //     sid = _this.id;
                //     api_url = "api/car_calender_check_remove.php";
                // }
                // else{
                    api_url = "api/car_calender_check_reject_feliix.php";
                    id = _this.id * -1;
                // }
            
                form_Data.append("jwt", token);
                form_Data.append("id", id);
                form_Data.append("sid", sid);
                form_Data.append("reason", result.value);
                form_Data.append("status", status);

                form_Data.append("date_use", this.check_date_use);
                form_Data.append("car_use", this.check_car_use);
                form_Data.append("driver", this.check_driver);
            
                form_Data.append("time_out", this.check_time_out);
                form_Data.append("time_in", this.check_time_in);

                form_Data.append("schedule_Name_f", this.schedule_Name);
                form_Data.append("date_use_f", this.date_use);
                form_Data.append("car_use_f", this.car_use);
                form_Data.append("driver_f", this.driver);
                form_Data.append("helper_f", this.helper);
                form_Data.append("time_out_f", this.time_out);
                form_Data.append("time_in_f", this.time_in);
                form_Data.append("notes_f", this.notes);
                form_Data.append("status_f", this.status);
                form_Data.append("items_f", JSON.stringify(this.items));
                form_Data.append("creator_f", this.creator.split(' at ')[0]);

                axios({
                        method: "post",
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                        url: api_url,
                        data: form_Data,
                    })
                    .then(function (response) {

                        let is_send = status;

                        let symbol = "";
                        if (is_send == "1") 
                            symbol = 'fa-question-circle';
                        if (is_send == "2")
                            symbol = 'fa-car';

                        let gbcolor = "";

                        if(_this.car_use == "Alphard")
                        gbcolor = "#FECC28";
                        if(_this.car_use == "Avanza")
                        gbcolor = "#4EB5BB";
                        if(_this.car_use == "Traviz 1")
                        gbcolor = "#009858";
                        if(_this.car_use == "Traviz 2")
                        gbcolor = "#A671AD";
                        if(_this.car_use == "Traviz 3")
                        gbcolor = "#1A17E8";
                        if(_this.car_use == "Toyota Rush")
                        gbcolor = "#F19DB4";
                        if(_this.car_use == "")
                        gbcolor = "#141415";

                        if(_this.id != 0)
                        {
                            
                            var event = calendar.getEventById(_this.id);

                            event.color = gbcolor;
                            event.backgroundColor = gbcolor;
                            event.borderColor = gbcolor;
                            event.extendedProps.description.status = is_send;
                            event.extendedProps.description.icon = symbol;

                            _this.id = 0;
                        }

                        $("#exampleModalScrollable").modal("toggle");

                        _this.clear_service();

                        reload();
                
                    
                })
            }
            else{
                return;
            }
        });
    },

    
    service_update_check: function(status) {
        let _this = this;

        if(this.check_date_use == "" || this.check_time_out == "" || this.check_time_in == "" || this.check_car_use == "")
        {
            Swal.fire({
                text: "Columns of Date, Time, Assigned Car cannot be blank.",
                icon: "warning",
                confirmButtonText: "OK",
            });
            return;
        }

        if(this.is_submit == true)
         return;

        this.is_submit = true;

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        let id = 0;

        if(this.org_schedule_check1.length > 0){
            id = this.org_schedule_check1[0].id;
            sid = this.id * -1;
            api_url = "api/car_calender_check_update_feliix.php";
        }
        else{
            api_url = "api/car_calender_check_save_feliix.php";
            id = this.id;
        }
    
        form_Data.append("jwt", token);
        form_Data.append("id", id);

        form_Data.append("sid", sid);
       
        form_Data.append("date_use", this.check_date_use);
        form_Data.append("car_use", this.check_car_use);
        form_Data.append("driver", this.check_driver);
    
        form_Data.append("time_out", this.check_time_out);
        form_Data.append("time_in", this.check_time_in);
        form_Data.append("status", status);

        form_Data.append("schedule_Name_f", this.schedule_Name);
        form_Data.append("date_use_f", this.date_use);
        form_Data.append("car_use_f", this.car_use);
        form_Data.append("driver_f", this.driver);
        form_Data.append("helper_f", this.helper);
        form_Data.append("time_out_f", this.time_out);
        form_Data.append("time_in_f", this.time_in);
        form_Data.append("notes_f", this.notes);
        form_Data.append("status_f", this.status);
        form_Data.append("items_f", JSON.stringify(this.items));
        form_Data.append("creator_f", this.creator.split(' at ')[0]);


        axios({
                method: "post",
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                url: api_url,
                data: form_Data,
            })
            .then(function (response) {

                if(_this.id != 0)
                {
                    
                    //_this.reload_schedule(_this.id);
                    _this.undo_service_edit_check();

                }

                //_this.id = 0;
                //$("#serviceModalScrollable").modal("toggle");

                //_this.clear_service();

                reload();
            })
            .catch(function (error) {
                //handle error
                Swal.fire({
                    text: error.data,
                    icon: "warning",
                    confirmButtonText: "OK",
                });
            }).finally(() => {
                _this.is_submit = false;
            });
    },

        
        service_edit: function() {
            this.showing = true;
            this.edit_servie = true;
        },

        service_edit_check: function() {
            if(this.access_check1){
                this.check_showing = true;
                this.edit_servie_check1 = true;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = true;
                this.edit_servie_check2 = true;
            }
        },

        undo_service_edit_check: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;
            }
        },

        service_edit_check2: function() {
            if(this.access_check1){
                this.check_showing = true;
                this.edit_servie_check1 = true;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = true;
                this.edit_servie_check2 = true;
            }
        },

        service_cancel_check: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;

                if(this.org_schedule_check1.length > 0){
                    this.check_date_use = moment(this.org_schedule_check1[0].date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule_check1[0].car_use;
                    this.check_driver = this.org_schedule_check1[0].driver;
                    this.check_time_out = moment(this.org_schedule_check1[0].time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_schedule_check1[0].time_in).format("HH:mm");
                }
                else{
                    this.check_date_use = moment(this.org_schedule.date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule.car_use;
                    this.check_driver = this.org_schedule.driver;
                    this.check_time_out = moment(this.org_schedule.time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_shedule.time_in).format("HH:mm");
                }
            }

            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;

                if(this.org_schedule_check2.length > 0){
                    this.check_driver = this.org_schedule_check2[0].driver;
                }
                else if(this.org_schedule_check1.length > 0){
                    this.check_driver = this.org_schedule_check1[0].driver;
                }
                else{
                    this.check_driver = this.org_schedule.driver;
                }
            }

        },

        service_cancel_check2: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;

                if(this.org_schedule_check1.length > 0){
                    this.check_date_use = moment(this.org_schedule_check1[0].date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule_check1[0].car_use;
                    this.check_driver = this.org_schedule_check1[0].driver;
                    this.check_time_out = moment(this.org_schedule_check1[0].time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_schedule_check1[0].time_in).format("HH:mm");
                }
                else{
                    this.check_date_use = moment(this.org_schedule.date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule.car_use;
                    this.check_driver = this.org_schedule.driver;
                    this.check_time_out = moment(this.org_schedule.time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_shedule.time_in).format("HH:mm");
                }
            }

            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;

                if(this.org_schedule_check2.length > 0){
                    this.check_driver = this.org_schedule_check2[0].driver;
                }
                else if(this.org_schedule_check1.length > 0){
                    this.check_driver = this.org_schedule_check1[0].driver;
                }
                else{
                    this.check_driver = this.org_schedule.driver;
                }
            }
            

        },

        service_cancel: function() {
            this.showing = false;
            this.edit_servie = false;

            this.schedule_Name = this.org_schedule.schedule_Name;
            this.date_use = this.org_schedule.date_use;
            this.car_use = this.org_schedule.car_use;
            this.driver = this.org_schedule.driver;
            this.helper = this.org_schedule.helper;
            this.time_out = this.org_schedule.time_out;
            this.time_in = this.org_schedule.time_in;
            this.notes = this.org_schedule.notes;
            this.items = JSON.parse(this.org_schedule.items);
            this.creator = this.org_schedule.creator;
     
        },

        export_service_full: function (full) {
            let _this = this;
            var form_Data = new FormData();

            const filename = "attendance";
            
            form_Data.append("full", full);
            form_Data.append("id", this.id * -1);
            form_Data.append("schedule_Name", this.schedule_Name);
            form_Data.append("date_use", this.date_use);
            form_Data.append("car_use", this.car_use);
            form_Data.append("driver", this.driver);
            form_Data.append("helper", this.helper);
            form_Data.append("time_out", this.time_out);
            form_Data.append("time_in", this.time_in);
            form_Data.append("notes", this.notes);
            form_Data.append("status", this.status);
            form_Data.append("items", JSON.stringify(this.items));

            form_Data.append("check_date_use", this.check_date_use);
            form_Data.append("check_car_use", this.check_car_use);
            form_Data.append("check_driver", this.check_driver);
            form_Data.append("check_time_out", this.check_time_out);
            form_Data.append("check_time_in", this.check_time_in);


            const token = sessionStorage.getItem("token");

            axios({
                    method: "post",
                    url: "car_schedule_data_word_feliix.php",
                    data: form_Data,
                    responseType: "blob", // important
                })
                .then(function (response) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;

                    link.setAttribute("download", "car_schedule.docx");

                    document.body.appendChild(link);
                    link.click();
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },
    }
});



var service = new Vue({
    el: '#serviceModalScrollable',
    data: {
        is_admin:false,

        schedule_Name: "",
        date_use: "",
        car_use: "",
        driver: "",
        helper: "",
        time_out: "",
        time_in: "",
        notes: "",
        creator: "",
        status: "",
        id: 0,

        item_schedule: "",
        item_company: "",
        item_address: "",
        item_purpose: "",
        items: [],
        editing : false,
        sn: 0,
        e_id: 0,

        org_schedule: {},
        org_schedule_check1: {},
        org_schedule_check2: {},

        edit_servie: false,

        access1 : false,
        access2 : false,

        showing : true,

        username: "",

        // for check
        check_showing: false,
        check_showing2: false,

        check_date_use: "",
        check_car_use: "",
        check_driver: "",
        check_time_out: "",
        check_time_in: "",

        access_check1: false,
        access_check2: false,

        edit_servie_check1: false,
        edit_servie_check2: false,

        schedule_Creator: "",

        is_submit: false,

    },

    created() {
        this.getAccess();
        this.getUserName();
    },

    watch: {
        id: function (val) {
            if(val == -1) this.id = 0;

            this.access_check1 = false;
            this.access_check2 = false;
            this.edit_servie_check1 = false;
            this.edit_servie_check2 = false;
            this.check_showing = false;
            this.check_showing2 = false;

            if((this.status == "1" || this.status == "2") && this.access1 == true)
            {
                this.access_check1 = true;
            }

            if(this.status == "2" && this.access2 == true && this.access1 == false)
            {
                this.access_check2 = true;
            }

            if(this.id == 0) 
            {
                this.showing = true;
                return;
            }
            else
               this.showing = false;

            

            if(this.status == "1" && this.access1 == true)
            {
                this.service_edit_check();
            }

            if(this.status == "2" && this.access1 == true && this.access2 == true)
            {
                this.service_edit_check();
            }
            else if(this.status == "2" && this.access1 == false && this.access2 == true)
            {
               // this.service_edit_check2();
            }

            if (this.creator != this.username) {
                this.showing = false;
                return;
            }
            
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
                url: 'api/car_calender_access_control.php',
                data: {}
            })
            .then(function(response) {
                //handle success
                _this.access1 = response.data.car_access1;
                _this.access2 = response.data.car_access2;

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

        service_reject_check: function(status) {
            let _this = this;

            Swal.fire({
                title: "Reject",
                text: "Reason",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                  },
                  showCancelButton: true,
                  confirmButtonText: 'OK',
              }).then((result) => {
                if (result.value) {

                    if(result.isDismissed) return;

                console.log("Result: " + result.value);
    

                var token = localStorage.getItem("token");
                var form_Data = new FormData();

                let id = 0;
                let sid = 0;

                // if(_this.org_schedule_check1.length > 0){
                //     id = _this.org_schedule_check1[0].id;
                //     sid = _this.id;
                //     api_url = "api/car_calender_check_remove.php";
                // }
                // else{
                    api_url = "api/car_calender_check_reject.php";
                    id = _this.id;
                // }
            
                form_Data.append("jwt", token);
                form_Data.append("id", id);
                form_Data.append("sid", sid);

                form_Data.append("date_use", this.check_date_use);
                form_Data.append("car_use", this.check_car_use);
                form_Data.append("driver", this.check_driver);
                form_Data.append("time_out", this.check_time_out);
                form_Data.append("time_in", this.check_time_in);
                form_Data.append("reason", result.value);

                form_Data.append("status", status);

                axios({
                        method: "post",
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                        url: api_url,
                        data: form_Data,
                    })
                    .then(function (response) {

                        let is_send = status;

                        let symbol = "";
                        if (is_send == "1") 
                            symbol = 'fa-question-circle';
                        if (is_send == "2")
                            symbol = 'fa-car';

                        let gbcolor = "";

                        if(_this.car_use == "Alphard")
                        gbcolor = "#FECC28";
                        if(_this.car_use == "Avanza")
                        gbcolor = "#4EB5BB";
                        if(_this.car_use == "Traviz 1")
                        gbcolor = "#009858";
                        if(_this.car_use == "Traviz 2")
                        gbcolor = "#A671AD";
                        if(_this.car_use == "Traviz 3")
                        gbcolor = "#1A17E8";
                        if(_this.car_use == "Toyota Rush")
                        gbcolor = "#F19DB4";
                        if(_this.car_use == "")
                        gbcolor = "#141415";

                        if(_this.id != 0)
                        {
                            
                            var event = calendar.getEventById(_this.id);

                            event.color = gbcolor;
                            event.backgroundColor = gbcolor;
                            event.borderColor = gbcolor;
                            event.extendedProps.description.status = is_send;
                            event.extendedProps.description.icon = symbol;

                            _this.id = -1;
                        }

                        $("#serviceModalScrollable").modal("toggle");

                        _this.clear_service();

                        reload();
                
                    
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
            }
            else{
                return;
            }
        });
    },

    
    service_change_check: function(status) {
        let _this = this;

        Swal.fire({
            title: "Change To Under Review",
            text: "Reason",
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                  },
                  showCancelButton: true,
                  confirmButtonText: 'OK',
          }).then((result) => {
            if (result.value) {

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            let id = 0;
            let sid = 0;

            if(_this.org_schedule_check1.length > 0){
                id = _this.org_schedule_check1[0].id;
                sid = _this.id;
                api_url = "api/car_calender_check_remove.php";
            }
            else{
                api_url = "api/car_calender_check_reject.php";
                id = _this.id;
            }
        
            form_Data.append("jwt", token);
            form_Data.append("id", id);
            form_Data.append("sid", sid);

            form_Data.append("date_use", this.check_date_use);
            form_Data.append("car_use", this.check_car_use);
            form_Data.append("driver", this.check_driver);
            form_Data.append("time_out", this.check_time_out);
            form_Data.append("time_in", this.check_time_in);
            form_Data.append("reason", result.value);
            

            form_Data.append("status", status);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let is_send = status;

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                        let gbcolor = "";

                        if(_this.car_use == "Alphard")
                        gbcolor = "#FECC28";
                        if(_this.car_use == "Avanza")
                        gbcolor = "#4EB5BB";
                        if(_this.car_use == "Traviz 1")
                        gbcolor = "#009858";
                        if(_this.car_use == "Traviz 2")
                        gbcolor = "#A671AD";
                        if(_this.car_use == "Traviz 3")
                        gbcolor = "#1A17E8";
                        if(_this.car_use == "Toyota Rush")
                        gbcolor = "#F19DB4";
                        if(_this.car_use == "")
                        gbcolor = "#141415";

                    if(_this.id != 0)
                    {
                        
                        var event = calendar.getEventById(_this.id);

                        event.color = gbcolor;
                        event.backgroundColor = gbcolor;
                        event.borderColor = gbcolor;
                        event.extendedProps.description.status = is_send;
                        event.extendedProps.description.icon = symbol;

                        _this.id = -1;
                    }

                    $("#serviceModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
            
                
            })
            .catch(function (error) {
                //handle error
                Swal.fire({
                    text: error.data,
                    icon: "warning",
                    confirmButtonText: "OK",
                });
            });
        }
        else{
            return;
        }
    });
},

        service_update_check: function(status) {
            let _this = this;

            if(this.check_date_use == "" || this.check_time_out == "" || this.check_time_in == "" || this.check_car_use == "")
            {
                Swal.fire({
                    text: "Columns of Date, Time, Assigned Car cannot be blank.",
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            let id = 0;

            if(this.org_schedule_check1.length > 0){
                id = this.org_schedule_check1[0].id;
                sid = this.id;
                api_url = "api/car_calender_check_update.php";
            }
            else{
                api_url = "api/car_calender_check_save.php";
                id = this.id;
            }
        
            form_Data.append("jwt", token);
            form_Data.append("id", id);
            form_Data.append("sid", sid);
           
            form_Data.append("date_use", this.check_date_use);
            form_Data.append("car_use", this.check_car_use);
            form_Data.append("driver", this.check_driver);
        
            form_Data.append("time_out", this.check_time_out);
            form_Data.append("time_in", this.check_time_in);
            form_Data.append("status", status);


            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    if(_this.id != 0)
                    {
                        
                        _this.reload_schedule(_this.id);
                        _this.undo_service_edit_check();

                    }

                    //_this.id = 0;
                    //$("#serviceModalScrollable").modal("toggle");

                    //_this.clear_service();

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
        },

        
        
        // service_update_check: function(status) {
        //     let _this = this; // for axios

        //     var token = localStorage.getItem("token");
        //     var form_Data = new FormData();

        //     let id = 0;

        //     if(this.org_schedule_check2.length > 0){
        //         id = this.org_schedule_check1[0].id;
        //         api_url = "api/car_calender_check_update.php";
        //     }
        //     else{
        //         api_url = "api/car_calender_check_save.php";
        //         id = this.id;
        //     }
        
        //     form_Data.append("jwt", token);
        //     form_Data.append("id", id);
           
        //     form_Data.append("date_use", this.check_date_use);
        //     form_Data.append("car_use", this.check_car_use);
        //     form_Data.append("driver", this.check_driver);
        
        //     form_Data.append("time_out", this.check_time_out);
        //     form_Data.append("time_in", this.check_time_in);
        //     form_Data.append("status", status);


        //     axios({
        //             method: "post",
        //             headers: {
        //                 "Content-Type": "multipart/form-data",
        //             },
        //             url: api_url,
        //             data: form_Data,
        //         })
        //         .then(function (response) {

        //             if(_this.id != 0)
        //             {
                        
        //                 _this.reload_schedule(_this.id);

        //             }

        //             _this.id = 0;
        //             $("#serviceModalScrollable").modal("toggle");

        //             _this.clear_service();

        //             reload();
        //         })
        //         .catch(function (error) {
        //             //handle error
        //             Swal.fire({
        //                 text: error.data,
        //                 icon: "warning",
        //                 confirmButtonText: "OK",
        //             });
        //         });
        // },

        reload_schedule: function(id) {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);

            form_Data.append("id", id);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/car_calender_main_single.php",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    //var data = JSON.parse(response.data);

                    let event = calendar.getEventById(_this.id);

                    for (var i = 0; i < response.data.length; i++) {
                 
                        var isAll = false;
                        var Lasteditor = "";
                        
                        
                        if (
                            response.data[i].updated_by != "" &&
                            response.data[i].updated_by != null
                        ) {
                            Lasteditor =
                                response.data[i].updated_by +
                                " at " +
                                response.data[i].updated_at;
                        } else {
                            Lasteditor =
                                response.data[i].created_by +
                                " at " +
                                response.data[i].created_at;
                        }

                        let symbol = "";
                        if (response.data[i].status == "1") 
                            symbol = 'fa-question-circle';
                        if (response.data[i].status == "2")
                            symbol = 'fa-car';

                            let gbcolor = "";
                            let dateuse = "";
    
                            if(response.data[i].status == "2")
                            {
                                if(response.data[i].check1.length > 0){
                                    if(response.data[i].check1[0].car_use == "Alphard")
                                    gbcolor = "#FECC28";
                                    if(response.data[i].check1[0].car_use == "Avanza")
                                    gbcolor = "#4EB5BB";
                                    if(response.data[i].check1[0].car_use == "Traviz 1")
                                    gbcolor = "#009858";
                                    if(response.data[i].check1[0].car_use == "Traviz 2")
                                    gbcolor = "#A671AD";
                                    if(response.data[i].check1[0].car_use == "Traviz 3")
                                    gbcolor = "#1A17E8";
                                    if(response.data[i].check1[0].car_use == "Toyota Rush")
                                    gbcolor = "#F19DB4";
                                    if(response.data[i].check1[0].car_use == "")
                                    gbcolor = "#141415";
    
                                    dateuse = moment(response.data[i].check1[0].date_use).format("YYYY-MM-DD");
                                }
                            }
    
                            if(response.data[i].status == "1" || response.data[i].status == "0")
                            {
                                    if(response.data[i].car_use == "Alphard")
                                    gbcolor = "#FECC28";
                                    if(response.data[i].car_use == "Avanza")
                                    gbcolor = "#4EB5BB";
                                    if(response.data[i].car_use == "Traviz 1")
                                    gbcolor = "#009858";
                                    if(response.data[i].car_use == "Traviz 2")
                                    gbcolor = "#A671AD";
                                    if(response.data[i].car_use == "Traviz 3")
                                    gbcolor = "#1A17E8";
                                    if(response.data[i].car_use == "Toyota Rush")
                                    gbcolor = "#F19DB4";
                                    if(response.data[i].car_use == "")
                                    gbcolor = "#141415";
    
                                    dateuse = moment(response.data[i].date_use).format("YYYY-MM-DD");
                            }



                        
                        event.title = response.data[i].schedule_Name;
                        event.setDate(moment(dateuse).format("YYYY-MM-DD"));
                        event.Date = moment(dateuse).format("YYYY-MM-DD");
                        event.start = moment(dateuse).format("YYYY-MM-DD"); // will be parsed
                        event.end = moment(dateuse).format("YYYY-MM-DD");
                        event.color = gbcolor;
                        event.backgroundColor = gbcolor;
                        event.borderColor = gbcolor;
                 
                        event.extendedProps.description.icon = symbol;
                        event.extendedProps.description.schedule_Name = UnescapeHTML(response.data[i].schedule_Name);
                        event.extendedProps.description.car_use = response.data[i].car_use;
                        event.extendedProps.description.driver = response.data[i].driver;
                        event.extendedProps.description.helper = response.data[i].helper;
                        event.extendedProps.description.date_use = moment(response.data[i].date_use).format("YYYY-MM-DD");
                        event.extendedProps.description.time_out = moment(response.data[i].time_out).format("HH:mm");
                        event.extendedProps.description.time_in = moment(response.data[i].time_in).format("HH:mm");
                        event.extendedProps.description.notes = response.data[i].notes;
                        event.extendedProps.description.asteditor = Lasteditor;
                        event.extendedProps.description.items = response.data[i].items;
                        event.extendedProps.description.status = response.data[i].status;
                        event.extendedProps.description.creator = response.data[i].created_by;

                        event.extendedProps.description.check1 = response.data[i].check1;
                        event.extendedProps.description.heck2 = response.data[i].check2;
                    
                    }
                })
                .catch(function (response) {
                    //handle error
                    //alert(JSON.stringify(response));
                });
        },

        service_save_check: function(check1, check2, is_send) {
            let _this = this;

            if(is_send == "2")
            {
                if(this.check_date_use == "" || this.check_time_out == "" || this.check_time_in == "" || this.check_car_use == "")
                {
                    Swal.fire({
                        text: "Columns of Date, Time, Assigned Car cannot be blank.",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
            }

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            let kind = "1";

            if(check2 == true) kind = "2";
        
            form_Data.append("jwt", token);
            form_Data.append("id", this.id);
           
            form_Data.append("date_use", this.check_date_use);
            form_Data.append("car_use", this.check_car_use);
            form_Data.append("driver", this.check_driver);
            form_Data.append("time_out", this.check_time_out);
            form_Data.append("time_in", this.check_time_in);

            form_Data.append("status", is_send);

            form_Data.append("kind", kind);

            api_url = "api/car_calender_check_save.php";

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                    let gbcolor = "";

                    if(_this.check_car_use == "Alphard")
                    gbcolor = "#FECC28";
                    if(_this.check_car_use == "Avanza")
                    gbcolor = "#4EB5BB";
                    if(_this.check_car_use == "Traviz 1")
                    gbcolor = "#009858";
                    if(_this.check_car_use == "Traviz 2")
                    gbcolor = "#A671AD";
                    if(_this.check_car_use == "Traviz 3")
                    gbcolor = "#1A17E8";
                    if(_this.check_car_use == "Toyota Rush")
                    gbcolor = "#F19DB4";
                    if(_this.check_car_use == "")
                    gbcolor = "#141415";
                    

                    if(_this.id == 0)
                    {
                        let sid = 0;
                        let created_at = "";
                        let created_by = "";

                        if (response.data !== "") 
                        {
                            sid = response.data.sid;
                            created_at = response.data.created_at;
                            created_by = response.data.created_by;
                        }

                        let Lasteditor = created_by + " at " + created_at;

                        if (
                            _this.time_out != "" &&
                            _this.time_in != "" &&
                            _this.time_in >= _this.time_out && sid != 0
                        ) {
                            calendar.addEvent({
                                id: sid,
                                title: _this.schedule_Name,
                                Date: moment(_this.date_use).format("YYYY-MM-DD"),
                                //start: _this.date_use + "T" + _this.time_out,
                                //end: _this.date_use + "T" + _this.time_in,
                                color : gbcolor,
                                backgroundColor : gbcolor,
                                borderColor : gbcolor,
                                allDay: true,
                                description: {
                                    icon: symbol,
                                    schedule_Name: UnescapeHTML(_this.schedule_Name),
                                    car_use: _this.car_use,
                                    driver: _this.driver,
                                    helper: _this.helper,
                                    date_use: moment(_this.date_use).format("YYYY-MM-DD"),
                                    time_out: moment(_this.date_use + ' ' + _this.time_out).format("HH:mm"),
                                    time_in: moment(_this.date_use + ' ' + _this.time_in).format("HH:mm"),
                                    notes: _this.notes,
                                    Lasteditor: Lasteditor,
                                    items: _this.items,
                                    status: is_send,
                                    creator: _this.username,
                                },
                            });
                        }
                    }

                    if(_this.id != 0)
                    {
                        
                        var event = calendar.getEventById(_this.id);
                        event.title = _this.schedule_Name;
                        event.allDay = true;
                        event.Date = moment(_this.date_use).format("YYYY-MM-DD");
                        event.start = _this.date_use;
                        event.end = _this.date_use;

                        event.extendedProps.description.schedule_Name = _this.schedule_Name;
                        event.extendedProps.icon = symbol,
                        event.extendedProps.description.car_use = _this.car_use;
                        event.extendedProps.description.driver = _this.driver;
                        event.extendedProps.description.helper = _this.helper;
                        event.extendedProps.description.date_use = moment(_this.date_use).format("YYYY-MM-DD");
                        event.extendedProps.description.time_out = moment(_this.date_use + ' ' + _this.time_out).format("HH:mm");
                        event.extendedProps.description.time_in = moment(_this.date_use + ' ' + _this.time_in).format("HH:mm");
                        event.extendedProps.description.notes = _this.notes;
                        event.extendedProps.description.items = _this.items;
                        event.extendedProps.description.status = _this.status;
                        event.extendedProps.description.creator = _this.username;

                    }

                    _this.id = -1

                    $("#serviceModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
        },

        getUserName: function() {
            var form_Data = new FormData();
            let _this = this;
    
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/on_duty_get_myname.php',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                _this.username = response.data.username;
    
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

        set_up: function(fromIndex, eid) {
            if(this.showing == false) return;
            
            var toIndex = fromIndex - 1;

            if (toIndex < 0) toIndex = 0;

            var element = this.items.find(({ id }) => id === eid);
            this.items.splice(fromIndex, 1);
            this.items.splice(toIndex, 0, element);
        },

        set_down: function(fromIndex, eid) {
            if(this.showing == false) return;

            var toIndex = fromIndex + 1;

            if (toIndex > this.items.length - 1) toIndex = this.items.length - 1;

            var element = this.items.find(({ id }) => id === eid);
            this.items.splice(fromIndex, 1);
            this.items.splice(toIndex, 0, element);
        },

        edit: function(eid) {
            if(this.showing == false) return;

            var element = this.items.find(({ id }) => id === eid);

            this.item_schedule = element.schedule;
            this.item_company = element.company;
            this.item_address = element.address;
            this.item_purpose = element.purpose;
            this.editing = true;
            this.e_id = eid;
        },

        service_edit: function() {
            this.showing = true;
            this.edit_servie = true;
        },

        service_edit_check: function() {
            if(this.access_check1){
                this.check_showing = true;
                this.edit_servie_check1 = true;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = true;
                this.edit_servie_check2 = true;
            }
        },

        undo_service_edit_check: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;
            }
        },

        service_edit_check2: function() {
            if(this.access_check1){
                this.check_showing = true;
                this.edit_servie_check1 = true;
            }
            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = true;
                this.edit_servie_check2 = true;
            }
        },

        service_cancel_check: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;

                if(this.org_schedule_check1.length > 0){
                    this.check_date_use = moment(this.org_schedule_check1[0].date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule_check1[0].car_use;
                    this.check_driver = this.org_schedule_check1[0].driver;
                    this.check_time_out = moment(this.org_schedule_check1[0].time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_schedule_check1[0].time_in).format("HH:mm");
                }
                else{
                    this.check_date_use = moment(this.org_schedule.date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule.car_use;
                    this.check_driver = this.org_schedule.driver;
                    this.check_time_out = moment(this.org_schedule.time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_shedule.time_in).format("HH:mm");
                }
            }

            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;

                if(this.org_schedule_check2.length > 0){
                    this.check_driver = this.org_schedule_check2[0].driver;
                }
                else if(this.org_schedule_check1.length > 0){
                    this.check_driver = this.org_schedule_check1[0].driver;
                }
                else{
                    this.check_driver = this.org_schedule.driver;
                }
            }

        },

        service_cancel_check2: function() {
            if(this.access_check1){
                this.check_showing = false;
                this.edit_servie_check1 = false;

                if(this.org_schedule_check1.length > 0){
                    this.check_date_use = moment(this.org_schedule_check1[0].date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule_check1[0].car_use;
                    this.check_driver = this.org_schedule_check1[0].driver;
                    this.check_time_out = moment(this.org_schedule_check1[0].time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_schedule_check1[0].time_in).format("HH:mm");
                }
                else{
                    this.check_date_use = moment(this.org_schedule.date_use).format("YYYY-MM-DD");
                    this.check_car_use = this.org_schedule.car_use;
                    this.check_driver = this.org_schedule.driver;
                    this.check_time_out = moment(this.org_schedule.time_out).format("HH:mm");
                    this.check_time_in = moment(this.org_shedule.time_in).format("HH:mm");
                }
            }

            if(this.access_check2 && this.access_check1 == false){
                this.check_showing2 = false;
                this.edit_servie_check2 = false;

                if(this.org_schedule_check2.length > 0){
                    this.check_driver = this.org_schedule_check2[0].driver;
                }
                else if(this.org_schedule_check1.length > 0){
                    this.check_driver = this.org_schedule_check1[0].driver;
                }
                else{
                    this.check_driver = this.org_schedule.driver;
                }
            }
            

        },

        service_cancel: function() {
            this.showing = false;
            this.edit_servie = false;

            this.schedule_Name = this.org_schedule.schedule_Name;
            this.date_use = this.org_schedule.date_use;
            this.car_use = this.org_schedule.car_use;
            this.driver = this.org_schedule.driver;
            this.helper = this.org_schedule.helper;
            this.time_out = this.org_schedule.time_out;
            this.time_in = this.org_schedule.time_in;
            this.notes = this.org_schedule.notes;
            this.items = JSON.parse(this.org_schedule.items);
            this.creator = this.org_schedule.creator;
            

            this.editing = false;
     
        },

        del: function(eid) {
            if(this.showing == false) return;

            var index = this.items.findIndex(({ id }) => id === eid);
            if (index > -1) {
                this.items.splice(index, 1);
            }
        },

        duplicate_service: function() {
            let _this = this;
            let is_send = "0";

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            form_Data.append("jwt", token);
            form_Data.append("id", 0);
            form_Data.append("schedule_Name", this.schedule_Name);
            form_Data.append("date_use", this.date_use);
            form_Data.append("car_use", this.car_use);
            form_Data.append("driver", this.driver);
            form_Data.append("helper", this.helper);
            form_Data.append("time_out", this.time_out);
            form_Data.append("time_in", this.time_in);
            form_Data.append("notes", this.notes);
            form_Data.append("status", is_send);
            form_Data.append("items", JSON.stringify(this.items));

            api_url = "api/car_calender_main_save.php";

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                        let gbcolor = "";

                        if(_this.car_use == "Alphard")
                        gbcolor = "#FECC28";
                        if(_this.car_use == "Avanza")
                        gbcolor = "#4EB5BB";
                        if(_this.car_use == "Traviz 1")
                        gbcolor = "#009858";
                        if(_this.car_use == "Traviz 2")
                        gbcolor = "#A671AD";
                        if(_this.car_use == "Traviz 3")
                        gbcolor = "#1A17E8";
                        if(_this.car_use == "Toyota Rush")
                        gbcolor = "#F19DB4";
                        if(_this.car_use == "")
                        gbcolor = "#141415";

                        let sid = 0;
                        let created_at = "";
                        let created_by = "";

                        if (response.data !== "") 
                        {
                            sid = response.data.sid;
                            created_at = response.data.created_at;
                            created_by = response.data.created_by;
                        }

                        let Lasteditor = created_by + " at " + created_at;

                        if (
                            _this.time_out != "" &&
                            _this.time_in != "" &&
                            _this.time_in >= _this.time_out && sid != 0
                        ) {
                            calendar.addEvent({
                                id: sid,
                                title: _this.schedule_Name,
                                Date: moment(_this.date_use).format("YYYY-MM-DD"),
                                //start: _this.date_use + "T" + _this.time_out,
                                //end: _this.date_use + "T" + _this.time_in,
                                color : gbcolor,
                                backgroundColor : gbcolor,
                                borderColor : gbcolor,
                                allDay: true,
                                description: {
                                    icon: symbol,
                                    schedule_Name: UnescapeHTML(_this.schedule_Name),
                                    car_use: _this.car_use,
                                    driver: _this.driver,
                                    helper: _this.helper,
                                    date_use: moment(_this.date_use).format("YYYY-MM-DD"),
                                    time_out: moment(_this.date_use + ' ' + _this.time_out).format("HH:mm"),
                                    time_in: moment(_this.date_use + ' ' + _this.time_in).format("HH:mm"),
                                    notes: _this.notes,
                                    Lasteditor: Lasteditor,
                                    items: _this.items,
                                    status: is_send,
                                    creator: _this.username,
                                },
                            });
                        }


                    $("#serviceModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
        },

        save_item: function() {
            if(this.showing == false) return;

            var element = this.items.find(({ id }) => id === this.e_id);

            element.schedule = this.item_schedule;
            element.company = this.item_company;
            element.address = this.item_address;
            element.purpose = this.item_purpose;

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";

            this.editing = false;
            this.e_id = 0;

        },

        cancel_save_item : function() {
            if(this.showing == false) return;

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";

            this.editing = false;
            this.e_id = 0;

        },

        add_item: function() {
            if(this.showing == false) return;

            var ad = {
                id: ++this.sn,
                schedule: this.item_schedule,
                company: this.item_company,
                address: this.item_address,
                purpose: this.item_purpose,
            };
            this.items.push(ad);

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";

        },

        service_status: function(status) {
            let _this = this;

            var form_Data = new FormData();
            form_Data.append("id", this.id);

            form_Data.append("check_date_use", this.check_date_use);
            form_Data.append("check_car_use", this.check_car_use);
            form_Data.append("check_driver", this.check_driver);
            form_Data.append("check_time_out", this.check_time_out);
            form_Data.append("check_time_in", this.check_time_in);

            form_Data.append("date_use", this.date_use);
            form_Data.append("car_use", this.car_use);
            form_Data.append("driver", this.driver);
            form_Data.append("time_out", this.time_out);
            form_Data.append("time_in", this.time_in);


            form_Data.append("status", status);

            axios({
                method: "post",
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                url: 'api/car_calender_main_status_update.php',
                data: form_Data,
            })
            .then(function (response) {

                let symbol = "";
                if (status == "1") 
                    symbol = 'fa-question-circle';
                if (status == "2")
                    symbol = 'fa-car';

                    let gbcolor = "";

                    if(_this.car_use == "Alphard")
                    gbcolor = "#FECC28";
                    if(_this.car_use == "Avanza")
                    gbcolor = "#4EB5BB";
                    if(_this.car_use == "Traviz 1")
                    gbcolor = "#009858";
                    if(_this.car_use == "Traviz 2")
                    gbcolor = "#A671AD";
                    if(_this.car_use == "Traviz 3")
                    gbcolor = "#1A17E8";
                    if(_this.car_use == "Toyota Rush")
                    gbcolor = "#F19DB4";
                    if(_this.car_use == "")
                    gbcolor = "#141415";
                    
                    var event = calendar.getEventById(_this.id);
                    event.color = gbcolor;
                    event.backgroundColor = gbcolor;
                    event.borderColor = gbcolor;
                    event.extendedProps.icon = symbol;

                    event.extendedProps.description.status = _this.status;

                _this.id = -1;

                $("#serviceModalScrollable").modal("toggle");

                _this.clear_service();

                reload();
            })
            .catch(function (error) {
                //handle error
                Swal.fire({
                    text: error.data,
                    icon: "warning",
                    confirmButtonText: "OK",
                });
            });

        },

        service_delete: function() {
            let _this = this;

            Swal.fire({
                title: "Delete",
                text: "Are you sure to delete?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
              }).then((result) => {
                if (result.value) {
                    var form_Data = new FormData();
                    form_Data.append("id", _this.id);

                    form_Data.append("check_date_use", _this.check_date_use);
                    form_Data.append("check_car_use", _this.check_car_use);
                    form_Data.append("check_driver", _this.check_driver);
                    form_Data.append("check_time_out", _this.check_time_out);
                    form_Data.append("check_time_in", _this.check_time_in);

                    form_Data.append("date_use", _this.date_use);
                    form_Data.append("car_use", _this.car_use);
                    form_Data.append("driver", _this.driver);
                    form_Data.append("time_out", _this.time_out);
                    form_Data.append("time_in", _this.time_in);
        
                    form_Data.append("status", -1);
        
                    axios({
                        method: "post",
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                        url: 'api/car_calender_main_status_update.php',
                        data: form_Data,
                    })
                    .then(function (response) {

        
                        $("#serviceModalScrollable").modal("toggle");
        
                        _this.clear_service();
        
                        reload();
                    })
                    .catch(function (error) {
                        //handle error
                        Swal.fire({
                            text: error.data,
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                    });

                  
                } else {
 
                }
              });

            

        },

        service_save: function(is_send) {
            let _this = this;

            if(is_send == "1")
            {
                if(this.schedule_Name == "" || this.date_use == "" || this.time_out == "" || this.time_in == "")
                {
                    Swal.fire({
                        text: "Columns of Schedule Name, Date User, Time Out and Time In cannot be blank.",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
            }

            if(is_send == "0")
            {
                if(this.schedule_Name == "" || this.date_use == "")
                {
                    Swal.fire({
                        text: "Columns of Schedule Name and Date User cannot be blank.",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
            }

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            if(this.is_submit == true) return;
            this.is_submit = true;
        
            form_Data.append("jwt", token);
            form_Data.append("id", this.id);
            form_Data.append("schedule_Name", this.schedule_Name);
            form_Data.append("date_use", this.date_use);
            form_Data.append("car_use", this.car_use);
            form_Data.append("driver", this.driver);
            form_Data.append("helper", this.helper);
            form_Data.append("time_out", this.time_out);
            form_Data.append("time_in", this.time_in);
            form_Data.append("notes", this.notes);
            form_Data.append("status", is_send);
            form_Data.append("items", JSON.stringify(this.items));

            if(this.id == 0)
                api_url = "api/car_calender_main_save.php";

            if(this.id != 0)
                api_url = "api/car_calender_main_update.php";

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: api_url,
                    data: form_Data,
                })
                .then(function (response) {

                    let symbol = "";
                    if (is_send == "1") 
                        symbol = 'fa-question-circle';
                    if (is_send == "2")
                        symbol = 'fa-car';

                        let gbcolor = "";

                    if(_this.car_use == "Alphard")
                    gbcolor = "#FECC28";
                    if(_this.car_use == "Avanza")
                    gbcolor = "#4EB5BB";
                    if(_this.car_use == "Traviz 1")
                    gbcolor = "#009858";
                    if(_this.car_use == "Traviz 2")
                    gbcolor = "#A671AD";
                    if(_this.car_use == "Traviz 3")
                    gbcolor = "#1A17E8";
                    if(_this.car_use == "Toyota Rush")
                    gbcolor = "#F19DB4";
                    if(_this.car_use == "")
                    gbcolor = "#141415";
                    

                    if(_this.id == 0)
                    {
                        let sid = 0;
                        let created_at = "";
                        let created_by = "";

                        if (response.data !== "") 
                        {
                            sid = response.data.sid;
                            created_at = response.data.created_at;
                            created_by = response.data.created_by;
                        }

                        let Lasteditor = created_by + " at " + created_at;

                        if (
                            _this.time_out != "" &&
                            _this.time_in != "" &&
                            _this.time_in >= _this.time_out && sid != 0
                        ) {
                            calendar.addEvent({
                                id: sid,
                                title: _this.schedule_Name,
                                Date: moment(_this.date_use).format("YYYY-MM-DD"),
                                //start: _this.date_use + "T" + _this.time_out,
                                //end: _this.date_use + "T" + _this.time_in,
                                color : gbcolor,
                                backgroundColor : gbcolor,
                                borderColor : gbcolor,
                                allDay: true,
                                description: {
                                    icon: symbol,
                                    schedule_Name: UnescapeHTML(_this.schedule_Name),
                                    car_use: _this.car_use,
                                    driver: _this.driver,
                                    helper: _this.helper,
                                    date_use: moment(_this.date_use).format("YYYY-MM-DD"),
                                    time_out: moment(_this.date_use + ' ' + _this.time_out).format("HH:mm"),
                                    time_in: moment(_this.date_use + ' ' + _this.time_in).format("HH:mm"),
                                    notes: _this.notes,
                                    Lasteditor: Lasteditor,
                                    items: _this.items,
                                    status: is_send,
                                    creator: _this.username,
                                },
                            });
                        }
                    }

                    if(_this.id != 0)
                    {
                        
                        var event = calendar.getEventById(_this.id);
                        event.title = _this.schedule_Name;
                        event.allDay = true;
                        event.Date = moment(_this.date_use).format("YYYY-MM-DD");
                        event.start = _this.date_use;
                        event.end = _this.date_use;

                        event.extendedProps.description.schedule_Name = _this.schedule_Name;
                        event.extendedProps.icon = symbol,
                        event.extendedProps.description.car_use = _this.car_use;
                        event.extendedProps.description.driver = _this.driver;
                        event.extendedProps.description.helper = _this.helper;
                        event.extendedProps.description.date_use = moment(_this.date_use).format("YYYY-MM-DD");
                        event.extendedProps.description.time_out = moment(_this.date_use + ' ' + _this.time_out).format("HH:mm");
                        event.extendedProps.description.time_in = moment(_this.date_use + ' ' + _this.time_in).format("HH:mm");
                        event.extendedProps.description.notes = _this.notes;
                        event.extendedProps.description.items = _this.items;
                        event.extendedProps.description.status = _this.status;
                        event.extendedProps.description.creator = _this.username;

                    }

                    $("#serviceModalScrollable").modal("toggle");

                    _this.clear_service();

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                })
                .finally(function () {
                    _this.is_submit = false;
                });
        },

        export_service: function () {
            let _this = this;

            let buttons = "Which do you want to export?" +
            "<br>" +
            '<button type="button" role="button" tabindex="0" class="SwalBtn1 customSwalBtn">' + 'Only “Content of Request”' + '</button>' +
            '<button type="button" role="button" tabindex="0" class="SwalBtn2 customSwalBtn">' + '”Request Review” and “Content of Request”' + '</button>' + 
            '<button type="button" role="button" tabindex="0" class="SwalBtn3 customSwalBtn">' + 'Cancel' + '</button>';

            if(this.status == "0" || this.status == "1")
            {
                // buttons = "Which do you want to export?" +
                // "<br>" +
                // '<button type="button" role="button" tabindex="0" class="SwalBtn1 customSwalBtn">' + 'Only “Content of Request”' + '</button>' +
                // '<button type="button" role="button" tabindex="0" class="SwalBtn3 customSwalBtn">' + 'Cancel' + '</button>';
                service.export_service_full('');
                return;
            }

            Swal.fire({
                title: "Export",
                icon: "warning",
                html: buttons,
                showCancelButton: false,
                showConfirmButton: false
              })
        },

        export_service_full: function (full) {
            let _this = this;
            var form_Data = new FormData();

            const filename = "attendance";
            
            form_Data.append("full", full);
            form_Data.append("id", this.id);
            form_Data.append("schedule_Name", this.schedule_Name);
            form_Data.append("date_use", this.date_use);
            form_Data.append("car_use", this.car_use);
            form_Data.append("driver", this.driver);
            form_Data.append("helper", this.helper);
            form_Data.append("time_out", this.time_out);
            form_Data.append("time_in", this.time_in);
            form_Data.append("notes", this.notes);
            form_Data.append("status", this.status);
            form_Data.append("items", JSON.stringify(this.items));

            form_Data.append("check_date_use", this.check_date_use);
            form_Data.append("check_car_use", this.check_car_use);
            form_Data.append("check_driver", this.check_driver);
            form_Data.append("check_time_out", this.check_time_out);
            form_Data.append("check_time_in", this.check_time_in);

            form_Data.append("creator", this.schedule_Creator);


            const token = sessionStorage.getItem("token");

            axios({
                    method: "post",
                    url: "car_schedule_data_word.php",
                    data: form_Data,
                    responseType: "blob", // important
                })
                .then(function (response) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;

                    link.setAttribute("download", "car_schedule.docx");

                    document.body.appendChild(link);
                    link.click();
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },

        service_click: function(sc_content) {
            this.org_schedule = JSON.parse(JSON.stringify(sc_content));
            this.org_schedule_check1 = JSON.parse(JSON.stringify(sc_content.check1));
            this.org_schedule_check2 = JSON.parse(JSON.stringify(sc_content.check2));

            this.schedule_Name = sc_content.schedule_Name;
            this.date_use = sc_content.date_use;
            this.car_use = sc_content.car_use;
            this.driver = sc_content.driver;
            this.helper = sc_content.helper;
            this.time_out = sc_content.time_out;
            this.time_in = sc_content.time_in;
            this.notes = sc_content.notes;
            this.items = JSON.parse(sc_content.items);
            this.creator = sc_content.creator;
            this.status = sc_content.status;

            this.schedule_Creator = sc_content.creator + " at " + sc_content.create_at;

            // for check
            this.check_date_use = sc_content.date_use;
            this.check_car_use = sc_content.car_use;
            this.check_driver = sc_content.driver;
            this.check_time_out = sc_content.time_out;
            this.check_time_in = sc_content.time_in;

            if(sc_content.check1.length > 0)
            {
                this.check_date_use = moment(sc_content.check1[0].date_use).format("YYYY-MM-DD");
                this.check_car_use = sc_content.check1[0].car_use;
                this.check_driver = sc_content.check1[0].driver;
                this.check_time_out = moment(sc_content.check1[0].time_out).format("HH:mm");
                this.check_time_in = moment(sc_content.check1[0].time_in).format("HH:mm");
            }

            if(sc_content.check2.length > 0)
            {
                this.check_driver = sc_content.check2[0].driver;
            }

            // if(this.status == "2" && sc_content.check1.length > 0)
            // {
            //     this.date_use = moment(sc_content.check1[0].date_use).format("YYYY-MM-DD");
            //     this.car_use = sc_content.check1[0].car_use;
            //     this.driver = sc_content.check1[0].driver;
            //     this.time_out = moment(sc_content.check1[0].time_out).format("HH:mm");
            //     this.time_in = moment(sc_content.check1[0].time_in).format("HH:mm");
            // }

            // if(this.status == "3" && sc_content.check2.length > 0)
            // {
            //     this.check_driver = sc_content.check2[0].driver;
            // }
        },

        clean_service: function() {
            this.schedule_Name = "";
            this.date_use = "";
            this.car_use = "";
            this.driver = "";
            this.helper = "";
            this.time_out = "";
            this.time_in = "";
            this.notes = "";
            this.creator = "";
            this.status = "";

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";
            this.items = [];
            this.id = -1;

            this.editing = false;

            this.schedule_Creator = "";
        },

        reset_service_check: function() {
            if(this.access_check1){
                if(this.check_showing == false) return;
    
                this.check_date_use = "";
                this.check_car_use = "";
                this.check_driver = "";
    
                this.check_time_out = "";
                this.check_time_in = "";

            }

            if(this.access_check2 && this.access_check1 == false){
                if(this.check_showing2 == false) return;

                this.check_driver = "";
            }
        
        },

        reset_service_check2: function() {
            
            if(this.access_check1){
                if(this.check_showing == false) return;
    
                this.check_date_use = "";
                this.check_car_use = "";
                this.check_driver = "";
    
                this.check_time_out = "";
                this.check_time_in = "";

            }

            if(this.access_check2 && this.access_check1 == false){
                if(this.check_showing2 == false) return;

                this.check_driver = "";
            }
        
        },

        reset_service: function() {
            if(this.showing == false) return;

            this.schedule_Name = "";
            this.date_use = "";
            this.car_use = "";
            this.driver = "";
            this.helper = "";
            this.time_out = "";
            this.time_in = "";
            this.notes = "";
            this.creator = "";
            this.status = "";

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";
            this.items = [];
            this.id = -1;
        },

        clear_service: function() {
            if(this.showing == false) return;

            this.schedule_Name = "";
            this.date_use = "";
            this.car_use = "";
            this.driver = "";
            this.helper = "";
            this.time_out = "";
            this.time_in = "";
            this.notes = "";
            this.creator = "";
            this.status = "";

            this.item_schedule = "";
            this.item_company = "";
            this.item_address = "";
            this.item_purpose = "";
            this.items = [];
            this.id = -1;

            this.editing = false;
            this.edit_servie = false;
            this.sn = 0;
            this.showing = false;
        },
    },
});

var app = new Vue({
    el: '#sc_relevant', 
    data: {
        name: "",
        department: "",
        title:"",
        today: "",
        items: [],
        agenda: [],
        messages: [],
        id: 0,
        sid:0,
        download_type: "docx",
        file_day: "",
        fileArray: [],
        filename: [],

        attendee:[],

        users: [],
        users_org: [],

        schedule_confirm: false,

        
    },
    created() {

        let _this = this;
        let uri = window.location.href.split("?");
        if (uri.length >= 2) {
        let vars = uri[1].split("&");

        let tmp = "";
        vars.forEach(async function(v) {
            tmp = v.split("=");
            if (tmp.length == 2) {
            switch (tmp[0]) {
                case "id":
                _this.sid = tmp[1];
                break;
               
                default:
                console.log(`Too many args`);
            }
            }
        });
        }

        
        // this.getMonthDay();
        // this.getUserName();
        // this.getUsers();

        // this.getAccess();
    
    },
    methods: {
        getUsers() {
  
            let _this = this;
      
            let token = localStorage.getItem('accessToken');
      
            axios
              .get('api/project02_user_online', { headers: { "Authorization": `Bearer ${token}` } })
              .then(
                (res) => {
                    _users = res.data;
                    _this.users = Object.keys(_users).map(function(k){return _users[k].username})
                    _this.users_org = Object.keys(_users).map(function(k){return _users[k].username})
                  
                },
                (err) => {
                  alert(err.response);
                },
              )
              .finally(() => {
      
              });
          },


        addMain2: function (details, main, du, calendar) {
            if (!main.Allday) {
                if (
                    main.Starttime != "" &&
                    main.Endtime != "" &&
                    main.Endtime >= main.Starttime
                ) {
                    //good to go
                    console.log("go");
                } else {
                    Swal.fire({
                        text: JSON.stringify("End-time has to behide start-time!"),
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
            }

            this.action = 22; //add
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("title", main.Title);
            form_Data.append("all_day", main.Allday);
            form_Data.append("start_time", main.Starttime);
            form_Data.append("end_time", main.Endtime);
            
            if(document.getElementById("sc_color_other").checked)
                form_Data.append("color_other", main.Color_Other);
            else
                form_Data.append("color_other", "");

            var colors = document.getElementsByName("sc_color");
            var color = "";
            for(var i=0; i<colors.length; i++)
            {
                if(colors[i].checked)
                    color = colors[i].value;
            }
            if(color == "" && !document.getElementById("sc_color_other").checked)
            {
                Swal.fire({
                    text: JSON.stringify("Please choose color for schedule."),
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }

            form_Data.append("color", color);

            form_Data.append("text_color", "white");
            form_Data.append("project", main.Project);
            form_Data.append("sales_executive", main.Sales_Executive);
            form_Data.append("project_in_charge", main.Project_in_charge);
            form_Data.append("project_relevant", main.Project_relevant);
            form_Data.append("installer_needed", main.Installer_needed);
            form_Data.append("installer_needed_other", main.Installer_needed_other);

            form_Data.append("related_project_id", main.Related_project_id);
            form_Data.append("related_stage_id", main.Related_stage_id);

            form_Data.append(
                "installer_needed_location",
                main.Location_Products_to_Bring
            );
            form_Data.append("things_to_bring", main.Things_to_Bring);
            form_Data.append(
                "things_to_bring_location",
                main.Location_Things_to_Bring
            );
            form_Data.append("products_to_bring", main.Products_to_Bring);
            if (du != 2) {
                form_Data.append("products_to_bring_files", _this.filename);
            } else {
                form_Data.append("products_to_bring_files", main.File_name);
            }
            form_Data.append("service", main.Service);
            form_Data.append("driver", main.Driver);

            if(main.Driver != 6)
                main.Driver_Other = "";

            form_Data.append("driver_other", main.Driver_Other);

            form_Data.append("back_up_driver", main.Back_up_Driver);

            if(main.Back_up_Driver != 6)
                main.Back_up_Driver_Other = "";
            
            form_Data.append("back_up_driver_other", main.Back_up_Driver_Other);

            form_Data.append("photoshoot_request", main.Photoshoot_Request);
            form_Data.append("notes", main.Notes);
            form_Data.append("is_enabled", main.is_enabled);
            form_Data.append("action", this.action);
            form_Data.append("created_by", this.name);
            form_Data.append("today", this.file_day);

            // make it in one process
            for (var i = 0; i < details.length; i++) {
                if (
                    details[i].appointtime != "" &&
                    details[i].appointtime != "Invalid date"
                ) {
                    var valid = moment(
                        details[i].appointtime,
                        "YYYY-MM-DD HH:mm",
                        true
                    ).isValid();
                    var valids = moment(
                        details[i].appointtime,
                        "YYYY-MM-DD HH:mm:ss",
                        true
                    ).isValid();
                    if (!valid && !valids) {
                        details[i].appointtime = main.Date + " " + details[i].appointtime;
                    }
                }else{
                    details[i].appointtime = "";
                }

                if (details[i].endtime != "" && details[i].endtime != "Invalid date") {
                    var valide = moment(
                        details[i].endtime,
                        "YYYY-MM-DD HH:mm",
                        true
                    ).isValid();
                    var valides = moment(
                        details[i].endtime,
                        "YYYY-MM-DD HH:mm:ss",
                        true
                    ).isValid();
                    if (!valide && !valides) {
                        details[i].endtime = main.Date + " " + details[i].endtime;
                    }
                }else{
                    details[i].endtime = "";
                }
            }

            form_Data.append("detail_list", JSON.stringify(details));

            for (var i = 0; i < this.fileArray.length; i++) {
                let file = this.fileArray[i];
                form_Data.append("files[" + i + "]", file);
            }

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    if (response.data === "") 
                    {
                        Swal.fire({
                            // text: JSON.stringify("Files size over 200MB, Please don't upload file too many at one time."),
                            text: JSON.stringify(response.data),
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                        return;
                    }

                    if (isNaN(response.data)) {
                        Swal.fire({
                            // text: JSON.stringify("Calendar schedule insert fail"),
                            text: JSON.stringify(response.data),
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                        return;
                    }

                    //handle success
                    if (main.Allday) {
                        calendar.addEvent({
                            title: main.Title,
                            start: main.Date,
                            allDay: main.Allday,
                            description: main,
                            borderColor: main.Color,
                            backgroundColor: main.Color,
                        });
                    } else {
                        if (
                            main.Starttime != "" &&
                            main.Endtime != "" &&
                            main.Endtime >= main.Starttime
                        ) {
                            calendar.addEvent({
                                title: main.Title,
                                start: main.Date + "T" + main.Starttime,
                                end: main.Date + "T" + main.Endtime,
                                allDay: main.Allday,
                                description: main,
                                borderColor: main.Color,
                                backgroundColor: main.Color,
                            });
                        }
                    }

                    app.filename = [];
                    app.fileArray = [];

                    document.getElementById("fileload").value = "";

                    $("#exampleModalScrollable").modal("toggle");

                    resetSchedule();

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
        },

        addMain: function (details, main, du) {
            this.action = 2; //add
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("title", main.Title);
            form_Data.append("all_day", main.Allday);
            form_Data.append("start_time", main.Starttime);
            form_Data.append("end_time", main.Endtime);
            
            if(document.getElementById("sc_color_other").checked)
                form_Data.append("color_other", main.Color_Other);

            form_Data.append("related_project_id", main.Related_project_id);
            form_Data.append("related_stage_id", main.Related_stage_id);

            form_Data.append("color", main.Color);
            form_Data.append("text_color", "white");
            form_Data.append("project", main.Project);
            form_Data.append("sales_executive", main.Sales_Executive);
            form_Data.append("project_in_charge", main.Project_in_charge);
            form_Data.append("project_relevant", main.Project_relevant);
            form_Data.append("installer_needed", main.Installer_needed);
            form_Data.append("installer_needed_other", main.Installer_needed_other);
            form_Data.append(
                "installer_needed_location",
                main.Location_Products_to_Bring
            );
            form_Data.append("things_to_bring", main.Things_to_Bring);
            form_Data.append(
                "things_to_bring_location",
                main.Location_Things_to_Bring
            );
            form_Data.append("products_to_bring", main.Products_to_Bring);
            if (du != 2) {
                form_Data.append("products_to_bring_files", _this.filename);
            } else {
                form_Data.append("products_to_bring_files", main.File_name);
            }
            form_Data.append("service", main.Service);
            form_Data.append("driver", main.Driver);

            if(main.Driver != 6)
                main.Driver_Other = "";

            form_Data.append("driver_other", main.Driver_Other);
            form_Data.append("back_up_driver", main.Back_up_Driver);

            form_Data.append("photoshoot_request", main.Photoshoot_Request);
            form_Data.append("notes", main.Notes);
            form_Data.append("is_enabled", main.is_enabled);
            form_Data.append("action", this.action);
            form_Data.append("created_by", this.name);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    console.log(details);
                    console.log(response);
                    console.log(response.data[0]);
                    _this.addDetails(response.data[0], details, main.Date);
                    //handle success
                    //_this.items = response.data
                    //console.log(_this.items)
                })
                .catch(function (response) {
                    //handle error
                    Swal.fire({
                        text: JSON.stringify(response),
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                });
            //this.upload();
            //this.reload();
        },

        addDetails: function (mainId, addDetails, date) {
            this.action = 2; //add
            var token = localStorage.getItem("token");
            let _this = this;
            for (var i = 0; i < addDetails.length; i++) {
                var form_Data = new FormData();
                form_Data.append("jwt", token);
                form_Data.append("main_id", mainId);
                form_Data.append("location", addDetails[i].location);
                form_Data.append("agenda", addDetails[i].agenda);
                if (
                    addDetails[i].appointtime != "" &&
                    addDetails[i].appointtime != "Invalid date"
                ) {
                    var valid = moment(
                        addDetails[i].appointtime,
                        "YYYY-MM-DD HH:mm",
                        true
                    ).isValid();
                    var valids = moment(
                        addDetails[i].appointtime,
                        "YYYY-MM-DD HH:mm:ss",
                        true
                    ).isValid();
                    if (!valid && !valids) {
                        form_Data.append(
                            "appoint_time",
                            date + " " + addDetails[i].appointtime
                        );
                    } else {
                        form_Data.append("appoint_time", addDetails[i].appointtime);
                    }
                }

                if (
                    addDetails[i].endtime != "" &&
                    addDetails[i].endtime != "Invalid date"
                ) {
                    var valide = moment(
                        addDetails[i].endtime,
                        "YYYY-MM-DD HH:mm",
                        true
                    ).isValid();
                    var valides = moment(
                        addDetails[i].endtime,
                        "YYYY-MM-DD HH:mm:ss",
                        true
                    ).isValid();
                    if (!valide && !valides) {
                        form_Data.append("end_time", date + " " + addDetails[i].endtime);
                    } else {
                        form_Data.append("end_time", addDetails[i].endtime);
                    }
                }
                form_Data.append("sort", addDetails[i].sort);
                form_Data.append("is_enabled", addDetails[i].is_enabled);
                form_Data.append("action", this.action);
                form_Data.append("created_by", this.name);
                axios({
                        method: "post",
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                        url: "api/work_calender_detail",
                        data: form_Data,
                    })
                    .then(function (response) {
                        //this.addDetails(response.data[0]);
                        //handle success
                        //_this.items = response.data
                        //console.log(_this.items)
                    })
                    .catch(function (response) {
                        //handle error
                        Swal.fire({
                            text: JSON.stringify(response),
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    });
                //this.upload();
                // this.reload();
            }
            _this.upload();
            reload();
        },

        getMain: function () {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            _this.items = [];
            this.action = 1; //select
            form_Data.append("jwt", token);
            form_Data.append("action", this.action);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main.php",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    //var data = JSON.parse(response.data);
                    for (var i = 0; i < response.data.length; i++) {
                        var agendas = [];
                        var isAll = false;
                        var Lasteditor = "";
                        var photoshoot = "No";
                        if (response.data[i].all_day == "1") {
                            isAll = true;
                        }
                        if (response.data[i].photoshoot_request == "1") {
                            photoshoot = "Yes";
                        }
                        if (
                            response.data[i].updated_by != "" &&
                            response.data[i].updated_by != null
                        ) {
                            Lasteditor =
                                response.data[i].updated_by +
                                " at " +
                                response.data[i].updated_at;
                        } else {
                            Lasteditor =
                                response.data[i].created_by +
                                " at " +
                                response.data[i].created_at;
                        }
                        for (var j = 0; j < _this.agenda.length; j++) {
                            if (_this.agenda[j].main_id == response.data[i].id) {
                                agendas.push({
                                    agenda: UnescapeHTML(_this.agenda[j].agenda),
                                    appointtime: moment(_this.agenda[j].appoint_time).format(
                                        "HH:mm"
                                    ),
                                    endtime: moment(_this.agenda[j].end_time).format("HH:mm"),
                                    sort: _this.agenda[j].sort,
                                    location: UnescapeHTML(_this.agenda[j].location),
                                });
                            }
                        }
                        //整理檔案
                        response.data[i].products_to_bring_files = response.data[
                            i
                        ].products_to_bring_files.replaceAll(",", '","');
                        if (response.data[i].products_to_bring_files.indexOf('"') == 0) {
                            response.data[i].products_to_bring_files =
                                "[" + response.data[i].products_to_bring_files + "]";
                            response.data[i].products_to_bring_files = JSON.parse(
                                response.data[i].products_to_bring_files
                            );
                        } else {
                            response.data[i].products_to_bring_files =
                                '["' + response.data[i].products_to_bring_files + '"]';
                            response.data[i].products_to_bring_files = JSON.parse(
                                response.data[i].products_to_bring_files
                            );
                        }
                        var files = "";
                        response.data[i].products_to_bring_files.forEach((element) => {
                            var file_str =
                                "<a href='https://storage.cloud.google.com/calendarfile/" +
                                element +
                                "' target='_blank'>" +
                                element +
                                "</a>&emsp;";
                            if(element.trim() !== '')
                                files += file_str;
                        });
                        _this.items.push({
                            id: response.data[i].id,
                            title: response.data[i].title,
                            Date: moment(response.data[i].start_time).format("YYYY-MM-DD"),
                            start: moment(response.data[i].start_time).format(
                                "YYYY-MM-DDTHH:mm"
                            ), // will be parsed
                            end: moment(response.data[i].end_time).format("YYYY-MM-DDTHH:mm"),
                            color: response.data[i].color,
                            color_other: response.data[i].color_other,
                            allDay: isAll,
                            description: {
                                Title: UnescapeHTML(response.data[i].title),
                                Color: response.data[i].color,
                                Color_Other: response.data[i].color_other,
                                Date: moment(response.data[i].start_time).format("YYYY-MM-DD"),
                                Allday: isAll,
                                Starttime: moment(response.data[i].start_time).format("HH:mm"),
                                Endtime: moment(response.data[i].end_time).format("HH:mm"),
                                Project: UnescapeHTML(response.data[i].project),
                                Sales_Executive: UnescapeHTML(response.data[i].sales_executive),
                                Project_in_charge: UnescapeHTML(
                                    response.data[i].project_in_charge
                                ),
                                Project_relevant: UnescapeHTML(
                                    response.data[i].project_relevant
                                ),
                                Installer_needed: UnescapeHTML(
                                    response.data[i].installer_needed
                                ),
                                Installer_needed_other: UnescapeHTML(
                                    response.data[i].installer_needed_other
                                ),
                                Location_Things_to_Bring: UnescapeHTML(
                                    response.data[i].things_to_bring_location
                                ),
                                Things_to_Bring: UnescapeHTML(response.data[i].things_to_bring),
                                Location_Products_to_Bring: UnescapeHTML(
                                    response.data[i].installer_needed_location
                                ),
                                Products_to_Bring: UnescapeHTML(
                                    response.data[i].products_to_bring
                                ),
                                Products_to_bring_files: files,
                                File_name: response.data[i].products_to_bring_files,
                                Service: response.data[i].service,
                                Driver: response.data[i].driver,
                                Driver_Other: response.data[i].driver_other,
                                Back_up_Driver: response.data[i].back_up_driver,
                                Back_up_Driver_Other: response.data[i].back_up_driver_other,
                                Photoshoot_Request: photoshoot,
                                Notes: UnescapeHTML(response.data[i].notes),
                                Lock: response.data[i].lock,
                                Confirm: response.data[i].confirm,
                                Agenda: agendas,
                                Lasteditor: Lasteditor,

                                Related_project_id : response.data[i].related_project_id,
                                Related_stage_id : response.data[i].related_stage_id,
                            },
                        });
                    }
                })
                .catch(function (response) {
                    //handle error
                    //alert(JSON.stringify(response));
                });
        },

        getInitMain: function (id) {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            _this.items = [];
            this.action = 10; //select
            form_Data.append("jwt", token);

            sdate = $("#sdate").val();
            edate = $("#edate").val();

            form_Data.append("sdate", sdate);
            form_Data.append("edate", edate);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/car_calender_main.php",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    //var data = JSON.parse(response.data);
                    for (var i = 0; i < response.data.length; i++) {
                 
                        var isAll = false;
                        var Lasteditor = "";
                        
                        
                        if (
                            response.data[i].updated_by != "" &&
                            response.data[i].updated_by != null
                        ) {
                            Lasteditor =
                                response.data[i].updated_by +
                                " at " +
                                response.data[i].updated_at;
                        } else {
                            Lasteditor =
                                response.data[i].created_by +
                                " at " +
                                response.data[i].created_at;
                        }

                        

                        let symbol = "";
                        if (response.data[i].status == "1") 
                            symbol = 'fa-question-circle';
                        if (response.data[i].status == "2")
                            symbol = 'fa-car';

                        let gbcolor = "";
                        let dateuse = "";

                        if(response.data[i].status == "2")
                        {
                            if(response.data[i].check1.length > 0){
                                if(response.data[i].check1[0].car_use == "Alphard")
                                gbcolor = "#FECC28";
                                if(response.data[i].check1[0].car_use == "Avanza")
                                gbcolor = "#4EB5BB";
                                if(response.data[i].check1[0].car_use == "Traviz 1")
                                gbcolor = "#009858";
                                if(response.data[i].check1[0].car_use == "Traviz 2")
                                gbcolor = "#A671AD";
                                if(response.data[i].check1[0].car_use == "Traviz 3")
                                gbcolor = "#1A17E8";
                                if(response.data[i].check1[0].car_use == "Toyota Rush")
                                gbcolor = "#F19DB4";
                                if(response.data[i].check1[0].car_use == "")
                                gbcolor = "#141415";

                                dateuse = moment(response.data[i].check1[0].date_use).format("YYYY-MM-DD");
                            }
                        }

                        if(response.data[i].status == "1" || response.data[i].status == "0")
                        {
                                if(response.data[i].car_use == "Alphard")
                                gbcolor = "#FECC28";
                                if(response.data[i].car_use == "Avanza")
                                gbcolor = "#4EB5BB";
                                if(response.data[i].car_use == "Traviz 1")
                                gbcolor = "#009858";
                                if(response.data[i].car_use == "Traviz 2")
                                gbcolor = "#A671AD";
                                if(response.data[i].car_use == "Traviz 3")
                                gbcolor = "#1A17E8";
                                if(response.data[i].car_use == "Toyota Rush")
                                gbcolor = "#F19DB4";
                                if(response.data[i].car_use == "")
                                gbcolor = "#141415";

                                dateuse = moment(response.data[i].date_use).format("YYYY-MM-DD");
                        }

                        if(response.data[i].feliix == "1")
                        {
                            var agendas = [];
                            var isAll = false;
                            var Lasteditor = "";
                            var photoshoot = "No";
                            if (response.data[i].all_day == "1") {
                                isAll = true;
                            }
                            if (response.data[i].photoshoot_request == "1") {
                                photoshoot = "Yes";
                            }
                            if (
                                response.data[i].updated_by != "" &&
                                response.data[i].updated_by != null
                            ) {
                                Lasteditor =
                                    response.data[i].updated_by +
                                    " at " +
                                    response.data[i].updated_at;
                            } else {
                                Lasteditor =
                                    response.data[i].created_by +
                                    " at " +
                                    response.data[i].created_at;
                            }


                            for (var j = 0; j < response.data[i].detail.length; j++) {
                                //if (_this.agenda[j].main_id == response.data[i].id) {
                                    agendas.push({
                                        agenda: UnescapeHTML(response.data[i].detail[j].agenda),
                                        appointtime: moment(response.data[i].detail[j].appoint_time).format(
                                            "HH:mm"
                                        ),
                                        endtime: moment(response.data[i].detail[j].end_time).format("HH:mm"),
                                        sort: response.data[i].detail[j].sort,
                                        location: UnescapeHTML(response.data[i].detail[j].location),
                                    });
                                //}
                            }
                        
                            
                            //整理檔案
                            response.data[i].products_to_bring_files = response.data[
                                i
                            ].products_to_bring_files.replaceAll(",", '","');
                            if (response.data[i].products_to_bring_files.indexOf('"') == 0) {
                                response.data[i].products_to_bring_files =
                                    "[" + response.data[i].products_to_bring_files + "]";
                                response.data[i].products_to_bring_files = JSON.parse(
                                    response.data[i].products_to_bring_files
                                );
                            } else {
                                response.data[i].products_to_bring_files =
                                    '["' + response.data[i].products_to_bring_files + '"]';
                                response.data[i].products_to_bring_files = JSON.parse(
                                    response.data[i].products_to_bring_files
                                );
                            }
                            var files = "";
                            response.data[i].products_to_bring_files.forEach((element) => {
                                var file_str =
                                    "<input type='checkbox' class='custom-control-input' id='" + element + "' checked name='file_elements' value='" + element + "' />" + 
                                    "<label class='custom-control-label' style='justify-content: flex-start;' for='" + element + "'>" +
                                    "<a href='" +
                                    element +
                                    "' target='_blank'>" +
                                    element +
                                    "</a></label>";
                                if(element.trim() !== '')
                                {
                                    files += "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + file_str + "</div>";
                                }
                            });

                            files = "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + files + "</div>";
                            _this.items.push({
                                id: response.data[i].id * -1,
                                title: response.data[i].title,
                                Date: moment(dateuse).format("YYYY-MM-DD"),
                                start: moment(dateuse).format("YYYY-MM-DD"), // will be parsed
                                end: moment(dateuse).format("YYYY-MM-DD"),
                                // color: ((response.data[i].color_other !== '') ? response.data[i].color_other : response.data[i].color),
                                color: gbcolor,
                                // color_other: response.data[i].color_other,
                                allDay: true,
                                
                                description: {
                                    icon: symbol,
                                    id: response.data[i].id,
                                    Title: UnescapeHTML(response.data[i].title),
                                    Color: response.data[i].color,
                                    Color_Other: response.data[i].color_other,
                                    Date: moment(response.data[i].start_time).format("YYYY-MM-DD"),
                                    Allday: isAll,
                                    Starttime: moment(response.data[i].start_time).format("HH:mm"),
                                    Endtime: moment(response.data[i].end_time).format("HH:mm"),
                                    Project: UnescapeHTML(response.data[i].project),
                                    Sales_Executive: UnescapeHTML(response.data[i].sales_executive),
                                    Project_in_charge: UnescapeHTML(
                                        response.data[i].project_in_charge
                                    ),
                                    Project_relevant: UnescapeHTML(
                                        response.data[i].project_relevant
                                    ),
                                    Installer_needed: UnescapeHTML(
                                        response.data[i].installer_needed
                                    ),
                                    Installer_needed_other: UnescapeHTML(
                                        response.data[i].installer_needed_other
                                    ),
                                    Location_Things_to_Bring: UnescapeHTML(
                                        response.data[i].things_to_bring_location
                                    ),
                                    Things_to_Bring: UnescapeHTML(response.data[i].things_to_bring),
                                    Location_Products_to_Bring: UnescapeHTML(
                                        response.data[i].installer_needed_location
                                    ),
                                    Products_to_Bring: UnescapeHTML(
                                        response.data[i].products_to_bring
                                    ),
                                    Products_to_bring_files: files,
                                    File_name: response.data[i].products_to_bring_files,
                                    Service: response.data[i].service,
                                    Driver: response.data[i].driver,
                                    Driver_Other: response.data[i].driver_other,
                                    Driver_Text : response.data[i].driver_text,
                                    Back_up_Driver: response.data[i].back_up_driver,
                                    Back_up_Driver_Other: response.data[i].back_up_driver_other,
                                    Photoshoot_Request: photoshoot,
                                    Notes: UnescapeHTML(response.data[i].notes),
                                    Lock: response.data[i].lock,
                                    Agenda: agendas,
                                    Lasteditor: Lasteditor,
                                    Related_project_id: response.data[i].related_project_id,
                                    Related_stage_id: response.data[i].related_stage_id,
                                    check1 : response.data[i].check1,
                                    check2 : response.data[i].check2,
                                    status: response.data[i].status,
                                    feliix: response.data[i].feliix,
                                    
                                },
                            });
                        }
                        else
                        {
                            _this.items.push({

                                id: response.data[i].id,
                                title: response.data[i].schedule_Name,
                                Date: moment(dateuse).format("YYYY-MM-DD"),
                                start: moment(dateuse).format("YYYY-MM-DD"), // will be parsed
                                end: moment(dateuse).format("YYYY-MM-DD"),
                                color: gbcolor,
                                display: 'block',
                                // color: ((response.data[i].color_other !== '') ? response.data[i].color_other : response.data[i].color),
                                // color_other: response.data[i].color_other,
                                backgroundColor: gbcolor,
                                borderColor: gbcolor,
                                allDay: true,
                                
                                description: {
                                    icon: symbol,
                                    schedule_Name: UnescapeHTML(response.data[i].schedule_Name),
                                    car_use: response.data[i].car_use,
                                    driver: response.data[i].driver,
                                    helper: response.data[i].helper,
                                    date_use: moment(response.data[i].date_use).format("YYYY-MM-DD"),
                                    time_out: moment(response.data[i].time_out).format("HH:mm"),
                                    time_in: moment(response.data[i].time_in).format("HH:mm"),
                                    notes: response.data[i].notes,
                                    Lasteditor: Lasteditor,
                                    items: response.data[i].items,
                                    status: response.data[i].status,
                                    creator : response.data[i].created_by,
                                    create_at: response.data[i].created_at,
    
                                    check1 : response.data[i].check1,
                                    check2 : response.data[i].check2,
                                    feliix: response.data[i].feliix,
                                },
                            });
                        }
                    }

                    initial();
                })
                .catch(function (response) {
                    //handle error
                    //alert(JSON.stringify(response));
                });
        },

        getInitial: function(id) {

            this.getInitMain(id);

            // var token = localStorage.getItem("token");
            // var form_Data = new FormData();
            // let _this = this;
            // this.action = 1; //select all
            // form_Data.append("jwt", token);
            // form_Data.append("action", this.action);

            // axios({
            //         method: "post",
            //         headers: {
            //             "Content-Type": "multipart/form-data",
            //         },
            //         url: "api/work_calender_detail",
            //         data: form_Data,
            //     })
            //     .then(function (response) {
            //         //handle success
            //         _this.agenda = response.data;

            //         _this.getInitMain();
            //     })
            //     .catch(function (response) {
            //         //handle error
            //         //alert(JSON.stringify(response));
            //         console.log(response);
            //     });

        },

        async setStages(pid) {
            let _this = this;
            let token = localStorage.getItem('accessToken');

            var stages = [];

            if (pid)
            {
                let res = await axios.get('api/project02_stages', { headers: { "Authorization": `Bearer ${token}` }, params: { pid: pid } });
                    stages = res.data;
            }

            // clear select sc_related_stage_id
            var select = document.getElementById("sc_related_stage_id");
            var length = select.options.length;
            for (i = length-1; i >= 0; i--) {
                select.options[i] = null;
            }

            // dynamic add option to select sc_related_stage_id
            for (var i = 0; i < stages.length; i++) {
                var opt = stages[i];
                var el = document.createElement("option");
                el.textContent = opt.sequence + " : " + opt.stage;
                el.value = opt.id;
                el.selected = (opt.id == _this.sc_related_stage_id);
                select.appendChild(el);
            }

            console.log("setStages");

            },


        getDetail: function () {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            this.action = 1; //select all
            form_Data.append("jwt", token);
            form_Data.append("action", this.action);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_detail",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    _this.agenda = response.data;
                })
                .catch(function (response) {
                    //handle error
                    //alert(JSON.stringify(response));
                    console.log(response);
                });
        },

        updateLock: function (vlock) {
            this.action = 8; //update lock status
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("id", _this.id);
            form_Data.append("lock", vlock);
            form_Data.append("action", _this.action);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    console.log(details);
                    console.log(response);
                    //console.log(response.data[0]);

                    //handle success
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },

        getAccess: async function() {
            var token = localStorage.getItem('token');
            var form_Data = new FormData();

            let res = await axios.get('api/access_control_kind_get', { headers: { "Authorization": `Bearer ${token}` }, params: { kind: 'schedule_confirm' } });
            this.schedule_confirm = res.data.schedule_confirm;
          },

        updateConfirm: function (vlock) {
            this.action = 9; //update lock status
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("id", _this.id);
            form_Data.append("confirm", vlock);
            form_Data.append("action", _this.action);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    //console.log(details);
                    console.log(response);
                    //console.log(response.data[0]);

                    //handle success
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },

        export: function () {
            var form_Data = new FormData();

            const filename = "attendance";
            let _this = this;

            const token = sessionStorage.getItem("token");

            axios({
                    method: "get",
                    url: "schedule_data_word?id=" + app.id,
                    data: form_Data,
                    responseType: "blob", // important
                })
                .then(function (response) {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;

                    link.setAttribute("download", "schedule." + _this.download_type);

                    document.body.appendChild(link);
                    link.click();
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },

        updateMain2: function (details, sc_content, files, time) {
            this.action = 33; //update
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("id", _this.id);
            form_Data.append("title", sc_content.Title);
            form_Data.append("all_day", sc_content.Allday);
            form_Data.append("start_time", sc_content.Starttime);
            form_Data.append("end_time", sc_content.Endtime);
            form_Data.append("color", sc_content.Color);

            if(document.getElementById("sc_color_other").checked)
                form_Data.append("color_other", sc_content.Color_Other);
            else
                form_Data.append("color_other", "");

            if(sc_content.Color == "" && !document.getElementById("sc_color_other").checked)
            {
                Swal.fire({
                    text: JSON.stringify("Please choose color for schedule."),
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }

            form_Data.append("text_color", "white");
            form_Data.append("project", sc_content.Project);
            form_Data.append("sales_executive", sc_content.Sales_Executive);
            form_Data.append("project_in_charge", sc_content.Project_in_charge);
            form_Data.append("related_project_id", sc_content.Related_project_id);
            form_Data.append("related_stage_id", sc_content.Related_stage_id);
            form_Data.append("project_relevant", sc_content.Project_relevant);
            form_Data.append("installer_needed", sc_content.Installer_needed);
            form_Data.append("installer_needed_other", sc_content.Installer_needed_other);
            form_Data.append(
                "installer_needed_location",
                sc_content.Location_Products_to_Bring
            );
            form_Data.append("things_to_bring", sc_content.Things_to_Bring);
            form_Data.append(
                "things_to_bring_location",
                sc_content.Location_Things_to_Bring
            );
            form_Data.append("products_to_bring", sc_content.Products_to_Bring);
            if (_this.filename != "") {
                var file_str = "";
                for (var i = 0; i < _this.filename.length; i++) {
                    let file = _this.filename[i];
                    file_str += file + ","
                }
                var file_elements = document.getElementsByName("file_elements")
                for(let i = 0;i < file_elements.length; i++)
                {
                    if(file_elements[i].checked)
                        file_str += file_elements[i].value + ","
                }
                form_Data.append("products_to_bring_files", file_str.slice(0, -1));
            } else {
                var file_str = "";
            
                var file_elements = document.getElementsByName("file_elements")
                for(let i = 0;i < file_elements.length; i++)
                {
                    if(file_elements[i].checked)
                        file_str += file_elements[i].value + ","
                }

                form_Data.append("products_to_bring_files", file_str.slice(0, -1));
            }
            form_Data.append("service", sc_content.Service);
            form_Data.append("driver", sc_content.Driver);

            if(sc_content.Driver != 6)
                sc_content.Driver_Other = "";

            form_Data.append("driver_other", sc_content.Driver_Other);
            form_Data.append("back_up_driver", sc_content.Back_up_Driver);
            
            if(sc_content.Back_up_Driver != 6)
                sc_content.Back_up_Driver_Other = "";

            form_Data.append("back_up_driver_other", sc_content.Back_up_Driver_Other);

            form_Data.append("photoshoot_request", sc_content.Photoshoot_Request);
            form_Data.append("notes", sc_content.Notes);
            form_Data.append("is_enabled", sc_content.is_enabled);
            form_Data.append("action", this.action);
            form_Data.append("updated_by", _this.name);
            form_Data.append("today", this.file_day);

            form_Data.append("detail_list", JSON.stringify(details));

            for (var i = 0; i < this.fileArray.length; i++) {
                let file = this.fileArray[i];
                form_Data.append("files[" + i + "]", file);
            }

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    console.log(details);
                    console.log(response);
                   // console.log(response.data[0]);

                    //_this.updateDetail(_this.id, details, sc_content.Date);
                    //_this.deleteDetail(_this.id);
                    //_this.addDetails(_this.id,details,main.Date);
                    //handle success

                    // update ui
                    document.getElementById("sc_product_files").innerHTML = files;
                    document.getElementById("sc_editor").value = app.name + " at " + time;
                    document.getElementById("last_editor").style.display = "block";
                    
                    if (sc_content.Allday) {
                        eventObj.setStart(sc_content.Date + "T00:00");
                        eventObj.setEnd(sc_content.Date + "T00:00");
                        eventObj.setAllDay(sc_content.Allday);
                        eventObj.setProp("title", sc_content.Title);
                        eventObj.setProp("borderColor", sc_content.Color);
                        eventObj.setProp("backgroundColor", sc_content.Color);
                        eventObj.setExtendedProp("description", sc_content);
                
                    } else {
                        if (
                            sc_content.Starttime != "" &&
                            sc_content.Endtime != "" &&
                            sc_content.Endtime >= sc_content.Starttime
                        ) {
                            eventObj.setAllDay(sc_content.Allday);
                            eventObj.setStart(sc_content.Date + "T" + sc_content.Starttime);
                            eventObj.setEnd(sc_content.Date + "T" + sc_content.Endtime);
                            eventObj.setProp("title", sc_content.Title);
                            eventObj.setProp("borderColor", sc_content.Color);
                            eventObj.setProp("backgroundColor", sc_content.Color);
                            eventObj.setExtendedProp("description", sc_content);
                        } else {
                            sc_content.Allday = eventObj.extendedProps.description.Allday;
                            sc_content.Starttime = eventObj.extendedProps.description.Starttime;
                            sc_content.Endtime = eventObj.extendedProps.description.Endtime;
                
                            if (eventObj.extendedProps.description.Allday) {
                                eventObj.setStart(sc_content.Date + "T00:00");
                                eventObj.setEnd(sc_content.Date + "T00:00");
                                eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                                eventObj.setProp("title", sc_content.Title);
                                eventObj.setProp("borderColor", sc_content.Color);
                                eventObj.setProp("backgroundColor", sc_content.Color);
                                eventObj.setExtendedProp("description", sc_content);
                            } else {
                                eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                                eventObj.setStart(
                                    sc_content.Date + "T" + eventObj.extendedProps.description.Starttime
                                );
                                eventObj.setEnd(
                                    sc_content.Date + "T" + eventObj.extendedProps.description.Endtime
                                );
                                eventObj.setProp("title", sc_content.Title);
                                eventObj.setProp("borderColor", sc_content.Color);
                                eventObj.setProp("backgroundColor", sc_content.Color);
                                eventObj.setExtendedProp("description", sc_content);
                            }
                
                            document.getElementById("sc_time").checked =
                                eventObj.extendedProps.description.Allday;
                            document.getElementById("sc_stime").value =
                                eventObj.extendedProps.description.Starttime;
                            document.getElementById("sc_etime").value =
                                eventObj.extendedProps.description.Endtime;
                        }
                    }

                    // reset ui
                    document.getElementById("sc_tb_location").value = "";
                    document.getElementById("sc_tb_agenda").value = "";
                    document.getElementById("sc_tb_appointtime").value = "";
                    document.getElementById("sc_tb_endtime").value = "";

                    Change_Schedule_State(true, eventObj.extendedProps.description.Allday);
                    icon_function_enable = false;

                    document.getElementById("upload_input").style.display = "none";
                    document.getElementById("btn_reset").style.display = "none";
                    document.getElementById("btn_add").style.display = "none";
                    document.getElementById("btn_duplicate").style.display = "inline";
                    document.getElementById("btn_export").style.display = "inline";
                    document.getElementById("btn_edit").style.display = "inline";
                    document.getElementById("btn_delete").style.display = "inline";
                    document.getElementById("btn_cancel").style.display = "none";
                    document.getElementById("btn_save").style.display = "none";

                    if (document.getElementById("lock").value == "Y") {
                        document.getElementById("btn_lock").style.display = "none";
                        document.getElementById("btn_unlock").style.display = "inline";
                    } else {
                        document.getElementById("btn_lock").style.display = "inline";
                        document.getElementById("btn_unlock").style.display = "none";
                    }

                    if (document.getElementById("confirm").value == "Y") {
                        document.getElementById("btn_confirm").style.display = "none";
                        document.getElementById("btn_unconfirm").style.display = "inline";
                    } else {
                        document.getElementById("btn_confirm").style.display = "inline";
                        document.getElementById("btn_unconfirm").style.display = "none";
                    }

                    app.filename = [];
                    app.fileArray = [];

                    document.getElementById("fileload").value = "";

                    reload();
                })
                .catch(function (error) {
                    //handle error
                    Swal.fire({
                        text: error.data,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                });
        },

        updateMain: function (details, main) {
            this.action = 3; //update
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;
            form_Data.append("jwt", token);
            form_Data.append("id", _this.id);
            form_Data.append("title", main.Title);
            form_Data.append("all_day", main.Allday);
            form_Data.append("start_time", main.Starttime);
            form_Data.append("end_time", main.Endtime);
            form_Data.append("color", main.Color);

            if(document.getElementById("sc_color_other").checked)
                form_Data.append("color_other", main.Color_Other);
            else
                form_Data.append("color_other", "");

            form_Data.append("text_color", "white");
            form_Data.append("project", main.Project);
            form_Data.append("sales_executive", main.Sales_Executive);
            form_Data.append("project_in_charge", main.Project_in_charge);
            form_Data.append("project_relevant", main.Project_relevant);
            form_Data.append("related_project_id", main.Related_project_id);
            form_Data.append("related_stage_id", main.Related_stage_id);
            form_Data.append("installer_needed", main.Installer_needed);
            form_Data.append("installer_needed_other", main.Installer_needed_other);
            form_Data.append(
                "installer_needed_location",
                main.Location_Products_to_Bring
            );
            form_Data.append("things_to_bring", main.Things_to_Bring);
            form_Data.append(
                "things_to_bring_location",
                main.Location_Things_to_Bring
            );
            form_Data.append("products_to_bring", main.Products_to_Bring);
            if (_this.filename != "") {
                form_Data.append("products_to_bring_files", _this.filename);
            } else {
                form_Data.append("products_to_bring_files", main.File_name);
            }
            form_Data.append("service", main.Service);
            form_Data.append("driver", main.Driver);

            if(main.Driver != 6)
                main.Driver_Other = "";
            
            form_Data.append("driver_other", main.Driver_Other);
            form_Data.append("back_up_driver", main.Back_up_Driver);
            form_Data.append("photoshoot_request", main.Photoshoot_Request);
            form_Data.append("notes", main.Notes);
            form_Data.append("is_enabled", main.is_enabled);
            form_Data.append("action", this.action);
            form_Data.append("updated_by", _this.name);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    console.log(details);
                    console.log(response);
                    //console.log(response.data[0]);

                    _this.updateDetail(_this.id, details, main.Date);
                    //_this.deleteDetail(_this.id);
                    //_this.addDetails(_this.id,details,main.Date);
                    //handle success
                    app.filename = [];
                    app.fileArray = [];

                    document.getElementById("fileload").value = "";
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                });
        },

        updateDetail: function (mainId, addDetails, date) {
            this.action = 7; //delete
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);
            form_Data.append("main_id", mainId);
            form_Data.append("action", _this.action);
            form_Data.append("deleted_by", _this.name);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_detail",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    _this.addDetails(mainId, addDetails, date);
                })
                .catch(function (response) {
                    //handle error
                });
        },

        deleteMe: function(id) {
            let _this = this;

            
        },

        // updateDetail: function(){},
        deleteMain: function (id) {
            this.action = 7; //delete
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);
            form_Data.append("id", id);
            form_Data.append("action", _this.action);
            form_Data.append("deleted_by", _this.name);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_main",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    //_this.items = response.data
                    eventObj.remove();
                    $("#exampleModalScrollable").modal("toggle");
                })
                .catch(function (response) {
                    //handle error
                });
        },
        deleteDetail: function (mainId) {
            this.action = 7; //delete
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);
            form_Data.append("main_id", mainId);
            form_Data.append("action", _this.action);
            form_Data.append("deleted_by", _this.name);
            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/work_calender_detail",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                })
                .catch(function (response) {
                    //handle error
                });
        },
        getUserName: function () {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);

            axios({
                    method: "post",
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                    url: "api/on_duty_get_myname",
                    data: form_Data,
                })
                .then(function (response) {
                    //handle success
                    _this.name = response.data.username.trim();
                    _this.is_manager = response.data.is_manager;
                    _this.manager_leave = response.data.manager_leave;
                    _this.al_credit = response.data.annual_leave;
                    _this.sl_credit = response.data.sick_leave;
                    _this.is_viewer = response.data.is_viewer;
                    _this.department = response.data.department;
                    _this.title = response.data.title;
                })
                .catch(function (response) {
                    //handle error
                    Swal.fire({
                        text: JSON.stringify(response),
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                });
        },
        getMonthDay: function () {
            let _this = this;
            var today = new Date();
            var first = new Date();
            var dd = ("0" + today.getDate()).slice(-2);
            var mm = ("0" + (today.getMonth() + 1)).slice(-2);
            var yyyy = today.getFullYear();
            today = yyyy + "-" + mm + "-" + dd;
            first = yyyy + "-" + mm + "-01";
            _this.file_day = yyyy + mm + dd;
            _this.start_date = first;
            _this.end_date = today;
        },
        upload: function () {
            var myArr = this.fileArray;
            var vm = this;
            console.log(myArr);
            myArr.forEach((element, index) => {
                var config = {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                };
                var data = myArr[index];
                var myForm = new FormData();
                myForm.append("file", data);
                myForm.append("batch_type", "proof");
                myForm.append("batch_id", 0);
                myForm.append("today", vm.file_day);

                axios
                    .post("api/work_calender_gcp", myForm, config)
                    .then(function (res) {
                        if (res.data.code == 0) {
                            myArr[index].progress = 1;
                            vm.$set(vm.fileArray, index, myArr[index]);
                            console.log(vm.fileArray, index);
                        } else {
                            alert(JSON.stringify(res.data));
                        }
                    })
                    .catch(function (err) {
                        console.log(err);
                    });
            });
        },
    },
});

var eventObj;
var icon_function_enable = true;

var initial = async (_id) =>  {
    var calendarEl = document.getElementById("calendar");

    calendar = new FullCalendar.Calendar(calendarEl, {
        titleFormat: {
            // will produce something like "Tuesday, September 18, 2018"
            month: "2-digit",
            year: "numeric",
            day: "2-digit",
        },

        headerToolbar: {
            left: "prev,next addEventButton",
            center: "title",
            right: "dayGridMonth,timeGridWeek",
        },
        events: app.items,
        //Add Schedule被點擊的方法
        customButtons: {
            addEventButton: {
                text: "Add Schedule",
                click: async function () {
                    document.getElementById("myLargeModalLabel_service").innerText =
                        "Add Schedule";
                    // document.getElementById("last_editor").style.display = "none";
                    // document.getElementById("btn_reset").style.display = "inline";
                    // document.getElementById("btn_add").style.display = "inline";
                    // document.getElementById("btn_duplicate").style.display = "none";
                    // document.getElementById("btn_export").style.display = "none";
                    // document.getElementById("btn_edit").style.display = "none";
                    // document.getElementById("btn_delete").style.display = "none";
                    // document.getElementById("btn_cancel").style.display = "none";
                    // document.getElementById("btn_save").style.display = "none";

                    // document.getElementById("btn_lock").style.display = "none";
                    // document.getElementById("btn_unlock").style.display = "none";

                    // document.getElementById("btn_confirm").style.display = "none";
                    // document.getElementById("btn_unconfirm").style.display = "none";

                    // document.getElementById("sc_product_files").innerHTML = "";
                    // document.getElementById("upload_input").style =
                    //     "display: flex; align-items: center; margin-top:1%;";

                    resetSchedule();

                    service.clean_service();

                    // if(project.add_project_id != 0 && project.add_stage_id != 0)
                    // {
                    //     project.project_id = project.add_project_id;
                    //     project.stage_id = project.add_stage_id;
                    //     await project.getStages(project.project_id);
                    // }
                    // else
                    // {
                    //     $('#sc_related_project_id').val(0);
                    //     $('#sc_related_stage_id').val(0);
                    // }
                    

                    Change_Schedule_State(false, true);
                    icon_function_enable = true;

                    $("#serviceModalScrollable").modal("toggle");
                },
            },
        },

        //Schedule被點擊的方法
        eventClick: async function (info) {

            service.sid = 0;
            service_feliix.sid = 0;
            
            document.getElementById("myLargeModalLabel_service").innerText =
                "Schedule Details";
            eventObj = info.event;

            if(eventObj.extendedProps.description.feliix == '')
            {
                service.clear_service();
    
                service.id = eventObj.id;
                var sc_content = eventObj.extendedProps.description;
    
                service.service_click(sc_content);
    
                $("#serviceModalScrollable").modal("toggle");
            }

            if(eventObj.extendedProps.description.feliix == '1')
            {
                document.getElementById("myLargeModalLabel").innerText =
                "Schedule Details";
                eventObj = info.event;
                resetSchedule();
                app.id = eventObj.id;
                var sc_content = eventObj.extendedProps.description;

                document.getElementById("sc_title").value = sc_content.Title;
                document.getElementById("sc_color").value = sc_content.Color;

                if(sc_content.Color_Other != "")
                {
                    document.getElementById("sc_color").value = sc_content.Color_Other;
                    document.getElementById("sc_color_other").checked = true;
                }
                else
                {
                    document.getElementById("sc_color").value = "#000000";
                    document.getElementById("sc_color_other").checked = false;
                }

                if(sc_content.Color != "")
                {
                    var checked = 0;
                    var colors = document.getElementsByName("sc_color");

                    for(var i = 0; i < colors.length; i++)
                    {
                        if(colors[i].value == sc_content.Color)
                        {
                            checked = 1;
                            colors[i].checked = true;
                        }
                    }

                    if(checked == 0 && sc_content.Color_Other == "")
                    {
                        document.getElementById("sc_color").value = sc_content.Color;
                        document.getElementById("sc_color_other").checked = true;
                    }
                }

                

                //設定最後編輯者資訊
                document.getElementById("sc_editor").value = sc_content.Lasteditor;
                document.getElementById("last_editor").style.display = "inline";

                document.getElementById("sc_date").value = sc_content.Date;
                document.getElementById("sc_time").checked = sc_content.Allday;
                document.getElementById("sc_stime").value = sc_content.Starttime;
                document.getElementById("sc_etime").value = sc_content.Endtime;
                document.getElementById("sc_project").value = sc_content.Project;
                document.getElementById("sc_sales").value = sc_content.Sales_Executive;
                document.getElementById("sc_incharge").value =
                    sc_content.Project_in_charge;
                document.getElementById("sc_relevant").value =
                    sc_content.Project_relevant;
                app.attendee = (sc_content.Project_relevant.split(",") === "" ? app.attendee = [] : sc_content.Project_relevant.split(","));
                if(sc_content.Project_relevant === "")
                    app.attendee = [];

                project.project_id = sc_content.Related_project_id;
                project.stage_id = sc_content.Related_stage_id;
    
                await project.getStages(project.project_id);
                    
                document.getElementById("sc_Installer_needed_other").value = sc_content.Installer_needed_other;

                app.users = app.users_org.concat(app.attendee);

                app.users = app.users.filter((item,index)=>{
                    return (app.users.indexOf(item) == index)
                })

                app.users.sort(function (a, b) {
                    return a.toLowerCase().localeCompare(b.toLowerCase());
                });
                
                var installer = sc_content.Installer_needed.split(",");
                var elements = document.getElementsByName("sc_Installer_needed");

                for (var i = 0; i < elements.length; i++)
                    elements[i].checked = false;

                for (var i = 0; i < installer.length; i++) {
                    for (var j = 0; j < elements.length; j++) {
                        if (elements[j].value == installer[i]) {
                            elements[j].checked = true;
                        }
                    }
                }

                // for (i = 0; i < installer.length; i++) {
                //     if (installer[i] == "AS")
                //         document.getElementsByName("sc_Installer_needed")[0].checked = true;

                //     if (installer[i] == "RM")
                //         document.getElementsByName("sc_Installer_needed")[1].checked = true;

                //     if (installer[i] == "RS")
                //         document.getElementsByName("sc_Installer_needed")[2].checked = true;

                //     if (installer[i] == "CJ")
                //         document.getElementsByName("sc_Installer_needed")[3].checked = true;

                //     if (installer[i] == "JO")
                //         document.getElementsByName("sc_Installer_needed")[4].checked = true;

                //     if (installer[i] == "EO")
                //         document.getElementsByName("sc_Installer_needed")[5].checked = true;

                //     if (installer[i] == "JM")
                //         document.getElementsByName("sc_Installer_needed")[6].checked = true;
                // }

                //加入Agenda內容(先刪除未儲存的)
                var agenda_object = document
                    .getElementById("agenda_table")
                    .getElementsByTagName("tr");

                for (i = agenda_object.length - 1; i > 1; i--) {
                    agenda_object[i].remove();
                }

                for (i = 0; i < sc_content.Agenda.length; i++) {
                    addAgendaitem(
                        sc_content.Agenda[i].location,
                        sc_content.Agenda[i].agenda,
                        sc_content.Agenda[i].appointtime,
                        sc_content.Agenda[i].endtime
                    );
                }

                document.getElementById("sc_location1").value =
                    sc_content.Location_Things_to_Bring;
                document.getElementById("sc_things").value = sc_content.Things_to_Bring;
                document.getElementById("sc_location2").value =
                    sc_content.Location_Products_to_Bring;
                document.getElementById("sc_products").value =
                    sc_content.Products_to_Bring;
                document.getElementById("upload_input").style = "display:none;";
                document.getElementById("sc_product_files").innerHTML =
                    sc_content.Products_to_bring_files;
                if (
                    sc_content.Products_to_bring_files !=
                    "<div class='custom-control custom-checkbox' style='padding-top: 1%;'></div>"
                )
                    app.download_type = "zip";
                else app.download_type = "docx";

                document.getElementById("sc_product_files_hide").value =
                    sc_content.File_name;
                document.getElementById("sc_service").value = sc_content.Service;
                document.getElementById("sc_driver1").value = sc_content.Driver;

                document.getElementById("sc_driver_other").value = sc_content.Driver_Other;

                document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;

                document.getElementById("sc_backup_driver_other").value = sc_content.Back_up_Driver_Other;

                if(sc_content.Driver != 6)
                    document.getElementById("sc_driver_other").style.display = "none";
                else
                    document.getElementById("sc_driver_other").style.display = "";

                if(sc_content.Back_up_Driver != 6)
                    document.getElementById("sc_backup_driver_other").style.display = "none";
                else
                    document.getElementById("sc_backup_driver_other").style.display = "";

                if (sc_content.Photoshoot_Request == "Yes") {
                    document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
                }
                if (sc_content.Photoshoot_Request == "No") {
                    document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
                }

                document.getElementById("sc_notes").value = sc_content.Notes;

                Change_Schedule_State(true, sc_content.Allday);
                icon_function_enable = false;

                // document.getElementById("btn_reset").style.display = "none";
                // document.getElementById("btn_add").style.display = "none";
                // document.getElementById("btn_duplicate").style.display = "inline";
                document.getElementById("btn_export").style.display = "inline";
                // document.getElementById("btn_edit").style.display = "inline";
                // document.getElementById("btn_delete").style.display = "inline";
                // document.getElementById("btn_cancel").style.display = "none";
                // document.getElementById("btn_save").style.display = "none";

                // // add schedual lock
                // document.getElementById("lock").value = sc_content.Lock;
                // if (sc_content.Lock != "") {
                //     document.getElementById("btn_lock").style.display = "none";
                //     document.getElementById("btn_unlock").style.display = "inline";

                //     document.getElementById("btn_edit").style.display = "none";
                //     document.getElementById("btn_delete").style.display = "none";
                // } else {
                //     document.getElementById("btn_lock").style.display = "inline";
                //     document.getElementById("btn_unlock").style.display = "none";
                // }

                service_feliix.clear_service();
    
                service_feliix.id = eventObj.id;
                var sc_content = eventObj.extendedProps.description;
    
                service_feliix.service_click(sc_content);

                $("#exampleModalScrollable").modal("toggle");
            }
        },

        // eventRender: function(info) {
    
        //     var i = document.createElement('i');
        //         if ( info.event.extendedProps.icon != '') {   
        //                 i.className = info.event.extendedProps.icon;
        //                 info.el.append(i);
        //                 }
        // },

        //載入日曆初始化時，如果 Schedule 的 confirmed 為True，則加上一個checkbox圖示在 日曆上的 Schedule 前方。
        eventDidMount: function (arg) {

            if( arg.event.extendedProps.description.icon != '' ){

                let icon = document.createElement("i");
                icon.classList.add('fa', arg.event.extendedProps.description.icon);

                if( arg.el.querySelector(".fc-event-title-container") ){
                    arg.el.querySelector(".fc-event-title").prepend(icon);
                }
                else {
                    arg.el.prepend(icon);
                }
            }

            // var i = document.createElement('i');
            //     if ( arg.event.extendedProps.description.icon != '') {   
            //             i.className = arg.event.extendedProps.description.icon;
            //             arg.el.append(i);
            //             }
        },

        //eventDrop: function (info) {
        //    eventObj = info.event;
        //
        //    alert(info.event.title + " has changed Date.\n\nDatabase should be updated too.");
        //
        //
        //},

        editable: false,
    });
    calendar.render();
    //clearTimeOut();

    if (app.name === "guest") {
        document.getElementsByClassName(
            "fc-addEventButton-button"
        )[0].style.visibility = "hidden";

        // document.getElementById("add_message").style.visibility = "hidden";

        // document.getElementById("btn_duplicate").style.visibility = "hidden";
        // // document.getElementById("btn_export").style.visibility="hidden";
        // document.getElementById("btn_delete").style.visibility = "hidden";
        // document.getElementById("btn_edit").style.visibility = "hidden";
    }

    // if (
    //     app.name != "Dennis Lin" &&
    //     app.name != "dereck" &&
    //     app.name != "Glendon Wendell Co" &&
    //     app.name != "Mary Jude Jeng Articulo" &&
    //     app.name != "Stefanie Mika C. Santos" &&
    //     app.name != "Edneil Fernandez" &&
    //     app.name != "Aiza Eisma" &&
    //     app.name != "Kristel Tan"
    // ) {
    //     document.getElementById("btn_lock").style.visibility = "hidden";
    //     document.getElementById("btn_unlock").style.visibility = "hidden";
    // }

    // if (
    //     app.schedule_confirm != true 
    // ) {
    //     document.getElementById("btn_confirm").style.visibility = "hidden";
    //     document.getElementById("btn_unconfirm").style.visibility = "hidden";
    // }

    

    if(app.sid != 0)
    {
        info = [];
        info = shallowCopy(app.items.find(element => element.id == app.sid));

        eventObj = {
            id: app.sid,
            title: info.title,
            Date: info.Date,
            start: info.start,
            end: info.end,
            color: info.color,
            allDay: info.allDay,
            extendedProps: {
                description : info.description,
            },
        }

        
        document.getElementById("myLargeModalLabel").innerText =
                "Schedule Details";
          
            resetSchedule();
            app.id = app.sid;
            var sc_content = info.description;

            document.getElementById("sc_title").value = sc_content.Title;
            document.getElementById("sc_color").value = sc_content.Color;

            if(sc_content.Color_Other != "")
            {
                document.getElementById("sc_color").value = sc_content.Color_Other;
                document.getElementById("sc_color_other").checked = true;
            }
            else
            {
                document.getElementById("sc_color").value = "#000000";
                document.getElementById("sc_color_other").checked = false;
            }

            if(sc_content.Color != "")
            {
                var checked = 0;
                var colors = document.getElementsByName("sc_color");

                for(var i = 0; i < colors.length; i++)
                {
                    if(colors[i].value == sc_content.Color)
                    {
                        checked = 1;
                        colors[i].checked = true;
                    }
                }

                if(checked == 0 && sc_content.Color_Other == "")
                {
                    document.getElementById("sc_color").value = sc_content.Color;
                    document.getElementById("sc_color_other").checked = true;
                }
            }

            

            //設定最後編輯者資訊
            document.getElementById("sc_editor").value = sc_content.Lasteditor;
            document.getElementById("last_editor").style.display = "inline";

            document.getElementById("sc_date").value = sc_content.Date;
            document.getElementById("sc_time").checked = sc_content.Allday;
            document.getElementById("sc_stime").value = sc_content.Starttime;
            document.getElementById("sc_etime").value = sc_content.Endtime;
            document.getElementById("sc_project").value = sc_content.Project;
            document.getElementById("sc_sales").value = sc_content.Sales_Executive;
            document.getElementById("sc_incharge").value =
                sc_content.Project_in_charge;

            project.project_id = sc_content.Related_project_id;
            project.stage_id = sc_content.Related_stage_id;

            await project.getStages(project.project_id);

            document.getElementById("sc_relevant").value =
                sc_content.Project_relevant;
            app.attendee = (sc_content.Project_relevant.split(",") === "" ? app.attendee = [] : sc_content.Project_relevant.split(","));
            if(sc_content.Project_relevant === "")
                app.attendee = [];
                
            document.getElementById("sc_Installer_needed_other").value = sc_content.Installer_needed_other;

            app.users = app.users_org.concat(app.attendee);

            app.users = app.users.filter((item,index)=>{
                return (app.users.indexOf(item) == index)
             })

            app.users.sort(function (a, b) {
                return a.toLowerCase().localeCompare(b.toLowerCase());
            });
            
            var installer = sc_content.Installer_needed.split(",");

            var elements = document.getElementsByName("sc_Installer_needed");

            for (var i = 0; i < elements.length; i++)
                elements[i].checked = false;

            for (var i = 0; i < installer.length; i++) {
                for (var j = 0; j < elements.length; j++) {
                    if (elements[j].value == installer[i]) {
                        elements[j].checked = true;
                    }
                }
            }
            
            /*

            for (i = 0; i < 5; i++) {
                document.getElementsByName("sc_Installer_needed")[i].checked = false;
            }

            for (i = 0; i < installer.length; i++) {
                if (installer[i] == "AS")
                    document.getElementsByName("sc_Installer_needed")[0].checked = true;

                if (installer[i] == "RM")
                    document.getElementsByName("sc_Installer_needed")[1].checked = true;

                if (installer[i] == "RS")
                    document.getElementsByName("sc_Installer_needed")[2].checked = true;

                if (installer[i] == "CJ")
                    document.getElementsByName("sc_Installer_needed")[3].checked = true;

                if (installer[i] == "JO")
                    document.getElementsByName("sc_Installer_needed")[4].checked = true;

                if (installer[i] == "EO")
                    document.getElementsByName("sc_Installer_needed")[5].checked = true;

                if (installer[i] == "JM")
                    document.getElementsByName("sc_Installer_needed")[6].checked = true;
            }
            */

            //加入Agenda內容(先刪除未儲存的)
            var agenda_object = document
                .getElementById("agenda_table")
                .getElementsByTagName("tr");

            for (i = agenda_object.length - 1; i > 1; i--) {
                agenda_object[i].remove();
            }

            for (i = 0; i < sc_content.Agenda.length; i++) {
                addAgendaitem(
                    sc_content.Agenda[i].location,
                    sc_content.Agenda[i].agenda,
                    sc_content.Agenda[i].appointtime,
                    sc_content.Agenda[i].endtime
                );
            }

            document.getElementById("sc_location1").value =
                sc_content.Location_Things_to_Bring;
            document.getElementById("sc_things").value = sc_content.Things_to_Bring;
            document.getElementById("sc_location2").value =
                sc_content.Location_Products_to_Bring;
            document.getElementById("sc_products").value =
                sc_content.Products_to_Bring;
            document.getElementById("upload_input").style = "display:none;";
            document.getElementById("sc_product_files").innerHTML =
                sc_content.Products_to_bring_files;
            if (
                sc_content.Products_to_bring_files !=
                "<div class='custom-control custom-checkbox' style='padding-top: 1%;'></div>"
            )
                app.download_type = "zip";
            else app.download_type = "docx";

            document.getElementById("sc_product_files_hide").value =
                sc_content.File_name;
            document.getElementById("sc_service").value = sc_content.Service;
            document.getElementById("sc_driver1").value = sc_content.Driver;

            document.getElementById("sc_driver_other").value = sc_content.Driver_Other;

            document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;

            document.getElementById("sc_backup_driver_other").value = sc_content.Back_up_Driver_Other;

            if(sc_content.Driver != 6)
                document.getElementById("sc_driver_other").style.display = "none";
            else
                document.getElementById("sc_driver_other").style.display = "";

            if(sc_content.Back_up_Driver != 6)
                document.getElementById("sc_backup_driver_other").style.display = "none";
            else
                document.getElementById("sc_backup_driver_other").style.display = "";

            if (sc_content.Photoshoot_Request == "Yes") {
                document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
            }
            if (sc_content.Photoshoot_Request == "No") {
                document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
            }

            document.getElementById("sc_notes").value = sc_content.Notes;

            Change_Schedule_State(true, sc_content.Allday);
            icon_function_enable = false;

            // document.getElementById("btn_reset").style.display = "none";
            // document.getElementById("btn_add").style.display = "none";
            // document.getElementById("btn_duplicate").style.display = "none";
            document.getElementById("btn_export").style.display = "none";
            // document.getElementById("btn_edit").style.display = "none";
            // document.getElementById("btn_delete").style.display = "none";
            // document.getElementById("btn_cancel").style.display = "none";
            // document.getElementById("btn_save").style.display = "none";

            // // add schedual lock
            // document.getElementById("lock").value = sc_content.Lock;
            // if (sc_content.Lock != "") {
            //     document.getElementById("btn_lock").style.display = "none";
            //     document.getElementById("btn_unlock").style.display = "none";

            //     document.getElementById("btn_edit").style.display = "none";
            //     document.getElementById("btn_delete").style.display = "none";
            // } else {
            //     document.getElementById("btn_lock").style.display = "none";
            //     document.getElementById("btn_unlock").style.display = "none";
            // }

            // document.getElementById("confirm").value = sc_content.Confirm;
            // if (sc_content.Confirm != "") {
            //     document.getElementById("btn_confirm").style.display = "none";
            //     document.getElementById("btn_unconfirm").style.display = "none";
            // } else {
            //     document.getElementById("btn_confirm").style.display = "none";
            //     document.getElementById("btn_unconfirm").style.display = "none";
            // }

            $("#exampleModalScrollable").modal("toggle");
    }

    
}

function onChangeFileUpload(e) {
    app.filename = [];
    app.fileArray = [];
    for (i = 0; i < e.target.files.length; i++) {
        const image = e.target.files[i];
        app.filename.push(app.file_day + "_" + e.target.files[i].name);
        app.fileArray.push(image);
    }
}

// $("button[id='btn_add']").click(function () {
//     var selected = [];
//     $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
//         selected.push($(this).val());
//     });

//     var agenda_object = document
//         .getElementById("agenda_table")
//         .getElementsByTagName("tr");
//     var agenda_content = [];
//     var appointtime = "";
//     var endtime = "";
//     for (i = 2; i < agenda_object.length; i++) {
//         appointtime = "";
//         endtime = "";
//         if (agenda_object[i].getElementsByTagName("input")[2].value != "") {
//             appointtime =
//                 document.getElementById("sc_date").value +
//                 " " +
//                 agenda_object[i].getElementsByTagName("input")[2].value;
//         }
//         if (agenda_object[i].getElementsByTagName("input")[3].value != "") {
//             endtime =
//                 document.getElementById("sc_date").value +
//                 " " +
//                 agenda_object[i].getElementsByTagName("input")[3].value;
//         }
//         agenda_content.push({
//             location: agenda_object[i].getElementsByTagName("input")[0].value,
//             agenda: agenda_object[i].getElementsByTagName("input")[1].value,
//             appointtime: appointtime,
//             endtime: endtime,
//             sort: i,
//         });
//     }

//     related_project_id = $('#sc_related_project_id').val()
//     related_stage_id = $('#sc_related_stage_id').val()

//     if(related_project_id == undefined || related_project_id == null || related_project_id == "") 
//         related_project_id = 0;

//     if(related_stage_id == undefined || related_stage_id == null || related_stage_id == "")
//         related_stage_id = 0;

//     var sc_content = {
//         Date: document.getElementById("sc_date").value,
//         Title: document.getElementById("sc_project").value,
//         Color: document.getElementById("sc_color").value,
//         Color_Other: document.getElementById("sc_color").value,
//         Allday: document.getElementById("sc_time").checked,

//         Starttime: document.getElementById("sc_date").value +
//             " " +
//             document.getElementById("sc_stime").value,
//         Endtime: document.getElementById("sc_date").value +
//             " " +
//             document.getElementById("sc_etime").value,
//         Project: document.getElementById("sc_project").value,
//         Sales_Executive: document.getElementById("sc_sales").value,
//         Project_in_charge: document.getElementById("sc_incharge").value,
//         Project_relevant: Object.keys(app.attendee).map(function(k){return app.attendee[k]}).join(","),
//         Agenda: agenda_content,
//         Installer_needed: selected.join(),
//         Installer_needed_other: document.getElementById("sc_Installer_needed_other").value,
//         Location_Things_to_Bring: document.getElementById("sc_location1").value,
//         Things_to_Bring: document.getElementById("sc_things").value,
//         Location_Products_to_Bring: document.getElementById("sc_location2").value,
//         Products_to_Bring: document.getElementById("sc_products").value,
//         Service: document.getElementById("sc_service").value,
//         Driver: document.getElementById("sc_driver1").value,
//         Driver_Other: document.getElementById("sc_driver_other").value,
//         Back_up_Driver: document.getElementById("sc_driver2").value,
//         Back_up_Driver_Other: document.getElementById("sc_backup_driver_other").value,
//         Photoshoot_Request: $("input[name=sc_Photoshoot_request]:checked").val(),
//         Notes: document.getElementById("sc_notes").value,
//         Lock: "",
//         Confirm:"",
//         is_enable: true,

//         Related_project_id : related_project_id,
//         Related_stage_id : related_stage_id,
//     };

//     if (sc_content.Allday) {
//         sc_content.Starttime =
//             document.getElementById("sc_date").value + " 00:00:00";
//         sc_content.Endtime = document.getElementById("sc_date").value + " 23:59:59";
//     }

//     app.addMain2(agenda_content, sc_content, 1, calendar);

// });

// $("button[id='btn_close']").click(function () {
//     resetSchedule();
// });

// $("button[id='btn_reset']").click(function () {
//     resetSchedule();
// });

function resetSchedule() {
    document.getElementById("sc_date").value = "";
    document.getElementById("sc_time").checked = true;
    document.getElementById("sc_stime").value = "";
    document.getElementById("sc_stime").disabled = true;
    document.getElementById("sc_etime").value = "";
    document.getElementById("sc_etime").disabled = true;
    document.getElementById("sc_title").value = "";
    document.getElementById("sc_color").value = "#000000";
    document.getElementById("sc_project").value = "";
    document.getElementById("sc_sales").value = "";
    document.getElementById("sc_incharge").value = "";
    document.getElementById("sc_relevant").value = "";

    document.getElementById("sc_tb_location").value = "";
    document.getElementById("sc_tb_agenda").value = "";
    document.getElementById("sc_tb_appointtime").value = "";
    document.getElementById("sc_tb_endtime").value = "";

    // set selected to false
    // $("input[id='sc_related_project_id']").each(function (i, v) {
    //     $(v).prop("selected", false);
    // });
    // $("input[id='sc_related_project_id']").each(function (i, v) {
    //     $(v).prop("selected", false);
    // });
    // $(".sc_related_project_id").val(0);
    // $(".sc_related_stage_id").val(0);


    $("input[name='sc_Installer_needed']").each(function (i, v) {
        $(v).prop("checked", false);
    });

    document.getElementById("sc_Installer_needed_other").value = "";

    document.getElementById("sc_driver_other").value = "";
    document.getElementById("sc_driver_other").style.display = "none";

    document.getElementById("sc_backup_driver_other").value = "";
    document.getElementById("sc_backup_driver_other").style.display = "none";

    document.getElementById("sc_location1").value = "";
    document.getElementById("sc_things").value = "";
    document.getElementById("sc_location2").value = "";
    document.getElementById("sc_products").value = "";
    document.getElementById("sc_service").value = "0";
    document.getElementById("sc_driver1").value = "0";
    document.getElementById("sc_driver2").value = "0";

    $("input[name='sc_Photoshoot_request']").each(function (i, v) {
        $(v).prop("checked", false);
    });

    document.getElementById("sc_notes").value = "";

    var agenda_object = document
        .getElementById("agenda_table")
        .getElementsByTagName("tr");

    for (i = agenda_object.length - 1; i > 1; i--) {
        agenda_object[i].remove();
    }

    var colors = document.getElementsByName("sc_color");
    for (var i = 0; i < colors.length; i++)
    {
        color = colors[i].checked = false;
    }

    app.attendee = [];
    app.users = app.users_org;
}

function Change_Schedule_State(status, time_status) {
    document.getElementById("sc_title").disabled = status;
    document.getElementById("sc_color").disabled = status;
    document.getElementById("sc_date").disabled = status;
    document.getElementById("sc_project").disabled = status;
    document.getElementById("sc_sales").disabled = status;
    document.getElementById("sc_incharge").disabled = status;
    document.getElementById("sc_relevant").disabled = status;

    document.getElementById("sc_related_project_id").disabled = status;
    document.getElementById("sc_related_stage_id").disabled = status;

    if (status == false) {
        $("#sc_relevant").removeClass("select_disabled");
    }

    if(status == true) {
        $("#sc_relevant").addClass("select_disabled");
    }

    if (status == false) {
        if (time_status == true) {
            document.getElementById("sc_time").disabled = false;
            document.getElementById("sc_stime").disabled = true;
            document.getElementById("sc_etime").disabled = true;
        } else {
            document.getElementById("sc_time").disabled = false;
            document.getElementById("sc_stime").disabled = false;
            document.getElementById("sc_etime").disabled = false;
        }
    } else {
        document.getElementById("sc_time").disabled = status;
        document.getElementById("sc_stime").disabled = status;
        document.getElementById("sc_etime").disabled = status;
    }

    document.getElementById("sc_tb_location").disabled = status;
    document.getElementById("sc_tb_agenda").disabled = status;
    document.getElementById("sc_tb_appointtime").disabled = status;
    document.getElementById("sc_tb_endtime").disabled = status;

    document.getElementById("add_agenda").disabled = status;

    document.getElementById("sc_color_orange").disabled = status;
    document.getElementById("sc_color_red").disabled = status;
    document.getElementById("sc_color_purple").disabled = status;
    document.getElementById("sc_color_green").disabled = status;
    document.getElementById("sc_color_blue").disabled = status;
    document.getElementById("sc_color_teal").disabled = status;
    document.getElementById("sc_color_other").disabled = status;

    document.getElementById("sc_color").disabled = status;

    if(status && document.getElementById("sc_color_other").checked)
        document.getElementById("sc_color").disabled = true;

    /*
    document.getElementsByName("sc_Installer_needed")[0].disabled = status;
    document.getElementsByName("sc_Installer_needed")[1].disabled = status;
    document.getElementsByName("sc_Installer_needed")[2].disabled = status;
    document.getElementsByName("sc_Installer_needed")[3].disabled = status;
    document.getElementsByName("sc_Installer_needed")[4].disabled = status;
    */
    var elements = document.getElementsByName("sc_Installer_needed");

    for (var i = 0; i < elements.length; i++)
        elements[i].disabled = status;

    document.getElementById("sc_Installer_needed_other").disabled = status;

    document.getElementById("sc_location1").disabled = status;
    document.getElementById("sc_things").disabled = status;
    document.getElementById("sc_location2").disabled = status;
    document.getElementById("sc_products").disabled = status;
    document.getElementById("sc_service").disabled = status;
    document.getElementById("sc_driver1").disabled = status;
    document.getElementById("sc_driver2").disabled = status;

    document.getElementById("sc_driver_other").disabled = status;
    document.getElementById("sc_backup_driver_other").disabled = status;

    document.getElementsByName("sc_Photoshoot_request")[0].disabled = status;
    document.getElementsByName("sc_Photoshoot_request")[1].disabled = status;

    document.getElementById("sc_notes").disabled = status;

    var file_elements = document.getElementsByName("file_elements")
    for(let i = 0;i < file_elements.length; i++)
    {
        file_elements[i].disabled = status;
    }
}

$(document).on("click", "#btn_edit", function () {
    //調整最下方出現按鈕
    document.getElementById("btn_reset").style.display = "none";
    document.getElementById("btn_add").style.display = "none";
    document.getElementById("btn_duplicate").style.display = "inline";
    document.getElementById("btn_export").style.display = "inline";
    document.getElementById("btn_edit").style.display = "none";
    document.getElementById("btn_delete").style.display = "none";
    document.getElementById("btn_cancel").style.display = "inline";
    document.getElementById("btn_save").style.display = "inline";

    document.getElementById("btn_lock").style.display = "none";
    document.getElementById("btn_unlock").style.display = "none";

    document.getElementById("btn_confirm").style.display = "none";
    document.getElementById("btn_unconfirm").style.display = "none";

    document.getElementById("last_editor").style.display = "none";

    document.getElementById("upload_input").style =
        "display: flex; align-items: center; margin-top:1%;";
    //切換元件成為可修改狀態
    Change_Schedule_State(false, eventObj.extendedProps.description.Allday);
    icon_function_enable = true;
});

$(document).on("click", "#btn_delete", function () {
    app.deleteMe(eventObj.id);
    
});

$(document).on('click', '.SwalBtn1', function() {
    //Some code 1
    console.log('Button 1');
    service.export_service_full('');
    swal.clickConfirm();
});
$(document).on('click', '.SwalBtn2', function() {
    //Some code 2 
    console.log('Button 2');
    service.export_service_full('1');
    swal.clickConfirm();
});
$(document).on('click', '.SwalBtn3', function() {
    //Some code 2 
    console.log('Button 3');
   
    swal.clickConfirm();
});

$(document).on('click', '.SwalBtn1_feliix', function() {
    //Some code 1
    console.log('Button 1');
    service_feliix.export_service_full('');
    swal.clickConfirm();
});
$(document).on('click', '.SwalBtn2_feliix', function() {
    //Some code 2 
    console.log('Button 2');
    service_feliix.export_service_full('1');
    swal.clickConfirm();
});
$(document).on('click', '.SwalBtn3_feliix', function() {
    //Some code 2 
    console.log('Button 3');
   
    swal.clickConfirm();
});

$(document).on("click", "#btn_duplicate", function () {
    var sc_content = eventObj.extendedProps.description;
    sc_content.Lock = "";
    sc_content.Confirm = "";

    console.log(sc_content);
    if (sc_content.Allday) {
        sc_content.Starttime = sc_content.Date + " 00:00:00";
        sc_content.Endtime = sc_content.Date + " 23:59:59";
    } else {
        if (
            sc_content.Starttime != "" 
        ) {
            sc_content.Starttime = sc_content.Date + " " + sc_content.Starttime;
      
        }

        if (
            sc_content.Endtime != "" 
        ) {
            sc_content.Endtime = sc_content.Date + " " + sc_content.Endtime;
        }
    }
    app.addMain2(sc_content.Agenda, sc_content, 2, calendar);

    //$("#exampleModalScrollable").modal("toggle");

    //resetSchedule();
});

// $(document).on("click", "#btn_export", function () {
//     app.export();
// });

$(document).on("click", "#btn_export", function () {
    let _this = this;

    let buttons = "Which do you want to export?" +
    "<br>" +
    '<button type="button" role="button" tabindex="0" class="SwalBtn1_feliix customSwalBtn">' + 'Only “Content of Request”' + '</button>' +
    '<button type="button" role="button" tabindex="0" class="SwalBtn2_feliix customSwalBtn">' + '”Request Review” and “Content of Request”' + '</button>' + 
    '<button type="button" role="button" tabindex="0" class="SwalBtn3_feliix customSwalBtn">' + 'Cancel' + '</button>';

    if(service_feliix.status == "0" || service_feliix.status == "1")
    {
        // buttons = "Which do you want to export?" +
        // "<br>" +
        // '<button type="button" role="button" tabindex="0" class="SwalBtn1_feliix customSwalBtn">' + 'Only “Content of Request”' + '</button>' +
        // '<button type="button" role="button" tabindex="0" class="SwalBtn3_feliix customSwalBtn">' + 'Cancel' + '</button>';
        service_feliix.export_service_full('');
        return;
    }

    Swal.fire({
        title: "Export",
        icon: "warning",
        html: buttons,
        showCancelButton: false,
        showConfirmButton: false
        })
});

$(document).on("click", "#btn_cancel", async function () {
    var sc_content = eventObj.extendedProps.description;

    document.getElementById("sc_title").value = sc_content.Title;
    document.getElementById("sc_color").value = sc_content.Color;

    //設定最後編輯者資訊
    document.getElementById("sc_editor").value = sc_content.Lasteditor;
    document.getElementById("last_editor").style.display = "inline";

    document.getElementById("sc_date").value = sc_content.Date;
    document.getElementById("sc_time").checked = sc_content.Allday;
    document.getElementById("sc_stime").value = sc_content.Starttime;
    document.getElementById("sc_etime").value = sc_content.Endtime;
    document.getElementById("sc_project").value = sc_content.Project;
    document.getElementById("sc_sales").value = sc_content.Sales_Executive;
    document.getElementById("sc_incharge").value = sc_content.Project_in_charge;


    project.project_id = sc_content.Related_project_id;
    project.project_name = sc_content.Related_stage_id;
    await project.getStages(sc_content.Related_project_id);

    // $("#sc_related_project_id").val(sc_content.Related_project_id);

    // await app.setStages(sc_content.Related_project_id);
    // $("#sc_related_stage_id").val(sc_content.Related_stage_id);
    

    document.getElementById("sc_relevant").value = sc_content.Project_relevant;
    app.attendee = (sc_content.Project_relevant.split(",") === "" ? app.attendee = [] : sc_content.Project_relevant.split(","));
    if(sc_content.Project_relevant === "")
                app.attendee = [];

    document.getElementById("sc_tb_location").value = "";
    document.getElementById("sc_tb_agenda").value = "";
    document.getElementById("sc_tb_appointtime").value = "";
    document.getElementById("sc_tb_endtime").value = "";

    var installer = sc_content.Installer_needed.split(",");

    var elements = document.getElementsByName("sc_Installer_needed");

    for (var i = 0; i < elements.length; i++)
        elements[i].checked = false;

    for (var i = 0; i < installer.length; i++) {
        for (var j = 0; j < elements.length; j++) {
            if (elements[j].value == installer[i]) {
                elements[j].checked = true;
            }
        }
    }

        /*
    for (i = 0; i < 5; i++) {
        document.getElementsByName("sc_Installer_needed")[i].checked = false;
    }

    for (i = 0; i < installer.length; i++) {
        if (installer[i] == "EO")
            document.getElementsByName("sc_Installer_needed")[0].checked = true;

        if (installer[i] == "JM")
            document.getElementsByName("sc_Installer_needed")[1].checked = true;

        if (installer[i] == "JC")
            document.getElementsByName("sc_Installer_needed")[2].checked = true;

        if (installer[i] == "GV")
            document.getElementsByName("sc_Installer_needed")[3].checked = true;

        if (installer[i] == "JS")
            document.getElementsByName("sc_Installer_needed")[4].checked = true;

    } */

    document.getElementsByName("sc_Installer_needed_other").value = sc_content.Installer_needed_other;

    //加入Agenda內容(先刪除未儲存的)
    var agenda_object = document
        .getElementById("agenda_table")
        .getElementsByTagName("tr");

    for (i = agenda_object.length - 1; i > 1; i--) {
        agenda_object[i].remove();
    }

    for (i = 0; i < sc_content.Agenda.length; i++) {
        addAgendaitem(
            sc_content.Agenda[i].location,
            sc_content.Agenda[i].agenda,
            sc_content.Agenda[i].appointtime,
            sc_content.Agenda[i].endtime
        );
    }

    document.getElementById("sc_location1").value =
        sc_content.Location_Things_to_Bring;
    document.getElementById("sc_things").value = sc_content.Things_to_Bring;
    document.getElementById("sc_location2").value =
        sc_content.Location_Products_to_Bring;
    document.getElementById("sc_products").value = sc_content.Products_to_Bring;
    document.getElementById("sc_service").value = sc_content.Service;
    document.getElementById("sc_driver1").value = sc_content.Driver;
    document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;

    document.getElementById("sc_driver_other").value = sc_content.Driver_Other;

    document.getElementById("sc_backup_driver_other").value = sc_content.Back_up_Driver_Other;

    if (sc_content.Photoshoot_Request == "Yes") {
        document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
    }
    if (sc_content.Photoshoot_Request == "No") {
        document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
    }

    document.getElementById("sc_notes").value = sc_content.Notes;

    Change_Schedule_State(true, sc_content.Allday);
    icon_function_enable = false;

    document.getElementById("upload_input").style.display = "none";
    document.getElementById("btn_reset").style.display = "none";
    document.getElementById("btn_add").style.display = "none";
    document.getElementById("btn_duplicate").style.display = "inline";
    document.getElementById("btn_export").style.display = "inline";
    document.getElementById("btn_edit").style.display = "inline";
    document.getElementById("btn_delete").style.display = "inline";
    document.getElementById("btn_cancel").style.display = "none";
    document.getElementById("btn_save").style.display = "none";

    if (document.getElementById("lock").value == "Y") {
        document.getElementById("btn_lock").style.display = "none";
        document.getElementById("btn_unlock").style.display = "inline";
    } else {
        document.getElementById("btn_lock").style.display = "inline";
        document.getElementById("btn_unlock").style.display = "none";
    }

    if (document.getElementById("confirm").value == "Y") {
        document.getElementById("btn_confirm").style.display = "none";
        document.getElementById("btn_unconfirm").style.display = "inline";
    } else {
        document.getElementById("btn_confirm").style.display = "inline";
        document.getElementById("btn_unconfirm").style.display = "none";
    }

    var file_elements = document.getElementsByName("file_elements")
    for(let i = 0;i < file_elements.length; i++)
    {
        file_elements[i].checked = true;
    }
});

$(document).on("click", "#btn_lock", async function () {
    document.getElementById("lock").value = "Y";
    await app.updateLock("Y");

    var sc_content = eventObj.extendedProps.description;
    sc_content.Lock = "Y";

    document.getElementById("btn_lock").style.display = "none";
    document.getElementById("btn_unlock").style.display = "inline";

    document.getElementById("btn_edit").style.display = "none";
    document.getElementById("btn_delete").style.display = "none";
});

$(document).on("click", "#btn_unlock", async function () {
    document.getElementById("lock").value = "";
    await app.updateLock("");

    var sc_content = eventObj.extendedProps.description;
    sc_content.Lock = "";

    document.getElementById("btn_lock").style.display = "inline";
    document.getElementById("btn_unlock").style.display = "none";

    document.getElementById("btn_edit").style.display = "inline";
    document.getElementById("btn_delete").style.display = "inline";
});

$(document).on("click", "#btn_confirm", async function () {
    document.getElementById("confirm").value = "Y";
    await app.updateConfirm("Y");

    var sc_content = eventObj.extendedProps.description;
    sc_content.confirm = "Y";

    document.getElementById("btn_confirm").style.display = "none";
    document.getElementById("btn_unconfirm").style.display = "inline";

    reload();
});

$(document).on("click", "#btn_unconfirm", async function () {
    document.getElementById("confirm").value = "";
    await app.updateConfirm("");

    var sc_content = eventObj.extendedProps.description;
    sc_content.confirm = "";

    document.getElementById("btn_confirm").style.display = "inline";
    document.getElementById("btn_unconfirm").style.display = "none";

    reload();

});

// $(document).on("click", "#btn_save", function () {
//     var selected = [];
//     $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
//         selected.push($(this).val());
//     });

//     var files = "";
//     if (app.filename != "") {
//         app.filename.forEach((element) => {
//             var file_str =
//                 "<input type='checkbox' class='custom-control-input' id='" + element + "' checked name='file_elements' value='" + element + "'>" + 
//                 "<label class='custom-control-label' style='justify-content: flex-start;' for='" + element + "'>" +
//                 "<a href='https://storage.cloud.google.com/calendarfile/" +
//                 element +
//                 "' target='_blank'>" +
//                 element +
//                 "</a></label>";
//             // files += file_str;
//             if(element.trim() !== '')
//             {
//                 files += "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + file_str + "</div>";
//             }
//         });

//         var file_elements = document.getElementsByName("file_elements")
//         for(let i = 0;i < file_elements.length; i++)
//         {
//             if(file_elements[i].checked)
//             {
//                 var file_str =
//                 "<input type='checkbox' class='custom-control-input' id='" + file_elements[i].value + "' checked name='file_elements' value='" + file_elements[i].value + "'>" + 
//                 "<label class='custom-control-label' style='justify-content: flex-start;' for='" + file_elements[i].value + "'>" +
//                 "<a href='https://storage.cloud.google.com/calendarfile/" +
//                 file_elements[i].value +
//                 "' target='_blank'>" +
//                 file_elements[i].value +
//                 "</a></label>";
//                 // files += file_str;
//                 if(file_elements[i].value.trim() !== '')
//                 {
//                     files += "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + file_str + "</div>";
//                 }
//             }
                
//         }

//     } else if (document.getElementById("sc_product_files_hide").value != "") {
//         var file_elements = document.getElementsByName("file_elements")
//         for(let i = 0;i < file_elements.length; i++)
//         {
//             if(file_elements[i].checked)
//             {
//                 var file_str =
//                 "<input type='checkbox' class='custom-control-input' id='" + file_elements[i].value + "' checked name='file_elements' value='" + file_elements[i].value + "'>" + 
//                 "<label class='custom-control-label' style='justify-content: flex-start;' for='" + file_elements[i].value + "'>" +
//                 "<a href='https://storage.cloud.google.com/calendarfile/" +
//                 file_elements[i].value +
//                 "' target='_blank'>" +
//                 file_elements[i].value +
//                 "</a></label>";
//                 // files += file_str;
//                 if(file_elements[i].value.trim() !== '')
//                 {
//                     files += "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + file_str + "</div>";
//                 }
//             }
                
//         }
//     }

//     files = "<div class='custom-control custom-checkbox' style='padding-top: 1%;'>" + files + "</div>";

//     var time = new Date();
//     time =
//         time.getFullYear() +
//         "-" +
//         time.getMonth() +
//         "-" +
//         time.getDate() +
//         " " +
//         time.getHours() +
//         ":" +
//         time.getMinutes() +
//         ":" +
//         time.getSeconds();

    

//     var agenda_object = document
//         .getElementById("agenda_table")
//         .getElementsByTagName("tr");
//     var agenda_content = [];
//     var appointtime = "";
//     var endtime = "";
//     for (i = 2; i < agenda_object.length; i++) {
//         appointtime = "";
//         endtime = "";
//         if (
//             agenda_object[i].getElementsByTagName("input")[2].value != "" &&
//             agenda_object[i].getElementsByTagName("input")[2].value != null
//         ) {
//             appointtime =
//                 document.getElementById("sc_date").value +
//                 " " +
//                 agenda_object[i].getElementsByTagName("input")[2].value;
//         }
//         if (
//             agenda_object[i].getElementsByTagName("input")[3].value != "" &&
//             agenda_object[i].getElementsByTagName("input")[3].value != null
//         ) {
//             endtime =
//                 document.getElementById("sc_date").value +
//                 " " +
//                 agenda_object[i].getElementsByTagName("input")[3].value;
//         }
//         agenda_content.push({
//             location: agenda_object[i].getElementsByTagName("input")[0].value,
//             agenda: agenda_object[i].getElementsByTagName("input")[1].value,
//             appointtime: appointtime,
//             endtime: endtime,
//             sort: i,
//         });
//     }

//     var Color_Other = "";
//     var Color = "";
//     if(document.getElementById("sc_color_other").checked)
//         Color_Other = document.getElementById("sc_color").value;
//     else
//         Color_Other = "";

//     var colors = document.getElementsByName("sc_color");
//     for(var i=0; i<colors.length; i++)
//     {
//         if(colors[i].checked)
//             Color = colors[i].value;
//     }

//     var sc_content = {
//         Date: document.getElementById("sc_date").value,
//         Title: document.getElementById("sc_project").value,
//         Color: Color,
//         Color_Other: Color_Other,
//         Allday: document.getElementById("sc_time").checked,
//         Starttime: document.getElementById("sc_date").value +
//             " " +
//             document.getElementById("sc_stime").value,
//         Endtime: document.getElementById("sc_date").value +
//             " " +
//             document.getElementById("sc_etime").value,
//         Project: document.getElementById("sc_project").value,
//         Sales_Executive: document.getElementById("sc_sales").value,
//         Project_in_charge: document.getElementById("sc_incharge").value,
//         Project_relevant: Object.keys(app.attendee).map(function(k){return app.attendee[k]}).join(","),
//         Agenda: agenda_content,
//         Installer_needed: selected.join(),
//         Installer_needed_other: document.getElementById("sc_Installer_needed_other").value,
//         Location_Things_to_Bring: document.getElementById("sc_location1").value,
//         Things_to_Bring: document.getElementById("sc_things").value,
//         Location_Products_to_Bring: document.getElementById("sc_location2").value,
//         Products_to_Bring: document.getElementById("sc_products").value,
//         File_name: document.getElementById("sc_product_files_hide").value,
//         Service: document.getElementById("sc_service").value,
//         Driver: document.getElementById("sc_driver1").value,
//         Driver_Other: document.getElementById("sc_driver_other").value,
//         Back_up_Driver: document.getElementById("sc_driver2").value,
//         Back_up_Driver_Other: document.getElementById("sc_backup_driver_other").value,
//         Photoshoot_Request: $("input[name=sc_Photoshoot_request]:checked").val(),
//         Notes: document.getElementById("sc_notes").value,
//         Lock: document.getElementById("lock").value,
//         Confirm: document.getElementById("confirm").value,

//         Related_project_id: $("#sc_related_project_id").val(),
//         Related_stage_id: $("#sc_related_stage_id").val(),
//     };

//     if (sc_content.Allday) {
//          sc_content.Starttime =
//             document.getElementById("sc_date").value + " 00:00:00";
//         sc_content.Endtime = document.getElementById("sc_date").value + " 23:59:59";
//     } else {
//         if (
//             sc_content.Starttime != "" &&
//             sc_content.Endtime != "" &&
//             sc_content.Endtime >= sc_content.Starttime
//         ) {
//             console.log('valid1');
//         } else {
//             sc_content.Allday = eventObj.extendedProps.description.Allday;
//             sc_content.Starttime = eventObj.extendedProps.description.Starttime;
//             sc_content.Endtime = eventObj.extendedProps.description.Endtime;

//             if (eventObj.extendedProps.description.Allday) {
//                 } else {
//                     console.log('valid2');
//             }
//         }
//     }

//     app.updateMain2(agenda_content, sc_content, files, time);
// });

$("input[id='sc_time']").change(function () {
    if (this.checked) {
        document.getElementById("sc_stime").disabled = true;
        document.getElementById("sc_etime").disabled = true;
        document.getElementById("sc_stime").value = "";
        document.getElementById("sc_etime").value = "";
    } else {
        document.getElementById("sc_stime").disabled = false;
        document.getElementById("sc_etime").disabled = false;
    }
});

$("input[id='photoshoot_yes']").change(function () {
    if (this.checked) {
        if(!app.attendee.includes('Gian Miguel Osorio'))
            app.attendee.push('Gian Miguel Osorio')
        if(!app.attendee.includes('Ronnel B. Balmeo'))
            app.attendee.push('Ronnel B. Balmeo')
    } 
});

$("input[id='photoshoot_no']").change(function () {
    if (this.checked) {
        var index = app.attendee.indexOf('Gian Miguel Osorio');
        if (index > -1) {
            app.attendee.splice(index, 1); // 2nd parameter means remove one item only
        }
        
        var index = app.attendee.indexOf('Ronnel B. Balmeo');
        if (index > -1) {
            app.attendee.splice(index, 1); // 2nd parameter means remove one item only
        }
    }
});

var addevent = document.getElementById("add_message");
var addinput = document.getElementsByClassName("add__input")[0];
var messageboard = document.getElementsByClassName("messageboard")[0];
var controlModify = true;

function autogrow(textarea) {
    var adjustedHeight = textarea.clientHeight;

    adjustedHeight = Math.max(textarea.scrollHeight, adjustedHeight);

    if (adjustedHeight > textarea.clientHeight) {
        textarea.style.height = adjustedHeight + 2 + "px";
    }
}

var addagenda = document.getElementById("add_agenda");

addagenda.onclick = function () {
    if (icon_function_enable) {
        addAgendaitem(
            document.getElementById("sc_tb_location").value.trim(),
            document.getElementById("sc_tb_agenda").value.trim(),
            document.getElementById("sc_tb_appointtime").value,
            document.getElementById("sc_tb_endtime").value
        );
        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";
    }
};

var controlEdit = true;

function addAgendaitem(location, agenda, appointtime, endtime) {
    var agendaItem = document.createElement("tr");
    var td_1 = document.createElement("td");
    var td_2 = document.createElement("td");
    var td_3 = document.createElement("td");
    var td_4 = document.createElement("td");
    var td_5 = document.createElement("td");
    var input_1 = document.createElement("input");
    var div_input_1 = document.createElement("div");
    var input_2 = document.createElement("input");
    var div_input_2 = document.createElement("div");
    var input_3 = document.createElement("input");
    var input_4 = document.createElement("input");
    var Icon_1 = document.createElement("i");
    var Icon_2 = document.createElement("i");
    var Icon_3 = document.createElement("i");
    var Icon_4 = document.createElement("i");

    //添加class
    td_1.className = "table__item";
    td_2.className = "table__item";
    td_3.className = "table__item";
    td_4.className = "table__item";
    td_5.className = "table__item";

    input_1.type = "text";
    input_2.type = "text";
    input_3.type = "time";
    input_4.type = "time";
    input_1.className = "form-control";
    div_input_1.className = "agenda__text";
    input_2.className = "form-control";
    div_input_2.className = "agenda__text";
    input_3.className = "form-control";
    input_4.className = "form-control";
    input_1.disabled = true;
    input_2.disabled = true;
    input_3.disabled = true;
    input_4.disabled = true;
    input_1.style.cssText = "border: none; background-color: white; display:none";
    div_input_1.style.cssText = "border: none; background-color: white;";
    input_2.style.cssText = "border: none; background-color: white; display:none";
    div_input_2.style.cssText = "border: none; background-color: white;";
    input_3.style.cssText = "border: none; background-color: white;";
    input_4.style.cssText = "border: none; background-color: white;";
    input_1.value = location;
    div_input_1.innerHTML = location;
    input_2.value = agenda;
    div_input_2.innerHTML = agenda;
    input_3.value = appointtime;
    input_4.value = endtime;

    Icon_1.className = "fas fa-arrow-alt-circle-up";
    Icon_2.className = "fas fa-arrow-alt-circle-down";
    Icon_3.className = "fas fa-edit";
    Icon_4.className = "fas fa-trash-alt";
    Icon_2.style.cssText = "margin-left: 2%;";
    Icon_3.style.cssText = "margin-left: 2%;";
    Icon_4.style.cssText = "margin-left: 2%;";

    //添加元素，形成父子關係
    td_1.appendChild(input_1);
    td_1.appendChild(div_input_1);
    td_2.appendChild(input_2);
    td_2.appendChild(div_input_2);
    td_3.appendChild(input_3);
    td_4.appendChild(input_4);
    td_5.appendChild(Icon_1);
    td_5.appendChild(Icon_2);
    td_5.appendChild(Icon_3);
    td_5.appendChild(Icon_4);
    agendaItem.appendChild(td_1);
    agendaItem.appendChild(td_2);
    agendaItem.appendChild(td_3);
    agendaItem.appendChild(td_4);
    agendaItem.appendChild(td_5);

    //添加到頁面上
    document.getElementById("agenda_table").appendChild(agendaItem);

    //增加刪除方法
    Icon_4.onclick = function () {
        if (icon_function_enable) {
            agendaItem.parentNode.removeChild(agendaItem);
        }
    };

    //增加修改方法
    Icon_3.onclick = function (ev) {
        if (icon_function_enable) {
            if (controlEdit == true) {
                controlEdit = false;

                input_1.disabled = false;
                div_input_1.disabled = false;

                input_1.style.cssText = "border: none; background-color: white;";
                div_input_1.style.cssText = "border: none; background-color: white; display:none;";

                input_2.disabled = false;
                div_input_2.disabled = false;

                input_2.style.cssText = "border: none; background-color: white;";
                div_input_2.style.cssText = "border: none; background-color: white; display:none;";

                input_3.disabled = false;
                input_4.disabled = false;
                input_1.focus();

                ev = window.event || ev;
                ev.stopPropagation ? ev.stopPropagation() : (ev.cancelBubble = true);

                document.onclick = function (event) {
                    if (
                        event.target != input_1 &&
                        event.target != input_2 &&
                        event.target != input_3 &&
                        event.target != input_4
                    ) {
                        input_1.disabled = true;
                        div_input_1.disabled = true;
                        input_2.disabled = true;
                        div_input_2.disabled = true;
                        input_3.disabled = true;
                        input_4.disabled = true;

                        input_1.style.cssText = "border: none; background-color: white; display:none;";
                        div_input_1.innerHTML = input_1.value;
                        div_input_1.style.cssText = "border: none; background-color: white;";

                        input_2.style.cssText = "border: none; background-color: white; display:none;";
                        div_input_2.innerHTML = input_2.value;
                        div_input_2.style.cssText = "border: none; background-color: white;";

                        controlEdit = true;
                    }
                };
            }
        }
    };

    //增加上移方法
    Icon_1.onclick = function () {
        if (icon_function_enable) {
            if (agendaItem.rowIndex != 2) {
                var node1 = agendaItem;
                var node2 = agendaItem.previousElementSibling;

                var _parent = node1.parentNode;
                //獲取兩個結點的相對位置
                var _t1 = node1.nextSibling;
                var _t2 = node2.nextSibling;

                //將node2插入到原來node1的位置
                if (_t1) _parent.insertBefore(node2, _t1);
                else _parent.appendChild(node2);
                //將node1插入到原來node2的位置
                if (_t2) _parent.insertBefore(node1, _t2);
                else _parent.appendChild(node1);
            }
        }
    };

    //增加下移方法
    Icon_2.onclick = function () {
        if (icon_function_enable) {
            var node1 = agendaItem;
            var node2 = agendaItem.nextElementSibling;

            var _parent = node1.parentNode;
            //獲取兩個結點的相對位置
            var _t1 = node1.nextSibling;
            var _t2 = node2.nextSibling;

            //將node2插入到原來node1的位置
            if (_t1) _parent.insertBefore(node2, _t1);
            else _parent.appendChild(node2);
            //將node1插入到原來node2的位置
            if (_t2) _parent.insertBefore(node1, _t2);
            else _parent.appendChild(node1);
        }
    };
}

var timeOut1;
var timeOut2;
var timeOut3;

function reload(id) {
    //timeOut3 = setTimeout(function () {
    //    app.getDetail();
    //}, 500);
    //timeOut1 = setTimeout(function () {
    //    app.getMain();
    //}, 1000);
    //timeOut2 = setTimeout(function () {
    //    initial();
    //}, 1500);

    app.getInitial(id);
}

function clearTimeOut() {
    //clearTimeout(timeOut1);
    //clearTimeout(timeOut2);
    //clearTimeout(timeOut3);
}

function UnescapeHTML(a) {
    a = "" + a;
    return a
        .replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">")
        .replace(/&amp;/g, "&")
        .replace(/&quot;/g, '"')
        .replace(/&apos;/g, "'");
}


function shallowCopy(obj) {
      var result = {};
      for (var i in obj) {
          result[i] = obj[i];
      }
      return result;
}

$(document).ready(function () {
    // get today's date
    var begin = new Date();
    var today = new Date();
    // get previous 6 months's date
    var sixMonthsAgo = new Date(today.setMonth(today.getMonth() - 6));
    var netmonth = new Date(begin.getFullYear(), begin.getMonth()+2, 0)

    let _id = "";
    // let uri = window.location.href.split("?");
    //   if (uri.length >= 2) {
    //     let vars = uri[1].split("&");

    //     let tmp = "";
    //     vars.forEach(async function(v) {
    //       tmp = v.split("=");
    //       if (tmp.length == 2) {
    //         switch (tmp[0]) {
    //           case "id":
    //             _id = tmp[1];
    //             break;
    //           default:
    //             console.log(`Too many args`);
    //         }
    //       }
    //     });
    //   }
 
    $('#sdate').val(sixMonthsAgo.toISOString().slice(0,7));
    $('#edate').val(netmonth.toISOString().slice(0,7));

    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    reload(_id);
});


// var msg = new Vue({
//     el: "#msg",
//     data: {
//         txt: "",
//         messages: [],
//         id: 0,
//         edit: 0,
//         user: "",
//     },
//     created() {
//         this.getUser();
//         this.getMessages();
//     },
//     methods: {
//         edit_msg: function (index, message) {
//             if (this.edit == index) {
//                 this.updateMessage(index, message);
//                 this.edit = -1;
//             } else {
//                 this.edit = index;
//             }
//         },
//         getMessages: function () {
//             var token = localStorage.getItem("token");
//             var form_Data = new FormData();
//             let _this = this;
//             _this.clear();
//             this.action = 1; //select all
//             form_Data.append("jwt", token);
//             form_Data.append("action", this.action);

//             axios({
//                     method: "post",
//                     headers: {
//                         "Content-Type": "multipart/form-data",
//                     },
//                     url: "api/work_calender_message",
//                     data: form_Data,
//                 })
//                 .then(function (response) {
//                     //handle success
//                     _this.messages = response.data;
//                 })
//                 .catch(function (response) {
//                     //handle error
//                     //alert(JSON.stringify(response));
//                     console.log(response);
//                 });
//         },
//         addMessages: function (message) {
//             this.action = 2; //add
//             var token = localStorage.getItem("token");
//             var form_Data = new FormData();
//             let _this = this;
//             _this.clear();
//             form_Data.append("jwt", token);
//             form_Data.append("message", message);
//             form_Data.append("is_enabled", true);
//             form_Data.append("action", this.action);
//             form_Data.append("created_by", _this.user);
//             axios({
//                     method: "post",
//                     headers: {
//                         "Content-Type": "multipart/form-data",
//                     },
//                     url: "api/work_calender_message",
//                     data: form_Data,
//                 })
//                 .then(function (response) {
//                     //_this.messages.push({id: response.data[0], message: message, is_enabled: "1", created_by: _this.user, created_at: "now" });
//                     _this.txt = "";
//                     //this.addDetails(response.data[0]);
//                     //handle success
//                     //_this.items = response.data
//                     //console.log(_this.items)
//                 })
//                 .catch(function (response) {
//                     //handle error
//                 });
//             _this.reloadMessages();
//         },
//         deleteMessages: function (id) {
//             this.action = 7; //delete
//             var token = localStorage.getItem("token");
//             var form_Data = new FormData();
//             let _this = this;
//             _this.clear();

//             form_Data.append("jwt", token);
//             form_Data.append("id", id);
//             form_Data.append("action", _this.action);
//             form_Data.append("deleted_by", _this.user);
//             axios({
//                     method: "post",
//                     headers: {
//                         "Content-Type": "multipart/form-data",
//                     },
//                     url: "api/work_calender_message",
//                     data: form_Data,
//                 })
//                 .then(function (response) {
//                     //handle success
//                     //_this.items = response.data
//                 })
//                 .catch(function (response) {
//                     //handle error
//                 });
//             _this.reloadMessages();
//         },
//         updateMessage: function (id, message) {
//             this.action = 3; //update
//             var token = localStorage.getItem("token");
//             var form_Data = new FormData();
//             let _this = this;
//             _this.clear();
//             form_Data.append("jwt", token);
//             form_Data.append("id", id);
//             form_Data.append("is_enabled", true);
//             form_Data.append("message", message);
//             form_Data.append("action", this.action);
//             form_Data.append("updated_by", _this.user);
//             axios({
//                     method: "post",
//                     headers: {
//                         "Content-Type": "multipart/form-data",
//                     },
//                     url: "api/work_calender_message",
//                     data: form_Data,
//                 })
//                 .then(function (response) {
//                     console.log(response);
//                     //handle success
//                 })
//                 .catch(function (response) {
//                     //handle error
//                     console.log(response);
//                 });
//             _this.reloadMessages();
//         },
//         getUser: function () {
//             var token = localStorage.getItem("token");
//             var form_Data = new FormData();
//             let _this = this;

//             form_Data.append("jwt", token);

//             axios({
//                     method: "post",
//                     headers: {
//                         "Content-Type": "multipart/form-data",
//                     },
//                     url: "api/on_duty_get_myname",
//                     data: form_Data,
//                 })
//                 .then(function (response) {
//                     //handle success
//                     _this.user = response.data.username;
//                 })
//                 .catch(function (response) {
//                     //handle error
//                 });
//         },
//         reloadMessages: function () {
//             let _this = this;
//             _this.myVar = setTimeout(function () {
//                 _this.getMessages();
//             }, 500);
//         },
//         clear: function () {
//             let _this = this;
//             clearTimeout(_this.myVar);
//         },
//     },
// });



// var project = new Vue({
//     el: "#projects",
//     data: {
//         projects: [],
//         project_id : 0,

//         stages: [],
//         stage_id : 0,

//         add_project_id : 0,
//         add_stage_id : 0,
  
//     },
//     created() {
//         let _this = this;
//         let uri = window.location.href.split("?");
//         if (uri.length >= 2) {
//             let vars = uri[1].split("&");

//             let tmp = "";
//             vars.forEach(async function(v) {
//                 tmp = v.split("=");
//                 if (tmp.length == 2) {
//                 switch (tmp[0]) {
//                     case "project_id":
//                         _this.project_id = tmp[1];
//                         _this.add_project_id = tmp[1];
//                     break;

//                     case "stage_id":
//                         _this.stage_id = tmp[1];
//                         _this.add_stage_id = tmp[1];
//                     break;
                
//                     default:
//                     console.log(`Too many args`);
//                 }
//                 }
//             });
//         }

//         //this.getProjects();
//     },

//     methods: {
//         getProjects() {
//             let _this = this;
//             let token = localStorage.getItem('accessToken');
//             axios
//                 .get('api/project02_get_project_name_by_keyword', { headers: { "Authorization": `Bearer ${token}` } })  
//                 .then(
//                     (res) => {
//                         _this.projects = res.data;
//                     },
//                     (err) => {
//                         alert(err.response);
//                     },
//                 )
//                 .finally(() => {
//                 });
//         },

//         async getStages(pid) {
//             let _this = this;
//             let token = localStorage.getItem('accessToken');

//             // clear select sc_related_stage_id (add by edit)
//             // var select = document.getElementById("sc_related_stage_id");
//             // var length = select.options.length;
//             // for (i = length-1; i >= 0; i--) {
//             //     select.options[i] = null;
//             // }

//             if(pid != undefined) 
//                 _this.project_id = pid;

//             let res = await axios.get('api/project02_stages', { headers: { "Authorization": `Bearer ${token}` }, params: { pid: _this.project_id } });
//                 _this.stages = res.data;

//             console.log("getStages");
//             },
        
//             clear(){
//                 let _this = this;
//                 _this.project_id = 0;
//                 _this.stage_id = 0;
//                 _this.stages = [];
//             },

//     },
// });