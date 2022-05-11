<?php
/**
 * Debug helper
 * Is current user id = 5 Ali Akpanov 
 *
 * @return bool
 */
function  me($item) 
{ 
    if(auth()->user()->ID == 5) {
        dd($item);
    }
} 

function memp($item) 
{ 
    if(auth()->user()->ID == 5) {
        dump($item);
    }
} 

function ali() {
    return auth()->user()->ID == 5;
}