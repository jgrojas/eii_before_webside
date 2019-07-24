module.exports = function(grunt) {

// 1. All configuration goes here 
grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'), 
	"svg-sprites": {
		options: { spriteElementPath: "images" },
		icons: {
			options: {
				spritePath: "images",
				cssPath: "css", 
				cssUnit: "rem", 
				layout: "packed", 
				sizes: {
					small: "15", 
					large: "30", 
				}, 
				refSize: "small"
			}
		}
	}		
}); // end grunt.initConfig

// 3. Where we tell Grunt we plan to use this plug-in.
grunt.loadNpmTasks('grunt-dr-svg-sprites');

// 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
grunt.registerTask('default', ['svg-sprites']);    

};
