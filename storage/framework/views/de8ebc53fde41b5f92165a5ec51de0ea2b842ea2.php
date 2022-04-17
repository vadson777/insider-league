<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#ffffff">

    <title><?php echo $__env->yieldContent('meta_title', config('app.name')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', config('app.name')); ?>"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body {font-family: 'Nunito', sans-serif;}</style>

    <?php echo $__env->yieldPushContent('head_meta'); ?>
    <?php echo $__env->yieldPushContent('head_styles'); ?>
    <?php echo $__env->yieldPushContent('head_scripts'); ?>
</head>
<body>
    <div class="container-fluid">
	    <?php echo $__env->yieldContent('body'); ?>
    </div>
    <?php echo $__env->yieldPushContent('body_scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/layout.blade.php ENDPATH**/ ?>