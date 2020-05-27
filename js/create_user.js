var app = new Vue({
	el: '#mainContent',
	data:{
		successMessage: "",
		errorMessage: "",
		registDetails: {username: '', email: '', password1: '', password2: '', g_recaptcha_response: ''},
	},
 
	methods:{
		keymonitor: function(event) {
       		if(event.key == "Enter"){
         		app.checkLogin();
        	}
       	},

       	sign_up: function(){
			if (app.registDetails.username === '')
			{
				this.$refs.username.focus();
				return false;
			}
			if(app.registDetails.email === '')
			{
				this.$refs.email.focus();
				return false;
			}
			if(!app.isEmail(app.registDetails.email))
			{
				this.$refs.email.focus();
				return false;
			}
			if (app.registDetails.password1 === '')
			{
				this.$refs.password1.focus();
				return false;
			}
			if(app.registDetails.password2 === '')
			{
				this.$refs.password2.focus();
				return false;
			}
			if (app.registDetails.password1 !== app.registDetails.password2)
			{
				this.$refs.password2.focus();
				return false;
			}
			if (grecaptcha.getResponse() === '')
			{
				app.errorMessage = "Please verify recaptcha";
	          	return false;
			}
	      	else
	      		app.registDetails.g_recaptcha_response = grecaptcha.getResponse();

	        var logForm = app.toFormData(app.registDetails);
			console.log(app.registDetails);
			axios.post('api/create_user.php', logForm)
				.then(function(response){
 					console.log(response);
					if(response.data['error']){
						app.errorMessage = response.data['error'];
					}
					else{
						setCookie("jwt", response.data['jwt']);
						setCookie("uid", response.data['uid']);
						app.registDetails = {username: '', email: '', password1: '', password2: '', g_recaptcha_response: ''};
						app.successMessage = response.data['message'];
						setTimeout(function(){
							window.location.href="main.php";
						},1000);
 
					}
				});
		},
 

		cancel:  function(obj){
			window.location.href = "index.php";
		},
 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

		clearForm: function(){
			clearMessage();
			registDetails.username = '';
			registDetails.email = '';
			registDetails.password1 = '';
			registDetails.password2 = '';
			this.successMessage = "";
			this.errorMessage = "";
		},
 
		clearMessage: function(){
			app.errorMessage = '';
			app.successMessage = '';
		},

		isEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
 
	}
});