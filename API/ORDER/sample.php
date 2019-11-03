<?php

//sample array

$cars = array
  (
  array('urun' => "Yogurt", 'adet' => 4, 'tutar' => 92),
  array('urun' => "Tatli", 'adet' => 3, 'tutar' => 42),
  );

echo json_encode(unserialize(serialize($cars)));