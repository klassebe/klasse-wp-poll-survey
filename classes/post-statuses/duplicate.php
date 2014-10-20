<?php

namespace kwps_classes;

require_once 'kwps-post-status.php';

    class Duplicate extends Kwps_Post_Status{
        public static $post_status = 'duplicate';

        public static $label_singular ='Duplicate';
        public static $label_plural ='Duplicates';

        public static $available_post_types = array('kwps_test_modus');
    }