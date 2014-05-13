<?php

namespace includes;

require_once 'kwps_post_status.php';

    class Locked extends  Kwps_Post_Status{

        public static $post_status = 'locked';

        public static $label_singular ='Locked';
        public static $label_plural ='Locked';

        public static $available_post_types = array('kwps_test_collection');
    }