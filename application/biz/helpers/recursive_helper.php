<?php
function recursiveMenu($sourceArr,$parent, $level, &$newMenu){
    if(count($sourceArr)>0){
        $class = ($level == 0) ? ' class="sTree2 listsClass" id="sTree2"' : '';
        $newMenu .= '<ul'.$class.'>';
        foreach ($sourceArr as $key => $value){
            $id   = $value['id'];
            $name = $value['name'];
            $danhmuc = $value['danhmuc'];
            $duration = !empty($value['duration']) ? $value['duration'] : 0;
            $tile = !empty($value['tile']) ? $value['tile'] : 0;
            
            $xemlist = !empty($value['xemlist']) ? $value['xemlist'] : '';
            $approvelist = !empty($value['approvelist']) ? $value['approvelist'] : '';
            $implementlist = !empty($value['implementlist']) ? $value['implementlist'] : '';
            
            if($value['parent'] == $parent){
                $newMenu .= '<li class="sortableListsOpen" data-module="'.$id.'" id="t_'.$id.'" data-tile="'. $tile .'" data-implementlist="'. $implementlist .'" data-approvelist="'. $approvelist .'" data-xemlist="'. $xemlist .'" data-duration="'. $duration .'" data-danhmuc="'. $danhmuc .'" data-name="'.$name.'"><div>'.$value['name'].' <span style="color: #666">(dự kiến: '. $duration .' ngày)</span></div></li>';
                $newParent = $value['id'];
                $newLevel  = $value['level'];
                unset($sourceArr[$key]);

                recursiveMenu($sourceArr,$newParent, $newLevel , $newMenu);
            }
        }

        $newMenu .= '</ul>';
    }
}