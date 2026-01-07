module.exports = function (grunt) {
	grunt.loadNpmTasks("grunt-contrib-uglify");
	// grunt.loadNpmTasks("grunt-contrib-concat");

	grunt.initConfig({
		uglify: {
			login: {
				src: [
					"plugins/jquery/jquery.min.js",
					"plugins/bootstrap/js/bootstrap.js",
					"plugins/node-waves/waves.js",
					"plugins/jquery-validation/jquery.validate.js",
					"js/admin.js",
					"js/pages/examples/sign-in.js",
					"js/pages/ui/notifications.js"
				],
				dest: "js/login.min.js"
			},
			plugins: {
				src: [
					"plugins/jquery/jquery.min.js",
					"js/custom/jquery-ui.js",
					"plugins/bootstrap/js/bootstrap.js",
					"plugins/bootstrap-select/js/bootstrap-select.js",
					"plugins/jquery-slimscroll/jquery.slimscroll.js",
					"plugins/node-waves/waves.js",
					"plugins/jquery-countto/jquery.countTo.js",
					"plugins/jquery-sparkline/jquery.sparkline.js",
					"plugins/jquery-spinner/js/jquery.spinner.js",
					"plugins/multi-select/js/jquery.multi-select.js",
					"plugins/jquery-datatable/jquery.dataTables.js",
					"plugins/jquery-datatable/dataTables.responsive.min.js",
					"plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js",
					"plugins/bootstrap-notify/bootstrap-notify.js",
					// "plugins/jquery-datatable/extensions/export/*.js"
				],
				dest: "js/plugins.min.js"
			},
			custom: {
				src: [
					"js/custom/dataTables.buttons.min.js",
					"js/custom/buttons.print.min.js",
					"js/custom/buttons.colVis.min.js",
					"js/custom/dataTables.select.min.js",
					"js/custom/jquery-confirm.js",
					"js/custom/Chart.min.js",
					"js/admin.js",
					"js/demo.js",
					"js/pages/index.js",
					"js/pages/ui/tooltips-popovers.js"
				],
				dest: "js/custom.min.js"
			}
		}
	});

	// Tarefas que serão executadas
	grunt.registerTask('default', ['uglify']);
};
