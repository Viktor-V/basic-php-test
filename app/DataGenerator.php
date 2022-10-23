<?php

class DataGenerator{
    function __construct()
    {
        global $a;
        $a->words = explode("\n", "tip
subject
betray
plead
manufacturer
owe
coalition
court
far
value
buy
stimulation
sculpture
dip
benefit
excuse
revolution
illusion
facility
wealth
commitment
prince
channel
first-hand
researcher
extract
queen
copyright
hostage
crown
tell
Koran
week
flat
cancel
accompany
calf
me
healthy
contrary
edition
stunning
talk
buttocks
range
provide
constraint
bride
cereal
election");
    }

    public function generateSubs($parentID, $min_childes, $max_childes){
        global $a;
        $childes = rand($min_childes, $max_childes);
        for($i = 0; $i < $childes; $i++){
            if($a->sql->insert('item_categories', [
                    'item_categoriesID' => $parentID,
                    'is_deleted' => floor(rand(1,12)/10),
                    'name' => $a->words[rand(0, count($a->words)-1)].' ' .$a->words[rand(0, count($a->words)-1)],
                ]) && ($newID = $a->sql->getLastID())
            ){
                self::generateSubs($newID, 0, $parentID ? ($max_childes-1) : 4);
            }
        }
    }
}