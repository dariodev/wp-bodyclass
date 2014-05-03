<?php

  // Add role class to body
  function add_role_to_body($classes) {
  
    global $current_user;
    $user_role = array_shift($current_user->roles);
    
    // add class 'role-user_role' to the $classes array
    $classes[] = 'role-'. $user_role; 
    
    return $classes;
  
  }
  
  add_filter('body_class','add_role_to_body');

?>
