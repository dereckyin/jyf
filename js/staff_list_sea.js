Vue.config.ignoredElements = ['eng']

let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    staff: '',
    phone: '',
    email: '',
    address: '',
    punch : 0,

    error_staff: '',
    error_phone: '',
    error_email: '',
    error_address: '',

    contactors:[],
   
    record: [],


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

    // searching
    keyword: ''
};

var app = new Vue({
    el: '#contactor',

    data: mainState,

    created () {
      console.log('Vue created');
      this.getRecords('');
      this.perPage = this.inventory.find(i => i.id === this.perPage);
    },

    watch: {
      contactors () {
        console.log('Vue watch contactors');
        this.setPages();
      },

      keyword () {
        console.log('Vue watch keyword');
        this.getRecords(this.keyword);
      }
    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');
        this.setPages();
        return this.paginate(this.contactors);
      },

      pageUrl() {
          var favorite = [];

          for (i = 0; i < this.contactors.length; i++) 
          {
              if(this.contactors[i].is_checked == 1)
                favorite.push(this.contactors[i].id);
          }

          return 'staff_list_excel_sea.php?id=' + favorite.join(",");
        }
    },

    updated: function() {
        console.log('Vue updated')
    },

    methods: {
        getRecords: function(keyword) {
          console.log("getRecords");
          let _this = this;
            axios.get('api/staff_list_sea.php?keyword=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    _this.contactors = response.data;
                    
                    //this.$refs.showUser1.$el.DataTable();
                    console.log("getRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        filteredList() {
          return this.contactors.filter(post => {
            return (contactors.shipping_mark.toLowerCase().includes(this.keyword.toLowerCase()) || contactors.customer.toLowerCase().includes(this.keyword.toLowerCase()))
          })
        },

        getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

        setPages () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.contactors.length / this.perPage.id);
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
          return  this.contactors.slice(from, to);
        },

        createReceiveRecord: function() {
            console.log("createReceiveRecord");

            if (this.validateForm()) {

                let formData = new FormData();
                //console.log("datepicker:", this.date_receive)
                //console.log(document.querySelector("input[name=datepicker]").value)
                formData.append('staff', this.staff)
                formData.append('phone', this.phone)
                formData.append('email', this.email)
                formData.append('address', this.address)
                formData.append('punch', this.punch)
            
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
                        url: 'api/staff_list_sea.php',
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

        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('staff', this.record.staff)
            formData.append('phone', this.record.phone)
            formData.append('email', this.record.email)
            formData.append('address', this.record.address)
            formData.append('punch', this.record.punch)
          
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/staff_list_sea.php',
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
                        
                        app.resetForm();
                      
                  }
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        delReceiveRecord: function(id) {
            console.log("delReceiveRecord")

            //targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('staff', "")
            formData.append('phone', "")
            formData.append('email', "")
            formData.append('address', "")
            formData.append('punch', "")
          
            formData.append('crud', "del");
            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/staff_list_sea.php',
                    data: formData
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    if (response.data !== "")
                        console.log(response.data);
                    //this.$forceUpdate();
                    //app.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        resetForm: function() {
          console.log("resetForm");
            this.staff = '';
            this.phone = '';
            this.email = '';
            this.address = '';
            this.punch = 0;
        
         
            this.isEditing = false;
            this.record = {};

            this.resetError();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getRecords('');
        },

        setPunch: function(event) {
     
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.punch = 1;
            } else {
                this.punch = 0;
            }
        },

        updatePunch: function(event) {
     
            let checked = event.target.checked;
            let value = event.target.value;
            if (checked) {
                this.record.punch = 1;
            } else {
                this.record.punch = 0;
            }
        },

        resetError: function() {
          console.log("resetError");
            this.error_staff = '';
            this.error_phone = '';
            this.error_email = '';
            this.error_address = '';
        },

        deleteRecord() {
          console.log("deleteRecord");
          var favorite = [];
          this.resetError();

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            for (i = 0; i < this.contactors.length; i++) 
            {
              if(this.contactors[i].is_checked == 1)
                favorite.push(this.contactors[i].id);
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

        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.contactors.length; i++) 
            {
              if(this.contactors[i].is_checked == 1)
                favorite.push(this.contactors[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                return;
            }
            this.record = this.shallowCopy(app.contactors.find(element => element.id == favorite));
            this.isEditing = true;

          
            $('.block.record').toggleClass('show');

            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        isNumeric: function (n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        },

        isDate: function (value) {
            switch (typeof value) {
                case 'string':
                    return !isNaN(Date.parse(value));
                case 'object':
                    if (value instanceof Date) {
                        return !isNaN(value.getTime());
                    }
                default:
                    return false;
            }
        },

        isEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },

        unCheckCheckbox()
        {
            for (i = 0; i < this.contactors.length; i++) 
            {
              this.contactors[i].is_checked = false;
            }
        },

        toggleCheckbox()
        {
          var i;
            for (i = 0; i < this.contactors.length; i++) 
            {
              this.contactors[i].is_checked = (this.clicked == 1 ? 0 : 1);
            }

            this.clicked = (this.clicked == 1 ? 0 : 1);
        },

        logout: function() {
          Swal.fire({
            title: "Logout",
            text: "Are you sure to logout?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.value) {
    
              setTimeout(function(){
                window.location.href="index.php";
              },500);
            }
          });
        },

        validateForm() {
            console.log("validateForm");
            this.resetError();

            if (this.isEditing) 
            {
              if (this.record.staff == "") 
              {
                  this.error_staff = '必須輸入姓名 (Name required)';
                  $(window).scrollTop(0);
                  return false;
              } 
/*
              if (this.record.supplier == "") 
              {
                  this.error_supplier = '必須輸入寄件人 (supplier required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.record.c_phone.length > 80) 
              {
                  this.error_c_phone = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.record.c_fax.length > 80) 
              {
                  this.error_c_fax = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.record.s_phone.length > 80) 
              {
                  this.error_s_phone = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.record.s_fax.length > 80) 
              {
                  this.error_s_fax = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 
*/
            }
            else
            {

              if (this.staff == "") 
              {
                  this.error_customer = '必須輸入姓名 (Name required)';
                  $(window).scrollTop(0);
                  return false;
              } 
/*
              if (this.supplier == "") 
              {
                  this.error_supplier = '必須輸入寄件人 (supplier required)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.c_phone.length > 80) 
              {
                  this.error_c_phone = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.c_fax.length > 80) 
              {
                  this.error_c_fax = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.s_phone.length > 80) 
              {
                  this.error_s_phone = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 

              if (this.s_fax.length > 80) 
              {
                  this.error_s_fax = '請勿超過80個字元 (Please do not over 80 characters)';
                  $(window).scrollTop(0);
                  return false;
              } 
*/
            }

            return true;
          
        },
    },
})