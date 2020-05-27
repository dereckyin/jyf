var app = new Vue({
	el: '#mainContent',
	data:{
		successMessage: "",
		errorMessage: "",
		logDetails: {username: '', password: '', g_recaptcha_response: ''},
	},
 
	methods:{
		keymonitor: function(event) {
       		if(event.key == "Enter"){
         		app.checkLogin();
        	}
       	},

		checkLogin: function(){
			if (app.logDetails.username === '')
				return false;
			if(app.logDetails.password === '')
				return false;
			if (grecaptcha.getResponse() === '')                             
	          return false
	      	else
	      		app.logDetails.g_recaptcha_response = grecaptcha.getResponse();

	        var logForm = app.toFormData(app.logDetails);
			console.log(app.logDetails);
			axios.post('api/login.php', logForm)
				.then(function(response){
 					console.log(response);
					if(response.data['error']){
						app.errorMessage = response.data['error'];
					}
					else{
						setCookie("jwt", response.data['jwt']);
						setCookie("uid", response.data['uid']);
						//localStorage.token = response.data['jwt'];
						app.logDetails = {username: '', password:''};
						app.successMessage = response.data['message'];
						setTimeout(function(){
							window.location.href="main.php";
						},1000);
 
					}
				});
		},

		register:  function(obj){
			window.location.href = "create_user.php";
		},

 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

 
		clearMessage: function(){
			app.errorMessage = '';
			app.successMessage = '';
		}
 
	}
});