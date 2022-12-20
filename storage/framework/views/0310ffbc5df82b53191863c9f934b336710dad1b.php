   <div id="app">
     <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" class="logo" href="<?php echo e(url('/')); ?>">
                        <?php echo e(config('app.name', 'MediaSend')); ?>

                    </a>
                </div>
                <div class="collapse navbar-collapse" id="app-navbar-collapse"> 
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>
                    <ul class="nav navbar-nav navbar-right">        
                        <?php if(auth()->guard()->guest()): ?>
                            <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                            <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
                        <?php else: ?>
                         <span class="time">10:34</span>
                         <span class="balanse">Баланс: <em>30 000 Т.</em></span>
                         <span class="id"><em>Ваш ID: </em>3456789</span>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    <?php echo e(Auth::user()->name); ?> <span class="caret"></span>
                                </a>        
                              <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo e(route('logout')); ?>" 
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                          Выход
                                        </a>

                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                            <?php echo e(csrf_field()); ?>

                                        </form>
                                    </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav><?php /**PATH /var/www/job/resources/views/includes/navbar.blade.php ENDPATH**/ ?>