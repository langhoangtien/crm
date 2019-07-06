<?php
function get_unnecessary_elements($new_array, $old_array) {
    $result = array();
    if(!empty($old_array)) {
        foreach($old_array as $val) {
            if(!in_array($val, $new_array))
                $result[] = $val;
        }
    }

   return $result;
}