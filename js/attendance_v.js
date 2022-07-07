
var app = new Vue({
  el: '#app',
  data:{
    receive_records: [],
    leave_records:{},
    holiday_records:{},
    username : [],

    apply_start:'',
    apply_end:'',
    apply_name:'',

  },

  created () {
    this.getRecords();
    this.getUsers();
    //this.getLeaveRecords();
    //this.getHolidayRecords();
  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },
  },

  mounted(){
   
  },

  methods:{
    getUsers: function() {
      let _this = this;
      axios.get('api/duty_get_user_v.php')
      .then(function(response) {
          console.log(response.data);
          _this.username = response.data;
      })
      .catch(function(error) {
          console.log(error);
      });
  },

    getRecords: function(keyword) {
        axios.get('api/attendance_v.php')
            .then(function(response) {
                console.log(response.data);
                app.receive_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    getLeaveRecords: function(keyword) {
        axios.get('api/leave.php')
            .then(function(response) {
                console.log(response.data);
                app.leave_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    getHolidayRecords: function(keyword) {
        axios.get('api/holiday.php')
            .then(function(response) {
                console.log(response.data);
                app.holiday_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    export_print() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("apply_start", this.apply_start);
      form_Data.append("apply_end", this.apply_end);
      form_Data.append("apply_name", this.apply_name);


      axios({
        method: "post",
        url: "api/attendance_print_v.php",
        data: form_Data,
        responseType: "blob",
      })
          .then(function(response) {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
               
                  link.setAttribute('download', 'Attendance Report.xlsx');
               
                document.body.appendChild(link);
                link.click();

          })
          .catch(function(response) {
              //handle error
              console.log(response)
          });
    },

      reset: function() {
          
            this.today = '';
            this.type = '';
            this.location = '';
            this.remark = '';
            this.time = '';
            this.explanation = '';
            this.err_msg = '';
            

            this.getLocation();
            this.getToday();
            
        },
 
  }
});