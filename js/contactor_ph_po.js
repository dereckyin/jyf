Vue.config.ignoredElements = ['eng']
Vue.config.ignoredElements = ['cht']

let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    company: '',
    customer: '',
    address: '',
    phone: '',
    fax: '',
    mobile: '',
    email: '',
    remark: '',
    color: '',

    acquisition: '',
    acquisition_by: '',
    date_to_call: '',
 
    contactors: [],
    record: {},

    // error
    company_err: '',
    customer_err: '',
    address_err: '',
    phone_err: '',
    fax_err: '',
    mobile_err: '',
    email_err: '',
    remark_err: '',

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

          return 'directory_excel_ph_po.php?id=' + favorite.join(",");
        }
    },

    updated: function() {
        console.log('Vue updated')
    },

    methods: {
        getRecords: function(keyword) {
          console.log("getRecords");
            axios.get('api/contactor_ph_po.php?keyword=' + keyword)
                .then(function(response) {
                    console.log(response.data);
                    app.contactors = response.data;
                    
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
                formData.append('company', this.company)
                formData.append('customer', this.customer)
                formData.append('address', this.address)
                formData.append('phone', this.phone)
                formData.append('fax', this.fax)
                formData.append('mobile', this.mobile)
                formData.append('email', this.email)
                formData.append('color', this.color)
                formData.append('remark', this.remark)
                formData.append('acquisition', this.acquisition)
                formData.append('acquisition_by', this.acquisition_by)
                formData.append('date_to_call', this.date_to_call)
   
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
                        url: 'api/contactor_ph_po.php',
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

            if (!this.validateForm())
            {
              return;
            }

            targetId = this.record.id;
            let formData = new FormData();
            //console.log(document.querySelector("input[name=datepicker1]").value)
            formData.append('company', this.record.company)
            formData.append('customer', this.record.customer)
            formData.append('address', this.record.address)
            formData.append('phone', this.record.phone)
            formData.append('fax', this.record.fax)
            formData.append('mobile', this.record.mobile)
            formData.append('email', this.record.email)
            formData.append('color', this.record.color)
            formData.append('remark', this.record.remark)
            formData.append('acquisition', this.record.acquisition)
            formData.append('acquisition_by', this.record.acquisition_by)
            formData.append('date_to_call', this.record.date_to_call)
         
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/contactor_ph_po.php',
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
            formData.append('company', "")
            formData.append('customer', "")
            formData.append('address', "")
            formData.append('phone', "")
            formData.append('fax', "")
            formData.append('mobile', "")
            formData.append('email', "")
            formData.append('color', "")
            formData.append('acquisition', "")
            formData.append('acquisition_by', "")
            formData.append('date_to_call', "")
            formData.append('remark', "")
         
            formData.append('crud', "del");
            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/contactor_ph_po.php',
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
            this.company = '';
            this.customer = '';
            this.address = '';
            this.phone = '';
            this.fax = '';
            this.mobile = '';
            this.email = '';
            this.color = '';
            this.acquisition = '';
            this.acquisition_by = '';
            this.date_to_call = '';
            this.remark = '';
        
         
            this.isEditing = false;
            this.record = {};

            this.resetError();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getRecords('');
        },


        resetError: function() {
          console.log("resetError");
            this.customer_err = '';
         
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

        validateForm() {
            console.log("validateForm");
            this.resetError();

            if (this.isEditing) 
            {
              if (this.record.customer == "") 
              {
                  this.customer_err = '必須輸入收件人 (customer required)';
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

              if (this.customer == "") 
              {
                  this.customer_err = '必須輸入收件人 (customer required)';
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