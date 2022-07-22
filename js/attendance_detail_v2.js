
var app = new Vue({
  el: '#app',
  data:{
  
    mdate: '',
    username: '',
    department: '',
    position: '',
    receive_records: [],
    leave_records: [],
  },

  created () {
    const _this = this;
    let uri = window.location.href.split('?');
    if (uri.length == 2)
    {
      let vars = uri[1].split('&');
      let getVars = {};
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        {
            if(tmp[0] == 'username')
                _this.username = tmp[1];
            if(tmp[0] == 'date')
                _this.mdate = tmp[1];
        }
      });
    }
    if(this.username !== '' && this.mdate !== '')
    {
        this.getRecords();
        //this.getLeaveRecords();
        
    }

  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },

    displayedLeaveRecord () {
        return this.leave_records;
      },
  },

  mounted(){
   
  },

  methods:{
    getRecords: function(keyword) {
        axios.get('api/attendance_detail_v2.php?username='+this.username+'&date='+this.mdate)
            .then(function(response) {
                console.log(response.data);
                app.receive_records = response.data;
                app.username = app.receive_records[0].username;
                app.department = app.receive_records[0].department;
                app.position = app.receive_records[0].position;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    getLeaveRecords: function(keyword) {
        axios.get('api/attendance_leave_detail?username='+this.username+'&date='+this.mdate)
            .then(function(response) {
                console.log(response.data);
                app.leave_records = response.data;

                app.username = app.leave_records[0].username;
                app.department = app.leave_records[0].department;
                app.position = app.leave_records[0].position;

            })
            .catch(function(error) {
                console.log(error);
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