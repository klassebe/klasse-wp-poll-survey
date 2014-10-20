<?php

namespace kwps_classes;

require_once 'kwps-post-status.php';

    class Locked extends  Kwps_Post_Status{

        public static $post_status = 'locked';

        public static $label_singular ='Locked';
        public static $label_plural ='Locked';

        public static $available_post_types = array('kwps_version', 'kwps_test_collection');
    }