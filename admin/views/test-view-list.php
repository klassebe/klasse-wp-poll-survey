<?php

    if( !class_exists('Test_View_List')):
        class Test_View_List {

            public static function render($list_of_tests = array())
            {
            ?>
                <h1><?php _e('Testen') ?></h1>
                <?php foreach ($list_of_tests as $test) : ?>
                    <?php foreach ($test as $property) : ?>
                        <?php echo $property . ' '; ?>
                    <?php endforeach; ?>
                <br>
                <?php endforeach; ?>

                <?php
            }

        }
    endif;
?>
