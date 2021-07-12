
let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    id: 0,
    username: '',
    email: '',
    password: '',
    password1: '',
    status: 0,
    status_1: 0,
    is_admin: '',

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

    receive_records: [],
    record: {},

    error_username: '',
    error_password: '',
    error_email:''
};

var app = new Vue({
	el: '#mainContent',

	data: mainState,

	created () {
      console.log('Vue created');
      this.getReceiveRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);
    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');

        //this.setPages();
        return this.paginate(this.receive_records);
      }
    },

    watch: {
      receive_records () {
        console.log('Vue watch receive_records');
        //this.setPages();
      },

    },
 
	methods:{
		getReceiveRecords: function(keyword) {
        let _this = this;
          console.log("getReceiveRecords");
            axios.get('../api/user.php')
                .then(function(response) {
                    console.log(response.data);
                    _this.receive_records = response.data;

                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
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
          return  this.user_records.slice(from, to);
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

        resetError: function() {
          console.log("resetError");
            this.error_username = '';
            this.error_email = '';
            this.error_password = '';
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
 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

		shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },
 
		clearMessage: function(){
			app.errorMessage = '';
			app.successMessage = '';
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

        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('username', this.record.username)
            formData.append('email', this.record.email)
            formData.append('status', this.record.status)
            formData.append('status_1', this.record.status_1)
            formData.append('is_admin', this.record.is_admin)
            formData.append('crud', "del");
            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/user.php',
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


            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        updateStatus: function(event) {
          console.log("updateStatus");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.status = 1;
            } else {
                this.status = 0;
            }
        },

        updateStatus_1: function(event) {
          console.log("updateStatus_1");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.status_1 = 1;
            } else {
                this.status_1 = 0;
            }
        },

        updateIsAdmin: function(event) {
          console.log("updateIsAdmin");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.is_admin = "1";
            } else {
                this.is_admin = "0";
            }
        },

        updateEditStatus: function(event) {
          console.log("updateEditStatus");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.status = 1;
            } else {
                this.record.status = 0;
            }
        },

        updateEditStatus_1: function(event) {
          console.log("updateEditStatus");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.status_1 = 1;
            } else {
                this.record.status_1 = 0;
            }
        },

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();

            if (this.record.username == "") 
            {
              this.error_username = '使用者名稱需輸入 (username required)';
              $(window).scrollTop(0);
              return false;
            } 

            if (this.record.email == "") 
            {
              this.error_email = 'email需輸入 (email required)';
              $(window).scrollTop(0);
              return false;
            } 


            formData.append('username', this.record.username)
            formData.append('email', this.record.email)
            formData.append('status', this.record.status)
            formData.append('status_1', this.record.status_1)
            formData.append('is_admin', this.record.is_admin)
           
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/user.php',
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
                        
                  }
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });

                app.resetForm();
        },

        createReceiveRecord: function() {
            console.log("createReceiveRecord");

            let formData = new FormData();

            if (this.username == "") 
            {
              this.error_username = '使用者名稱需輸入 (username required)';
              $(window).scrollTop(0);
              return false;
            } 

            if (this.email == "") 
            {
              this.error_email = 'email需輸入 (email required)';
              $(window).scrollTop(0);
              return false;
            } 

            if (this.password == "") 
            {
              this.error_password = '密碼需輸入 (password required)';
              $(window).scrollTop(0);
              return false;
            } 

            if (this.password1 == "") 
            {
              this.error_password = '密碼需輸入 (password required)';
              $(window).scrollTop(0);
              return false;
            } 

            if (this.password !== this.password1) 
            {
              this.error_password = '密碼驗證不符 (password not match)';
              $(window).scrollTop(0);
              return false;
            } 

            formData.append('username', this.username)
            formData.append('email', this.email)
            formData.append('password', this.password)
            formData.append('status', this.status)
            formData.append('status_1', this.status_1)
            formData.append('is_admin', this.is_admin)
            formData.append('crud', "insert");
            formData.append('id', '');

            var receive_record = {};
            formData.forEach(function(value, key) {
                receive_record[key] = value;
            });

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/user.php',
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
            
        },

        resetForm: function() {
          console.log("resetForm");
            this.username = '';
            this.email = '';
            this.password = '';
            this.password1 = '';
            this.status = 0;
            this.status_1 = 0;
            this.is_admin = '';
            this.isEditing = false;
            this.record = {};

            this.resetError();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },


        updateEditIsAdmin: function(event) {
          console.log("updateEditIsAdmin");
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.is_admin = "1";
            } else {
                this.record.is_admin = "0";
            }
        }
 
	}
});