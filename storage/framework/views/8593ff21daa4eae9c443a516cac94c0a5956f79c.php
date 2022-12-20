<div class="fixed pin-t pin-x z-40">
    <div class="bg-gradient-primary text-white h-1"></div>

    <nav class="flex items-center justify-between text-black bg-navbar shadow-xs h-16">
        <div class="flex items-center flex-no-shrink">
            <a href="<?php echo e(url('/')); ?>" class="flex items-center flex-no-shrink text-black mx-4">
                <?php echo $__env->make("larecipe::partials.logo", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <p class="inline-block font-semibold mx-1 text-grey-dark">
                    <?php echo e(config('app.name')); ?>

                </p>
            </a>

            <div class="switch">
                <input type="checkbox" name="1" id="1" v-model="sidebar" class="switch-checkbox" />
                <label class="switch-label" for="1"></label>
            </div>
        </div>

        <div class="block mx-4 flex items-center">
            <?php if(config('larecipe.search.enabled')): ?>
                <larecipe-button id="search-button"
                    :type="searchBox ? 'primary' : 'link'"
                    @click="searchBox = ! searchBox"
                    class="px-4">
                    <i class="fas fa-search" id="search-button-icon"></i>
                </larecipe-button>
            <?php endif; ?>

            <larecipe-button tag="a" href="https://github.com/saleem-hadad/larecipe" target="__blank" type="black" class="mx-2 px-4">
                <i class="fab fa-github"></i>
            </larecipe-button>

            
            <larecipe-dropdown>
                <larecipe-button type="primary" class="flex">
                    <?php echo e($currentVersion); ?> <i class="mx-1 fa fa-angle-down"></i>
                </larecipe-button>

                <template slot="list">
                    <ul class="list-reset">
                        <?php $__currentLoopData = $versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="py-2 hover:bg-grey-lightest">
                                <a class="px-6 text-grey-darkest" href="<?php echo e(route('larecipe.show', ['version' => $version, 'page' => $currentSection])); ?>"><?php echo e($version); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </template>
            </larecipe-dropdown>
            

            <?php if(auth()->guard()->check()): ?>
                
                <larecipe-dropdown>
                    <larecipe-button type="white" class="ml-2">
                        <?php echo e(auth()->user()->name ?? 'Account'); ?> <i class="fa fa-angle-down"></i>
                    </larecipe-button>

                    <template slot="list">
                        <form action="/logout" method="POST">
                            <?php echo e(csrf_field()); ?>


                            <button type="submit" class="py-2 px-4 text-white bg-danger inline-flex"><i class="fa fa-power-off mr-2"></i> Logout</button>
                        </form>
                    </template>
                </larecipe-dropdown>
                
            <?php endif; ?>
        </div>
    </nav>
</div><?php /**PATH /var/www/job/resources/views/vendor/larecipe/partials/nav.blade.php ENDPATH**/ ?>