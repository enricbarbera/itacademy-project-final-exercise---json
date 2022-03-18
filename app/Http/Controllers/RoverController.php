<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoverController extends Controller
{
    public function play(Request $request){
        $in_bounds = true;
        $dim_err = false;
        $pos_err = false;
        $data_err = false;
        $json = $request->json()->all(); // ample(X), llarg(Y), x inicial, y inicial, orientació inicial, llista de moviments (ex. AAALAALAAARAALAAAARA)
        // dd($request);
        // dd($json);
        $xPosition = $json['rover']['initialPosition']['x'];
        $yPosition = $json['rover']['initialPosition']['y'];
        $initialOrientation = $json['rover']['initialOrientation'];
        Switch($initialOrientation){
            case 'N':
                $orientation = 1;
                break;
            case 'E':
                $orientation = 2;
                break;
            case 'S':
                $orientation = 3;
                break;
            case 'W':
                $orientation = 4;
                break;
            default:
                $orientation = 0;
        }
        $width = $json['square']['width'];
        $height = $json['square']['height'];
        // return "$xPosition, $yPosition, $orientation, $width, $height";
        if($width <= 0 || $height <= 0){
            echo 'Dimensions must be both greater than 0<br>';
            $dim_err = true;
        }
        if(!$dim_err && ($xPosition < 0 || $xPosition >= $width-1 || $yPosition < 0 || $yPosition >= $height-1)){
            echo "Rover must be inside square ($width x $height) <br>";
            $pos_err = true;
        }
        if(!$dim_err && !$pos_err && $orientation != '1' && $orientation != '2' && $orientation != '3' && $orientation != '4'){
            echo 'Orientation restricted to: N (North), E (East), S (South), W (West)<br>Rover did not move<br>';
            $data_err = true;
        }
        foreach($json['movement'] as $movement){
            if(!$in_bounds || $dim_err || $pos_err || $data_err){
                break;
            }
            if($movement!='A' && $movement!='R' && $movement!='L'){
                echo 'Movements restricted to: A (advance), R (turn right), L (turn left)<br>Rover did not move<br>';
                $data_err = true;
                break;
            }
            switch($movement){
                case 'A':
                    if($orientation == 1 && $yPosition == $height-1 || $orientation == 2 && $xPosition == $width-1 || $orientation == 3 && $yPosition == 0 || $orientation == 4 && $xPosition == 0){
                        echo 'Rover went out of bounds from position  '.$xPosition.', '.$yPosition.'<br>';
                        $in_bounds = false;
                        break;
                    }else{
                        switch($orientation){
                            case 1:
                                $yPosition++;
                                break;
                            case 2:
                                $xPosition++;
                                break;
                            case 3:
                                $yPosition--;
                                break;
                            case 4:
                                $xPosition--;
                                break;
                        }
                    }
                    break;
                case 'R':
                    $orientation++;
                    if ($orientation == 5){
                        $orientation = 1;
                    }
                    break;
                case 'L':
                    $orientation--;
                    if ($orientation == 0){
                        $orientation = 4;
                    }
                    break;
            }
        }
        if($in_bounds && !$data_err && !$dim_err && !$pos_err){
            Switch ($orientation){
                case 1:
                    $finalOrientation = 'N';
                    break;
                case 2:
                    $finalOrientation = 'E';
                    break;
                case 3:
                    $finalOrientation = 'S';
                    break;
                case 4:
                    $finalOrientation = 'W';
                    break;
            }
            echo 'Final Rover position is: <br> X = '.$xPosition.'<br> Y = '.$yPosition.'<br> Orientation = '.$finalOrientation.'<br>';
            $arr=[$in_bounds, $xPosition, $yPosition, $finalOrientation];
            return response()->json($arr);
        }elseif(!$data_err && !$dim_err && !$pos_err){
            return response()->json($in_bounds);
        }
    }
}
