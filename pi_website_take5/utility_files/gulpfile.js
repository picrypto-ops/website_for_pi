gulp.task('sass', function() {
    return gulp
        .src('./assets/scss/main.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./assets/css'));
}); 