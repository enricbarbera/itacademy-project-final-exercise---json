<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoverController extends Controller
{
    public function play(Request $request){
        // $mov_err = false;
        $or_err = false;
        $json = $request->json()->all(); // ample(X), llarg(Y), x inicial, y inicial, orientació inicial, llista de moviments (AAALAALAAARAALAAAARA)
        // dd($json);
        // echo $json['width'].'<br>';
        // echo $json['oini'].'<br>';
        // if($json['width']<=0){
        //     echo 'Width must be greater than 0';
        // }
        foreach($json['movement'] as $movement){
            if($json['yini']>=$json['height'] || $json['xini']>=$json['width'] || $json['yini']<=0 || $json['xini']<=0){
                echo 'Rover went out of bounds from position  '.$json['xini'].', '.$json['yini'].'<br>';
                break;
            }
            if($movement!='A' && $movement!='R' && $movement!='L'){
                echo 'Movements restricted to: A (advance), R (turn right), L (turn left)<br>';
                // $mov_err = true;
                break;
            }
            if($json['oini'] != 1 && $json['oini'] != 2 && $json['oini'] != 3 && $json['oini'] != 4){
                echo 'Orientation restricted to: 1 (North), 2 (East), 3 (South), 4 (West)<br>Rover did not move<br>';
                $or_err = true;
                break;
            }
            switch($movement){
                case 'A':
                    switch($json['oini']){
                        case 1:
                            $json['yini']++;
                            break;
                        case 2:
                            $json['xini']++;
                            break;
                        case 3:
                            $json['yini']--;
                            break;
                        case 4:
                            $json['xini']--;
                            break;
                        // default:
                        //     echo 'Orientation restricted to: 1 (North), 2 (East), 3 (South), 4 (West)';
                        //     break;
                    }
                    break;
                case 'R':
                    $json['oini']++;
                    if ($json['oini']==5){
                        $json['oini']=1;
                    }
                    break;
                case 'L':
                    $json['oini']--;
                    if ($json['oini']==0){
                        $json['oini']=4;
                    }
                    break;
                // default:
                //     echo 'Movements restricted to: A (advance), R (turn right), L (turn left)';
            }
        }
        if ($or_err){
            return 'Final Rover position is: <br> X = '.$json['xini'].'<br> Y = '.$json['yini'];
        }else{
            return 'Final Rover position is: <br> X = '.$json['xini'].'<br> Y = '.$json['yini'].'<br> Orientation = '.$json['oini'];
        }
    }
}
