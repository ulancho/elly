var App = (function(){
		// Тут можно определить приватные переменные и методы, например
	var someArray = []; // Не будет доступен по ссылке App.someArray, не как либо еще вне объекта

		// Объект, содержащий публичное API
	return {
		init: function() {
		},

		needLogin: function(options) {
			EllyCore.showAjaxForm({
				element: $('#form_login_contaner'),
				title: 'Sign in',
			});

			return false;
		},

		btnSignup: function(options) {
			EllyCore.showAjaxForm({
				element: $('#form_signup_contaner'),
				title: 'Create a New Account',
			});

			return false;
		},
	}
})();

App.init();
